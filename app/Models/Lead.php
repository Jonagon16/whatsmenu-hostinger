<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $fillable = [
        'user_id',
        'phone',
        'last_interaction',
        'current_path',
        'interactions_count',
    ];

    protected $casts = [
        'last_interaction' => 'datetime',
    ];
}
