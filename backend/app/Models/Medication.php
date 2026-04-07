<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Medication extends Model
{
    protected $fillable = ['name', 'lot_number', 'description', 'expiration_date'];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
