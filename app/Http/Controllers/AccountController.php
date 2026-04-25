<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use App\Models\StoreOrder;
use App\Models\StoreOrderItem;
use App\Models\Item;
use App\Models\Address;

use App\Models\Subscription;
use App\Models\WalletTransaction;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\StoreAddressRequest;
use App\Http\Requests\ProcessPaymentRequest;
use Illuminate\Support\Facades\DB;
use Midtrans\Config;
use Midtrans\Snap;

class AccountController extends Controller
{
    /**
     * Dashboard Pelanggan
     */
    public function index()
    {
        $user = Auth::user();
        $orders = StoreOrder::where('user_id', $user->id)
            ->select('id', 'user_id', 'order_number', 'address_id', 'shipping_method', 'shipping_cost', 'sub_total', 'grand_total', 'order_status', 'payment_status', 'payment_method', 'created_at', 'updated_at', 'prescription_path')
            ->with(['items.item:id,name,image_path', 'address:id,label,full_address'])
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'orders_page');

        // Fetch specifically for the success modal if requested
        $justPaidOrder = null;
        if (request()->has('just_paid')) {
            $justPaidOrder = StoreOrder::where('user_id', $user->id)
                ->where('id', request('just_paid'))
                ->with(['address', 'items.item'])
                ->first();
        }

        $addresses = Address::where('user_id', $user->id)
            ->select('id', 'user_id', 'label', 'full_address', 'is_primary')
            ->orderBy('is_primary', 'desc')->get();
        
        // Data Langganan Aktif
        $subscriptions = Subscription::where('user_id', $user->id)
            ->select('id', 'user_id', 'item_id', 'quantity', 'interval_days', 'status', 'next_delivery_date')
            ->where('status', 'active')
            ->with('item:id,name,image_path')
            ->get();

        // Personalized Wellness: find article matching latest order's items
        $latestOrder = StoreOrder::where('user_id', $user->id)->latest()->with('items.item:id,name')->first();

        $wellnessArticles = collect();

        if ($latestOrder && $latestOrder->items->isNotEmpty()) {
            // Get item names from the latest order
            $itemNames = $latestOrder->items->pluck('item.name')->filter()->map(fn($n) => strtolower($n));

            // Find articles whose keyword appears in any ordered item name
            $wellnessArticles = \App\Models\HealthArticle::where('is_active', true)
                ->get()
                ->filter(function($a) use ($itemNames) {
                    $keyword = strtolower($a->keyword ?? '');
                    if (!$keyword) return false;
                    foreach ($itemNames as $name) {
                        if (str_contains($name, $keyword) || str_contains($keyword, $name)) {
                            return true;
                        }
                    }
                    return false;
                })
                ->take(5)
                ->values();
        }

        // Fallback: general tips if no match
        if ($wellnessArticles->isEmpty()) {
            $wellnessArticles = \App\Models\HealthArticle::where('is_active', true)->limit(5)->get();
        }

