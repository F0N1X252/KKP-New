{{-- filepath: k:\K\KKP\Laravel-Support-Ticketing\resources\views\emails\data-change.blade.php --}}
@extends('emails.layout')

@section('content')
<h1>📝 Account Update Notification</h1>

<p class="lead">Hello {{ $user->name }},</p>

<p>We're writing to inform you that changes have been made to your account information by an administrator.</p>

<div style="background: #f8fafc; border-left: 4px solid #3b82f6; padding: 20px; border-radius: 8px; margin: 25px 0;">
    <h2 style="margin-top: 0;">Changed Information:</h2>
    @foreach($changes as $field => $value)
        <p style="margin: 8px 0;">
            <strong>{{ ucfirst(str_replace('_', ' ', $field)) }}:</strong> 
            @if($field === 'password')
                Your password has been updated
            @else
                {{ $value }}
            @endif
        </p>
    @endforeach
</div>

@if(in_array('password', array_keys($changes)))
<div style="background: #fef3c7; border: 1px solid #f59e0b; border-radius: 8px; padding: 15px; margin: 25px 0;">
    <p style="margin: 0; color: #92400e;">
        <strong>🔐 Password Changed:</strong> Your password has been updated. If you didn't request this change, 
        please contact our support team immediately.
    </p>
</div>
@endif

<div class="btn-container">
    <a href="{{ route('login') }}" class="btn">
        🔑 Login to Your Account
    </a>
</div>

<h2>🔒 Security</h2>
<p>These changes were made on {{ now()->format('M j, Y \a\t g:i A') }}. If you did not authorize these changes or have any concerns about your account security, please contact our support team immediately.</p>

<p style="margin-top: 30px;">
    <strong>Need assistance?</strong> Our support team is here to help with any questions about your account.
</p>

<p style="margin-top: 20px; font-size: 14px; color: #6b7280;">
    This notification was sent because your account information was modified. 
    These security notifications cannot be disabled.
</p>
@endsection