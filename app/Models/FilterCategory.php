<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FilterCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'image', 'is_active'];

    public function filters()
    {
        return $this->hasMany(Filter::class);
    }
}
