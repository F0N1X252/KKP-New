<?php

namespace App\Helpers;

use Laravel\Passport\Client;
use Illuminate\Support\Facades\Log;

class ApiTokenHelper
{
    public static function generateTokenForUser($user)
    {
        try {
            // Check if Personal Access Client exists
            $personalClient = Client::where('personal_access_client', 1)->first();
            
            if (!$personalClient) {
                // Create Personal Access Client programmatically
                $personalClient = Client::create([
                    'name' => config('app.name') . ' Personal Access Client',
                    'secret' => \Illuminate\Support\Str::random(40),
                    'redirect' => '',
                    'personal_access_client' => true,
                    'password_client' => false,
                    'revoked' => false,
                ]);
            }
            
            return $user->createToken('admin-token')->accessToken;
            
        } catch (\Exception $e) {
            Log::error('Failed to generate API token: ' . $e->getMessage());
            return null;
        }
    }
}