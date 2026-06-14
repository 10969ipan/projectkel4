<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * CONTROLLER USER (PENGGUNA)
 * 
 * Menangani operasi CRUD untuk manajemen pengguna dan profil
 * Controller ini menangani 2 jenis operasi:
 * 1. Manajemen User (oleh Admin) - CRUD lengkap untuk semua user
 * 2. Manajemen Profil Sendiri (oleh semua user) - Update profil sendiri
 */
class UserController extends Controller
{
    /**
     * FUNGSI INDEX: Menampilkan daftar semua pengguna
     * 
     * Hanya bisa diakses oleh Admin
     * 
     * @return View
     */
    public function index(): View
    {
        // Tampilkan semua user manajemen (Admin, Staff, dan Peran Kustom lainnya) kecuali Pelanggan Toko
        $users = User::where(function ($query) {
            $query->whereNull('store_role')
                  ->where('role', '!=', 'customer')
                  ->where('role', '!=', 'pelanggan');
        })->get();

        return view('backend.users.index', compact('users'));
    }

    /**
     * FUNGSI CREATE: Menampilkan form untuk membuat user baru
     * 
     * Hanya bisa diakses oleh Admin
     * 
     * @return View
     */
    public function create(): View
    {
        return view('backend.users.create');
    }

    /**
     * FUNGSI STORE: Menyimpan user baru ke database
     * 
     * Proses yang dilakukan:
     * 1. Validasi input (name, email, password, foto profil)
     * 2. Hash password untuk keamanan
     * 3. Upload foto profil (jika ada)
     * 4. Simpan user dengan role 'staff' secara default
     * 
     * @param Request $request - Request yang berisi data user
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        // VALIDASI INPUT
        $request->validate([
            'name' => 'required|string|max:255',                                    // Nama wajib diisi
            'email' => 'required|email|unique:users',                               // Email wajib, format email, dan harus unik
            'password' => 'required|string|min:8',                                  // Password minimal 8 karakter
            'role' => 'required|string|max:50',                             // Peran user: admin, staff, atau kustom lainnya
            'menu_permissions' => 'nullable|array',                                 // Izin navigasi
            'menu_permissions.*' => 'string',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',   // Foto opsional, max 2MB
        ]);

        // Siapkan data user yang akan disimpan
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            // KEAMANAN: Hash password menggunakan bcrypt sebelum disimpan
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'menu_permissions' => $request->menu_permissions ?? [],
        ];

        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');
            $data['profile_photo'] = 'data:' . $file->getMimeType() . ';base64,' . base64_encode(file_get_contents($file->getRealPath()));
        }

        // Simpan user baru ke database
        User::create($data);

        // Redirect ke halaman daftar user dengan pesan sukses
        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    /**
     * FUNGSI EDIT: Menampilkan form untuk mengedit user
     * 
     * Hanya bisa diakses oleh Admin
     * 
     * @param User $user - User yang akan diedit (auto-binding dari route)
     * @return View
     */
    public function edit(User $user): View
    {
        return view('backend.users.edit', compact('user'));
    }

