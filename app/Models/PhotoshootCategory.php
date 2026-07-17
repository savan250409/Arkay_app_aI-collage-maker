<?php

namespace App\Models;

use App\Support\ApiCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhotoshootCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'image', 'is_active', 'sort_order'];

    protected static function booted(): void
    {
        static::saved(fn () => ApiCache::flushPhotoshoots());
        static::deleted(fn () => ApiCache::flushPhotoshoots());
    }

    public function photoshoots()
    {
        return $this->hasMany(Photoshoot::class);
    }
}
