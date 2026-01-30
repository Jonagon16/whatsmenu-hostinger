<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $fillable = [
        'bot_config_id',
        'wa_id',
        'display_name',
        'current_node_id',
        'metadata',
        'last_message_at',
        'pinned',
        'closed_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'last_message_at' => 'datetime',
        'closed_at' => 'datetime',
        'pinned' => 'boolean',
    ];

    public function botConfig()
    {
        return $this->belongsTo(BotConfig::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}
