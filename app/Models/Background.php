<?php

namespace App\Models;

use App\Support\ApiCache;
use Illuminate\Database\Eloquent\Model;

class Background extends Model
{
    protected $fillable = ['background_category_id', 'images'];

    protected $casts = [
        'images' => 'array',
    ];

    protected static function booted(): void
    {
        static::saved(fn () => ApiCache::flushBackgrounds());
        static::deleted(fn () => ApiCache::flushBackgrounds());
    }

    public function category()
    {
        return $this->belongsTo(BackgroundCategory::class, 'background_category_id');
    }
}
