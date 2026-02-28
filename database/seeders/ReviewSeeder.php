<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReviewSeeder extends Seeder
{
    public function run()
    {
        // simple seed: pick first user and first product
        $user = DB::table('users')->where('role', 'user')->first();
        $product = DB::table('products')->first();
        if ($user && $product) {
            // ensure there is a completed order for this user/product so they can review
            $orderId = DB::table('orders')->insertGetId([
                'user_id' => $user->id,
                'total' => $product->price,
                'status' => 'completed',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            DB::table('order_items')->insert([
                'order_id' => $orderId,
                'product_id' => $product->id,
                'quantity' => 1,
                'price' => $product->price,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('reviews')->insert([
                'user_id' => $user->id,
                'product_id' => $product->id,
                'rating' => 4,
                'comment' => 'Great product, highly recommend!',
                'approved' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
