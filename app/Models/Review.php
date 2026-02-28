<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = ['user_id', 'product_id', 'order_item_id', 'rating', 'review', 'comment', 'approved'];

    protected $casts = [
        'approved' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    // support both 'review' and legacy 'comment' column names
    public function getReviewAttribute()
    {
        if (array_key_exists('review', $this->attributes)) {
            return $this->attributes['review'];
        }
        return $this->attributes['comment'] ?? null;
    }
}
