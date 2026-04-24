<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StoreReview;
use App\Models\StoreOrder;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Submit a new review for an order
     */
    public function store(Request $request)
    {
        $request->validate([
            'store_order_id' => 'required|exists:store_orders,id',
            'rating'         => 'required|integer|min:1|max:5',
            'comment'        => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();
        $order = StoreOrder::where('id', $request->store_order_id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        // Check if already reviewed
        $existingReview = StoreReview::where('store_order_id', $order->id)->first();
        if ($existingReview) {
            return response()->json(['success' => false, 'message' => 'Anda sudah memberikan rating untuk pesanan ini.'], 400);
        }

        StoreReview::create([
            'user_id'        => $user->id,
            'store_order_id' => $order->id,
            'rating'         => $request->rating,
            'comment'        => $request->comment,
            'is_visible'     => true,
        ]);

        return response()->json(['success' => true, 'message' => 'Terima kasih atas ulasan Anda!']);
    }

    /**
     * Get reviews for the homepage
     */
    public static function getLatestReviews($limit = 6)
    {
        return StoreReview::where('is_visible', true)
            ->with('user:id,name')
            ->latest()
            ->limit($limit)
            ->get();
    }
}
