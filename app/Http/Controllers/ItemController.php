<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use App\Models\ItemSize;
use App\Models\Unit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * CONTROLLER BARANG (ITEM)
 * 
 * Menangani operasi CRUD untuk barang dengan fitur varian ukuran
 * Controller ini kompleks karena menangani:
 * 1. Data barang utama (items table)
 * 2. Varian ukuran barang (item_sizes table)
 * 3. Relasi dengan kategori dan satuan
 */
class ItemController extends Controller
{
    /**
     * FUNGSI INDEX: Menampilkan daftar semua barang dengan fitur pencarian
     * 
     * @param Request $request - Request yang mungkin berisi parameter search
     * @return View
     */
    public function index(Request $request): View
    {
        // EAGER LOADING: Muat relasi category, unit, dan sizes sekaligus
        // Ini mencegah N+1 query problem dan meningkatkan performa
        $query = Item::with(['category', 'unit', 'sizes']);

        // FITUR PENCARIAN: Jika ada parameter search
        if ($request->has('search')) {
            $search = $request->search;

            // Cari di kolom name, code, atau size dari tabel items
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('code', 'like', "%$search%")
                    ->orWhere('size', 'like', "%$search%");
            })
                // ATAU cari di nama kategori (relasi)
                ->orWhereHas('category', function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%");
                });
        }

        // Pagination 10 item per halaman
        $items = $query->paginate(10);

        return view('items.index', compact('items'));
    }

    /**
     * FUNGSI CREATE: Menampilkan form untuk membuat barang baru
     * 
     * @return View
     */
    public function create(): View
    {
        // Ambil semua kategori dan satuan untuk dropdown di form
        $categories = Category::all();
        $units = Unit::all();

        return view('items.create', compact('categories', 'units'));
    }

    /**
     * FUNGSI STORE: Menyimpan barang baru beserta varian ukurannya
     * 
     * Proses yang dilakukan:
     * 1. Validasi input
     * 2. Hitung total stok dari semua varian ukuran
     * 3. Simpan data barang utama
     * 4. Simpan setiap varian ukuran ke tabel item_sizes
     * 
     * Menggunakan database transaction untuk memastikan data konsisten
     * 
     * @param Request $request - Request yang berisi data barang dan varian
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        // VALIDASI INPUT
        $request->validate([
            'code' => 'required|string|max:50|unique:items',           // Kode barang harus unik
            'name' => 'required|string|max:255',                        // Nama barang wajib
            'category_id' => 'required|exists:categories,id',           // Kategori harus ada di database
            'unit_id' => 'required|exists:units,id',                    // Satuan harus ada di database
            'price' => 'required|numeric|min:0',                        // Harga minimal 0
            'description' => 'nullable|string',                         // Deskripsi opsional
            'sizes' => 'nullable|array',                                // Array ukuran (opsional)
            'sizes.*' => 'required|string',                             // Setiap ukuran wajib string
            'stocks' => 'nullable|array',                               // Array stok per ukuran
            'stocks.*' => 'required|integer|min:0',                     // Setiap stok minimal 0
        ]);

        // DATABASE TRANSACTION: Pastikan semua operasi berhasil atau semua dibatalkan
        DB::transaction(function () use ($request) {
            $totalStock = 0;
            $sizeSummary = null;

            // HITUNG TOTAL STOK: Jika ada varian ukuran
            if ($request->has('sizes') && is_array($request->sizes)) {
                // Jumlahkan semua stok dari setiap ukuran
                // Contoh: [10, 20, 15] = 45 total
                $totalStock = array_sum($request->stocks ?? []);

                // Buat ringkasan ukuran untuk ditampilkan
                // Contoh: "S, M, L, XL"
                $sizeSummary = implode(', ', $request->sizes);
            }

            // LANGKAH 1: Simpan data barang utama ke tabel items
            $item = Item::create([
                'code' => $request->code,
                'name' => $request->name,
                'category_id' => $request->category_id,
                'unit_id' => $request->unit_id,
                'price' => $request->price,
                'description' => $request->description,
                'stock' => $totalStock,           // Total stok dari semua ukuran
                'size' => $sizeSummary,           // Ringkasan ukuran (untuk display)
            ]);

            // LANGKAH 2: Simpan setiap varian ukuran ke tabel item_sizes
            if ($request->has('sizes')) {
                foreach ($request->sizes as $index => $sizeVal) {
                    // Skip jika ukuran kosong
                    if (!empty($sizeVal)) {
                        ItemSize::create([
                            'item_id' => $item->id,                     // ID barang yang baru dibuat
                            'size' => $sizeVal,                         // Nama ukuran (S, M, L, dll)
                            'stock' => $request->stocks[$index] ?? 0,   // Stok untuk ukuran ini
                        ]);
                    }
                }
            }
        });

        // Redirect ke halaman daftar barang dengan pesan sukses
        return redirect()
            ->route('items.index')
            ->with('success', 'Barang berhasil ditambahkan.');
    }

    /**
     * FUNGSI SHOW: Menampilkan detail barang beserta varian ukurannya
     * 
     * @param Item $item - Barang yang akan ditampilkan (auto-binding)
     * @return View
     */
    public function show(Item $item): View
    {
        // Load relasi sizes untuk menampilkan detail setiap ukuran
        $item->load('sizes');

        return view('items.show', compact('item'));
    }

    /**
     * FUNGSI EDIT: Menampilkan form untuk mengedit barang
     * 
     * @param Item $item - Barang yang akan diedit
     * @return View
     */
    public function edit(Item $item): View
    {
        // Ambil data untuk dropdown
        $categories = Category::all();
        $units = Unit::all();

        // Load varian ukuran yang sudah ada
        $item->load('sizes');

        return view('items.edit', compact('item', 'categories', 'units'));
    }

    /**
     * FUNGSI UPDATE: Memperbarui data barang dan varian ukurannya
     * 
     * Proses yang dilakukan:
     * 1. Validasi input
     * 2. Hitung ulang total stok dari varian baru
     * 3. Update data barang utama
     * 4. Hapus semua varian lama
     * 5. Simpan varian baru
     * 
     * @param Request $request - Request yang berisi data update
     * @param Item $item - Barang yang akan diupdate
     * @return RedirectResponse
     */
    public function update(Request $request, Item $item): RedirectResponse
    {
        // VALIDASI INPUT
        $request->validate([
            'code' => 'required|string|max:50|unique:items,code,' . $item->id,
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'unit_id' => 'required|exists:units,id',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'sizes' => 'nullable|array',
            'stocks' => 'nullable|array',
        ]);

        // DATABASE TRANSACTION
        DB::transaction(function () use ($request, $item) {
            $totalStock = 0;
            $sizeSummary = null;

            // HITUNG ULANG TOTAL STOK dari varian baru
            if ($request->has('sizes') && is_array($request->sizes)) {
                $totalStock = array_sum($request->stocks ?? []);
                $sizeSummary = implode(', ', $request->sizes);
            }

            // LANGKAH 1: Update data barang utama
            $item->update([
                'code' => $request->code,
                'name' => $request->name,
                'category_id' => $request->category_id,
                'unit_id' => $request->unit_id,
                'price' => $request->price,
                'description' => $request->description,
                'stock' => $totalStock,
                'size' => $sizeSummary,
            ]);

            // LANGKAH 2: RESET VARIAN UKURAN
            // Hapus semua varian lama, lalu insert varian baru
            // Ini lebih sederhana daripada update satu per satu
            $item->sizes()->delete();

            // LANGKAH 3: Simpan varian baru
            if ($request->has('sizes')) {
                foreach ($request->sizes as $index => $sizeVal) {
                    if (!empty($sizeVal)) {
                        ItemSize::create([
                            'item_id' => $item->id,
                            'size' => $sizeVal,
                            'stock' => $request->stocks[$index] ?? 0,
                        ]);
                    }
                }
            }
        });

        // Redirect ke halaman daftar barang dengan pesan sukses
        return redirect()
            ->route('items.index')
            ->with('success', 'Barang berhasil diperbarui.');
    }

    /**
     * FUNGSI DESTROY: Menghapus barang dari database
     * 
     * CATATAN: Varian ukuran akan terhapus otomatis karena foreign key cascade
     * 
     * @param Item $item - Barang yang akan dihapus
     * @return RedirectResponse
     */
    public function destroy(Item $item): RedirectResponse
    {
        // Hapus barang (varian ukuran akan terhapus otomatis)
        $item->delete();

        // Redirect ke halaman daftar barang dengan pesan sukses
        return redirect()
            ->route('items.index')
            ->with('success', 'Barang berhasil dihapus.');
    }
}