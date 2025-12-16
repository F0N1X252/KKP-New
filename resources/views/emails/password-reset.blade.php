{{-- filepath: k:\K\KKP\Laravel-Support-Ticketing\resources\views\emails\password-reset.blade.php --}}
@extends('emails.layout')

@section('content')
<h1>🔑 Reset Your Password</h1>

<p class="lead">Hello {{ $user->name ?? 'there' }},</p>

<p>We received a request to reset your password for your Krealogi Support account. If you made this request, click the button below to reset your password.</p>

<div class="btn-container">
    <a href="{{ $resetUrl }}" class="btn">
        🔑 Reset Password
    </a>
</div>

<h2>🔒 Security Information</h2>
<div style="background: #f8fafc; border-left: 4px solid #3b82f6; padding: 20px; margin: 25px 0;">
    <p style="margin: 0 0 10px 0;"><strong>⏰ Link expires in 60 minutes</strong><br>Use this link as soon as possible</p>
    <p style="margin: 0 0 10px 0;"><strong>🎯 One-time use only</strong><br>The link becomes invalid after use</p>
    <p style="margin: 0;"><strong>🛡️ Secure process</strong><br>Your account remains protected throughout</p>
</div>

<h2>🚨 Didn't request this?</h2>
<div style="background: #fef2f2; border: 1px solid #f87171; border-radius: 8px; padding: 15px; margin: 25px 0;">
    <p style="margin: 0; color: #991b1b;">
        <strong>No action needed.</strong> If you didn't request a password reset, you can safely ignore this email. 
        Your password will remain unchanged and your account stays secure.
    </p>
</div>

<h2>💡 Tips for a Strong Password</h2>
<p>• Use at least 8 characters with mixed case letters</p>
<p>• Include numbers and special characters</p>
<p>• Avoid using personal information</p>
<p>• Don't reuse passwords from other accounts</p>

<p style="margin-top: 30px;">
    Having trouble with the button? Copy and paste this link into your browser:<br>
    <span style="font-family: monospace; background: #f3f4f6; padding: 8px; border-radius: 4px; font-size: 12px; word-break: break-all;">
        {{ $resetUrl }}
    </span>
</p>

<p style="margin-top: 20px;">
    If you continue to have problems, please contact our support team for assistance.
</p>
@endsection