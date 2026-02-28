<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        DB::table('users')->insert([
            'name' => 'Admin User',
            'email' => 'admin@upsupplies.com',
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'role' => 'admin',
            'active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create regular user
        DB::table('users')->insert([
            'name' => 'Test User',
            'email' => 'test@upsupplies.com',
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'role' => 'user',
            'active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->call(CategorySeeder::class);
        $this->call(ProductSeeder::class);
        $this->call(ReviewSeeder::class);
    }
}
