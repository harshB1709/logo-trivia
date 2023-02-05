<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Player extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'display_name',
        'email',
        'phone'
    ];

    protected function displayName(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => !empty($value) ? $value : $attributes['name'],
        );
    }

    public function game() {
        return $this->hasOne(Game::class);
    }
}
