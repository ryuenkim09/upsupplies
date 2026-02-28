<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = ['user_id', 'product_id', 'quantity'];

    /**
     * CartItem belongs to a user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * CartItem refers to a product being added.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
