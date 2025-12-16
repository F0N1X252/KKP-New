<?php

namespace Database\Seeders;

use App\Priority;
use FontLib\Table\Type\name;
use Illuminate\Database\Seeder;


class PrioritiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $priorities = [
            ['name' => 'Low', 'color' => '#28a745'],
            ['name' => 'Medium', 'color' => '#ffc107'],
            ['name' => 'High', 'color' => '#dc3545'],
            ['name' => 'Critical', 'color' => '#343a40'],
        ];

        foreach($priorities as $priority)
        {
            Priority::firstOrCreate(['name' => $priority['name']], $priority);
        }
        
        echo "Priority data berhasil di-seed!\n";
    }
}
