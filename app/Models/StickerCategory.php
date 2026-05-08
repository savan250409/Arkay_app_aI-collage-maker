<?php

namespace App\Models;

use App\Support\ApiCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StickerCategory extends Model
{
    protected $fillable = ['name', 'image', 'is_premium', 'row_order', 'is_active'];

    protected static function booted(): void
    {
        static::saved(fn () => ApiCache::flushStickers());
        static::deleted(fn () => ApiCache::flushStickers());
    }

    public function stickers()
    {
        return $this->hasMany(Sticker::class);
    }
}