    /**
     * FUNGSI UPDATE: Memperbarui data user di database
     * 
     * Proses yang dilakukan:
     * 1. Validasi input (email harus unik kecuali untuk user ini sendiri)
     * 2. Update password hanya jika diisi (opsional)
     * 3. Update foto profil (hapus foto lama jika ada, upload foto baru)
     * 4. Simpan perubahan ke database
     * 
     * @param Request $request - Request yang berisi data user yang diupdate
     * @param User $user - User yang akan diupdate
     * @return RedirectResponse
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        // VALIDASI INPUT
        $rules = [
            'name' => 'required|string|max:255',
            // Email harus unik, tapi abaikan email user ini sendiri
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            // Password opsional (nullable) - hanya diupdate jika diisi
            'password' => 'nullable|string|min:8',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];

        // Hanya validasi role dan permissions jika tidak sedang mengedit diri sendiri
        if (auth()->id() !== $user->id) {
            $rules['role'] = 'required|string|max:50';
            $rules['menu_permissions'] = 'nullable|array';
            $rules['menu_permissions.*'] = 'string';
        }

        $request->validate($rules);

        // Siapkan data yang akan diupdate
        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        // Hanya update role dan permissions jika tidak sedang mengedit diri sendiri (keamanan lockout)
        if (auth()->id() !== $user->id) {
            $data['role'] = $request->role;
            $data['menu_permissions'] = $request->menu_permissions ?? [];
        }

        // UPDATE PASSWORD (hanya jika diisi)
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('profile_photo')) {
            if ($user->profile_photo && !str_starts_with($user->profile_photo, 'data:image/') && Storage::disk('public')->exists($user->profile_photo)) {
                Storage::disk('public')->delete($user->profile_photo);
            }
            $file = $request->file('profile_photo');
            $data['profile_photo'] = 'data:' . $file->getMimeType() . ';base64,' . base64_encode(file_get_contents($file->getRealPath()));
        }

        // Update data user di database
        $user->update($data);

        // Redirect ke halaman daftar user dengan pesan sukses
        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    /**
     * FUNGSI DESTROY: Menghapus user dari database
     * 
     * Proses yang dilakukan:
     * 1. Hapus foto profil dari storage (jika ada)
     * 2. Hapus user dari database
     * 
     * CATATAN: Sebaiknya gunakan soft delete untuk data user
     * agar riwayat transaksi tetap terjaga
     * 
     * @param User $user - User yang akan dihapus
     * @return RedirectResponse
     */
    public function destroy(User $user): RedirectResponse
    {
        // LANGKAH 1: Hapus foto profil dari storage (jika ada)
        // Ini penting untuk membersihkan file yang tidak terpakai
        if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
            Storage::disk('public')->delete($user->profile_photo);
        }

        // LANGKAH 2: Hapus user dari database
        $user->delete();

        // Redirect ke halaman daftar user dengan pesan sukses
        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil dihapus.');
    }

    // ========================================================================
    // FITUR PROFIL SENDIRI
    // ========================================================================
    // Fungsi-fungsi di bawah ini untuk user mengelola profil mereka sendiri
    // Bisa diakses oleh semua user (admin dan staff)
    // ========================================================================

    /**
     * FUNGSI PROFILE: Menampilkan form edit profil untuk user yang sedang login
     * 
     * Menggunakan view yang sama dengan edit user (users.edit)
     * tapi datanya adalah user yang sedang login
     * 
     * @return View
     */
    public function profile(): View
    {
        // Ambil data user yang sedang login
        // auth()->user() mengembalikan instance User yang sedang login
        return view('backend.users.edit', ['user' => auth()->user()]);
    }

    /**
     * FUNGSI UPDATE PROFILE: Memperbarui profil user yang sedang login
     * 
     * Proses yang dilakukan sama dengan update(), tapi:
     * - Target user adalah user yang sedang login (auth()->user())
     * - Redirect ke dashboard (bukan ke users.index)
     * - Tidak bisa mengubah role
     * 
     * @param Request $request - Request yang berisi data profil yang diupdate
     * @return RedirectResponse
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        // Ambil user yang sedang login
        $user = auth()->user();

        // VALIDASI INPUT
        $request->validate([
            'name' => 'required|string|max:255',
            // Email harus unik, tapi abaikan email user ini sendiri
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            // Password opsional - hanya diupdate jika diisi
            'password' => 'nullable|string|min:8',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Siapkan data yang akan diupdate
        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        // UPDATE PASSWORD (hanya jika diisi)
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('profile_photo')) {
            if ($user->profile_photo && !str_starts_with($user->profile_photo, 'data:image/') && Storage::disk('public')->exists($user->profile_photo)) {
                Storage::disk('public')->delete($user->profile_photo);
            }
            $file = $request->file('profile_photo');
            $data['profile_photo'] = 'data:' . $file->getMimeType() . ';base64,' . base64_encode(file_get_contents($file->getRealPath()));
        }

        // Update data profil di database
        /** @var \App\Models\User $user */
        $user->update($data);

        // Redirect ke dashboard (bukan ke users.index seperti update biasa)
        return redirect()
            ->route('dashboard')
            ->with('success', 'Profil berhasil diperbarui.');
    }
}