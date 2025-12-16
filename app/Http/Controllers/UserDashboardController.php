<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Ticket;
use App\Status;
use App\Priority;
use App\Category;

class UserDashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('verified');
    }

    /**
     * Show the user dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = auth()->user();

        // Get user's tickets statistics
        $userTickets = Ticket::where('author_name', $user->name)
            ->orWhere('author_email', $user->email);

        $stats = [
            'total_tickets' => $userTickets->count(),
            'open_tickets' => $userTickets->whereHas('status', function($q) {
                $q->where('name', 'Open');
            })->count(),
            'pending_tickets' => $userTickets->whereHas('status', function($q) {
                $q->where('name', 'Pending');
            })->count(),
            'closed_tickets' => $userTickets->whereHas('status', function($q) {
                $q->where('name', 'Closed');
            })->count(),
        ];

        // Get recent tickets for user
        $recentTickets = Ticket::where('author_name', $user->name)
            ->orWhere('author_email', $user->email)
            ->with(['status', 'priority', 'category'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get available options for creating tickets
        $statuses = Status::all();
        $priorities = Priority::all();
        $categories = Category::all();

        return view('home', compact(
            'stats',
            'recentTickets', 
            'statuses',
            'priorities',
            'categories'
        ));
    }
}