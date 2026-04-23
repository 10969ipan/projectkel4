<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * MODEL TRANSACTION (TRANSAKSI)
 * 
 * Model ini merepresentasikan transaksi pergerakan stok barang
 * Transaksi mencatat setiap barang masuk (in) atau keluar (out) dari gudang
 * 
 * Tabel Database: transactions
 * 
 * Kolom:
 * - id: Primary key
 * - item_id: Foreign key ke tabel items (barang yang ditransaksikan)
 * - item_id: Foreign key ke tabel items (barang yang ditransaksikan)
 * - user_id: Foreign key ke tabel users (user yang membuat transaksi)
 * - type: Tipe transaksi ('in' = masuk, 'out' = keluar)
 * - quantity: Jumlah barang yang ditransaksikan
 * - date: Tanggal transaksi
 * - note: Catatan tambahan (opsional)
 * - created_at: Waktu pembuatan record
 * - updated_at: Waktu update terakhir
 * 
 * Tipe Transaksi:
 * - 'in' (Masuk): Barang masuk ke gudang (menambah stok)
 *   Contoh: Pembelian barang baru, retur dari customer
 * 
 * - 'out' (Keluar): Barang keluar dari gudang (mengurangi stok)
 *   Contoh: Penjualan, permintaan staff yang diapprove, barang rusak
 * 
 * Konsep Penting:
 * - Setiap transaksi harus update stok di 2 tempat:
 *   1. items.stock (stok total obat)
 *   2. items.stock (total stok)
 * - Transaksi bersifat immutable (tidak bisa diedit, hanya bisa dibuat baru)
 * - Untuk koreksi, buat transaksi baru dengan tipe berlawanan
 * 
 * Relasi:
 * - item: Transaksi belongs to satu barang
 * - user: Transaksi belongs to satu user (yang membuat)
 */
class Transaction extends Model
{
    /**
     * MASS ASSIGNMENT
     * 
     * Kolom yang boleh diisi secara mass assignment
     */
    protected $fillable = [
        'item_id',      // ID barang yang ditransaksikan
        'user_id',      // ID user yang membuat transaksi
        'type',         // Tipe: 'in' (masuk) atau 'out' (keluar)
        'quantity',     // Jumlah barang
        'date',         // Tanggal transaksi
        'note'          // Catatan tambahan (contoh: "Pembelian dari supplier X")
    ];

    /**
     * ATTRIBUTE CASTING
     * 
     * Konversi tipe data otomatis
     * 
     * @var array<string, string>
     */
    protected $casts = [
        'date' => 'date',  // Cast kolom date ke Carbon date object
    ];

    // ========================================================================
    // ELOQUENT RELATIONSHIPS
    // ========================================================================

    /**
     * RELASI: Transaksi belongs to satu barang
     * 
     * Setiap transaksi harus terkait dengan satu barang
     * Digunakan untuk mengetahui barang apa yang ditransaksikan
     * 
     * Contoh penggunaan:
     * $transaction->item // Barang yang ditransaksikan
     * $transaction->item->name // Nama barang
     * 
     * @return BelongsTo
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * RELASI: Transaksi belongs to satu user
     * 
     * Setiap transaksi harus terkait dengan satu user (yang membuat)
     * Digunakan untuk tracking siapa yang membuat transaksi
     * 
     * Contoh penggunaan:
     * $transaction->user // User yang membuat transaksi
     * $transaction->user->name // Nama user
     * 
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


}