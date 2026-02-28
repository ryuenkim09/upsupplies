<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class ReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request, $productId)
    {
        $data = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        // ensure user purchased the product
        $purchased = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.user_id', Auth::id())
            ->where('order_items.product_id', $productId)
            ->where('orders.status', 'completed')
            ->exists();

        if (!$purchased) {
            return Redirect::back()->with('error', 'You can only review products you have purchased.');
        }

        DB::table('reviews')->insert([
            'user_id' => Auth::id(),
            'product_id' => $productId,
            'rating' => $data['rating'],
            'comment' => $data['comment'] ?? null,
            'approved' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return Redirect::back()->with('success', 'Review submitted and awaiting approval.');
    }
}
