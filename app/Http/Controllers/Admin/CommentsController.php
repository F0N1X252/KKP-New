<?php

namespace App\Http\Controllers\Admin;

use App\Comment;
use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyCommentRequest;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Ticket;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;
// PENTING: Tambahkan ini
use Yajra\DataTables\Facades\DataTables;

class CommentsController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            // Load relasi ticket dan user agar tidak N+1 Query
            $query = Comment::with(['ticket', 'user'])->select(sprintf('%s.*', (new Comment)->getTable()));
            
            $table = DataTables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'comment_show';
                $editGate      = 'comment_edit';
                $deleteGate    = 'comment_delete';
                $crudRoutePart = 'comments';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->editColumn('id', function ($row) {
                return $row->id ? $row->id : "";
            });
            
            // Kolom Author (Digabungkan di View, tapi kita kirim datanya)
            $table->editColumn('author_name', function ($row) {
                // Prioritas: Nama user login > Nama author manual
                return $row->user ? $row->user->name : ($row->author_name ?? 'Guest');
            });
            
            $table->editColumn('author_email', function ($row) {
                return $row->user ? $row->user->email : ($row->author_email ?? '-');
            });

            // Kolom Ticket Title (Relasi)
            $table->addColumn('ticket.title', function ($row) {
                return $row->ticket ? $row->ticket->title : '';
            });

            $table->editColumn('comment_text', function ($row) {
                return $row->comment_text ? $row->comment_text : "";
            });

            $table->rawColumns(['actions', 'placeholder', 'ticket.title']);

            return $table->make(true);
        }

        return view('admin.comments.index');
    }

    public function create()
    {
        abort_if(Gate::denies('comment_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $tickets = Ticket::all()->pluck('title', 'id')->prepend(trans('global.pleaseSelect'), '');
        $users = User::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.comments.create', compact('tickets', 'users'));
    }

    public function store(StoreCommentRequest $request)
    {
        $data = $request->all();
        
        // Auto-fill data user jika login
        if(auth()->check()){
            $data['user_id'] = auth()->id();
            $data['author_name'] = auth()->user()->name;
            $data['author_email'] = auth()->user()->email;
        }

        $comment = Comment::create($data);

        // Kirim Notifikasi (jika ada logic-nya)
        // $comment->ticket->sendCommentNotification($comment);

        // --- PERUBAHAN DI SINI ---
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Reply posted successfully!'
            ]);
        }

        // Fallback jika tidak pakai AJAX: Kembali ke halaman sebelumnya (Ticket Show), BUKAN Index
        return back()->with('message', 'Comment added successfully');
        
    }

    public function edit(Comment $comment)
    {
        abort_if(Gate::denies('comment_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $tickets = Ticket::all()->pluck('title', 'id')->prepend(trans('global.pleaseSelect'), '');
        $users = User::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $comment->load('ticket', 'user');

        return view('admin.comments.edit', compact('tickets', 'users', 'comment'));
    }

    public function update(UpdateCommentRequest $request, Comment $comment)
    {
        $comment->update($request->all());

        return redirect()->route('admin.tickets.show', $comment->ticket_id)->with('message', 'Comment updated successfully');
    }

    public function show(Comment $comment)
    {
        abort_if(Gate::denies('comment_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $comment->load('ticket', 'user');

        return view('admin.comments.show', compact('comment'));
    }

    public function destroy(Comment $comment)
    {
        abort_if(Gate::denies('comment_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $comment->delete(); // Ini sekarang Hapus Permanen

        return back()->with('message', 'Comment deleted permanently');
    }

    public function massDestroy(MassDestroyCommentRequest $request)
    {
        Comment::whereIn('id', request('ids'))->delete(); // Ini sekarang Hapus Permanen

        return response(null, Response::HTTP_NO_CONTENT);
    }
    // public function destroy(Comment $comment)
    // {
    //     abort_if(Gate::denies('comment_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

    //     $comment->delete();

    //     return back()->with('message', 'Comment deleted successfully');
    // }

    // public function massDestroy(MassDestroyCommentRequest $request)
    // {
    //     Comment::whereIn('id', request('ids'))->delete();

    //     return response(null, Response::HTTP_NO_CONTENT);
    // }
}