<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_order_id',
        'item_id',
        'quantity',
        'price',
        'sub_total',
    ];

    public function order()
    {
        return $this->belongsTo(StoreOrder::class, 'store_order_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
