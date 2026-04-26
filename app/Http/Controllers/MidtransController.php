<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StoreOrder;
use Midtrans\Config;
use Midtrans\Notification;

class MidtransController extends Controller
{
    /**
     * Handle Midtrans Notification Webhook
     */
    public function callback(Request $request)
    {
        // Set configuration
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production');
        Config::$isSanitized = config('services.midtrans.is_sanitized');
        Config::$is3ds = config('services.midtrans.is_3ds');

        try {
            // Bypass Midtrans SDK Notification() because php://input is often consumed on Vercel Serverless
            $transaction = $request->transaction_status;
            $type = $request->payment_type;
            $order_id = $request->order_id;
            $fraud = $request->fraud_status;

            // Handle order_id with timestamp suffix (e.g. ORD-123-1714000000)
            $real_order_id = $order_id;
            $parts = explode('-', $order_id);
            if (count($parts) > 2) {
                array_pop($parts); // Remove the timestamp
                $real_order_id = implode('-', $parts);
            }

            $order = StoreOrder::where('order_number', $real_order_id)->first();

            if (!$order) {
                // Return 200 even if order not found, so Midtrans doesn't retry invalid/test orders
                return response()->json(['message' => 'Order not found (Ignored)'], 200);
            }

            if ($transaction == 'capture') {
                if ($type == 'credit_card') {
                    if ($fraud == 'challenge') {
                        $order->update(['payment_status' => 'pending']);
                    } else {
                        $order->update(['payment_status' => 'paid', 'order_status' => 'paid']);
                        if ($order->user) {
                            $order->user->notify(new \App\Notifications\OrderStatusNotification($order, 'paid'));
                        }
                    }
                }
            } else if ($transaction == 'settlement') {
                $order->update(['payment_status' => 'paid', 'order_status' => 'paid']);
                if ($order->user_id) {
                    \Illuminate\Support\Facades\DB::table('notifications')->insert([
                        'id' => \Illuminate\Support\Str::uuid(),
                        'type' => 'App\Notifications\OrderStatusNotification',
                        'notifiable_type' => 'App\Models\User',
                        'notifiable_id' => $order->user_id,
                        'data' => json_encode([
                            'order_id' => $order->id,
                            'message' => "Pembayaran untuk pesanan #{$order->order_number} berhasil!",
                            'icon' => 'fa-check-circle',
                            'status' => 'paid',
                            'needs_rating' => false,
                        ]),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            } else if ($transaction == 'pending') {
                $order->update(['payment_status' => 'pending']);
            } else if ($transaction == 'deny') {
                $order->update(['payment_status' => 'cancelled', 'order_status' => 'cancelled']);
            } else if ($transaction == 'expire') {
                $order->update(['payment_status' => 'cancelled', 'order_status' => 'cancelled']);
                if ($order->user) {
                    $order->user->notify(new \App\Notifications\OrderStatusNotification($order, 'cancelled'));
                }
            } else if ($transaction == 'cancel') {
                $order->update(['payment_status' => 'cancelled', 'order_status' => 'cancelled']);
                if ($order->user) {
                    $order->user->notify(new \App\Notifications\OrderStatusNotification($order, 'cancelled'));
                }
            }

            return response()->json(['message' => 'Notification processed']);

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
