<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BotConfig extends Model
{
    use HasFactory;

    protected $table = 'bot_config';

    protected $fillable = [
        'user_id',
        'whatsapp_verify_token',
        'whatsapp_app_secret',
        'whatsapp_access_token',
        'whatsapp_phone_number_id',
        'whatsapp_business_account_id',
        'is_active',
        'bot_name',
        'last_interaction_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_interaction_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
