<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;

class CartController extends Controller
{
    /**
     * Tampilkan isi keranjang
     */
    public function index()
    {
        $cart = session()->get('cart', []);
        $items = [];
        $grandTotal = 0;

        foreach ($cart as $key => $details) {
            $item = Item::find($details['id']);
            if ($item) {
                // Apply 10% discount ONLY if it's a subscription
                $unitPrice = $item->price;
                if ($details['type'] === 'subscription') {
                    $unitPrice = $unitPrice * 0.9;
                }

                $subtotal = $unitPrice * $details['qty'];
                $grandTotal += $subtotal;
                
                $items[] = [
                    'id'                   => $item->id,
                    'key'                  => $key,
                    'name'                 => $item->name,
                    'price'                => $unitPrice,
                    'original_price'       => $item->price,
                    'qty'                  => $details['qty'],
                    'type'                 => $details['type'],
                    'interval'             => $details['interval'],
                    'subtotal'             => $subtotal,
                    'requires_prescription'=> $item->requires_prescription,
                    'image_path'           => $item->image_path,
                ];
            }
        }

        return view('frontend.cart.index', compact('items', 'grandTotal'));
    }

    /**
     * Tambahkan item ke keranjang
     */
    public function add(Request $request, $itemId)
    {
        if (!auth()->check()) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Silakan login terlebih dahulu.', 'action' => 'login_required'], 401);
            }
            return redirect()->route('store.login')->with('warning', 'Silakan login terlebih dahulu.');
        }

        $item = Item::findOrFail($itemId);
        $qty = (int) $request->input('qty', 1);
        $type = $request->input('type', 'once'); // once or subscription
        $interval = (int) $request->input('interval', 30);
        
        if ($qty < 1) $qty = 1;

        // Allow adding to cart, we will enforce prescription upload at checkout

        $cart = session()->get('cart', []);
        
        // Unique key per item+type to allow mixed cart if wanted, 
        // but for simplicity we'll just use item_id and update the last type selected
        if (isset($cart[$itemId])) {
            $cart[$itemId]['qty'] += $qty;
            $cart[$itemId]['type'] = $type;
            $cart[$itemId]['interval'] = $interval;
        } else {
            $cart[$itemId] = [
                'id' => $itemId,
                'qty' => $qty,
                'type' => $type,
                'interval' => $interval
            ];
        }

        session()->put('cart', $cart);

        if ($request->expectsJson()) {
            return $this->summary();
        }

        return back()->with('success', $item->name . ' ditambahkan.');
    }

    /**
     * Update kuantitas
     */
    public function update(Request $request, $itemId)
    {
        $qty = (int) $request->input('qty', 1);
        $cart = session()->get('cart', []);

        // Robust update: ensures we target the right item regardless of key type
        $found = false;
        foreach ($cart as $key => $details) {
            if ($details['id'] == $itemId) {
                if ($qty < 1) {
                    unset($cart[$key]);
                } else {
                    $cart[$key]['qty'] = $qty;
                }
                $found = true;
                break;
            }
        }

        session()->put('cart', $cart);

        if ($request->expectsJson()) {
            return $this->summary();
        }

        return redirect()->route('cart.index')->with('success', 'Keranjang diperbarui.');
    }

    /**
     * Hapus item
     */
    public function remove($itemId)
    {
        $cart = session()->get('cart', []);
        
        // Robust remove: filter out any item matching the product ID
        $cart = array_filter($cart, function($details) use ($itemId) {
            return $details['id'] != $itemId;
        });

        session()->put('cart', $cart);

        if (request()->expectsJson()) {
            return $this->summary();
        }

        return redirect()->route('cart.index')->with('success', 'Item dihapus.');
    }

    /**
     * Kosongkan keranjang
     */
    public function clear()
    {
        session()->forget('cart');
        return redirect()->route('store.index')->with('success', 'Keranjang dikosongkan.');
    }

    /**
     * Get Cart Summary for AJAX
     */
    public function summary()
    {
        $cart = session()->get('cart', []);
        $items = [];
        $grandTotal = 0;
        $count = 0;

        foreach ($cart as $id => $details) {
            $item = Item::find($details['id']);
            if ($item) {
                $unitPrice = $item->price;
                if ($details['type'] === 'subscription') {
                    $unitPrice = $unitPrice * 0.9;
                }

                $subtotal = $unitPrice * $details['qty'];
                $grandTotal += $subtotal;
                $count += $details['qty'];

                $items[] = [
                    'id'         => $item->id,
                    'name'       => $item->name,
                    'price'      => $unitPrice,
                    'qty'        => $details['qty'],
                    'type'       => $details['type'],
                    'subtotal'   => $subtotal,
                    'image_path' => $item->image_path,
                    'requires_prescription' => $item->requires_prescription,
                ];
            }
        }

        return response()->json([
            'success'     => true,
            'items'       => $items,
            'grand_total' => $grandTotal,
            'cart_count'  => $count,
        ]);
    }
}
