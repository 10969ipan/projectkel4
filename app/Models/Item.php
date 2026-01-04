<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * MODEL ITEM (BARANG)
 * 
 * Model ini merepresentasikan barang dalam sistem inventory
 * Barang bisa memiliki varian ukuran (contoh: Kaos dengan size S, M, L, XL)
 * 
 * Tabel Database: items
 * 
 * Kolom:
 * - id: Primary key
 * - code: Kode barang/SKU (unik, contoh: "CLN-JEANS-001")
 * - name: Nama barang (contoh: "Celana Jeans Slimfit")
 * - size: Ringkasan ukuran (contoh: "S, M, L, XL") - untuk display
 * - category_id: Foreign key ke tabel categories
 * - unit_id: Foreign key ke tabel units
 * - stock: TOTAL stok dari semua varian ukuran
 * - price: Harga barang
 * - description: Deskripsi barang (opsional)
 * - created_at: Waktu pembuatan
 * - updated_at: Waktu update terakhir
 * 
 * Konsep Penting:
 * - Kolom 'stock' adalah TOTAL dari semua varian ukuran
 * - Kolom 'size' hanya untuk display, data detail ada di tabel item_sizes
 * - Setiap perubahan stok varian harus update total stock di tabel ini
 * 
 * Relasi:
 * - category: Barang belongs to satu kategori
 * - unit: Barang belongs to satu satuan
 * - sizes: Barang memiliki banyak varian ukuran (One-to-Many ke ItemSize)
 * - transactions: Barang memiliki banyak riwayat transaksi
 * - itemRequests: Barang bisa diminta berkali-kali oleh staff
 */
class Item extends Model
{
    /**
     * MASS ASSIGNMENT
     * 
     * Kolom yang boleh diisi secara mass assignment
     */
    protected $fillable = [
        'code',         // Kode barang/SKU (unik)
        'name',         // Nama barang
        'size',         // Ringkasan ukuran (contoh: "S, M, L") - untuk display saja
        'category_id',  // ID kategori
        'unit_id',      // ID satuan
        'stock',        // TOTAL stok dari semua varian ukuran
        'price',        // Harga barang
        'description'   // Deskripsi barang
    ];

    // ========================================================================
    // ELOQUENT RELATIONSHIPS - BelongsTo (Many-to-One)
    // ========================================================================

    /**
     * RELASI: Barang belongs to satu kategori
     * 
     * Setiap barang harus memiliki satu kategori
     * Contoh: Kaos Polos → Kategori "Pakaian"
     * 
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * RELASI: Barang belongs to satu satuan
     * 
     * Setiap barang harus memiliki satu satuan pengukuran
     * Contoh: Kaos Polos → Satuan "pcs"
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
     * RELASI: Barang memiliki banyak varian ukuran
     * 
     * Satu barang bisa memiliki banyak varian ukuran
     * Contoh: Kaos Polos memiliki Size S, M, L, XL
     * 
     * PENTING: Ini adalah relasi utama untuk sistem varian ukuran
     * Setiap varian memiliki stok sendiri yang tercatat di tabel item_sizes
     * 
     * Contoh penggunaan:
     * $item->sizes // Collection of ItemSize
     * $item->sizes->where('size', 'M')->first()->stock // Stok size M
     * 
     * @return HasMany
     */
    public function sizes(): HasMany
    {
        return $this->hasMany(ItemSize::class);
    }

    /**
     * RELASI: Barang memiliki banyak riwayat transaksi
     * 
     * Satu barang bisa memiliki banyak transaksi (masuk/keluar)
     * Digunakan untuk tracking riwayat pergerakan stok
     * 
     * Contoh penggunaan:
     * $item->transactions // Semua transaksi barang ini
     * $item->transactions()->where('type', 'in')->get() // Transaksi masuk saja
     * 
     * @return HasMany
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * RELASI: Barang memiliki banyak permintaan dari staff
     * 
     * Satu barang bisa diminta berkali-kali oleh staff
     * Digunakan untuk tracking permintaan barang
     * 
     * Contoh penggunaan:
     * $item->itemRequests // Semua permintaan untuk barang ini
     * $item->itemRequests()->where('status', 'pending')->get() // Permintaan pending
     * 
     * @return HasMany
     */
    public function itemRequests(): HasMany
    {
        return $this->hasMany(ItemRequest::class);
    }
}