<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemSize;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

        return view('transactions.index', compact('transactions'));
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
        $items = Item::with('unit', 'sizes')->get();

        return view('transactions.create', compact('items'));
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
            'item_size_id' => 'required|exists:item_sizes,id',    // Ukuran harus ada di database
            'type' => 'required|in:in,out',                        // Tipe hanya boleh 'in' atau 'out'
            'quantity' => 'required|integer|min:1',                // Jumlah minimal 1
            'date' => 'required|date',                             // Tanggal harus format date yang valid
            'note' => 'nullable|string',                           // Catatan opsional
        ]);

        // LANGKAH 1: Cari data varian ukuran yang dipilih
        // Contoh: Jika user pilih "Kaos Polos - Size M", maka cari data size M
        $itemSize = ItemSize::findOrFail($request->item_size_id);

        // LANGKAH 2: Ambil data item utama dari varian ukuran
        $item = $itemSize->item;

        // VALIDASI STOK: Khusus untuk transaksi KELUAR (out)
        // Pastikan stok ukuran yang dipilih mencukupi
        if ($request->type === 'out' && $itemSize->stock < $request->quantity) {
            return back()
                ->withInput()
                ->with('error', 'Stok ukuran ' . $itemSize->size . ' tidak mencukupi. Stok tersedia: ' . $itemSize->stock);
        }

        // LANGKAH 3: Simpan transaksi ke database
        $transaction = Transaction::create([
            'item_id' => $request->item_id,              // ID barang
            'item_size_id' => $request->item_size_id,    // ID varian ukuran (PENTING untuk tracking)
            'user_id' => auth()->id(),                    // ID user yang membuat transaksi
            'type' => $request->type,                     // Tipe: 'in' (masuk) atau 'out' (keluar)
            'quantity' => $request->quantity,             // Jumlah barang
            'date' => $request->date,                     // Tanggal transaksi
            'note' => $request->note,                     // Catatan tambahan
        ]);

        // LANGKAH 4: Update stok barang
        // Update dilakukan di 2 tempat: ItemSize (stok per ukuran) dan Item (stok total)

        if ($request->type === 'in') {
            // TRANSAKSI MASUK: Tambah stok

            // Tambah stok pada varian ukuran spesifik
            // Contoh: Size M bertambah dari 10 menjadi 15
            $itemSize->increment('stock', $request->quantity);

            // Tambah stok total item
            // Contoh: Total stok bertambah dari 50 menjadi 55
            $item->increment('stock', $request->quantity);

        } else {
            // TRANSAKSI KELUAR: Kurangi stok

            // Kurangi stok pada varian ukuran spesifik
            // Contoh: Size M berkurang dari 10 menjadi 5
            $itemSize->decrement('stock', $request->quantity);

            // Kurangi stok total item
            // Contoh: Total stok berkurang dari 50 menjadi 45
            $item->decrement('stock', $request->quantity);
        }

        // Redirect ke halaman daftar transaksi dengan pesan sukses
        return redirect()
            ->route('transactions.index')
            ->with('success', 'Transaksi berhasil dibuat dan stok telah diperbarui.');
    }
}