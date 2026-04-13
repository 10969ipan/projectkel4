<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Facades\Cache;

class StoreController extends Controller
{
    /**
     * Display a listing of the items for the store front.
     * Optimized with Pagination & Caching for professional performance.
     */
    public function index()
    {
        // Cache data katalog selama 15 menit agar respon menu antar-halaman kilat
        $items = Cache::remember('store_catalog_page_' . request('page', 1), 900, function() {
            return Item::with(['category:id,name', 'unit:id,name'])
                ->select('id', 'name', 'price', 'unit_id', 'image_path', 'requires_prescription', 'category_id', 'stock')
                ->latest()
                ->paginate(20);
        });

        if (request()->ajax()) {
            return view('frontend.store.partials.product-grid', compact('items'))->render();
        }

        // Fetch all curated wellness articles for the homepage highlight
        $wellnessArticles = Cache::remember('store_wellness_highlights_v2', 3600, function() {
            return \App\Models\HealthArticle::where('is_active', true)->latest()->get();
        });

        return view('frontend.store.index', compact('items', 'wellnessArticles'));
    }

    /**
     * Display the specified item details.
     */
    public function show($id)
    {
        $item = Cache::remember('store_item_' . $id, 3600, function() use ($id) {
            return Item::with(['category:id,name', 'unit:id,name', 'sizes'])
                ->select('id', 'name', 'price', 'description', 'unit_id', 'image_path', 'requires_prescription', 'category_id', 'stock')
                ->findOrFail($id);
        });
        
        return view('frontend.store.show', compact('item'));
    }

    /**
     * Get Item Data for Quick View Modal
     */
    public function quickView($id)
    {
        $item = Item::with(['category', 'unit'])->findOrFail($id);
        
        return response()->json([
            'id' => $item->id,
            'name' => $item->name,
            'price' => $item->price,
            'description' => $item->description ?? 'Tidak ada deskripsi produk.',
            'category' => $item->category->name ?? 'Uncategorized',
            'unit' => $item->unit->name ?? 'Kemasan',
            'image_url' => $item->image_path ? asset($item->image_path) : null,
            'requires_prescription' => $item->requires_prescription,
            'stock' => $item->stock,
        ]);
    }

    /**
     * AJAX Search for Autocomplete
     */
    public function searchAjax(Request $request)
    {
        $query = $request->get('q');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $items = Item::with('category')->where('name', 'LIKE', "%{$query}%")
            ->select('id', 'name', 'price', 'image_path', 'category_id', 'requires_prescription')
            ->limit(10)
            ->get();

        return response()->json($items);
    }
}
