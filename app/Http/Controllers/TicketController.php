<?php

namespace App\Http\Controllers;

use App\Ticket;
use App\Status;
use App\Priority;
use App\Category;
use App\User;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Notifications\CommentEmailNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    use MediaUploadingTrait;

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Ambil data untuk dropdown select2
        $statuses = Status::all()->pluck('name', 'id');
        $priorities = Priority::all()->pluck('name', 'id');
        $categories = Category::all()->pluck('name', 'id');
        
        // Tambahkan assigned_to_users - hanya user yang memiliki role agent/admin
        $assigned_to_users = User::whereHas('roles', function($query) {
            $query->whereIn('title', ['Admin', 'Agent']); // Sesuaikan dengan nama role Anda
        })->pluck('name', 'id');

        // Jika tidak ada role system, ambil semua user
        if ($assigned_to_users->isEmpty()) {
            $assigned_to_users = User::all()->pluck('name', 'id');
        }

        return view('tickets.create', compact('statuses', 'priorities', 'categories', 'assigned_to_users'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'         => 'required',
            'content'       => 'required',
            'author_name'   => 'required',
            'author_email'  => 'required|email',
        ]);

        // Prepare data
        $data = $request->only([
            'title', 'content', 'author_name', 'author_email', 'assigned_to_user_id'
        ]);

        // Set default values untuk user yang submit ticket
        $data['status_id'] = $request->status_id ?: 1; // Default: Open
        $data['priority_id'] = $request->priority_id ?: 1; // Default: Low/Medium
        $data['category_id'] = $request->category_id ?: 1; // Default category

        $ticket = Ticket::create($data);

        // Handle File Upload - Update untuk Laravel 12 (tanpa Spatie Media)
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('attachments', $filename, 'public');
            $ticket->update(['attachment' => $path]);
        }

        return redirect()->back()->withStatus('Your ticket has been submitted, we will be in touch. You can view ticket status <a href="'.route('tickets.show', $ticket->id).'">here</a>');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Ticket  $ticket
     * @return \Illuminate\Http\Response
     */
    public function show(Ticket $ticket)
    {
        $ticket->load('comments', 'status', 'priority', 'category', 'assigned_to_user');

        return view('tickets.show', compact('ticket'));
    }

    public function storeComment(Request $request, Ticket $ticket)
    {
        $request->validate([
            'comment_text' => 'required'
        ]);

        $comment = $ticket->comments()->create([
            'author_name'   => $ticket->author_name,
            'author_email'  => $ticket->author_email,
            'comment_text'  => $request->comment_text
        ]);

        // Jika ada method notification
        if (method_exists($ticket, 'sendCommentNotification')) {
            $ticket->sendCommentNotification($comment);
        }

        return redirect()->back()->withStatus('Your comment added successfully');
    }
}