<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Frame extends Model
{
    use HasFactory;

    protected $fillable = [
        'frame_category_id',
        'images',
        'image_input_counts',
        'image_types',
        'frame_thumbnail',
        'row_order'
    ];

    protected $casts = [
        'images' => 'array',
        'image_input_counts' => 'array',
        'image_types' => 'array',
        'frame_thumbnail' => 'array'
    ];

    public function category()
    {
        return $this->belongsTo(FrameCategory::class, 'frame_category_id');
    }
}
