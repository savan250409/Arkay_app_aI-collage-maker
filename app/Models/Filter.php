<?php

namespace App\Models;

use App\Support\ApiCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Filter extends Model
{
    use HasFactory;

    protected $fillable = [
        'filter_category_id',
        'name',
        'saturation',
        'brightness',
        'contrast',
        'red',
        'green',
        'blue',
        'type'
    ];

    protected static function booted(): void
    {
        static::saved(fn () => ApiCache::flushFilters());
        static::deleted(fn () => ApiCache::flushFilters());
    }

    public function category()
    {
        return $this->belongsTo(FilterCategory::class, 'filter_category_id');
    }
}
