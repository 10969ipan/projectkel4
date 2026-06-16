<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StoreOrder;
use App\Models\User;
use App\Models\TelemedicineChat;
use Illuminate\Support\Facades\Hash;

class PharmacareAdminController extends Controller
{
    public function index()
    {
        // Get all store orders
        $orders = StoreOrder::with(['user', 'address'])->latest()->limit(5)->get();
        
        // Get all users who have store role
        $customers = User::where('store_role', 'customer')->limit(5)->get();

        return view('backend.pharmacare.index', compact('orders', 'customers'));
    }

    public function transactions()
    {
        // View to process pending/paid orders
        $orders = StoreOrder::with(['user', 'address', 'items.item'])
            ->whereNotIn('order_status', ['completed', 'cancelled'])
            ->latest()
            ->get();
            
        return view('backend.pharmacare.transactions', compact('orders'));
    }
    
    public function transactionLogs(Request $request)
    {
        // View for historical transactions with filtering
        $query = StoreOrder::with(['user', 'address', 'items.item'])
            ->whereIn('order_status', ['completed', 'cancelled']);

        if ($request->filled('date_start')) {
            $query->whereDate('created_at', '>=', $request->date_start);
        }
        if ($request->filled('date_end')) {
            $query->whereDate('created_at', '<=', $request->date_end);
        }
        if ($request->filled('status')) {
            $query->where('order_status', $request->status);
        }

        $orders = $query->latest()->paginate(20);
            
        return view('backend.pharmacare.transaction_logs', compact('orders'));
    }
    
    public function updateTransaction(Request $request, $id)
    {
        $order = StoreOrder::findOrFail($id);
        
        $request->validate([
            'order_status' => 'required|in:ordered,paid,processing,completed,cancelled',
            'tracking_number' => 'nullable|string|max:50'
        ]);
        
        $order->order_status = $request->order_status;
        if ($request->filled('tracking_number')) {
            $order->tracking_number = $request->tracking_number;
        }
        $order->save();
        
        // Trigger Notification Manual with Debug
        try {
            if (in_array($request->order_status, ['paid', 'processing', 'completed'])) {
                $message = '';
                $icon = '';
                
                if ($request->order_status === 'completed') {
                    $message = "Pesanan #{$order->order_number} telah sampai. Yuk berikan rating!";
                    $icon = 'fa-star';
                } elseif ($request->order_status === 'processing') {
                    $message = "Pesanan #{$order->order_number} sedang dikirim!";
                    $icon = 'fa-truck';
                } elseif ($request->order_status === 'paid') {
                    $message = "Pembayaran untuk pesanan #{$order->order_number} berhasil!";
                    $icon = 'fa-check-circle';
                }
                
                \Illuminate\Support\Facades\DB::table('notifications')->insert([
                    'id' => \Illuminate\Support\Str::uuid()->toString(),
                    'type' => 'App\Notifications\OrderStatusNotification',
                    'notifiable_type' => 'App\Models\User',
                    'notifiable_id' => (int) $order->user_id,
                    'data' => json_encode([
                        'order_id' => $order->id,
                        'message' => $message,
                        'icon' => $icon,
                        'status' => $request->order_status,
                        'needs_rating' => $request->order_status === 'completed',
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        } catch (\Exception $e) {
            return back()->with('error', 'DEBUG NOTIFIKASI GAGAL: ' . $e->getMessage());
        }
        
        return back()->with('success', 'Status pesanan berhasil diperbarui!');
    }

    public function customers()
    {
        $customers = User::where('store_role', 'customer')
            ->orWhere('role', 'pelanggan')
            ->with(['addresses', 'storeOrders', 'subscriptions.item', 'walletTransactions'])
            ->latest()
            ->paginate(10);
        return view('backend.pharmacare.customers', compact('customers'));
    }

    public function updateCustomer(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => 'nullable|string|min:10|max:20|unique:users,phone,' . $id,
            'password' => 'nullable|min:6',
            'address' => 'nullable|string',
            'wallet_balance' => 'required|numeric|min:0',
            'paylater_limit' => 'required|numeric|min:0',
            'is_prescription_approved' => 'nullable|boolean'
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->wallet_balance = $request->wallet_balance;
        $user->paylater_limit = $request->paylater_limit;
        $user->is_prescription_approved = $request->has('is_prescription_approved');
        
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        
        $user->save();

        if ($request->filled('address')) {
            $user->addresses()->updateOrCreate(
                ['is_primary' => true],
                ['full_address' => $request->address, 'label' => 'Utama Admin']
            );
        }

        return back()->with('success', 'Data pelanggan berhasil diperbarui!');
    }

    public function approvePrescription($userId)
    {
        $user = User::findOrFail($userId);
        $user->is_prescription_approved = true;
        $user->save();

        return back()->with('success', 'Apoteker memverifikasi resep digital Pelanggan.');
    }

    public function updatePaylater(Request $request, $userId)
    {
        $user = User::findOrFail($userId);
        $user->paylater_limit = $request->limit;
        $user->save();

        return back()->with('success', 'Limit Paylater berhasil diperbarui.');
    }

    public function showInvoice($id)
    {
        $order = StoreOrder::with(['user', 'address', 'items.item.category'])
            ->findOrFail($id);

        return view('frontend.account.invoice', compact('order'));
    }
}
