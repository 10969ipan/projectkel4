<?php

use App\Models\User;
use App\Models\Item;
use App\Models\StoreOrder;
use App\Models\StoreOrderItem;
use App\Models\Address;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

// Bootstrap Laravel (since this is run via tinker or a script)
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = User::where('store_role', 'customer')->first();
$item = Item::where('stock', '>', 5)->first();
$address = Address::where('user_id', $user->id)->first() ?? Address::create([
    'user_id' => $user->id,
    'label' => 'Test Home',
    'full_address' => 'Jl. Test No. 123, Jakarta',
    'is_primary' => true
]);

echo "Testing for User: {$user->name} (ID: {$user->id})\n";
echo "Testing for Item: {$item->name} (Qty: 2)\n";

$methods = ['wallet', 'qris', 'bank', 'paylater', 'cod'];

foreach ($methods as $method) {
    echo "\nProcessing Payment Method: " . strtoupper($method) . "...\n";
    
    try {
        Auth::login($user);
        
        $subTotal = $item->price * 2;
        $shippingCost = 15000;
        $grandTotal = $subTotal + $shippingCost;
        
        // Ensure balance/limit for testing
        if ($method === 'wallet' && $user->wallet_balance < $grandTotal) {
            $user->update(['wallet_balance' => $grandTotal + 10000]);
        }
        if ($method === 'paylater' && $user->paylater_limit < $grandTotal) {
            $user->update(['paylater_limit' => $grandTotal + 10000]);
        }

        DB::transaction(function() use ($user, $item, $address, $method, $subTotal, $shippingCost, $grandTotal) {
            $status = in_array($method, ['cod', 'bank']) ? 'pending' : 'paid';
            
            if ($method === 'wallet') {
                $user->decrement('wallet_balance', $grandTotal);
            } elseif ($method === 'paylater') {
                $user->decrement('paylater_limit', $grandTotal);
            }

            $order = StoreOrder::create([
                'user_id' => $user->id,
                'order_number' => 'TEST-' . strtoupper($method) . '-' . uniqid(),
                'address_id' => $address->id,
                'shipping_method' => 'instant',
                'payment_method' => $method,
                'payment_status' => $status,
                'order_status' => ($status === 'paid' ? 'paid' : 'ordered'),
                'sub_total' => $subTotal,
                'shipping_cost' => $shippingCost,
                'grand_total' => $grandTotal,
            ]);

            StoreOrderItem::create([
                'store_order_id' => $order->id,
                'item_id' => $item->id,
                'quantity' => 2,
                'price' => $item->price,
                'sub_total' => $subTotal,
            ]);

            $item->decrement('stock', 2);
        });

        echo "SUCCESS: Order created for " . strtoupper($method) . "\n";
    } catch (\Exception $e) {
        echo "FAILED: " . $e->getMessage() . "\n";
    }
}

echo "\n--- ALL TEST CASES COMPLETED ---\n";
