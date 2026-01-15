<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'is_active',
        'tree',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'tree' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
