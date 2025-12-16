<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class CommentEmailNotification extends Notification
{
    use Queueable;

    protected $comment;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($comment)
    {
        $this->comment = $comment;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('💬 New Comment on Ticket #' . $this->comment->ticket->id)
            ->view('emails.comment-notification', [
                'comment' => $this->comment,
                'user' => $notifiable,
                'title' => 'New Comment Added',
                'subject' => 'New Comment on Ticket #' . $this->comment->ticket->id
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'comment_id' => $this->comment->id,
            'ticket_id' => $this->comment->ticket->id,
            'ticket_title' => $this->comment->ticket->title,
            'comment_author' => $this->comment->user->name,
            'message' => 'New comment on ticket: ' . $this->comment->ticket->title
        ];
    }
}
