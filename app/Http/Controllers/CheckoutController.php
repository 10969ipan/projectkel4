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

        // Batch load all items to avoid N+1 queries
        $itemIds = array_column($cart, 'id');
        $itemsMap = Item::whereIn('id', $itemIds)->get()->keyBy('id');

        $cartItems = [];
        $subTotal = 0;
        foreach ($cart as $key => $details) {
            $item = $itemsMap->get($details['id']);
            if ($item) {
                // Apply 10% discount for subscription items
                $unitPrice = $item->price;
                if ($details['type'] === 'subscription') {
                    $unitPrice = $unitPrice * 0.9;
                }

                $itemSubtotal = $unitPrice * $details['qty'];
                $subTotal += $itemSubtotal;
                
                $cartItems[] = [
                    'id'                   => $item->id,
                    'name'                 => $item->name,
                    'price'                => $unitPrice,
                    'qty'                  => $details['qty'],
                    'type'                 => $details['type'],
                    'interval'             => $details['interval'],
                    'subtotal'             => $itemSubtotal,
                    'requires_prescription'=> $item->requires_prescription,
                    'image_path'           => $item->image_path,
                ];
            }
        }

        $shippingCost = 15000; // default instant
        
        // Calculate gross subtotal for frontend reactivity (Batch optimized)
        $subTotalGross = 0;
        foreach($cart as $details) {
            $item = $itemsMap->get($details['id']);
            if ($item) {
                $subTotalGross += ($item->price * $details['qty']);
            }
        }

        $grandTotal = $subTotal + $shippingCost;

        $hasPrescriptionItem = false;
        foreach($cartItems as $ci) {
            if (!empty($ci['requires_prescription'])) {
                $hasPrescriptionItem = true;
                break;
            }
        }

        $addresses = Address::where('user_id', $user->id)->orderBy('is_primary', 'desc')->get();

        return view('frontend.checkout.index', compact('user', 'cartItems', 'subTotal', 'subTotalGross', 'shippingCost', 'grandTotal', 'addresses', 'hasPrescriptionItem'));
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

        $subTotal = 0;
        $hasPrescriptionItem = false;
        $checkoutDetails = [];

        // Batch load all items to avoid N+1 queries
        $itemIds = array_column($cart, 'id');
        $itemsMap = Item::whereIn('id', $itemIds)->get()->keyBy('id');

        foreach ($cart as $key => $details) {
            $item = $itemsMap->get($details['id']);
            if (!$item) continue;

            if ($item->requires_prescription) $hasPrescriptionItem = true;

            // Check if item was upgraded to subscription at checkout
            $isSub = $request->has('is_subscription') && isset($request->is_subscription[$item->id]);
            $interval = $isSub ? ($request->subscription_interval[$item->id] ?? 30) : 30;

            $unitPrice = $item->price;
            if ($isSub) {
                $unitPrice = $unitPrice * 0.9;
            }

            $subTotal += ($unitPrice * $details['qty']);
            
            // Store checkout-specific preferences for this order
            $checkoutDetails[$item->id] = [
                'type' => $isSub ? 'subscription' : 'once',
                'interval' => $interval,
                'price' => $unitPrice
            ];
        }

        if ($hasPrescriptionItem && !$request->hasFile('prescription') && !$user->is_prescription_approved) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Item ini membutuhkan resep dokter. Silakan unggah resep manual untuk melanjutkan.'], 422);
            }
            return back()->with('error', 'Salah satu item keras membutuhkan Resep.');
        }

        $prescriptionPath = null;
        if ($request->hasFile('prescription')) {
            $file = $request->file('prescription');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('storage/prescriptions'), $filename);
            $prescriptionPath = 'prescriptions/' . $filename;
        }

        $tempOrderNumber = 'ORD-' . strtoupper(uniqid());

        session()->put('pending_checkout', [
            'order_number' => $tempOrderNumber,
            'address_id' => $request->address_id,
            'prescription_path' => $prescriptionPath,
            'sub_total' => $subTotal,
            'items_override' => $checkoutDetails // Carry these to the final payment
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'order' => [
                    'id' => null,
                    'number' => $tempOrderNumber,
                    'subtotal' => $subTotal,
                    'url' => route('account.orders.pay.new')
                ]
            ]);
        }

        return redirect()->route('account.dashboard')->with('info', 'Silakan selesaikan pembayaran.');
    }
}
