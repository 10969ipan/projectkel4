<?php

use App\Models\StoreOrder;
use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "--- ADMIN ORDER PROCESSING SIMULATION ---\n";

// Find all test orders created recently
$orders = StoreOrder::where('order_number', 'LIKE', 'TEST-%')
    ->whereNotIn('order_status', ['completed', 'cancelled'])
    ->get();

if ($orders->isEmpty()) {
    echo "No pending test orders found.\n";
    exit;
}

foreach ($orders as $order) {
    echo "\nProcessing Order: {$order->order_number} (Status: {$order->order_status})\n";
    
    try {
        DB::transaction(function() use ($order) {
            // Step 1: Handle Payment Confirmation for Pending items
            if ($order->payment_status === 'pending') {
                echo "-> Confirming Payment...\n";
                $order->update([
                    'payment_status' => 'paid',
                    'order_status' => 'paid'
                ]);
            }
            
            // Step 2: Processing
            echo "-> Changing status to PROCESSING...\n";
            $order->update(['order_status' => 'processing']);
            
            // Step 3: Shipped (Simulated via processing or manual if column exists)
            // The controller supports 'completed' directly after
            
            // Step 4: Complete
            echo "-> Completing Order...\n";
            $order->update(['order_status' => 'completed']);
        });
        
        echo "SUCCESS: Order {$order->order_number} is now COMPLETED.\n";
    } catch (\Exception $e) {
        echo "FAILED to process {$order->order_number}: " . $e->getMessage() . "\n";
    }
}

echo "\n--- ADMIN PROCESSING FINISHED ---\n";
