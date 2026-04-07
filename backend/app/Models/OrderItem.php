<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = ['order_id', 'medication_id', 'quantity'];

    // Each item belongs to one order
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Each item refers to one medication
    public function medication()
    {
        return $this->belongsTo(Medication::class);
    }
}
