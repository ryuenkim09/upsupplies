<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Food & Treats',
            'Toys & Playthings',
            'Accessories',
            'Grooming Supplies',
            'Bedding & Housing',
            'Health & Wellness',
        ];

        foreach ($categories as $name) {
            DB::table('categories')->insert([
                'name' => $name,
                'description' => 'High-quality ' . $name . ' for your pets',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
