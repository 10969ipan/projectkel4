<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StoreOrder;
use App\Models\StoreOrderItem;
use App\Models\Item;
use App\Models\Address;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    /**
     * Display the checkout page with cart items.
     */
    public function index()
    {
        $user = Auth::user();
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Keranjang Anda masih kosong.');
        }

        // Ambil data item dari database sesuai isi keranjang
        $cartItems = [];
        $subTotal = 0;
        foreach ($cart as $itemId => $qty) {
            $item = Item::find($itemId);
            if ($item) {
                $subtotal = $item->price * $qty;
                $subTotal += $subtotal;
                $cartItems[] = [
                    'id'                   => $item->id,
                    'name'                 => $item->name,
                    'price'                => $item->price,
                    'qty'                  => $qty,
                    'subtotal'             => $subtotal,
                    'requires_prescription'=> $item->requires_prescription,
                ];
            }
        }

        $shippingCost = 15000; // default instant
        $grandTotal = $subTotal + $shippingCost;

        $addresses = Address::where('user_id', $user->id)->orderBy('is_primary', 'desc')->get();

        return view('checkout.index', compact('user', 'cartItems', 'subTotal', 'shippingCost', 'grandTotal', 'addresses'));
    }

    /**
     * Store the checkout order.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Keranjang kosong.');
        }

        // Hitung subtotal dari keranjang
        $subTotal = 0;
        $cartItems = [];
        $hasPrescriptionItem = false;
        foreach ($cart as $itemId => $qty) {
            $item = Item::find($itemId);
            if (!$item) continue;

            if ($item->requires_prescription) $hasPrescriptionItem = true;

            $lineTotal = $item->price * $qty;
            $subTotal += $lineTotal;
            $cartItems[] = ['item' => $item, 'qty' => $qty, 'subtotal' => $lineTotal];
        }

        // Validasi Resep (Hanya blokir jika item keras ada DAN tidak ada upload file)
        if ($hasPrescriptionItem && !$request->hasFile('prescription')) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Item ini membutuhkan resep dokter. Silakan unggah resep manual untuk melanjutkan.'], 422);
            }
            return back()->with('error', 'Salah satu item keras membutuhkan Resep. Silakan unggah resep manual.');
        }

        $shippingCost = 0; // Finalized on payment page
        $grandTotal   = $subTotal + $shippingCost;

        // Handle Prescription Upload
        $prescriptionPath = null;
        if ($request->hasFile('prescription')) {
            $file = $request->file('prescription');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('storage/prescriptions'), $filename);
            $prescriptionPath = 'prescriptions/' . $filename;
        }

        $tempOrderNumber = 'ORD-' . strtoupper(uniqid());

        // SIMPAN DATA SEMENTARA KE SESSION (BUKAN DATABASE)
        session()->put('pending_checkout', [
            'order_number' => $tempOrderNumber,
            'address_id' => $request->address_id,
            'prescription_path' => $prescriptionPath,
            'sub_total' => $subTotal,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'order' => [
                    'id' => null, // Beritahu frontend ini pesanan baru
                    'number' => $tempOrderNumber,
                    'subtotal' => $subTotal,
                    'reqPres' => $hasPrescriptionItem,
                    'hasFile' => !empty($prescriptionPath),
                    'url' => route('account.orders.pay.new') // Route baru untuk handle checkout atomic
                ]
            ]);
        }

        return redirect()->route('account.dashboard')->with('info', 'Silakan selesaikan pembayaran.');
    }
}
