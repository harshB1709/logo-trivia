<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Storage;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'wordset_id',
        'start_date',
        'end_date',
        'home_content',
        'background_img_url',
        'is_active'
    ];

    protected $casts = [
        'start_date' => 'date:d-m-Y',
        'end_date' => 'date:d-m-Y'
    ];

    protected function backgroundImgUrl(): Attribute
    {
        $disk = config('filesystems.default');
        return Attribute::make(
            get: fn (?string $value) => $value ? Storage::disk(($disk === 'local') ? 'public' : $disk)->url($value) : null,
        );
    }

    public function wordset() {
        return $this->belongsTo(Wordset::class);
    }

    public function words() {
        return $this->belongsToMany(Word::class, 'wordset_words', 'wordset_id', 'word_id', 'wordset_id', 'id');
    }

    public function appSettings() {
        return $this->hasMany(AppSetting::class);
    }

    public function players() {
        return $this->hasMany(Player::class);
    }
}
