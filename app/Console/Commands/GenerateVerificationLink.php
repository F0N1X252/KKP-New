<?php


namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;

class GenerateVerificationLink extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'verification:generate {email}';

    /**
     * The console command description.
     */
    protected $description = 'Generate verification link for a user email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("User with email {$email} not found.");
            return 1;
        }
        
        if ($user->hasVerifiedEmail()) {
            $this->warn("User {$user->name} ({$email}) is already verified.");
            return 0;
        }
        
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(60),
            [
                'id' => $user->getKey(),
                'hash' => sha1($user->getEmailForVerification()),
            ]
        );
        
        $this->info("Verification link for {$user->name} ({$email}):");
        $this->line($verificationUrl);
        $this->line('');
        $this->info('This link will expire in 60 minutes and can be used from any device.');
        
        return 0;
    }
}