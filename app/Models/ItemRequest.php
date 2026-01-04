<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * MODEL ITEM REQUEST (PERMINTAAN BARANG)
 * 
 * Model ini merepresentasikan permintaan barang dari staff ke admin
 * Staff membuat permintaan, admin menyetujui (approve) atau menolak (reject)
 * 
 * Tabel Database: item_requests
 * 
 * Kolom:
 * - id: Primary key
 * - item_id: Foreign key ke tabel items (barang yang diminta)
 * - item_size_id: Foreign key ke tabel item_sizes (ukuran yang diminta)
 * - user_id: Foreign key ke tabel users (staff yang meminta)
 * - size: Nama ukuran (untuk display, data detail ada di item_size_id)
 * - quantity: Jumlah yang diminta
 * - reason: Alasan permintaan (wajib diisi oleh staff)
 * - status: Status permintaan ('pending', 'approved', 'rejected')
 * - rejection_reason: Alasan penolakan (diisi jika status = rejected)
 * - processed_by: Foreign key ke tabel users (admin yang memproses)
 * - processed_at: Waktu pemrosesan (approve/reject)
 * - created_at: Waktu pembuatan permintaan
 * - updated_at: Waktu update terakhir
 * 
 * Alur Proses:
 * 1. Staff membuat permintaan (status: pending)
 * 2. Admin melihat permintaan
 * 3. Admin approve ATAU reject:
 *    - Approve: Status → approved, buat transaksi keluar, kurangi stok
 *    - Reject: Status → rejected, simpan alasan penolakan, stok tidak berubah
 * 
 * Status Permintaan:
 * - 'pending': Menunggu persetujuan admin
 * - 'approved': Disetujui admin, stok sudah dikurangi
 * - 'rejected': Ditolak admin, stok tidak berubah
 * 
 * Konsep Penting:
 * - Permintaan yang sudah diproses (approved/rejected) tidak bisa diubah
 * - Saat approved, otomatis membuat transaksi keluar
 * - Validasi stok dilakukan saat approve (bukan saat create request)
 * 
 * Relasi:
 * - item: Permintaan belongs to satu barang
 * - itemSize: Permintaan belongs to satu varian ukuran
 * - user: Permintaan belongs to satu user (staff yang meminta)
 * - processedBy: Permintaan belongs to satu user (admin yang memproses)
 */
class ItemRequest extends Model
{
    /**
     * MASS ASSIGNMENT
     * 
     * Kolom yang boleh diisi secara mass assignment
     */
    protected $fillable = [
        'item_id',          // ID barang yang diminta
        'item_size_id',     // ID varian ukuran yang diminta (PENTING untuk tracking)
        'user_id',          // ID staff yang membuat permintaan
        'size',             // Nama ukuran (untuk display, contoh: "M", "L", "39")
        'quantity',         // Jumlah yang diminta
        'reason',           // Alasan permintaan (wajib, contoh: "Untuk kebutuhan proyek X")
        'status',           // Status: 'pending', 'approved', 'rejected'
        'rejection_reason', // Alasan penolakan (diisi jika rejected)
        'processed_by',     // ID admin yang memproses (approve/reject)
        'processed_at'      // Waktu pemrosesan
    ];

    /**
     * ATTRIBUTE CASTING
     * 
     * Konversi tipe data otomatis
     * 
     * @var array<string, string>
     */
    protected $casts = [
        'processed_at' => 'datetime',  // Cast ke Carbon datetime object
    ];

    // ========================================================================
    // ELOQUENT RELATIONSHIPS
    // ========================================================================

    /**
     * RELASI: Permintaan belongs to satu barang
     * 
     * Setiap permintaan harus terkait dengan satu barang
     * Digunakan untuk mengetahui barang apa yang diminta
     * 
     * Contoh penggunaan:
     * $request->item // Barang yang diminta
     * $request->item->name // Nama barang
     * 
     * @return BelongsTo
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * RELASI: Permintaan belongs to satu varian ukuran
     * 
     * Setiap permintaan harus terkait dengan satu varian ukuran
     * Ini PENTING untuk validasi stok dan tracking per ukuran
     * 
     * Contoh: Staff minta 5 pcs Kaos Polos Size M
     * - item_id: ID Kaos Polos
     * - item_size_id: ID Size M dari Kaos Polos
     * - quantity: 5
     * 
     * Saat approve, sistem akan:
     * 1. Cek stok Size M (dari item_size_id)
     * 2. Jika cukup, kurangi stok Size M
     * 3. Kurangi total stok Kaos Polos
     * 
     * Contoh penggunaan:
     * $request->itemSize // Varian ukuran yang diminta
     * $request->itemSize->size // Nama ukuran
     * $request->itemSize->stock // Stok tersedia untuk ukuran ini
     * 
     * @return BelongsTo
     */
    public function itemSize(): BelongsTo
    {
        return $this->belongsTo(ItemSize::class);
    }

    /**
     * RELASI: Permintaan belongs to satu user (staff yang meminta)
     * 
     * Setiap permintaan harus terkait dengan satu staff
     * Digunakan untuk tracking siapa yang membuat permintaan
     * 
     * Contoh penggunaan:
     * $request->user // Staff yang membuat permintaan
     * $request->user->name // Nama staff
     * 
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * RELASI: Permintaan belongs to satu user (admin yang memproses)
     * 
     * Setiap permintaan yang sudah diproses terkait dengan satu admin
     * Digunakan untuk tracking siapa yang approve/reject
     * 
     * CATATAN: Relasi ini menggunakan kolom 'processed_by' bukan 'user_id'
     * 
     * Contoh penggunaan:
     * $request->processedBy // Admin yang memproses
     * $request->processedBy->name // Nama admin
     * 
     * Jika permintaan masih pending, processedBy akan null
     * 
     * @return BelongsTo
     */
    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}