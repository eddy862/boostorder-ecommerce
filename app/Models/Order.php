<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\OrderItem;
use App\Models\User;

class Order extends Model
{
    public function items()
    {
        return $this->hasMany(OrderItem::class); // returns all order items that belong to this order
    }

    public function user()
    {
        return $this->belongsTo(User::class); // returns the user who placed this order
    }

    protected $fillable = [
        'user_id',
        'total_price',
        'status'
    ];
}
