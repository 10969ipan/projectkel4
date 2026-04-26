<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;

use App\Models\Unit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * CONTROLLER OBAT & ALKES (SIMA-APOTEK)
 * 
 * Menangani operasi CRUD untuk obat-obatan dan alat kesehatan.
 * Sistem ini mendukung:
 * 1. Data Obat (Nama Generik, Produsen)
 * 2. Manajemen Batch (Nomor Batch, Tanggal Kadaluwarsa)
 * 3. Inventaris Terpusat
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
        // EAGER LOADING: Muat relasi category dan unit
        $query = Item::with(['category', 'unit']);

        // FITUR PENCARIAN: Jika ada parameter search
        if ($request->has('search')) {
            $search = $request->search;

            // Cari di kolom name, code, generic_name, atau manufacturer
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('code', 'like', "%$search%")
                    ->orWhere('generic_name', 'like', "%$search%")
                    ->orWhere('manufacturer', 'like', "%$search%");
            })
                // ATAU cari di nama kategori (relasi)
                ->orWhereHas('category', function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%");
                });
        }

        // Pagination 10 item per halaman
        $items = $query->paginate(10);

        return view('backend.items.index', compact('items'));
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

        return view('backend.items.create', compact('categories', 'units'));
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
        $request->validate([
            'code' => 'required|string|max:50|unique:items',
            'name' => 'required|string|max:255',
            'generic_name' => 'nullable|string|max:255',
            'manufacturer' => 'nullable|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'unit_id' => 'required|exists:units,id',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
            $file->move(public_path('image/items'), $filename);
            $imagePath = 'image/items/' . $filename;
        }

        Item::create([
            'code' => $request->code,
            'name' => $request->name,
            'generic_name' => $request->generic_name,
            'manufacturer' => $request->manufacturer,
            'category_id' => $request->category_id,
            'unit_id' => $request->unit_id,
            'price' => $request->price,
            'description' => $request->description,
            'stock' => $request->stock,
            'image_path' => $imagePath,
        ]);

        // Clear Store Cache for all pages
        for ($i = 1; $i <= 5; $i++) {
            Cache::forget('store_catalog_page_' . $i);
        }

        return redirect()
            ->route('items.index')
            ->with('success', 'Obat/Alkes berhasil ditambahkan.');
    }

    /**
     * FUNGSI SHOW: Menampilkan detail barang beserta varian ukurannya
     * 
     * @param Item $item - Barang yang akan ditampilkan (auto-binding)
     * @return View
     */
    public function show(Item $item): View
    {


        return view('backend.items.show', compact('item'));
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



        return view('backend.items.edit', compact('item', 'categories', 'units'));
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
        $request->validate([
            'code' => 'required|string|max:50|unique:items,code,' . $item->id,
            'name' => 'required|string|max:255',
            'generic_name' => 'nullable|string|max:255',
            'manufacturer' => 'nullable|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'unit_id' => 'required|exists:units,id',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|max:2048',
        ]);

        $data = [
            'code' => $request->code,
            'name' => $request->name,
            'generic_name' => $request->generic_name,
            'manufacturer' => $request->manufacturer,
            'category_id' => $request->category_id,
            'unit_id' => $request->unit_id,
            'price' => $request->price,
            'description' => $request->description,
            'stock' => $request->stock,
        ];

        if ($request->hasFile('image')) {
            if ($item->image_path && file_exists(public_path($item->image_path))) {
                @unlink(public_path($item->image_path));
            }
            $file = $request->file('image');
            $filename = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
            $file->move(public_path('image/items'), $filename);
            $data['image_path'] = 'image/items/' . $filename;
        }

        $item->update($data);

        // Clear Store Cache for all pages and this specific item
        for ($i = 1; $i <= 5; $i++) {
            Cache::forget('store_catalog_page_' . $i);
        }
        Cache::forget('store_item_' . $item->id);

        return redirect()
            ->route('items.index')
            ->with('success', 'Obat/Alkes berhasil diperbarui.');
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

        // Clear Store Cache for all pages and this specific item
        for ($i = 1; $i <= 5; $i++) {
            Cache::forget('store_catalog_page_' . $i);
        }
        Cache::forget('store_item_' . $item->id);

        // Redirect ke halaman daftar barang dengan pesan sukses
        return redirect()
            ->route('items.index')
            ->with('success', 'Barang berhasil dihapus.');
    }
}