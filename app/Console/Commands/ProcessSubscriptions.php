<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscription;
use App\Models\StoreOrder;
use App\Models\StoreOrderItem;
use App\Models\WalletTransaction;
use App\Models\User;
use App\Models\Item;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pharmacare:process-subscriptions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process due medicine subscriptions and deduct from user wallets';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for due subscriptions...');

        $dueSubscriptions = Subscription::where('status', 'active')
            ->where('next_delivery_date', '<=', now())
            ->with(['user', 'item'])
            ->get();

        if ($dueSubscriptions->isEmpty()) {
            $this->comment('No subscriptions due today.');
            return;
        }

        foreach ($dueSubscriptions as $sub) {
            $this->processSubscription($sub);
        }

        $this->info('Finished processing subscriptions.');
    }

    private function processSubscription(Subscription $sub)
    {
        $user = $sub->user;
        $item = $sub->item;

        if (!$user || !$item) {
            $sub->update(['status' => 'error']);
            return;
        }

        // Calculate Price with 10% discount
        $unitPrice = $item->price * (1 - ($sub->discount_percentage / 100));
        $shippingCost = 10000; // Regular delivery for auto-subs
        $grandTotal = ($unitPrice * $sub->quantity) + $shippingCost;

        // Check Wallet Balance
        if ($user->wallet_balance < $grandTotal) {
            $this->error("Insufficient balance for User ID {$user->id} (Sub ID {$sub->id})");
            // Optional: Notify user
            return;
        }

        // Check Stock
        if ($item->stock < $sub->quantity) {
            $this->error("Insufficient stock for Item ID {$item->id}");
            return;
        }

        try {
            DB::transaction(function() use ($sub, $user, $item, $unitPrice, $grandTotal, $shippingCost) {
                // 1. Deduct Wallet
                $user->decrement('wallet_balance', $grandTotal);
                WalletTransaction::create([
                    'user_id' => $user->id,
                    'amount' => -$grandTotal,
                    'type' => 'payment',
                    'description' => "Otomatis: Perpanjangan langganan {$item->name}",
                ]);

                // 2. Create Order
                $orderNumber = 'SUB-' . strtoupper(uniqid());
                // Use user's primary address
                $address = $user->addresses()->where('is_primary', true)->first();
                
                $order = StoreOrder::create([
                    'user_id' => $user->id,
                    'order_number' => $orderNumber,
                    'address_id' => $address ? $address->id : null,
                    'shipping_method' => 'regular',
                    'payment_method' => 'wallet',
                    'payment_status' => 'paid',
                    'order_status' => 'paid',
                    'sub_total' => $unitPrice * $sub->quantity,
                    'shipping_cost' => $shippingCost,
                    'grand_total' => $grandTotal,
                    'notes' => 'Pesanan otomatis dari langganan aktif.'
                ]);

                StoreOrderItem::create([
                    'store_order_id' => $order->id,
                    'item_id' => $item->id,
                    'quantity' => $sub->quantity,
                    'price' => $unitPrice,
                    'sub_total' => $unitPrice * $sub->quantity,
                ]);

                // 3. Update Item Stock
                $item->decrement('stock', $sub->quantity);

                // 4. Update Subscription
                $sub->update([
                    'next_delivery_date' => now()->addDays($sub->interval_days)
                ]);

                $this->info("Subscription processed successfully for User {$user->name} (Order: {$orderNumber})");
            });

        } catch (\Exception $e) {
            Log::error("Failed to process subscription ID {$sub->id}: " . $e->getMessage());
            $this->error("Error: " . $e->getMessage());
        }
    }
}
