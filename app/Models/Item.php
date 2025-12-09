<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    protected $fillable = [
        'code',
        'name',
        'size', // Ini akan kita gunakan sebagai ringkasan (misal: "S, M, L")
        'category_id',
        'unit_id',
        'stock', // Ini adalah TOTAL stok dari semua varian
        'price',
        'description'
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    // Relasi baru ke ItemSize
    public function sizes(): HasMany
    {
        return $this->hasMany(ItemSize::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function itemRequests(): HasMany
    {
        return $this->hasMany(ItemRequest::class);
    }
}