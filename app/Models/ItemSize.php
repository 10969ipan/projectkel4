<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * MODEL ITEM SIZE (VARIAN UKURAN BARANG)
 * 
 * Model ini merepresentasikan varian ukuran dari sebuah barang
 * Setiap barang bisa memiliki banyak varian ukuran dengan stok masing-masing
 * 
 * Tabel Database: item_sizes
 * 
 * Kolom:
 * - id: Primary key
 * - item_id: Foreign key ke tabel items (barang induk)
 * - size: Nama ukuran (contoh: "S", "M", "L", "XL", "27", "28", "39", "40")
 * - stock: Stok untuk ukuran ini
 * - created_at: Waktu pembuatan
 * - updated_at: Waktu update terakhir
 * 
 * Konsep Penting:
 * - Setiap varian ukuran memiliki stok sendiri
 * - Total stok di tabel items = SUM(stock) dari semua varian
 * - Saat stok varian berubah, total stok di items juga harus diupdate
 * 
 * Contoh Data:
 * Item: Kaos Polos (Total Stock: 50)
 * ├─ ItemSize: S (stock: 10)
 * ├─ ItemSize: M (stock: 20)
 * ├─ ItemSize: L (stock: 15)
 * └─ ItemSize: XL (stock: 5)
 * 
 * Relasi:
 * - item: Varian belongs to satu barang induk
 * - transactions: Varian bisa tercatat di banyak transaksi
 * - itemRequests: Varian bisa diminta berkali-kali
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
        'item_id',  // ID barang induk
        'size',     // Nama ukuran (S, M, L, XL, 27, 28, 39, 40, dll)
        'stock'     // Stok untuk ukuran ini
    ];

    // ========================================================================
    // ELOQUENT RELATIONSHIPS
    // ========================================================================

    /**
     * RELASI: Varian ukuran belongs to satu barang induk
     * 
     * Setiap varian ukuran harus memiliki satu barang induk
     * Contoh: Size M → Kaos Polos
     * 
     * Contoh penggunaan:
     * $itemSize->item // Barang induk
     * $itemSize->item->name // Nama barang induk
     * 
     * @return BelongsTo
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * RELASI: Varian ukuran memiliki banyak riwayat transaksi
     * 
     * Satu varian ukuran bisa tercatat di banyak transaksi
     * Ini penting untuk tracking stok per ukuran
     * 
     * Contoh: Size M dari Kaos Polos bisa memiliki transaksi:
     * - Transaksi 1: Masuk 10 pcs
     * - Transaksi 2: Keluar 5 pcs
     * - Transaksi 3: Masuk 20 pcs
     * 
     * Contoh penggunaan:
     * $itemSize->transactions // Semua transaksi untuk ukuran ini
     * $itemSize->transactions()->where('type', 'out')->sum('quantity') // Total keluar
     * 
     * @return HasMany
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * RELASI: Varian ukuran bisa diminta berkali-kali
     * 
     * Satu varian ukuran bisa diminta berkali-kali oleh staff
     * Ini penting untuk tracking permintaan per ukuran
     * 
     * Contoh: Size M dari Kaos Polos bisa diminta:
     * - Staff A minta 5 pcs
     * - Staff B minta 3 pcs
     * - Staff C minta 10 pcs
     * 
     * Contoh penggunaan:
     * $itemSize->itemRequests // Semua permintaan untuk ukuran ini
     * $itemSize->itemRequests()->where('status', 'approved')->sum('quantity') // Total approved
     * 
     * @return HasMany
     */
    public function itemRequests(): HasMany
    {
        return $this->hasMany(ItemRequest::class);
    }
}