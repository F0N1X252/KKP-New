<?php

namespace Database\Seeders;

use App\Status;
use Illuminate\Database\Seeder;

class StatusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $statuses = [
            ['name' => 'Open', 'color' => '#007bff'],
            ['name' => 'Closed', 'color' => '#28a745'],
            ['name' => 'Pending', 'color' => '#ffc107'],
            ['name' => 'In Progress', 'color' => '#17a2b8'],
        ];

        foreach($statuses as $status)
        {
            Status::firstOrCreate(['name' => $status['name']], $status);
        }

        echo "Status data berhasil di-seed!\n";
    }
}
