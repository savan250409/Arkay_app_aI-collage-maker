<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FrameCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'image', 'is_active', 'row_order'];

    public function frames()
    {
        return $this->hasMany(Frame::class);
    }
}
