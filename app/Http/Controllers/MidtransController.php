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
            $notification = new Notification();

            $transaction = $notification->transaction_status;
            $type = $notification->payment_type;
            $order_id = $notification->order_id;
            $fraud = $notification->fraud_status;

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
                    }
                }
            } else if ($transaction == 'settlement') {
                $order->update(['payment_status' => 'paid', 'order_status' => 'paid']);
            } else if ($transaction == 'pending') {
                $order->update(['payment_status' => 'pending']);
            } else if ($transaction == 'deny') {
                $order->update(['payment_status' => 'cancelled', 'order_status' => 'cancelled']);
            } else if ($transaction == 'expire') {
                $order->update(['payment_status' => 'cancelled', 'order_status' => 'cancelled']);
            } else if ($transaction == 'cancel') {
                $order->update(['payment_status' => 'cancelled', 'order_status' => 'cancelled']);
            }

            return response()->json(['message' => 'Notification processed']);

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
