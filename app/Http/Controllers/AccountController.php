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
use App\Models\ItemSize;
use App\Models\Subscription;
use App\Models\WalletTransaction;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\StoreAddressRequest;
use App\Http\Requests\ProcessPaymentRequest;
use Illuminate\Support\Facades\DB;

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

        $order = null;
        $prescriptionPath = null;
        $subTotal = 0;
        $addressId = null;
        $cart = session()->get('cart', []);

        if ($orderId) {
            $order = StoreOrder::where('user_id', $user->id)->findOrFail($orderId);
            if ($order->payment_status !== 'pending') {
                return redirect()->route('account.orders')->with('error', 'Pesanan ini sudah diproses.');
            }
            $subTotal = $order->sub_total;
            $prescriptionPath = $order->prescription_path;
        } else {
            $pendingData = session()->get('pending_checkout');
            if (!$pendingData || empty($cart)) {
                return redirect()->route('store.index')->with('error', 'Sesi checkout berakhir.');
            }
            $subTotal = $pendingData['sub_total'];
            $prescriptionPath = $pendingData['prescription_path'];
            $addressId = $pendingData['address_id'];
        }

        $shippingCost = (int)($request->shipping_method === 'instant' ? 15000 : 10000);
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

        // Validasi Stok
        foreach ($cart as $key => $details) {
            $item = Item::find($details['id']);
            if (!$item || $item->stock < $details['qty']) {
                $msg = "Stok " . ($item->name ?? 'Item') . " tidak mencukupi.";
                if ($request->ajax()) return response()->json(['success' => false, 'error' => $msg], 422);
                return back()->with('error', $msg);
            }
        }

        try {
            $order = DB::transaction(function() use ($request, $user, $orderId, $subTotal, $prescriptionPath, $addressId, $grandTotal, $shippingCost, $cart) {
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
                // Other methods (QRIS, Bank, etc.) are "passed" as per user request for demo gateway

                if ($orderId) {
                    $order = StoreOrder::where('user_id', $user->id)->findOrFail($orderId);
                    $order->update([
                        'shipping_method' => $request->shipping_method,
                        'shipping_cost'   => $shippingCost,
                        'grand_total'     => $grandTotal,
                        'payment_method'  => $request->payment_method,
                        'payment_status'  => $status,
                        'order_status'    => ($status === 'paid' ? 'paid' : 'ordered'),
                    ]);
                    return $order;
                } else {
                    $pendingData = session()->get('pending_checkout');
                    $order = StoreOrder::create([
                        'user_id'        => $user->id,
                        'order_number'   => $pendingData['order_number'] ?? ('ORD-' . strtoupper(uniqid())),
                        'address_id'     => $addressId,
                        'shipping_method'=> $request->shipping_method,
                        'payment_method' => $request->payment_method,
                        'payment_status' => $status,
                        'order_status'   => ($status === 'paid' ? 'paid' : 'ordered'),
                        'sub_total'      => $subTotal,
                        'shipping_cost'  => $shippingCost,
                        'grand_total'    => $grandTotal,
                        'prescription_path' => $prescriptionPath,
                    ]);

                    $overrides = $pendingData['items_override'] ?? [];

                    foreach ($cart as $key => $details) {
                        $item = Item::find($details['id']);
                        if ($item) {
                            // Determine subscription status from override or fallback
                            $override = $overrides[$item->id] ?? null;
                            $type = $override ? $override['type'] : $details['type'];
                            $interval = $override ? $override['interval'] : ($details['interval'] ?? 30);
                            $unitPrice = $override ? $override['price'] : ($type === 'subscription' ? $item->price * 0.9 : $item->price);

                            if ($type === 'subscription') {
                                // Create Subscription Record
                                Subscription::create([
                                    'user_id' => $user->id,
                                    'item_id' => $item->id,
                                    'quantity' => $details['qty'],
                                    'interval_days' => (int)$interval,
                                    'discount_percentage' => 10.00,
                                    'next_delivery_date' => now()->addDays((int)$interval),
                                    'status' => 'active'
                                ]);
                            }

                            StoreOrderItem::create([
                                'store_order_id' => $order->id,
                                'item_id'        => $item->id,
                                'quantity'       => $details['qty'],
                                'price'          => $unitPrice,
                                'sub_total'      => $unitPrice * $details['qty'],
                            ]);

                            $item->decrement('stock', $details['qty']);
                            
                            // Batch handling
                            $remaining = $details['qty'];
                            $batches = ItemSize::where('item_id', $item->id)->where('stock', '>', 0)->orderBy('expiry_date', 'asc')->get();
                            foreach ($batches as $batch) {
                                if ($remaining <= 0) break;
                                $red = min($batch->stock, $remaining);
                                $batch->decrement('stock', $red);
                                $remaining -= $red;
                            }
                        }
                    }
                    session()->forget('pending_checkout');
                    session()->forget('cart');
                    \App\Models\CartItem::where('user_id', $user->id)->delete();
                    
                    // Clear Store Cache for all pages and this specific item
                    for ($i = 1; $i <= 5; $i++) {
                        Cache::forget('store_catalog_page_' . $i);
                    }
                    foreach ($cart as $details) {
                        Cache::forget('store_item_' . $details['id']);
                    }
                    
                    return $order;
                }
            });

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
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
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
}
