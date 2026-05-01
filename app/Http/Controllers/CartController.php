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
        $this->syncFromDatabase();
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
                    'stock'                => $item->stock, // Stok tersedia
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

        // STOCK CHECK: Blokir jika stok habis (0 atau negatif)
        if ($item->stock <= 0) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Stok ' . $item->name . ' sudah habis.'], 422);
            }
            return back()->with('error', 'Maaf, stok ' . $item->name . ' sudah habis.');
        }

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

        if (auth()->check()) {
            \App\Models\CartItem::updateOrCreate(
                ['user_id' => auth()->id(), 'item_id' => $itemId],
                ['qty' => $cart[$itemId]['qty'], 'type' => $type, 'interval_days' => $interval]
            );
        }

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

        if (auth()->check()) {
            if ($qty < 1) {
                \App\Models\CartItem::where('user_id', auth()->id())->where('item_id', $itemId)->delete();
            } else {
                \App\Models\CartItem::where('user_id', auth()->id())->where('item_id', $itemId)->update(['qty' => $qty]);
            }
        }

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
        $itemId = intval($itemId);
        $cart = session()->get('cart', []);
        
        // Use the same robust loop logic as update()
        foreach ($cart as $key => $details) {
            if (intval($details['id']) === $itemId) {
                unset($cart[$key]);
            }
        }

        if (auth()->check()) {
            \App\Models\CartItem::where('user_id', auth()->id())
                ->where('item_id', $itemId)
                ->delete();
        }

        // Force a fresh sync to ensure summary is correct
        if (auth()->check()) {
            $this->syncFromDatabase();
        }

        request()->session()->save();

        if (request()->expectsJson()) {
            return response()->json(array_merge(['success' => true], $this->getCartData()));
        }

        return redirect()->route('cart.index')->with('success', 'Item dihapus.');
    }

    /**
     * Kosongkan keranjang
     */
    public function clear()
    {
        session()->forget('cart');
        if (auth()->check()) {
            \App\Models\CartItem::where('user_id', auth()->id())->delete();
        }
        return redirect()->route('store.index')->with('success', 'Keranjang dikosongkan.');
    }

    /**
     * Get Cart Summary for AJAX
     */
    public function summary()
    {
        if (auth()->check()) {
            $this->syncFromDatabase();
        }
        
        return response()->json(array_merge(['success' => true], $this->getCartData()));
    }

    /**
     * Helper to get structured cart data for response
     */
    private function getCartData()
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
                    'stock'      => $item->stock, // Stok tersedia
                    'type'       => $details['type'],
                    'subtotal'   => $subtotal,
                    'image_path' => $item->image_path,
                    'requires_prescription' => $item->requires_prescription,
                ];
            }
        }

        return [
            'items'       => $items,
            'grand_total' => $grandTotal,
            'cart_count'  => $count,
        ];
    }

    /**
     * Sinkronkan keranjang dari database ke session (Hanya jika login)
     */
    private function syncFromDatabase()
    {
        if (auth()->check()) {
            $dbItems = \App\Models\CartItem::where('user_id', auth()->id())->get();
            $cart = []; // Reset locally then fill from DB
            foreach ($dbItems as $item) {
                $cart[$item->item_id] = [
                    'id' => $item->item_id,
                    'qty' => $item->qty,
                    'type' => $item->type,
                    'interval' => $item->interval_days
                ];
            }
            session()->put('cart', $cart);
        }
    }
}
