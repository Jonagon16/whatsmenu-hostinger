<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuOption extends Model
{
    protected $fillable = [
        'menu_node_id',
        'label',
        'description',
        'next_node_slug',
        'action_type',
        'action_value',
        'sort_order'
    ];

    public function node()
    {
        return $this->belongsTo(MenuNode::class, 'menu_node_id');
    }
}
