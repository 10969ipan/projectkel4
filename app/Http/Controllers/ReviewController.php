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

        // Update notification needs_rating to false
        $notifications = \Illuminate\Support\Facades\DB::table('notifications')
            ->where('notifiable_id', $user->id)
            ->where('type', 'App\Notifications\OrderStatusNotification')
            ->get();
            
        foreach ($notifications as $notif) {
            $data = json_decode($notif->data, true);
            if (isset($data['order_id']) && $data['order_id'] == $order->id && isset($data['needs_rating']) && $data['needs_rating']) {
                $data['needs_rating'] = false;
                \Illuminate\Support\Facades\DB::table('notifications')
                    ->where('id', $notif->id)
                    ->update(['data' => json_encode($data)]);
            }
        }

        return response()->json(['success' => true, 'message' => 'Terima kasih atas ulasan Anda!']);
    }

    /**
     * Get reviews for the homepage
     */
    public static function getLatestReviews($limit = 6)
    {
        return StoreReview::where('is_visible', true)
            ->with(['user:id,name', 'order.items.item'])
            ->latest()
            ->limit($limit)
            ->get();
    }
}
