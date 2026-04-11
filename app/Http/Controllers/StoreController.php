<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;

class StoreController extends Controller
{
    /**
     * Display a listing of the items for the store front.
     */
    public function index()
    {
        // In a real app we might paginate or filter by stock
        $items = Item::with(['category', 'sizes'])->get();
        return view('frontend.store.index', compact('items'));
    }

    /**
     * Display the specified item details.
     */
    public function show($id)
    {
        $item = Item::with(['category'])->findOrFail($id);
        return view('frontend.store.show', compact('item'));
    }
}
