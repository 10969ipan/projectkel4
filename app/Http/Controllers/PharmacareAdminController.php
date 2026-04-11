<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StoreOrder;
use App\Models\User;
use App\Models\TelemedicineChat;

class PharmacareAdminController extends Controller
{
    public function index()
    {
        // Get all store orders
        $orders = StoreOrder::with(['user', 'address'])->latest()->limit(5)->get();
        
        // Get all users who have store role
        $customers = User::where('store_role', 'customer')->limit(5)->get();

        return view('admin.pharmacare.index', compact('orders', 'customers'));
    }

    public function customers()
    {
        $customers = User::where('store_role', 'customer')
            ->with(['addresses', 'storeOrders'])
            ->latest()
            ->get();
        return view('admin.pharmacare.customers', compact('customers'));
    }

    public function updateCustomer(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|min:6',
            'address' => 'nullable|string'
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        
        if ($request->filled('password')) {
            $user->password = $request->password; // Autohashed in model cast
        }
        
        $user->save();

        if ($request->filled('address')) {
            $user->addresses()->updateOrCreate(
                ['is_primary' => true],
                ['full_address' => $request->address, 'label' => 'Utama Admin']
            );
        }

        return back()->with('success', 'Data pelanggan berhasil diperbarui!');
    }

    public function approvePrescription($userId)
    {
        $user = User::findOrFail($userId);
        $user->is_prescription_approved = true;
        $user->save();

        return back()->with('success', 'Apoteker memverifikasi resep digital Pelanggan.');
    }

    public function updatePaylater(Request $request, $userId)
    {
        $user = User::findOrFail($userId);
        $user->paylater_limit = $request->limit;
        $user->save();

        return back()->with('success', 'Limit Paylater berhasil diperbarui.');
    }
}
