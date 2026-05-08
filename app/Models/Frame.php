<?php

namespace App\Models;

use App\Support\ApiCache;
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

    protected static function booted(): void
    {
        static::saved(fn () => ApiCache::flushFrames());
        static::deleted(fn () => ApiCache::flushFrames());
    }

    public function category()
    {
        return $this->belongsTo(FrameCategory::class, 'frame_category_id');
    }
}
