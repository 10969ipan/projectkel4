<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_number',
        'address_id',
        'shipping_method',
        'payment_method',
        'payment_status',
        'order_status',
        'sub_total',
        'shipping_cost',
        'unique_code',
        'grand_total',
        'prescription_path',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function items()
    {
        return $this->hasMany(StoreOrderItem::class);
    }
}
