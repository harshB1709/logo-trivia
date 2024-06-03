<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'key',
        'value',
        'message'
    ];

    protected $casts = [
        'value' => 'boolean'
    ];

    public $timestamps = false;

    public function event() {
        return $this->belongsTo(Event::class);
    }
}
