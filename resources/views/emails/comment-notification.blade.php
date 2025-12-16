{{-- filepath: k:\K\KKP\Laravel-Support-Ticketing\resources\views\emails\comment-notification.blade.php --}}
@extends('emails.layout')

@section('content')
<h1>💬 New Comment Added</h1>

<p class="lead">Hello,</p>

<p>A new comment has been added to one of your tickets. Here are the details:</p>

<div class="ticket-card">
    <div class="ticket-title">{{ $comment->ticket->title }}</div>
    <div class="ticket-meta">
        <strong>Ticket #{{ $comment->ticket->id }}</strong> • 
        Comment by <strong>{{ $comment->user->name }}</strong> • 
        {{ $comment->created_at->format('M j, Y \a\t g:i A') }}
    </div>
    <div class="ticket-content">
        {!! $comment->comment_text !!}
    </div>
</div>

<div class="btn-container">
    <a href="{{ route('admin.tickets.show', $comment->ticket->id) }}" class="btn">
        💬 View Full Conversation
    </a>
</div>

<h2>🔄 Ticket Status</h2>
<p>
    Current Status: 
    <span class="status-badge status-{{ strtolower($comment->ticket->status->name) }}">
        {{ $comment->ticket->status->name }}
    </span>
    • Priority: 
    <span class="priority-badge priority-{{ strtolower($comment->ticket->priority->name) }}">
        {{ $comment->ticket->priority->name }}
    </span>
</p>

@if($comment->ticket->assigned_to_user)
<p><strong>Assigned to:</strong> {{ $comment->ticket->assigned_to_user->name }}</p>
@endif

<p style="margin-top: 30px;">
    Stay updated with your ticket progress. Click the button above to view the complete conversation and add your response.
</p>

<p style="margin-top: 20px; font-size: 14px; color: #6b7280;">
    This notification was sent because you're involved in ticket #{{ $comment->ticket->id }}. 
    You can manage your notification preferences in your account settings.
</p>
@endsection