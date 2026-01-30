<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuNode extends Model
{
    protected $fillable = [
        'menu_id',
        'slug',
        'title',
        'body_text',
        'footer_text',
        'type'
    ];

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function options()
    {
        return $this->hasMany(MenuOption::class)->orderBy('sort_order');
    }
}
