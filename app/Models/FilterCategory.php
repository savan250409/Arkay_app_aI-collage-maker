<?php

namespace App\Models;

use App\Support\ApiCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FilterCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'image', 'is_active'];

    protected static function booted(): void
    {
        static::saved(fn () => ApiCache::flushFilters());
        static::deleted(fn () => ApiCache::flushFilters());
    }

    public function filters()
    {
        return $this->hasMany(Filter::class);
    }
}
