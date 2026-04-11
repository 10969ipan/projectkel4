<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\StoreOrder;
use App\Models\StoreOrderItem;
use App\Models\Item;
use App\Models\Address;

class AccountController extends Controller
{
    /**
     * Tampilkan dashboard pesanan pelanggan
     */
    public function index()
    {
        $user = Auth::user();
        $orders = StoreOrder::where('user_id', $user->id)
            ->with(['items.item', 'address'])
            ->orderBy('created_at', 'desc')
            ->get();
        $addresses = Address::where('user_id', $user->id)->orderBy('is_primary', 'desc')->get();
        return view('account.dashboard', compact('user', 'orders', 'addresses'));
    }

    /**
     * Tampilkan halaman Akun Saya (profil, alamat, password)
     */
    public function showProfile()
    {
        $user = Auth::user();
        $addresses = Address::where('user_id', $user->id)->orderBy('is_primary', 'desc')->get();
        return view('account.profile', compact('user', 'addresses'));
    }

    /**
     * Simpan alamat baru
     */
    public function storeAddress(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'label' => 'required|string|max:100',
            'full_address' => 'required|string',
        ]);

        // Jika alamat pertama, jadikan primary
        $isFirst = Address::where('user_id', $user->id)->count() === 0;

        Address::create([
            'user_id' => $user->id,
            'label' => $request->label,
            'full_address' => $request->full_address,
            'is_primary' => $isFirst,
        ]);

        return back()->with('success', 'Alamat baru berhasil ditambahkan! 🏠');
    }

    /**
     * Update alamat yang ada
     */
    public function updateAddress(Request $request, $id)
    {
        $address = Address::where('user_id', Auth::id())->findOrFail($id);
        
        $request->validate([
            'label' => 'required|string|max:100',
            'full_address' => 'required|string',
        ]);

        $address->update([
            'label' => $request->label,
            'full_address' => $request->full_address,
        ]);

        return back()->with('success', 'Alamat berhasil diperbarui!');
    }

    /**
     * Hapus alamat
     */
    public function deleteAddress($id)
    {
        $address = Address::where('user_id', Auth::id())->findOrFail($id);
        
        // Jangan hapus jika primary dan masih ada alamat lain (opsional)
        $address->delete();

        return back()->with('success', 'Alamat berhasil dihapus.');
    }

    /**
     * Set alamat sebagai utama
     */
    public function setPrimaryAddress($id)
    {
        $user_id = Auth::id();
        
        // Reset all to false
        Address::where('user_id', $user_id)->update(['is_primary' => false]);
        
        // Set target to true
        $address = Address::where('user_id', $user_id)->findOrFail($id);
        $address->update(['is_primary' => true]);

        return back()->with('success', 'Alamat utama berhasil diubah!');
    }

    /**
     * Update profil dasar (Nama & Email)
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ], [
            'email.unique' => 'Email ini sudah digunakan oleh akun lain.',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        return back()->with('success', 'Profil Anda berhasil diperbarui! ✨');
    }

    /**
     * Ganti Password
     */
    public function changePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ], [
            'new_password.min' => 'Password baru minimal 8 karakter.',
            'new_password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        // Cek password lama
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Password lama yang Anda masukkan salah.');
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return back()->with('success', 'Password berhasil diganti! Jaga keamanan akun Anda. 🔐');
    }

    /**
     * Tampilkan halaman pembayaran untuk order yang sudah ada
     */
    public function showPayment($orderId)
    {
        $user = Auth::user();
        $order = StoreOrder::where('user_id', $user->id)
            ->with(['items.item', 'address'])
            ->findOrFail($orderId);

        // Hanya boleh bayar jika masih pending
        if ($order->payment_status !== 'pending') {
            return redirect()->route('account.orders')->with('error', 'Pesanan ini sudah dibayar atau tidak bisa diproses.');
        }

        return view('account.payment', compact('user', 'order'));
    }

    /**
     * Proses pembayaran (Handle Checkout Baru & Order Dashboard)
     */
    public function processPayment(Request $request, $orderId = null)
    {
        $user = Auth::user();
        
        $request->validate([
            'payment_method' => 'required|in:qris,paylater,bank,ewallet,cod',
            'shipping_method' => 'required|in:instant,regular'
        ]);

        $order = null;
        $prescriptionPath = null;
        $subTotal = 0;
        $addressId = null;

        if ($orderId) {
            // FLOW LAMA (Dari Dashboard)
            $order = StoreOrder::where('user_id', $user->id)->findOrFail($orderId);
            if ($order->payment_status !== 'pending') {
                return redirect()->route('account.orders')->with('error', 'Pesanan ini sudah diproses sebelumnya.');
            }
            $subTotal = $order->sub_total;
            $prescriptionPath = $order->prescription_path;
            
            // Verifikasi Resep Dasar
            $requiresPrescription = false;
            foreach ($order->items as $oi) {
                if ($oi->item && $oi->item->requires_prescription) {
                    $requiresPrescription = true;
                    break;
                }
            }
            if ($requiresPrescription && empty($order->prescription_path)) {
                return back()->with('error', 'Pesanan ini mengandung obat keras dan wajib melampirkan foto resep dokter.');
            }
        } else {
            // FLOW BARU (Atomic Checkout)
            $pendingData = session()->get('pending_checkout');
            if (!$pendingData) {
                return redirect()->route('store.index')->with('error', 'Sesi checkout berakhir. Silakan ulangi.');
            }

            $cart = session()->get('cart', []);
            if (empty($cart)) {
                return redirect()->route('store.index')->with('error', 'Keranjang kosong.');
            }

            $subTotal = $pendingData['sub_total'];
            $prescriptionPath = $pendingData['prescription_path'];
            $addressId = $pendingData['address_id'];
        }

        // Kalkulasi biaya kirim & total
        $shippingCost = $request->shipping_method === 'instant' ? 15000 : 10000;
        $grandTotal = $subTotal + $shippingCost;

        // Validasi Paylater
        if ($request->payment_method === 'paylater') {
            if ($user->paylater_limit < $grandTotal) {
                return back()->with('error', 'Limit Paylater tidak mencukupi. Limit Anda: Rp ' . number_format($user->paylater_limit, 0, ',', '.'));
            }
            $user->paylater_limit -= $grandTotal;
            $user->save();
        }

        $status = ($request->payment_method === 'cod') ? 'pending' : 'paid';

        if ($orderId) {
            // Update exist
            $order->update([
                'shipping_method' => $request->shipping_method,
                'shipping_cost'   => $shippingCost,
                'grand_total'     => $grandTotal,
                'payment_method'  => $request->payment_method,
                'payment_status'  => $status,
                'order_status'    => ($status === 'paid' ? 'paid' : 'ordered'),
            ]);
        } else {
            // CREATE BARU
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

            // Save Items
            $cart = session()->get('cart', []);
            foreach ($cart as $itemId => $qty) {
                $item = Item::find($itemId);
                if ($item) {
                    StoreOrderItem::create([
                        'store_order_id' => $order->id,
                        'item_id'        => $item->id,
                        'quantity'       => $qty,
                        'price'          => $item->price,
                        'sub_total'      => $item->price * $qty,
                    ]);
                }
            }

            session()->forget('pending_checkout');
        }

        // Kosongkan keranjang hanya setelah pembayaran sukses
        session()->forget('cart');

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'order_number' => $order->order_number,
                'grand_total' => $order->grand_total,
                'payment_method' => $order->payment_method,
                'paylater_limit' => $user->paylater_limit,
                'redirect_url' => route('account.orders')
            ]);
        }

        return redirect()->route('account.orders')->with('success', 'Pembayaran pesanan #' . $order->order_number . ' berhasil dikonfirmasi! 🎉');
    }

    /**
     * Batalkan & hapus pesanan yang belum dibayar
     */
    public function cancelOrder($orderId)
    {
        $order = StoreOrder::where('user_id', Auth::id())
            ->where('payment_status', 'pending')
            ->where('order_status', 'ordered')
            ->findOrFail($orderId);

        $order->items()->delete();
        $order->delete();

        return redirect()->route('account.orders')->with('success', 'Pesanan berhasil dibatalkan dan dihapus.');
    }
}
