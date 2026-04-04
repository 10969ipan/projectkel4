<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * MODEL ITEM BATCH (BATCH OBAT & ALKES) - SIMA-APOTEK
 * 
 * Model ini merepresentasikan batch dari sebuah obat dalam sistem farmasi.
 * Setiap obat bisa memiliki banyak batch dengan nomor batch dan tanggal kadaluwarsa sendiri.
 * 
 * Tabel Database: item_sizes (di-reuse untuk Batch)
 * 
 * Kolom:
 * - id: Primary key
 * - item_id: Foreign key ke tabel items (obat induk)
 * - batch_number: Nomor batch (contoh: "BN20240321", "LOT-X123")
 * - expiry_date: Tanggal kadaluwarsa obat
 * - stock: Stok untuk batch ini
 * - created_at: Waktu pembuatan
 * - updated_at: Waktu update terakhir
 * 
 * Konsep Penting:
 * - Setiap batch memiliki stok sendiri
 * - Total stok di tabel items = SUM(stock) dari semua batch aktif
 * 
 * Relasi:
 * - item: Batch belongs to satu obat induk
 * - transactions: Batch bisa tercatat di banyak transaksi
 * - itemRequests: Batch bisa diminta berkali-kali
 */
class ItemSize extends Model
{
    use HasFactory;

    /**
     * MASS ASSIGNMENT
     * 
     * Kolom yang boleh diisi secara mass assignment
     */
    protected $fillable = [
        'item_id',      // ID obat induk
        'batch_number',  // Nomor Batch
        'expiry_date',   // Tanggal Kadaluwarsa
        'stock'         // Stok untuk batch ini
    ];

    // ========================================================================
    // ELOQUENT RELATIONSHIPS
    // ========================================================================

    /**
     * RELASI: Batch belongs to satu obat induk
     * 
     * @return BelongsTo
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * RELASI: Batch memiliki banyak riwayat transaksi
     * 
     * @return HasMany
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * RELASI: Batch bisa diminta berkali-kali
     * 
     * @return HasMany
     */
    public function itemRequests(): HasMany
    {
        return $this->hasMany(ItemRequest::class);
    }
}