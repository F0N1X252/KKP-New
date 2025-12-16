{{-- filepath: k:\K\KKP\Laravel-Support-Ticketing\resources\views\emails\ticket-assigned.blade.php --}}
@extends('emails.layout')

@section('content')
<h1>🎫 New Ticket Assigned</h1>

<p class="lead">Hello {{ $user->name }},</p>

<p>You have been assigned a new support ticket. Please review the details below and take appropriate action.</p>

<div class="ticket-card">
    <div class="ticket-title">{{ $ticket->title }}</div>
    <div class="ticket-meta">
        <strong>Ticket #{{ $ticket->id }}</strong> • 
        Created {{ $ticket->created_at->format('M j, Y \a\t g:i A') }} • 
        <span class="status-badge status-{{ strtolower($ticket->status->name) }}">
            {{ $ticket->status->name }}
        </span>
        <span class="priority-badge priority-{{ strtolower($ticket->priority->name) }}">
            {{ $ticket->priority->name }} Priority
        </span>
    </div>
    @if($ticket->content)
        <div class="ticket-content">
            {!! Str::limit(strip_tags($ticket->content), 300) !!}
        </div>
    @endif
</div>

<div class="btn-container">
    <a href="{{ route('admin.tickets.show', $ticket->id) }}" class="btn">
        🔍 View Ticket Details
    </a>
</div>

<h2>📋 Next Steps</h2>
<p>• Review the ticket details and priority level</p>
<p>• Contact the customer if additional information is needed</p>
<p>• Update the ticket status as you work on it</p>
<p>• Provide timely updates to keep the customer informed</p>

<p style="margin-top: 30px;">
    <strong>Need help?</strong> Contact our support team if you have any questions about handling this ticket.
</p>

<p style="margin-top: 20px; font-size: 14px; color: #6b7280;">
    This notification was sent because you were assigned to ticket #{{ $ticket->id }}. 
    You can manage your notification preferences in your account settings.
</p>
@endsection