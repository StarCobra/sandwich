<?php

namespace lbs\order\models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $table = 'item';
    protected $primaryKey = "id";
    public $incrementing = false;

    function order()
    {
        return $this->hasOne(Order::class, 'id', 'command_id');
    }
}
