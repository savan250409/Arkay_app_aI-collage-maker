<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Background extends Model
{
    protected $fillable = ['background_category_id', 'images'];

    protected $casts = [
        'images' => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(BackgroundCategory::class, 'background_category_id');
    }
}
