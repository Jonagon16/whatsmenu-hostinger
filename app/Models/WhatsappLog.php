<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappLog extends Model
{
    protected $fillable = [
        'user_id',
        'from_number',
        'message',
        'current_path',
        'response',
        'was_ai',
    ];

    protected $casts = [
        'was_ai' => 'boolean',
    ];
}
