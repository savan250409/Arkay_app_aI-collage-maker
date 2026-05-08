<?php

namespace App\Models;

use App\Support\ApiCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sticker extends Model
{
    protected $fillable = ['sticker_category_id', 'images'];

    protected $casts = [
        'images' => 'array',
    ];

    protected static function booted(): void
    {
        static::saved(fn () => ApiCache::flushStickers());
        static::deleted(fn () => ApiCache::flushStickers());
    }

    public function category()
    {
        return $this->belongsTo(StickerCategory::class, 'sticker_category_id');
    }
}
