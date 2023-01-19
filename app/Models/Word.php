<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Storage;

class Word extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'url',
        'points',
        'hints',
        'is_active'
    ];

    protected function url(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => Storage::url($value),
        );
    }
}
