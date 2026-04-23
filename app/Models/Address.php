<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'label',
        'full_address',
        'province_id',
        'city_id',
        'latitude',
        'longitude',
        'is_primary',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
