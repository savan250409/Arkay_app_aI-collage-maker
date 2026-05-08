<?php

namespace App\Models;

use App\Support\ApiCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doodle extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'doodle_type',
        'image',
        'type',
        'row_order'
    ];

    protected static function booted(): void
    {
        static::saved(fn () => ApiCache::flushDoodles());
        static::deleted(fn () => ApiCache::flushDoodles());
    }
}
