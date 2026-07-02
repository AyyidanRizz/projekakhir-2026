<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            CategoriesSeeder::class,     // 👈 Pastikan Master Data di atas
            ProductsSeeder::class,       // 👈 Pastikan Master Data di atas
            ProductsVariantsSeeder::class, // 👈 Pastikan Master Data di atas
            OrdersSeeder::class, 
            PaymentsSeeder::class, 
            ShippingsSeeder::class,
            DesignsSeeder::class,
            RefundsSeeder::class,
        ]);
    }
}
