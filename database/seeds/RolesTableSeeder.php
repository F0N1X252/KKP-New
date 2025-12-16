<?php

namespace Database\Seeders;

use App\Role;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            [
                'title' => 'Admin',
            ],
            [
                'title' => 'User',
            ],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['title' => $role['title']], $role);
        }
    }
}
