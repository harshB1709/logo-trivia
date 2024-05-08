<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'wordset_id',
        'is_active'
    ];

    public function wordset() {
        return $this->belongsTo(Wordset::class);
    }

    public function words() {
        return $this->belongsToMany(Word::class, 'wordset_words', 'wordset_id', 'word_id', 'wordset_id', 'id');
    }
}
