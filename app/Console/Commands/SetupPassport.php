<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Laravel\Passport\Client;
use Laravel\Passport\Passport;

class SetupPassport extends Command
{
    protected $signature = 'passport:setup';
    protected $description = 'Setup Laravel Passport with Personal Access Client';

    public function handle()
    {
        $this->info('Setting up Laravel Passport...');

        try {
            // 1. Install Passport
            $this->call('passport:install', ['--force' => true]);
            
            // 2. Create Personal Access Client jika belum ada
            $personalClient = Client::where('personal_access_client', 1)->first();
            
            if (!$personalClient) {
                $this->call('passport:client', [
                    '--personal' => true,
                    '--name' => config('app.name') . ' Personal Access Client',
                ]);
                $this->info('Personal Access Client created successfully!');
            } else {
                $this->info('Personal Access Client already exists.');
            }

            $this->info('Passport setup completed successfully!');
            
        } catch (\Exception $e) {
            $this->error('Failed to setup Passport: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}