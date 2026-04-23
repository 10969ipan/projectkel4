<?php

namespace App\Http\Controllers;

use App\Models\Item;

use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

/**
 * CONTROLLER TRANSAKSI
 * 
 * Menangani semua operasi transaksi barang (masuk dan keluar)
 * Transaksi mencatat pergerakan stok barang di gudang
 */
class TransactionController extends Controller
{
    /**
     * FUNGSI INDEX: Menampilkan daftar semua transaksi
     * 
     * @return View
     */
    public function index(): View
    {
        // Ambil semua transaksi dengan relasi item dan user
        // Diurutkan dari yang terbaru (latest) dan dipaginasi 10 per halaman
        $transactions = Transaction::with(['item', 'user'])->latest()->paginate(10);

        return view('backend.transactions.index', compact('transactions'));
    }

    /**
     * FUNGSI CREATE: Menampilkan form untuk membuat transaksi baru
     * 
     * @return View
     */
    public function create(): View
    {
        // EAGER LOADING: Muat data item beserta unit dan sizes (varian ukuran)
        // Ini penting agar data stok per ukuran tersedia untuk validasi di frontend
        $items = Item::with('unit')->get();

        return view('backend.transactions.create', compact('items'));
    }

    /**
     * FUNGSI STORE: Menyimpan transaksi baru ke database
     * 
     * Proses yang dilakukan:
     * 1. Validasi input dari form
     * 2. Cari data varian ukuran yang dipilih
     * 3. Validasi stok (khusus untuk transaksi keluar)
     * 4. Simpan transaksi ke database
     * 5. Update stok barang (varian dan total)
     * 
     * @param Request $request - Request yang berisi data transaksi
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        // VALIDASI INPUT: Pastikan semua data yang diperlukan valid
        $request->validate([
            'item_id' => 'required|exists:items,id',              // Barang harus ada di database
            'type' => 'required|in:in,out',                        // Tipe hanya boleh 'in' atau 'out'
            'quantity' => 'required|integer|min:1',                // Jumlah minimal 1
            'date' => 'required|date',                             // Tanggal harus format date yang valid
            'note' => 'nullable|string',                           // Catatan opsional
        ]);

        $item = Item::findOrFail($request->item_id);

        // VALIDASI STOK: Khusus untuk transaksi KELUAR (out)
        if ($request->type === 'out' && $item->stock < $request->quantity) {
            return back()
                ->withInput()
                ->with('error', 'Stok tidak mencukupi. Stok tersedia: ' . $item->stock);
        }

        // LANGKAH 3: Simpan transaksi ke database
        $transaction = Transaction::create([
            'item_id' => $request->item_id,              // ID barang
            'user_id' => auth()->id(),                    // ID user yang membuat transaksi
            'type' => $request->type,                     // Tipe: 'in' (masuk) atau 'out' (keluar)
            'quantity' => $request->quantity,             // Jumlah barang
            'date' => $request->date,                     // Tanggal transaksi
            'note' => $request->note,                     // Catatan tambahan
        ]);

        // LANGKAH 4: Update stok barang langsung pada model Item

        if ($request->type === 'in') {
            // TRANSAKSI MASUK: Tambah stok total item
            $item->increment('stock', $request->quantity);
        } else {
            // TRANSAKSI KELUAR: Kurangi stok total item
            $item->decrement('stock', $request->quantity);
        }

        // Clear Store Cache for all pages and this specific item
        for ($i = 1; $i <= 5; $i++) {
            Cache::forget('store_catalog_page_' . $i);
        }
        Cache::forget('store_item_' . $item->id);

        // Redirect ke halaman daftar transaksi dengan pesan sukses
        return redirect()
            ->route('transactions.index')
            ->with('success', 'Transaksi berhasil dibuat dan stok telah diperbarui.');
    }
}