        return view('frontend.account.dashboard', compact('user', 'orders', 'addresses', 'subscriptions', 'wellnessArticles', 'justPaidOrder'));
    }

    /**
     * Wallet / Dompet Saya
     */
    public function showWallet()
    {
        $user = Auth::user();
        $transactions = WalletTransaction::where('user_id', $user->id)
            ->select('id', 'user_id', 'amount', 'type', 'description', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();
        return view('frontend.account.wallet', compact('user', 'transactions'));
    }

    /**
     * Simulation Top Up (Untuk Prototipe)
     */
    public function topUp(Request $request)
    {
        $user = Auth::user();
        $amount = (float) $request->input('amount', 100000);

        DB::transaction(function() use ($user, $amount) {
            $user->increment('wallet_balance', $amount);
            WalletTransaction::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'type' => 'topup',
                'description' => 'Top up saldo dompet (Simulasi)',
            ]);
        });

        return back()->with('success', 'Top up berhasil! Saldo Anda bertambah Rp ' . number_format($amount, 0, ',', '.'));
    }

    /**
     * Tampilkan profil
     */
    public function showProfile()
    {
        $user = Auth::user();
        $addresses = Address::where('user_id', $user->id)
            ->select('id', 'user_id', 'label', 'full_address', 'is_primary')
            ->orderBy('is_primary', 'desc')->get();
        $transactions = WalletTransaction::where('user_id', $user->id)
            ->select('id', 'user_id', 'amount', 'type', 'description', 'created_at')
            ->orderBy('created_at', 'desc')
            ->limit(10) // Optimization: limit transaction history in secondary views
            ->get();
        return view('frontend.account.profile', compact('user', 'addresses', 'transactions'));
    }

    /**
     * Proses pembayaran (Handle Wallet & Subscription Creation)
     */
    public function processPayment(ProcessPaymentRequest $request, $orderId = null)
    {
        $user = Auth::user();
        
        if (!$orderId) {
            if ($request->ajax()) return response()->json(['success' => false, 'error' => 'ID Pesanan tidak ditemukan.'], 422);
            return redirect()->route('account.orders')->with('error', 'ID Pesanan tidak ditemukan.');
        }

        $order = StoreOrder::where('user_id', $user->id)->findOrFail($orderId);
        
        if ($order->payment_status !== 'pending') {
            if ($request->ajax()) return response()->json(['success' => false, 'error' => 'Pesanan ini sudah diproses.'], 422);
            return redirect()->route('account.orders')->with('error', 'Pesanan ini sudah diproses.');
        }

        $subTotal = $order->sub_total;
        $shippingCost = (int)$request->input('shipping_cost', 0);
        
        // Use existing order shipping cost if not provided or invalid
        if ($shippingCost <= 0) {
            $shippingCost = (int)$order->shipping_cost;
        }

        $grandTotal = (float)($subTotal + $shippingCost);

        // Validasi Saldo Wallet
        if ($request->payment_method === 'wallet') {
            if ($user->wallet_balance < $grandTotal) {
                $msg = 'Saldo dompet tidak mencukupi. Sisa saldo: Rp ' . number_format($user->wallet_balance, 0, ',', '.');
                if ($request->ajax()) return response()->json(['success' => false, 'error' => $msg], 422);
                return back()->with('error', $msg);
            }
        }

        // Validasi Paylater
        if ($request->payment_method === 'paylater') {
            if ($user->paylater_limit < $grandTotal) {
                if ($request->ajax()) return response()->json(['success' => false, 'error' => 'Limit Paylater tidak mencukupi.'], 422);
                return back()->with('error', 'Limit Paylater tidak mencukupi.');
            }
        }

        try {
            $order = DB::transaction(function() use ($request, $user, $order, $grandTotal, $shippingCost) {
                $status = in_array($request->payment_method, ['cod', 'bank']) ? 'pending' : 'paid';

                if ($request->payment_method === 'wallet') {
                    // Check balance for Wallet
                    if ($user->wallet_balance < $grandTotal) {
                        throw new \Exception('Saldo dompet tidak mencukupi untuk transaksi ini.');
                    }
                    $user->decrement('wallet_balance', $grandTotal);
                    
                    // Log wallet transaction
                    \App\Models\WalletTransaction::create([
                        'user_id' => $user->id,
                        'type' => 'payment',
                        'amount' => $grandTotal,
                        'description' => "Pembayaran pesanan Pharmacare",
                        'status' => 'completed'
                    ]);
                } elseif ($request->payment_method === 'paylater') {
                    // Check limit for Paylater
                    if ($user->paylater_limit < $grandTotal) {
                        throw new \Exception('Limit Paylater tidak mencukupi untuk transaksi ini.');
                    }
                    $user->decrement('paylater_limit', $grandTotal);
                }

                // MIDTRANS HANDLING
                if ($request->payment_method === 'midtrans') {
                    $status = 'pending'; // Midtrans starts as pending
                }

                $order->update([
                    'shipping_method' => $request->shipping_method,
                    'shipping_cost'   => $shippingCost,
                    'grand_total'     => $grandTotal,
                    'payment_method'  => $request->payment_method,
                    'payment_status'  => $status,
                    'order_status'    => ($status === 'paid' ? 'paid' : 'ordered'),
                ]);

                return $order;
            });

                if ($request->payment_method === 'midtrans') {
                    // Konfigurasi Midtrans
                    Config::$serverKey = config('services.midtrans.server_key');
                    Config::$isProduction = config('services.midtrans.is_production');
                    Config::$isSanitized = config('services.midtrans.is_sanitized');
                    Config::$is3ds = config('services.midtrans.is_3ds');

                    $timestamp = time();
                    $midtransOrderId = $order->order_number . '-' . $timestamp;
                    
                    // Save timestamp to unique_code for status checking later
                    $order->update(['unique_code' => $timestamp]);

                    // Build Item Details
                    $item_details = [];
                    foreach ($order->items as $orderItem) {
                        $item_details[] = [
                            'id' => $orderItem->item_id,
                            'price' => (int) $orderItem->price,
                            'quantity' => (int) $orderItem->quantity,
                            'name' => substr($orderItem->item->name ?? 'Produk Apotek', 0, 50),
                        ];
                    }

                    // Add shipping cost as an item if exists
                    if ($order->shipping_cost > 0) {
                        $item_details[] = [
                            'id' => 'SHIPPING',
                            'price' => (int) $order->shipping_cost,
                            'quantity' => 1,
                            'name' => 'Ongkos Kirim (' . ($order->shipping_method ?? 'Reguler') . ')',
                        ];
                    }

                    $params = [
                        'transaction_details' => [
                            'order_id' => $midtransOrderId,
                            'gross_amount' => (int) $order->grand_total,
                        ],
                        'item_details' => $item_details,
                        'customer_details' => [
                            'first_name' => $user->name,
                            'email' => $user->email,
                            'phone' => '0800000000', // Default if phone not available
                            'shipping_address' => [
                                'first_name' => $user->name,
                                'email' => $user->email,
                                'phone' => '0800000000',
                                'address' => $order->address->full_address ?? 'Alamat tidak diisi',
                            ]
                        ],
                        'enabled_payments' => [
                            'credit_card', 'gopay', 'shopeepay', 'permata_va', 'bca_va', 'bni_va', 'bri_va', 'other_va'
                        ],
                    ];

                    $snapToken = Snap::getSnapToken($params);

                    if ($request->ajax()) {
                        return response()->json([
                            'success' => true,
                            'message' => 'Snap Token generated',
                            'snap_token' => $snapToken,
                            'order_id' => $order->id
                        ]);
                    }
                }

            if ($request->ajax()) {
                session()->flash('success', 'Pembayaran berhasil dikonfirmasi! 🎉');
                return response()->json([
                    'success' => true,
                    'message' => 'Pembayaran berhasil dikonfirmasi!',
                    'redirect_url' => route('account.orders', ['just_paid' => $order->id])
                ]);
            }
            return redirect()->route('account.orders', ['just_paid' => $order->id])->with('success', 'Transaksi berhasil diselesaikan! 🎉');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Payment Error: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'payment_method' => $request->payment_method,
                'order_id' => $order->id ?? 'N/A',
                'order_number' => $order->order_number ?? 'N/A',
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => substr($e->getTraceAsString(), 0, 1000)
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Gagal memproses pembayaran: ' . $e->getMessage()
                ], 500);
            }
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    /**
     * Store a new address
     */
    public function storeAddress(StoreAddressRequest $request)
    {
        $user = Auth::user();
        
        // Check if user already has addresses
        $isFirst = Address::where('user_id', $user->id)->count() === 0;

        Address::create([
            'user_id' => $user->id,
            'label' => $request->label,
            'full_address' => $request->full_address,
            'province_id' => $request->province_id,
            'city_id' => $request->city_id,
            'is_primary' => $isFirst
        ]);

        return back()->with('success', 'Alamat berhasil ditambahkan!');
    }

    /**
     * Set address as primary
     */
    public function setPrimaryAddress($id)
    {
        $user = Auth::user();
        
        // Reset all primary flags
        Address::where('user_id', $user->id)->update(['is_primary' => false]);
        
        // Set new primary
        $address = Address::where('user_id', $user->id)->findOrFail($id);
        $address->update(['is_primary' => true]);

        return back()->with('success', 'Alamat utama berhasil diperbarui!');
    }

    /**
     * Delete an address
     */
    public function deleteAddress($id)
    {
        $address = Address::where('user_id', Auth::id())->findOrFail($id);
        $address->delete();

        return back()->with('success', 'Alamat berhasil dihapus!');
    }

    /**
     * Update User Profile
     */
    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = Auth::user();

        $user->update([
            'name' => $request->name,
            'email' => $request->email
        ]);

        return back()->with('success', 'Profil berhasil diperbarui!');
    }

    /**
     * Change Password
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Password saat ini salah.');
        }

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return back()->with('success', 'Password berhasil diganti!');
    }

    public function cancelOrder($orderId)
    {
        $order = StoreOrder::where('user_id', Auth::id())
            ->where('payment_status', 'pending')
            ->findOrFail($orderId);
        $order->items()->delete();
        $order->delete();
        return redirect()->route('account.orders')->with('success', 'Pesanan dibatalkan.');
    }

    /**
     * Show order invoice
     */
    public function showInvoice($id)
    {
        $order = StoreOrder::where('user_id', Auth::id())
            ->with(['user', 'address', 'items.item.category'])
            ->findOrFail($id);

        return view('frontend.account.invoice', compact('order'));
    }

    /**
     * Check payment status from Midtrans manually
     */
    public function checkPaymentStatus($id)
    {
        $user = Auth::user();
        $order = StoreOrder::where('user_id', $user->id)->findOrFail($id);

        if ($order->payment_method !== 'midtrans') {
            return response()->json(['success' => false, 'message' => 'Hanya untuk pembayaran Midtrans.'], 400);
        }

        if ($order->payment_status === 'paid') {
            return response()->json(['success' => true, 'message' => 'Pembayaran sudah lunas.', 'status' => 'paid']);
        }

        // Set configuration
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production');

        try {
            // Reconstruct the Order ID sent to Midtrans
            $attempts = [];
            
            // Attempt 1: With saved unique_code (timestamp)
            if ($order->unique_code) {
                $attempts[] = $order->order_number . '-' . $order->unique_code;
            }
            
            // Attempt 2: Without unique_code (just the order number)
            $attempts[] = $order->order_number;

            $lastError = '';
            foreach ($attempts as $midtransOrderId) {
                try {
                    $status = \Midtrans\Transaction::status($midtransOrderId);
                    
                    $transaction = $status->transaction_status;
                    $type = $status->payment_type;
                    $fraud = $status->fraud_status;

                    if ($transaction == 'settlement' || $transaction == 'capture') {
                        if ($transaction == 'capture' && $type == 'credit_card' && $fraud == 'challenge') {
                            // Still pending
                        } else {
                            $order->update(['payment_status' => 'paid', 'order_status' => 'paid']);
                            return response()->json(['success' => true, 'message' => 'Pembayaran berhasil dikonfirmasi!', 'status' => 'paid']);
                        }
                    } else if (in_array($transaction, ['deny', 'expire', 'cancel'])) {
                        $order->update(['payment_status' => 'cancelled', 'order_status' => 'cancelled']);
                        return response()->json(['success' => true, 'message' => 'Pembayaran dibatalkan/kadaluarsa.', 'status' => 'cancelled']);
                    }

                    return response()->json(['success' => true, 'message' => 'Status di Midtrans: ' . $transaction, 'status' => 'pending']);
                    
                } catch (\Exception $e) {
                    $lastError = $e->getMessage();
                    continue; // Try next ID
                }
            }

            // If we reached here, none of the IDs worked
            return response()->json(['success' => false, 'message' => 'Data transaksi tidak ditemukan di sistem Midtrans.'], 404);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }
}
