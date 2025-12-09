<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use App\Models\ItemSize;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $query = Item::with(['category', 'unit']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('code', 'like', "%$search%")
                  ->orWhere('size', 'like', "%$search%");
            })->orWhereHas('category', function ($q) use ($search) {
                $q->where('name', 'like', "%$search%");
            });
        }

        $items = $query->paginate(10);
        return view('items.index', compact('items'));
    }

    public function create()
    {
        $categories = Category::all();
        $units = Unit::all();
        return view('items.create', compact('categories', 'units'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:items',
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'unit_id' => 'required|exists:units,id',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            // Validasi Array Ukuran
            'sizes' => 'nullable|array',
            'sizes.*' => 'required|string|distinct',
            'stocks' => 'nullable|array',
            'stocks.*' => 'required|integer|min:0',
        ]);

        DB::transaction(function () use ($request) {
            // 1. Hitung Total Stok dari input varian
            $totalStock = 0;
            $sizeSummary = null;

            if ($request->has('sizes') && is_array($request->sizes)) {
                $totalStock = array_sum($request->stocks);
                $sizeSummary = implode(', ', $request->sizes); // Ringkasan size: "S, M, L"
            }

            // 2. Buat Item Utama
            $item = Item::create([
                'code' => $request->code,
                'name' => $request->name,
                'category_id' => $request->category_id,
                'unit_id' => $request->unit_id,
                'price' => $request->price,
                'description' => $request->description,
                'stock' => $totalStock, // Stok tersinkronisasi
                'size' => $sizeSummary, // Ringkasan text
            ]);

            // 3. Simpan Detail Ukuran (ItemSize)
            if ($request->has('sizes')) {
                foreach ($request->sizes as $index => $sizeVal) {
                    // Pastikan size tidak kosong dan stock ada
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

        return redirect()->route('items.index')->with('success', 'Item berhasil dibuat dengan detail ukuran.');
    }

    public function show(Item $item)
    {
        // Load sizes agar bisa ditampilkan di detail
        $item->load('sizes');
        return view('items.show', compact('item'));
    }

    public function edit(Item $item)
    {
        $categories = Category::all();
        $units = Unit::all();
        $item->load('sizes'); // Load detail ukuran yang sudah ada
        return view('items.edit', compact('item', 'categories', 'units'));
    }

    public function update(Request $request, Item $item)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:items,code,' . $item->id,
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'unit_id' => 'required|exists:units,id',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'sizes' => 'nullable|array',
            'sizes.*' => 'required|string', // distinct dihapus disini agar tidak error validasi saat form dikirim (bisa divalidasi manual di JS/Logic jika perlu)
            'stocks' => 'nullable|array',
            'stocks.*' => 'required|integer|min:0',
        ]);

        DB::transaction(function () use ($request, $item) {
            // 1. Hitung Total Baru
            $totalStock = 0;
            $sizeSummary = null;

            if ($request->has('sizes') && is_array($request->sizes)) {
                $totalStock = array_sum($request->stocks);
                $sizeSummary = implode(', ', array_unique($request->sizes));
            }

            // 2. Update Item Utama
            $item->update([
                'code' => $request->code,
                'name' => $request->name,
                'category_id' => $request->category_id,
                'unit_id' => $request->unit_id,
                'price' => $request->price,
                'description' => $request->description,
                'stock' => $totalStock, // Update total stok
                'size' => $sizeSummary,
            ]);

            // 3. Sinkronisasi ItemSize (Hapus lama, buat baru - strategi replace)
            // Ini cara paling aman untuk memastikan data sinkron
            $item->sizes()->delete();

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

        return redirect()->route('items.index')->with('success', 'Item berhasil diperbarui.');
    }

    public function destroy(Item $item)
    {
        $item->delete();
        return redirect()->route('items.index')->with('success', 'Item deleted successfully.');
    }
}