{{-- filepath: k:\K\KKP\Laravel-Support-Ticketing\resources\views\emails\verify-email.blade.php --}}
@extends('emails.layout')

@section('content')
<h1>🔐 Verify Your Email Address</h1>

<p class="lead">Welcome to Krealogi Support System, {{ $user->name }}! 👋</p>

<p>Thank you for registering with our Support Ticketing System. To complete your registration and access all features, please verify your email address.</p>

<div class="btn-container">
    <a href="{{ $verificationUrl }}" class="btn">
        ✅ Verify Email Address
    </a>
</div>

<h2>🚀 What happens after verification?</h2>
<div style="background: #f8fafc; padding: 20px; border-radius: 12px; margin: 25px 0;">
    <p style="margin: 0 0 10px 0;"><strong>✅ Email Instantly Verified</strong><br>Your account will be fully activated</p>
    <p style="margin: 0 0 10px 0;"><strong>🎫 Create Support Tickets</strong><br>Submit and track your support requests</p>
    <p style="margin: 0 0 10px 0;"><strong>📊 Access Dashboard</strong><br>View your tickets and communication history</p>
    <p style="margin: 0;"><strong>🔒 Secure Account</strong><br>Your account will be fully protected</p>
</div>

<h2>⚡ Important Information</h2>
<p>• 🕐 This verification link will <strong>expire in 60 minutes</strong></p>
<p>• 🔗 You can verify from <strong>any device</strong> - no login required</p>
<p>• 🛡️ This link is secure and can only be used once</p>
<p>• 📱 The link works on mobile devices and desktop computers</p>

<div style="background: #fef3c7; border: 1px solid #f59e0b; border-radius: 8px; padding: 15px; margin: 25px 0;">
    <p style="margin: 0; color: #92400e; font-weight: 600;">
        ⚠️ Security Note: If you didn't create this account, please ignore this email and the account will be automatically removed.
    </p>
</div>

<p style="margin-top: 30px;">
    Having trouble? Copy and paste this link into your browser:<br>
    <span style="font-family: monospace; background: #f3f4f6; padding: 8px; border-radius: 4px; font-size: 12px; word-break: break-all;">
        {{ $verificationUrl }}
    </span>
</p>

<p style="margin-top: 20px;">
    Welcome aboard! We're excited to help you with all your support needs.
</p>
@endsection