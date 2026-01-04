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
     * MASS ASSIGNMENT
     * 
     * Kolom yang boleh diisi secara mass assignment
     */
    protected $fillable = [
        'name',             // Nama lengkap user
        'email',            // Email untuk login
        'password',         // Password (akan di-hash otomatis)
        'role',             // Peran: 'admin' atau 'staff'
        'profile_photo',    // Path foto profil (contoh: profile-photos/abc123.jpg)
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
        ];
    }

    // ========================================================================
    // HELPER METHODS - Cek Role User
    // ========================================================================

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
}