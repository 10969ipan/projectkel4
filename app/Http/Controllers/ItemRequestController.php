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
        return view('backend.item-requests.index', compact('requests'));
    }

    public function create()
    {
        $items = Item::all();
        return view('backend.item-requests.create', compact('items'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1',
            'type' => 'required|in:in,out',
            'reason' => 'required|string',
        ]);

        $item = Item::findOrFail($request->item_id);

        if ($request->type === 'out' && $request->quantity > $item->stock) {
            return back()->withInput()->with('error', 'Jumlah permintaan melebihi stok tersedia.');
        }

        // Simpan ke Database
        try {
            ItemRequest::create([
                'item_id' => $request->item_id,
                'user_id' => auth()->id(),
                'quantity' => $request->quantity,
                'type' => $request->type,
                'reason' => $request->reason,
                'status' => 'pending',
            ]);
            return redirect()->route('item-requests.index')->with('success', 'Permintaan berhasil dikirim.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal menyimpan permintaan: ' . $e->getMessage());
        }
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

        // VALIDASI 2: Cek ketersediaan stok sebelum approve (Hanya untuk OUT)
        if ($itemRequest->type === 'out' && $item->stock < $itemRequest->quantity) {
            return back()->with('error', 'Stok total tidak mencukupi.');
        }

        // PROSES APPROVAL: Gunakan database transaction untuk memastikan semua operasi berhasil
        DB::transaction(function () use ($itemRequest, $item) {

            // LANGKAH 1: Update status permintaan menjadi 'approved'
            $itemRequest->update([
                'status' => 'approved',
                'processed_by' => auth()->id(),
                'processed_at' => now(),
            ]);

            // LANGKAH 2: Buat transaksi otomatis sesuai tipe permintaan
            $item->transactions()->create([
                'user_id' => $itemRequest->user_id,
                'type' => $itemRequest->type,
                'quantity' => $itemRequest->quantity,
                'date' => now(),
                'note' => 'Approved request #' . $itemRequest->id . ' (' . ($itemRequest->type == 'in' ? 'Tambah' : 'Kurang') . ')',
            ]);

            // LANGKAH 3: Update stok total barang di tabel items
            if ($itemRequest->type === 'in') {
                $item->increment('stock', $itemRequest->quantity);
            } else {
                $item->decrement('stock', $itemRequest->quantity);
            }
        });

        // Redirect kembali dengan pesan sukses
        return redirect()->route('item-requests.index')->with('success', 'Permintaan disetujui & stok berhasil diperbarui.');
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

        return view('backend.item-requests.show', compact('itemRequest'));
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