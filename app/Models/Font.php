<?php

namespace App\Models;

use App\Support\ApiCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Font extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'file', 'type', 'font_preview'];

    protected static function booted(): void
    {
        static::saved(fn () => ApiCache::flushFonts());
        static::deleted(fn () => ApiCache::flushFonts());
    }
}
