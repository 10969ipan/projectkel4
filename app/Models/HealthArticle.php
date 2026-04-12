<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HealthArticle extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'keyword',
        'content',
        'image_path',
        'is_active',
    ];
}
