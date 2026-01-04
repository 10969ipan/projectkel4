<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * MODEL UNIT (SATUAN)
 * 
 * Model ini merepresentasikan satuan pengukuran barang
 * Satuan digunakan untuk mengukur kuantitas barang (contoh: pcs, kg, meter, liter)
 * 
 * Tabel Database: units
 * 
 * Kolom:
 * - id: Primary key
 * - name: Nama satuan lengkap (contoh: "Pieces", "Kilogram")
 * - symbol: Simbol satuan (contoh: "pcs", "kg")
 * - created_at: Waktu pembuatan
 * - updated_at: Waktu update terakhir
 * 
 * Relasi:
 * - items: Satu satuan digunakan oleh banyak barang (One-to-Many)
 */
class Unit extends Model
{
    /**
     * MASS ASSIGNMENT
     * 
     * Kolom yang boleh diisi secara mass assignment
     */
    protected $fillable = [
        'name',     // Nama satuan lengkap (contoh: "Pieces", "Kilogram", "Meter")
        'symbol'    // Simbol satuan (contoh: "pcs", "kg", "m")
    ];

    /**
     * RELASI: Satuan digunakan oleh banyak barang
     * 
     * Satu satuan bisa digunakan oleh banyak barang
     * Contoh: Satuan "pcs" digunakan oleh Kaos, Celana, Topi, dll
     * 
     * @return HasMany
     */
    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }
}