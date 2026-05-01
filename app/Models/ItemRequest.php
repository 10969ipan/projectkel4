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
 * - user_id: Foreign key ke tabel users (staff yang meminta)
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
        'user_id',          // ID staff yang membuat permintaan
        'quantity',         // Jumlah yang diminta
        'type',             // Jenis: 'in' (tambah stok), 'out' (kurangi stok)
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