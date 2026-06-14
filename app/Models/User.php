<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

/**
 * MODEL USER (PENGGUNA)
 * 
 * Model ini merepresentasikan pengguna sistem (Admin dan Staff)
 * Extends Authenticatable untuk fitur autentikasi Laravel
 * 
 * Tabel Database: users
 * 
 * Kolom:
 * - id: Primary key
 * - name: Nama lengkap user
 * - email: Email (unik, untuk login)
 * - password: Password (di-hash dengan bcrypt)
 * - role: Peran user ('admin' atau 'staff')
 * - profile_photo: Path foto profil (opsional)
 * - remember_token: Token untuk "Remember Me"
 * - email_verified_at: Waktu verifikasi email
 * - created_at: Waktu pembuatan
 * - updated_at: Waktu update terakhir
 * 
 * Role System:
 * - Admin: Akses penuh (CRUD semua data, approve/reject request)
 * - Staff: Akses terbatas (lihat barang, buat permintaan, lihat transaksi sendiri)
 * 
 * Relasi:
 * - transactions: User membuat banyak transaksi
 * - itemRequests: User (staff) membuat banyak permintaan barang
 * - processedRequests: User (admin) memproses banyak permintaan
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;
    
    /**
     * BOOTED METHOD
     * 
     * Handles automatic limit assignment for new customers.
     */
    protected static function booted()
    {
        static::creating(function ($user) {
            // Default Paylater limit for Customers only
            if ($user->store_role === 'customer' || $user->role === 'pelanggan') {
                $user->paylater_limit = 500000;
            } else {
                $user->paylater_limit = 0;
            }
        });
    }

    /**
     * MASS ASSIGNMENT
     * 
     * Kolom yang boleh diisi secara mass assignment
     */
    protected $fillable = [
        'name',             // Nama lengkap user
        'email',            // Email untuk login
        'phone',            // Nomor HP (WhatsApp)
        'password',         // Password (akan di-hash otomatis)
        'role',             // Peran: 'admin' atau 'staff'
        'profile_photo',    // Path foto profil (contoh: profile-photos/abc123.jpg)
        'paylater_limit',
        'wallet_balance',
        'is_prescription_approved',
        'store_role',
        'google_id',        // Google OAuth ID
        'avatar',           // URL foto profil dari Google
        'menu_permissions', // Izin menu navigasi
    ];

    /**
     * HIDDEN ATTRIBUTES
     * 
     * Kolom yang disembunyikan saat serialisasi (toArray(), toJson())
     * Untuk keamanan, password dan remember_token tidak boleh terekspos
     */
    protected $hidden = [
        'password',         // Jangan pernah tampilkan password
        'remember_token',   // Token untuk "Remember Me" feature
    ];

    /**
     * ATTRIBUTE CASTING
     * 
     * Konversi tipe data otomatis
     * 
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',  // Cast ke Carbon datetime
            'password' => 'hashed',              // Auto-hash password saat set
            'menu_permissions' => 'array',       // Cast JSON ke PHP array otomatis
        ];
    }

    // ========================================================================
    // HELPER METHODS - Cek Role & Izin User
    // ========================================================================

    /**
     * Cek apakah user memiliki izin untuk menu navigasi tertentu
     *
     * @param string $menu
     * @return bool
     */
    public function hasPermissionToMenu(string $menu): bool
    {
        // Jika user secara eksplisit memiliki array izin, cek di dalamnya
        if (!is_null($this->menu_permissions)) {
            $permissions = is_array($this->menu_permissions)
                ? $this->menu_permissions
                : json_decode($this->menu_permissions, true);
            return in_array($menu, $permissions ?: []);
        }

        // Default fallback (jika belum diatur di database/null):
        // 1. Admin memiliki akses ke semua menu navigasi
        if ($this->isAdmin()) {
            return true;
        }

        // 2. Staff memiliki akses default ke menu operasional tertentu
        if ($this->isStaff()) {
            $defaultStaffMenus = [
                'dashboard',
                'items',
                'transactions',
                'item_requests',
                'store_transactions',
                'store_logs'
            ];
            return in_array($menu, $defaultStaffMenus);
        }

        return false;
    }

    /**
     * CEK APAKAH USER ADALAH ADMIN
     * 
     * Digunakan untuk authorization dan conditional logic
     * Contoh: if (auth()->user()->isAdmin()) { ... }
     * 
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * CEK APAKAH USER ADALAH STAFF
     * 
     * Digunakan untuk authorization dan conditional logic
     * Contoh: if (auth()->user()->isStaff()) { ... }
     * 
     * @return bool
     */
    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }

    /**
     * CEK APAKAH USER ADALAH PELANGGAN TOKO
     * 
     * @return bool
     */
    public function isCustomer(): bool
    {
        return $this->store_role === 'customer';
    }

    /**
     * CEK APAKAH USER ADALAH USER INTERNAL / MANAGEMENT
     * 
     * @return bool
     */
    public function isInternal(): bool
    {
        return $this->store_role !== 'customer' && $this->role !== 'customer' && $this->role !== 'pelanggan';
    }

    // ========================================================================
    // ELOQUENT RELATIONSHIPS
    // ========================================================================

    /**
     * RELASI: User membuat banyak transaksi
     * 
     * Satu user bisa membuat banyak transaksi (masuk/keluar)
     * Digunakan untuk tracking siapa yang membuat transaksi
     * 
     * @return HasMany
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * RELASI: User (Staff) membuat banyak permintaan barang
     * 
     * Satu staff bisa membuat banyak permintaan barang
     * Relasi ini menggunakan kolom 'user_id' di tabel item_requests
     * 
     * @return HasMany
     */
    public function itemRequests(): HasMany
    {
        return $this->hasMany(ItemRequest::class);
    }

    /**
     * RELASI: User (Admin) memproses banyak permintaan barang
     * 
     * Satu admin bisa memproses (approve/reject) banyak permintaan
     * Relasi ini menggunakan kolom 'processed_by' di tabel item_requests
     * 
     * Contoh penggunaan:
     * $admin->processedRequests // Semua permintaan yang diproses admin ini
     * 
     * @return HasMany
     */
    public function processedRequests(): HasMany
    {
        return $this->hasMany(ItemRequest::class, 'processed_by');
    }

    // ========================================================================
    // PHARMACARE E-COMMERCE RELATIONSHIPS
    // ========================================================================

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    public function storeOrders(): HasMany
    {
        return $this->hasMany(StoreOrder::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function telemedicineChatsAsPatient(): HasMany
    {
        return $this->hasMany(TelemedicineChat::class, 'user_id');
    }

    public function telemedicineChatsAsDoctor(): HasMany
    {
        return $this->hasMany(TelemedicineChat::class, 'doctor_id');
    }

    public function walletTransactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class);
    }
}