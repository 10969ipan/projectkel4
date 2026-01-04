<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * MODEL CATEGORY (KATEGORI)
 * 
 * Model ini merepresentasikan kategori barang di sistem inventory
 * Kategori digunakan untuk mengelompokkan barang (contoh: Pakaian, Elektronik, Alat Tulis)
 * 
 * Tabel Database: categories
 * 
 * Kolom:
 * - id: Primary key
 * - name: Nama kategori (unik)
 * - description: Deskripsi kategori (opsional)
 * - created_at: Waktu pembuatan
 * - updated_at: Waktu update terakhir
 * 
 * Relasi:
 * - items: Satu kategori memiliki banyak barang (One-to-Many)
 */
class Category extends Model
{
    /**
     * MASS ASSIGNMENT
     * 
     * Kolom yang boleh diisi secara mass assignment
     * Digunakan saat create() atau update()
     */
    protected $fillable = [
        'name',         // Nama kategori (contoh: "Pakaian", "Elektronik")
        'description'   // Deskripsi kategori (opsional)
    ];

    /**
     * RELASI: Kategori memiliki banyak barang
     * 
     * Satu kategori bisa memiliki banyak barang
     * Contoh: Kategori "Pakaian" memiliki Kaos, Celana, Jaket, dll
     * 
     * @return HasMany
     */
    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }
}