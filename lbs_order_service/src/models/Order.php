<?php

namespace lbs\order\models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'commande';
    protected $primaryKey = "id";
    public $incrementing = false;
    public $keyType = 'string';

    function items()
    {
        return $this->hasMany(Item::class, 'command_id', 'id');
    }
}