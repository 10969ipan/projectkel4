<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ItemRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = ItemRequest::with(['item', 'user', 'processedBy']);

        if (auth()->user()->isStaff()) {
            $query->where('user_id', auth()->id());
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $requests = $query->latest()->paginate(10);
        return view('item-requests.index', compact('requests'));
    }

    public function create()
    {
        $items = Item::all();
        return view('item-requests.create', compact('items'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|string',
            'size' => 'nullable|string',
        ]);

        $item = Item::findOrFail($request->item_id);
        $sizeId = null; // Default null (jika barang tidak punya ukuran)

        // LOGIKA BARU: Cari & Simpan ID Ukuran
        if ($request->filled('size')) {
            $itemSize = $item->sizes()->where('size', $request->size)->first();

            if (!$itemSize) {
                return back()->withInput()->with('error', "Ukuran '{$request->size}' tidak ditemukan.");
            }

            // Simpan ID ukuran ke variabel
            $sizeId = $itemSize->id;

            if ($request->quantity > $itemSize->stock) {
                return back()->withInput()->with('error', "Stok ukuran {$request->size} tidak cukup (Sisa: {$itemSize->stock})");
            }
        } else {
            if ($request->quantity > $item->stock) {
                return back()->withInput()->with('error', 'Jumlah permintaan melebihi stok tersedia.');
            }
        }

        // Simpan ke Database (Pastikan kolom item_size_id terisi)
        ItemRequest::create([
            'item_id' => $request->item_id,
            'user_id' => auth()->id(),
            'item_size_id' => $sizeId, // <--- INI YANG PENTING
            'size' => $request->size,
            'quantity' => $request->quantity,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        return redirect()->route('item-requests.index')->with('success', 'Permintaan berhasil dikirim.');
    }

    /**
     * FUNGSI APPROVE: Menyetujui permintaan barang dari staff
     * 
     * Proses yang dilakukan:
     * 1. Validasi status permintaan (harus pending)
     * 2. Validasi ketersediaan stok
     * 3. Update status permintaan menjadi approved
     * 4. Buat transaksi keluar otomatis
     * 5. Kurangi stok barang (per ukuran dan total)
     * 
     * @param ItemRequest $itemRequest - Permintaan yang akan disetujui
     * @return RedirectResponse
     */
    public function approve(ItemRequest $itemRequest)
    {
        // VALIDASI 1: Cek apakah permintaan masih berstatus pending
        // Jika sudah diproses sebelumnya (approved/rejected), tolak aksi ini
        if ($itemRequest->status !== 'pending') {
            return back()->with('error', 'Permintaan ini sudah diproses sebelumnya.');
        }

        // Ambil data barang yang diminta
        $item = $itemRequest->item;

        // VALIDASI 2: Cek ketersediaan stok sebelum approve
        // Validasi berbeda tergantung apakah barang memiliki varian ukuran atau tidak
        if ($itemRequest->item_size_id) {
            // KASUS A: Barang memiliki varian ukuran (contoh: baju, celana, sepatu)
            // Cek stok pada ukuran spesifik yang diminta
            $itemSize = \App\Models\ItemSize::find($itemRequest->item_size_id);

            // Validasi: Pastikan ukuran masih ada dan stoknya mencukupi
            if (!$itemSize || $itemSize->stock < $itemRequest->quantity) {
                return back()->with('error', 'Stok ukuran ini sudah habis, tidak bisa di-approve.');
            }
        } else {
            // KASUS B: Barang tidak memiliki varian ukuran (contoh: alat tulis, aksesoris)
            // Cek stok total barang
            if ($item->stock < $itemRequest->quantity) {
                return back()->with('error', 'Stok total tidak mencukupi.');
            }
        }

        // PROSES APPROVAL: Gunakan database transaction untuk memastikan semua operasi berhasil
        // Jika salah satu gagal, semua akan di-rollback (dibatalkan)
        DB::transaction(function () use ($itemRequest, $item) {

            // LANGKAH 1: Update status permintaan menjadi 'approved'
            // Simpan juga siapa yang memproses dan kapan diproses
            $itemRequest->update([
                'status' => 'approved',
                'processed_by' => auth()->id(),  // ID admin yang menyetujui
                'processed_at' => now(),          // Waktu persetujuan
            ]);

            // LANGKAH 2: Buat transaksi keluar otomatis
            // Transaksi ini merekam bahwa barang telah keluar dari gudang
            $item->transactions()->create([
                'user_id' => $itemRequest->user_id,              // Staff yang meminta
                'item_size_id' => $itemRequest->item_size_id,    // Ukuran yang diminta (jika ada)
                'type' => 'out',                                  // Tipe: keluar dari gudang
                'quantity' => $itemRequest->quantity,             // Jumlah yang keluar
                'date' => now(),                                  // Tanggal transaksi
                'note' => 'Approved request #' . $itemRequest->id . ($itemRequest->size ? " (Size: {$itemRequest->size})" : ""),
            ]);

            // LANGKAH 3: Kurangi stok pada tabel varian ukuran (ItemSize)
            // Hanya dilakukan jika barang memiliki varian ukuran
            if ($itemRequest->item_size_id) {
                $itemSize = \App\Models\ItemSize::find($itemRequest->item_size_id);
                if ($itemSize) {
                    // Kurangi stok ukuran spesifik
                    $itemSize->decrement('stock', $itemRequest->quantity);
                }
            }

            // LANGKAH 4: Kurangi stok total barang di tabel items
            // Ini dilakukan untuk semua jenis barang (dengan atau tanpa varian)
            $item->decrement('stock', $itemRequest->quantity);
        });

        // Redirect kembali dengan pesan sukses
        return redirect()->route('item-requests.index')->with('success', 'Permintaan disetujui & stok berhasil dikurangi.');
    }

    /**
     * FUNGSI SHOW: Menampilkan detail permintaan barang
     * 
     * @param ItemRequest $itemRequest - Permintaan yang akan ditampilkan
     * @return View
     */
    public function show(ItemRequest $itemRequest)
    {
        // Validasi akses: Staff hanya bisa melihat permintaan mereka sendiri
        if (auth()->user()->isStaff() && $itemRequest->user_id != auth()->id()) {
            abort(403);
        }

        return view('item-requests.show', compact('itemRequest'));
    }

    /**
     * FUNGSI REJECT: Menolak permintaan barang dari staff
     * 
     * @param Request $request - Request yang berisi alasan penolakan
     * @param ItemRequest $itemRequest - Permintaan yang akan ditolak
     * @return RedirectResponse
     */
    public function reject(Request $request, ItemRequest $itemRequest)
    {
        // Validasi input: Alasan penolakan wajib diisi
        $request->validate([
            'rejection_reason' => 'required|string',
        ]);

        // Validasi: Pastikan permintaan masih berstatus pending
        if ($itemRequest->status !== 'pending') {
            return back()->with('error', 'Permintaan ini sudah diproses sebelumnya.');
        }

        // Update status menjadi rejected dan simpan alasan penolakan
        $itemRequest->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,  // Alasan kenapa ditolak
            'processed_by' => auth()->id(),                     // Admin yang menolak
            'processed_at' => now(),                            // Waktu penolakan
        ]);

        // Redirect dengan pesan sukses
        return redirect()->route('item-requests.index')->with('success', 'Permintaan berhasil ditolak.');
    }
}