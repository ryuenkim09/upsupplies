<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            // Food & Treats
            ['category_id' => 1, 'name' => 'Premium Dog Food', 'description' => 'Nutritious and delicious dog food for all breeds', 'price' => 29.99, 'stock' => 50],
            ['category_id' => 1, 'name' => 'Cat Treats Bundle', 'description' => 'Variety pack of tasty treats for cats', 'price' => 19.99, 'stock' => 75],
            ['category_id' => 1, 'name' => 'Fish Food Pellets', 'description' => 'Complete nutrition for aquarium fish', 'price' => 12.99, 'stock' => 40],
            ['category_id' => 1, 'name' => 'Bird Seed Mix', 'description' => 'Nutritious seed mix for pet birds', 'price' => 15.99, 'stock' => 60],
            ['category_id' => 1, 'name' => 'Rabbit Pellets', 'description' => 'Balanced diet for rabbits and small animals', 'price' => 18.99, 'stock' => 35],
            
            // Toys & Playthings
            ['category_id' => 2, 'name' => 'Tennis Ball Set', 'description' => 'Durable tennis balls for fetch and play', 'price' => 14.99, 'stock' => 80],
            ['category_id' => 2, 'name' => 'Squeaky Toy Pack', 'description' => 'Interactive squeaky toys for dogs', 'price' => 22.99, 'stock' => 45],
            ['category_id' => 2, 'name' => 'Feather Wand Toy', 'description' => 'Interactive toy for cats', 'price' => 16.99, 'stock' => 55],
            ['category_id' => 2, 'name' => 'Rope Pull Toy', 'description' => 'Great for tug-of-war games', 'price' => 11.99, 'stock' => 70],
            ['category_id' => 2, 'name' => 'Laser Pointer', 'description' => 'Interactive laser toy for cats', 'price' => 9.99, 'stock' => 90],
            
            // Accessories
            ['category_id' => 3, 'name' => 'LED Collar', 'description' => 'Safety LED collar for nighttime visibility', 'price' => 19.99, 'stock' => 38],
            ['category_id' => 3, 'name' => 'Pet Leash', 'description' => 'Durable leash for dogs', 'price' => 24.99, 'stock' => 42],
            ['category_id' => 3, 'name' => 'Water Bowl', 'description' => 'Stainless steel water bowl', 'price' => 13.99, 'stock' => 65],
            ['category_id' => 3, 'name' => 'Pet Carrier Bag', 'description' => 'Comfortable carrier for small pets', 'price' => 39.99, 'stock' => 20],
            ['category_id' => 3, 'name' => 'ID Tag', 'description' => 'Personalized pet ID tag', 'price' => 8.99, 'stock' => 100],
            
            // Grooming Supplies
            ['category_id' => 4, 'name' => 'Dog Shampoo', 'description' => 'Gentle dog shampoo for all coat types', 'price' => 17.99, 'stock' => 55],
            ['category_id' => 4, 'name' => 'Brush Comb Set', 'description' => 'Professional grooming brush and comb set', 'price' => 21.99, 'stock' => 30],
            ['category_id' => 4, 'name' => 'Nail Clippers', 'description' => 'Safe pet nail clippers', 'price' => 14.99, 'stock' => 48],
            ['category_id' => 4, 'name' => 'Dental Paste', 'description' => 'Pet-safe dental paste for brushing', 'price' => 12.99, 'stock' => 37],
            ['category_id' => 4, 'name' => 'Dry Shampoo', 'description' => 'Waterless pet shampoo', 'price' => 15.99, 'stock' => 52],
            
            // Bedding & Housing
            ['category_id' => 5, 'name' => 'Pet Bed Cushion', 'description' => 'Comfortable cushioned bed for pets', 'price' => 34.99, 'stock' => 25],
            ['category_id' => 5, 'name' => 'Cat Hammock', 'description' => 'Hanging hammock bed for cats', 'price' => 22.99, 'stock' => 32],
            ['category_id' => 5, 'name' => 'Guinea Pig Cage', 'description' => 'Spacious cage for guinea pigs', 'price' => 79.99, 'stock' => 8],
            ['category_id' => 5, 'name' => 'Hamster Wheel', 'description' => 'Exercise wheel for small rodents', 'price' => 18.99, 'stock' => 44],
            ['category_id' => 5, 'name' => 'Aquarium Stand', 'description' => 'Sturdy stand for aquariums', 'price' => 49.99, 'stock' => 15],
            
            // Health & Wellness
            ['category_id' => 6, 'name' => 'Vitamin Supplement', 'description' => 'Multi-vitamin supplement for pets', 'price' => 24.99, 'stock' => 40],
            ['category_id' => 6, 'name' => 'Flea & Tick Prevention', 'description' => 'Monthly flea and tick prevention', 'price' => 32.99, 'stock' => 22],
            ['category_id' => 6, 'name' => 'Pet Probiotic', 'description' => 'Digestive health supplement', 'price' => 19.99, 'stock' => 35],
            ['category_id' => 6, 'name' => 'First Aid Kit', 'description' => 'Complete pet first aid kit', 'price' => 44.99, 'stock' => 18],
            ['category_id' => 6, 'name' => 'Joint Support', 'description' => 'Hip and joint support supplement', 'price' => 28.99, 'stock' => 28],
        ];

        foreach ($products as $product) {
            DB::table('products')->insert([
                'name' => $product['name'],
                'description' => $product['description'],
                'price' => $product['price'],
                'stock' => $product['stock'],
                'category_id' => $product['category_id'],
                'image' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
