<?php

use Illuminate\Database\Seeder;
use App\Status;
use App\Priority;
use App\Category;

class BasicDataSeeder extends Seeder
{
    public function run()
    {
        // Create basic statuses
        $statuses = [
            ['name' => 'Open', 'color' => '#007bff'],
            ['name' => 'Pending', 'color' => '#ffc107'], 
            ['name' => 'Closed', 'color' => '#28a745'],
            ['name' => 'In Progress', 'color' => '#17a2b8'],
        ];

        foreach ($statuses as $status) {
            Status::firstOrCreate(['name' => $status['name']], $status);
        }

        // Create basic priorities
        $priorities = [
            ['name' => 'Low', 'color' => '#28a745'],
            ['name' => 'Medium', 'color' => '#ffc107'],
            ['name' => 'High', 'color' => '#dc3545'],
            ['name' => 'Critical', 'color' => '#343a40'],
        ];

        foreach ($priorities as $priority) {
            Priority::firstOrCreate(['name' => $priority['name']], $priority);
        }

        // Create basic categories
        $categories = [
            ['name' => 'Technical Support', 'color' => '#007bff'],
            ['name' => 'Bug Report', 'color' => '#dc3545'],
            ['name' => 'Feature Request', 'color' => '#17a2b8'],
            ['name' => 'General Inquiry', 'color' => '#6c757d'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(['name' => $category['name']], $category);
        }

        $this->command->info('Basic data seeded successfully!');
    }
}