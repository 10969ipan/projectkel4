<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionCancellationTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_cancel_their_own_subscription(): void
    {
        // 1. Create a customer user
        $customer = User::factory()->create([
            'role' => 'pelanggan',
            'store_role' => 'customer',
        ]);

        // 2. Create category & unit & item
        $category = Category::create(['name' => 'Obat Bebas']);
        $unit = Unit::create(['name' => 'Tablet', 'symbol' => 'tab']);
        $item = Item::create([
            'code' => 'OBT-001',
            'name' => 'Paracetamol 500mg',
            'price' => 5000,
            'stock' => 100,
            'category_id' => $category->id,
            'unit_id' => $unit->id,
        ]);

        // 3. Create active subscription
        $subscription = Subscription::create([
            'user_id' => $customer->id,
            'item_id' => $item->id,
            'quantity' => 2,
            'interval_days' => 30,
            'discount_percentage' => 10,
            'next_delivery_date' => now()->addDays(30)->toDateString(),
            'status' => 'active',
        ]);

        // 4. Act: Log in and hit cancel route
        $response = $this->actingAs($customer)
            ->post(route('account.subscriptions.cancel', $subscription->id));

        // 5. Assert: Redirect back with success message and status is cancelled
        $response->assertStatus(302);
        $response->assertSessionHas('success', 'Langganan berhasil dibatalkan.');
        
        $subscription->refresh();
        $this->assertEquals('cancelled', $subscription->status);
    }

    public function test_customer_cannot_cancel_others_subscription(): void
    {
        // 1. Create two customer users
        $customer1 = User::factory()->create(['role' => 'pelanggan', 'store_role' => 'customer']);
        $customer2 = User::factory()->create(['role' => 'pelanggan', 'store_role' => 'customer']);

        // 2. Create category & unit & item
        $category = Category::create(['name' => 'Obat Bebas']);
        $unit = Unit::create(['name' => 'Tablet', 'symbol' => 'tab']);
        $item = Item::create([
            'code' => 'OBT-001',
            'name' => 'Paracetamol 500mg',
            'price' => 5000,
            'stock' => 100,
            'category_id' => $category->id,
            'unit_id' => $unit->id,
        ]);

        // 3. Create active subscription for customer 1
        $subscription = Subscription::create([
            'user_id' => $customer1->id,
            'item_id' => $item->id,
            'quantity' => 2,
            'interval_days' => 30,
            'discount_percentage' => 10,
            'next_delivery_date' => now()->addDays(30)->toDateString(),
            'status' => 'active',
        ]);

        // 4. Act: Log in as customer 2 and try to hit cancel route for customer 1's subscription
        $response = $this->actingAs($customer2)
            ->post(route('account.subscriptions.cancel', $subscription->id));

        // 5. Assert: 404 Not Found (since query is scoped by user_id)
        $response->assertStatus(404);
        
        $subscription->refresh();
        $this->assertEquals('active', $subscription->status);
    }
}
