<?php

namespace App\Models;

use App\Support\ApiCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BackgroundCategory extends Model
{
    protected $fillable = ['name', 'image', 'is_premium', 'row_order', 'is_active'];

    protected static function booted(): void
    {
        static::saved(fn () => ApiCache::flushBackgrounds());
        static::deleted(fn () => ApiCache::flushBackgrounds());
    }

    public function backgrounds()
    {
        return $this->hasMany(Background::class);
    }
}
