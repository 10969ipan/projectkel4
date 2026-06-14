<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MenuPermissionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test admin users automatically get access to any menu (fallback).
     */
    public function test_admin_has_fallback_permissions(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'menu_permissions' => null,
        ]);

        $this->assertTrue($admin->isAdmin());
        $this->assertTrue($admin->hasPermissionToMenu('dashboard'));
        $this->assertTrue($admin->hasPermissionToMenu('users'));
        $this->assertTrue($admin->hasPermissionToMenu('non_existent_menu'));
    }

    /**
     * Test staff users have default operational permissions when menu_permissions is null.
     */
    public function test_staff_has_default_permissions_when_null(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
            'menu_permissions' => null,
        ]);

        $this->assertTrue($staff->isStaff());
        $this->assertTrue($staff->hasPermissionToMenu('dashboard'));
        $this->assertTrue($staff->hasPermissionToMenu('items'));
        $this->assertTrue($staff->hasPermissionToMenu('transactions'));
        $this->assertFalse($staff->hasPermissionToMenu('users'));
        $this->assertFalse($staff->hasPermissionToMenu('categories'));
    }

    /**
     * Test staff users are allowed access when specifically granted in JSON.
     */
    public function test_staff_has_customized_json_permissions(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
            'menu_permissions' => ['dashboard', 'users'],
        ]);

        $this->assertTrue($staff->hasPermissionToMenu('dashboard'));
        $this->assertTrue($staff->hasPermissionToMenu('users'));
        $this->assertFalse($staff->hasPermissionToMenu('items'));
    }

    /**
     * Test that middleware allows authorized access and redirects/blocks unauthorized.
     */
    public function test_menu_permission_middleware_blocks_unauthorized(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
            'menu_permissions' => ['dashboard'], // No access to items
        ]);

        // Access dashboard (should be allowed if authenticated and mapped, but route might require other middleware)
        // Let's test the middleware directly or through routing
        $response = $this->actingAs($staff)->get(route('items.index'));
        $response->assertStatus(403);
    }

    /**
     * Test that middleware allows authorized menu items.
     */
    public function test_menu_permission_middleware_allows_authorized(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
            'menu_permissions' => ['dashboard', 'items'], // Has access to items
        ]);

        $response = $this->actingAs($staff)->get(route('items.index'));
        $response->assertStatus(200);
    }

    /**
     * Test that custom roles (e.g. manager, supervisor) can have customized menu permissions.
     */
    public function test_custom_role_permissions(): void
    {
        $supervisor = User::factory()->create([
            'role' => 'supervisor',
            'menu_permissions' => ['dashboard', 'categories', 'units'],
        ]);

        $this->assertEquals('supervisor', $supervisor->role);
        $this->assertTrue($supervisor->hasPermissionToMenu('dashboard'));
        $this->assertTrue($supervisor->hasPermissionToMenu('categories'));
        $this->assertTrue($supervisor->hasPermissionToMenu('units'));
        $this->assertFalse($supervisor->hasPermissionToMenu('users'));
    }

    /**
     * Test that custom role users can successfully login to the dashboard and bypass customer-only restrictions.
     */
    public function test_custom_role_can_login_to_dashboard(): void
    {
        $customUser = User::factory()->create([
            'email' => 'custom@sima.com',
            'password' => \Hash::make('secret123'),
            'role' => 'supervisor',
        ]);

        // Try logging in via AuthController
        $response = $this->post(route('login'), [
            'email' => 'custom@sima.com',
            'password' => 'secret123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($customUser);
    }

    /**
     * Test that internal/management users cannot access store routes and are redirected to the admin dashboard.
     */
    public function test_internal_users_cannot_access_store_routes(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
        ]);

        $response = $this->actingAs($staff)->get(route('store.index'));
        $response->assertRedirect(route('dashboard'));
    }

    /**
     * Test that customer users cannot access back-office routes and are redirected to the store.
     */
    public function test_customer_users_cannot_access_backoffice_routes(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
            'store_role' => 'customer',
        ]);

        $response = $this->actingAs($customer)->get(route('dashboard'));
        $response->assertRedirect(route('store.index'));
    }
}
