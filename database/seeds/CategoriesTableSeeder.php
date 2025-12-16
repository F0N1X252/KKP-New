<?php

namespace Database\Seeders;

use App\Category;
use Illuminate\Database\Seeder;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            ['name' => 'Uncategorized', 'color' => '#6c757d'],
            ['name' => 'Billing/Payments', 'color' => '#ffc107'],
            ['name' => 'Technical question', 'color' => '#007bff'],
            ['name' => 'Bug Report', 'color' => '#dc3545'],
            ['name' => 'Feature Request', 'color' => '#17a2b8'],
        ];

        foreach($categories as $category)
        {
            Category::firstOrCreate(['name' => $category['name']], $category);
        }
        
        echo "Category data berhasil di-seed!\n";
    }
}
