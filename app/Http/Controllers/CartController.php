<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;

class CartController extends Controller
{
    /**
     * Tampilkan isi keranjang (JSON untuk AJAX, atau redirect ke checkout)
     */
    public function index()
    {
        $cart = session()->get('cart', []);
        $items = [];
        $grandTotal = 0;

        foreach ($cart as $itemId => $qty) {
            $item = Item::find($itemId);
            if ($item) {
                $subtotal = $item->price * $qty;
                $grandTotal += $subtotal;
                $items[] = [
                    'id' => $item->id,
                    'name' => $item->name,
                    'price' => $item->price,
                    'qty' => $qty,
                    'subtotal' => $subtotal,
                    'requires_prescription' => $item->requires_prescription,
                ];
            }
        }

        return view('cart.index', compact('items', 'grandTotal'));
    }

    /**
     * Tambahkan item ke keranjang
     */
    public function add(Request $request, $itemId)
    {
        // Jika belum login, redirect ke halaman login khusus toko
        if (!auth()->check()) {
            return redirect()->route('store.login')
                ->with('warning', 'Silakan login atau daftar akun terlebih dahulu untuk membeli obat.');
        }

        $item = Item::findOrFail($itemId);
        $qty = (int) $request->input('qty', 1);
        if ($qty < 1) $qty = 1;

        // Validasi resep: jika obat keras & belum diapprove, tolak
        if ($item->requires_prescription && !auth()->user()->is_prescription_approved) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Obat ini memerlukan resep yang sudah divalidasi Dokter. Silakan konsultasi dulu.'
                ], 403);
            }
            return back()->with('error', 'Obat ini memerlukan validasi Resep Dokter terlebih dahulu.');
        }

        $cart = session()->get('cart', []);
        if (isset($cart[$itemId])) {
            $cart[$itemId] += $qty;
        } else {
            $cart[$itemId] = $qty;
        }
        session()->put('cart', $cart);

        $cartCount = array_sum($cart);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $item->name . ' ditambahkan ke keranjang.',
                'cart_count' => $cartCount,
            ]);
        }

        return back()->with('success', $item->name . ' berhasil ditambahkan ke keranjang!');
    }

    /**
     * Update kuantitas item di keranjang
     */
    public function update(Request $request, $itemId)
    {
        $qty = (int) $request->input('qty', 1);
        $cart = session()->get('cart', []);

        if ($qty < 1) {
            unset($cart[$itemId]);
        } else {
            $cart[$itemId] = $qty;
        }

        session()->put('cart', $cart);
        return redirect()->route('cart.index')->with('success', 'Keranjang diperbarui.');
    }

    /**
     * Hapus item dari keranjang
     */
    public function remove($itemId)
    {
        $cart = session()->get('cart', []);
        unset($cart[$itemId]);
        session()->put('cart', $cart);
        return redirect()->route('cart.index')->with('success', 'Item dihapus dari keranjang.');
    }

    /**
     * Kosongkan seluruh keranjang
     */
    public function clear()
    {
        session()->forget('cart');
        return redirect()->route('store.index')->with('success', 'Keranjang dikosongkan.');
    }
}
