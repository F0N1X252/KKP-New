<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Auth\Events\Verified;
use Illuminate\Auth\Access\AuthorizationException;

class VerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles email verification with one-click verification.
    | Users can verify their email from any device without needing to login.
    |
    */

    /**
     * Where to redirect users after verification.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Remove auth middleware for verify method to allow cross-device verification
        $this->middleware('auth')->only('show', 'resend');
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    /**
     * Show the email verification notice.
     */
    public function show(Request $request)
    {
        return $request->user()->hasVerifiedEmail()
            ? redirect($this->redirectPath())->with('verified', true)
            : view('auth.verify', [
                'pageTitle' => 'Verify Your Email Address'
            ]);
    }

    /**
     * Mark the authenticated user's email address as verified.
     */
    public function verify(Request $request)
    {
        // Get user by ID from route parameter (no auth required)
        $user = User::findOrFail($request->route('id'));

        // Verify the hash matches
        if (!hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
            throw new AuthorizationException('Invalid verification link.');
        }

        // Check if email is already verified
        if ($user->hasVerifiedEmail()) {
            return $this->verificationSuccessResponse($user, 'Email address was already verified.');
        }

        // Mark email as verified
        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return $this->verificationSuccessResponse($user, 'Your email has been successfully verified!');
    }

    /**
     * Resend the email verification notification.
     */
    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect($this->redirectPath())->with('verified', true);
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('resent', true);
    }

    /**
     * Handle successful verification response.
     */
    protected function verificationSuccessResponse(User $user, $message)
    {
        // Create a beautiful success page that auto-redirects to login
        return view('auth.verification-success', [
            'user' => $user,
            'message' => $message,
            'loginUrl' => route('login'),
            'pageTitle' => 'Email Verified Successfully'
        ]);
    }

    /**
     * Get the post-verification redirect path.
     */
    protected function redirectPath()
    {
        return $this->redirectTo ?? '/home';
    }

    /**
     * Generate a verification URL for the user (for testing).
     */
    public function generateVerificationUrl(User $user)
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(config('auth.verification.expire', 60)),
            [
                'id' => $user->getKey(),
                'hash' => sha1($user->getEmailForVerification()),
            ]
        );
    }
}
