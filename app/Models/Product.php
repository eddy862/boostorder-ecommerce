<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\OrderItem;

class Product extends Model
{
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class); // returns all order items that include this product
    }
}
