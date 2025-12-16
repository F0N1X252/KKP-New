<?php

namespace Database\Seeders;

use App\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        $users = [
            [
                'name'              => 'Admin',
                'email'             => 'admin@admin.com',
                'password'          => bcrypt('password'),
                'remember_token'    => null,
            ],
            
        ];

        foreach ($users as $user) {
            User::firstOrCreate(['email' => $user['email']], $user);
        }
    }
}