<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\CafeTable;
use App\Models\Category;
use App\Models\Menu;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@brewnest.com'], // cek berdasarkan email
            [
                'name' => 'Admin BrewNest',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );



        // Tables
        for ($i = 1; $i <= 10; $i++) {
            CafeTable::updateOrCreate(
                ['number' => $i], // cek berdasarkan number
                [
                    'name' => 'Meja ' . $i,
                    'status' => 'available',
                ]
                );
            }

        // Categories
        $categories = [
            ['name' => 'Makanan',  'slug' => 'makanan',  'icon' => '🍳', 'sort_order' => 1],
            ['name' => 'Cafe',     'slug' => 'cafe',     'icon' => '☕', 'sort_order' => 2],
            ['name' => 'Non Cafe', 'slug' => 'non-cafe', 'icon' => '🥤', 'sort_order' => 3],
            ['name' => 'Snack',    'slug' => 'snack',    'icon' => '🍟', 'sort_order' => 4],
        ];
        foreach ($categories as $cat) {
            Category::create($cat);
        }

        $catIds = Category::pluck('id', 'slug');

        // Menus
        $menus = [
            // Makanan
            ['category_id' => $catIds['makanan'], 'name' => 'Nasi Goreng Spesial', 'price' => 28000, 'emoji' => '🍳', 'description' => 'Nasi goreng dengan telur, ayam, dan sayuran segar'],
            ['category_id' => $catIds['makanan'], 'name' => 'Mie Goreng', 'price' => 25000, 'emoji' => '🍜', 'description' => 'Mie goreng dengan bumbu khas dan topping lengkap'],
            ['category_id' => $catIds['makanan'], 'name' => 'Ayam Bakar', 'price' => 35000, 'emoji' => '🍗', 'description' => 'Ayam bakar dengan sambal matah dan lalapan'],
            ['category_id' => $catIds['makanan'], 'name' => 'Sandwich Club', 'price' => 32000, 'emoji' => '🥪', 'description' => 'Roti tawar lapis dengan ayam, keju, dan sayuran segar'],
            ['category_id' => $catIds['makanan'], 'name' => 'Pasta Carbonara', 'price' => 38000, 'emoji' => '🍝', 'description' => 'Pasta creamy dengan bacon dan keju parmesan'],
            ['category_id' => $catIds['makanan'], 'name' => 'Nasi Bowl Tuna', 'price' => 30000, 'emoji' => '🍱', 'description' => 'Nasi bowl dengan tuna saus teriyaki dan edamame'],
            // Cafe
            ['category_id' => $catIds['cafe'], 'name' => 'Espresso', 'price' => 18000, 'emoji' => '☕', 'description' => 'Shot espresso murni dari biji kopi pilihan'],
            ['category_id' => $catIds['cafe'], 'name' => 'Cappuccino', 'price' => 28000, 'emoji' => '🫗', 'description' => 'Espresso dengan foam susu lembut yang sempurna'],
            ['category_id' => $catIds['cafe'], 'name' => 'Caramel Latte', 'price' => 32000, 'emoji' => '🧋', 'description' => 'Latte dengan saus karamel manis'],
            ['category_id' => $catIds['cafe'], 'name' => 'Cold Brew', 'price' => 30000, 'emoji' => '🥤', 'description' => 'Kopi cold brew 12 jam, smooth & refreshing'],
            ['category_id' => $catIds['cafe'], 'name' => 'Matcha Latte', 'price' => 30000, 'emoji' => '🍵', 'description' => 'Matcha premium Jepang dengan susu segar'],
            ['category_id' => $catIds['cafe'], 'name' => 'Americano', 'price' => 22000, 'emoji' => '☕', 'description' => 'Espresso dengan air panas, kuat dan bold'],
            // Non Cafe
            ['category_id' => $catIds['non-cafe'], 'name' => 'Es Teh Manis', 'price' => 12000, 'emoji' => '🍹', 'description' => 'Teh manis dingin yang menyegarkan'],
            ['category_id' => $catIds['non-cafe'], 'name' => 'Jus Alpukat', 'price' => 22000, 'emoji' => '🥑', 'description' => 'Jus alpukat segar dengan susu dan coklat'],
            ['category_id' => $catIds['non-cafe'], 'name' => 'Mango Smoothie', 'price' => 25000, 'emoji' => '🥭', 'description' => 'Smoothie mangga segar dengan yogurt'],
            ['category_id' => $catIds['non-cafe'], 'name' => 'Lemon Mint', 'price' => 20000, 'emoji' => '🍋', 'description' => 'Air lemon segar dengan daun mint'],
            // Snack
            ['category_id' => $catIds['snack'], 'name' => 'Kentang Goreng', 'price' => 18000, 'emoji' => '🍟', 'description' => 'Kentang goreng renyah dengan saus pilihan'],
            ['category_id' => $catIds['snack'], 'name' => 'Onion Ring', 'price' => 20000, 'emoji' => '🧅', 'description' => 'Onion ring crispy dengan saus ranch'],
            ['category_id' => $catIds['snack'], 'name' => 'Cheese Stick', 'price' => 22000, 'emoji' => '🧀', 'description' => 'Stik keju goreng garing dengan saus tomat'],
            ['category_id' => $catIds['snack'], 'name' => 'Pisang Goreng', 'price' => 15000, 'emoji' => '🍌', 'description' => 'Pisang goreng crispy dengan topping keju'],
        ];

        foreach ($menus as $menu) {
            Menu::create($menu);
        }
    }
}
