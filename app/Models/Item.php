<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * MODEL ITEM (OBAT & ALKES) - SIMA-APOTEK
 * 
 * Model ini merepresentasikan obat atau alat kesehatan dalam sistem farmasi.
 * Setiap obat memiliki nama generik dan produsen, serta dapat memiliki banyak batch (ItemSize).
 * 
 * Tabel Database: items
 * 
 * Kolom:
 * - id: Primary key
 * - code: Kode obat/SKU (unik, contoh: "OBT-PARA-001")
 * - name: Nama obat (contoh: "Paracetamol 500mg")
 * - generic_name: Nama generik obat (contoh: "Paracetamol")
 * - manufacturer: Nama produsen obat (contoh: "Kimia Farma")
 * - size: Ringkasan batch (contoh: "Batch #001, Batch #002") - untuk display
 * - category_id: Foreign key ke tabel categories
 * - unit_id: Foreign key ke tabel units
 * - stock: TOTAL stok dari semua batch
 * - price: Harga obat
 * - description: Deskripsi obat (opsional)
 * - created_at: Waktu pembuatan
 * - updated_at: Waktu update terakhir
 * 
 * Konsep Penting:
 * - Kolom 'stock' adalah TOTAL dari semua batch (item_sizes)
 * - Kolom 'size' dalam tabel items digunakan untuk menyimpan ringkasan nomor batch untuk tampilan cepat
 * - Relasi 'sizes' sebenarnya mengarah ke manajemen Batch sekarang
 * 
 * Relasi:
 * - category: Obat belongs to satu kategori
 * - unit: Obat belongs to satu satuan
 * - sizes: Obat memiliki banyak batch (One-to-Many ke ItemSize/Batch)
 * - transactions: Obat memiliki banyak riwayat transaksi
 * - itemRequests: Obat bisa diminta berkali-kali oleh staff
 */
class Item extends Model
{
    /**
     * MASS ASSIGNMENT
     * 
     * Kolom yang boleh diisi secara mass assignment
     */
    protected $fillable = [
        'code',             // Kode obat (unik)
        'name',             // Nama obat
        'generic_name',      // Nama Generik
        'manufacturer',      // Produsen
        'size',             // Ringkasan Batch (format string)
        'category_id',      // ID kategori
        'unit_id',          // ID satuan
        'stock',            // TOTAL stok dari semua batch
        'price',            // Harga
        'description',      // Deskripsi
        'requires_prescription',
        'image_path',
    ];

    // ========================================================================
    // ELOQUENT RELATIONSHIPS - BelongsTo (Many-to-One)
    // ========================================================================

    /**
     * RELASI: Obat belongs to satu kategori
     * 
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * RELASI: Obat belongs to satu satuan
     * 
     * @return BelongsTo
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    // ========================================================================
    // ELOQUENT RELATIONSHIPS - HasMany (One-to-Many)
    // ========================================================================

    /**
     * RELASI: Obat memiliki banyak batch
     * 
     * @return HasMany
     */
    public function sizes(): HasMany
    {
        return $this->hasMany(ItemSize::class);
    }

    /**
     * RELASI: Obat memiliki banyak riwayat transaksi
     * 
     * @return HasMany
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * RELASI: Obat memiliki banyak permintaan
     * 
     * @return HasMany
     */
    public function itemRequests(): HasMany
    {
        return $this->hasMany(ItemRequest::class);
    }

    // ========================================================================
    // PHARMACARE E-COMMERCE RELATIONSHIPS
    // ========================================================================

    public function storeOrderItems(): HasMany
    {
        return $this->hasMany(StoreOrderItem::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }
}