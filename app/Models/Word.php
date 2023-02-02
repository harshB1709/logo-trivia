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

    protected $appends = ['svg'];

    protected function svg(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => ($attributes['url'] ?? null) ? Storage::disk('public')->get($attributes['url']) : null,
        );
    }
}
