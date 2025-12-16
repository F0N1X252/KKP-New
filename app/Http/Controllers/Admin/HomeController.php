<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Ticket;
use App\User;
use App\Category;
use App\Status;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    public function index()
    {
        // Check if user's email is verified
        if (!auth()->user()->hasVerifiedEmail()) {
            return redirect()->route('verification.notice')
                ->with('warning', 'Please verify your email address before accessing the admin dashboard.');
        }

        // Get filter from request, default to 'all'
        $filter = request('filter', 'all');
        
        // Get date range based on filter
        $dateRange = $this->getDateRange($filter);
        
        // Get tickets data for charts
        $ticketsData = $this->getTicketsData($filter, $dateRange);
        
        // Get statistics
        $stats = $this->getStatistics($dateRange);
        
        return view('home', compact('ticketsData', 'stats', 'filter'));
    }

    public function getDashboardData()
    {
        try {
            $filter = request('filter', 'all');
            
            // Get date range based on filter
            $dateRange = $this->getDateRange($filter);
            
            // Get tickets data for charts
            $ticketsData = $this->getTicketsData($filter, $dateRange);
            
            // Get statistics
            $stats = $this->getStatistics($dateRange);
            
            return response()->json([
                'success' => true,
                'ticketsData' => $ticketsData,
                'stats' => $stats,
                'filter' => $filter
            ]);
        } catch (\Exception $e) {
            Log::error('Dashboard data error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to load dashboard data'
            ], 500);
        }
    }

    private function getDateRange($filter)
    {
        $now = Carbon::now();
        
        switch ($filter) {
            case 'daily':
                $start = $now->copy()->startOfDay();
                $end = $now->copy()->endOfDay();
                break;
                
            case 'monthly':
                $start = $now->copy()->startOfMonth();
                $end = $now->copy()->endOfMonth();
                break;
                
            case 'yearly':
                $start = $now->copy()->startOfYear();
                $end = $now->copy()->endOfYear();
                break;
                
            case 'all':
            default:
                // Get the earliest and latest ticket dates
                $earliest = Ticket::min('created_at');
                $latest = Ticket::max('created_at');
                
                $start = $earliest ? Carbon::parse($earliest) : $now->copy()->subYear();
                $end = $latest ? Carbon::parse($latest) : $now;
                break;
        }
        
        $range = ['start' => $start, 'end' => $end];
        
        Log::info('Date Range Debug', [
            'filter' => $filter,
            'start' => $range['start']->format('Y-m-d H:i:s'),
            'end' => $range['end']->format('Y-m-d H:i:s')
        ]);
        
        return $range;
    }

    private function getTicketsData($filter, $dateRange)
    {
        Log::info('=== DEBUG TICKETS DATA ===');
        
        // Status distribution with specific ordering and inclusion of "On Progress"
        $statusData = DB::table('tickets')
            ->join('statuses', 'tickets.status_id', '=', 'statuses.id')
            ->whereBetween('tickets.created_at', [$dateRange['start'], $dateRange['end']])
            ->select('statuses.name', 'statuses.color', DB::raw('count(*) as count'))
            ->groupBy('statuses.name', 'statuses.color')
            ->orderByRaw("CASE 
                WHEN statuses.name = 'Open' THEN 1
                WHEN statuses.name = 'On Progress' THEN 2
                WHEN statuses.name = 'Pending' THEN 3
                WHEN statuses.name = 'Closed' THEN 4
                ELSE 5
            END")
            ->get();
        
        Log::info('Status Data:', $statusData->toArray());

        // Priority distribution
        $priorityData = DB::table('tickets')
            ->join('priorities', 'tickets.priority_id', '=', 'priorities.id')
            ->whereBetween('tickets.created_at', [$dateRange['start'], $dateRange['end']])
            ->select('priorities.name', 'priorities.color', DB::raw('count(*) as count'))
            ->groupBy('priorities.name', 'priorities.color')
            ->get();
        
        Log::info('Priority Data:', $priorityData->toArray());

        // Category distribution
        $categoryData = DB::table('tickets')
            ->join('categories', 'tickets.category_id', '=', 'categories.id')
            ->whereBetween('tickets.created_at', [$dateRange['start'], $dateRange['end']])
            ->select('categories.name', 'categories.color', DB::raw('count(*) as count'))
            ->groupBy('categories.name', 'categories.color')
            ->get();
        
        Log::info('Category Data:', $categoryData->toArray());

        // Timeline data
        $timelineData = $this->getTimelineData($filter, $dateRange);

        $result = [
            'status' => [
                'labels' => $statusData->pluck('name')->toArray(),
                'data' => $statusData->pluck('count')->toArray(),
                'colors' => $statusData->pluck('color')->map(function($color) {
                    return $color ?: '#6c757d'; // Default color if null
                })->toArray()
            ],
            'priority' => [
                'labels' => $priorityData->pluck('name')->toArray(),
                'data' => $priorityData->pluck('count')->toArray(),
                'colors' => $priorityData->pluck('color')->map(function($color) {
                    return $color ?: '#6c757d'; // Default color if null
                })->toArray()
            ],
            'category' => [
                'labels' => $categoryData->pluck('name')->toArray(),
                'data' => $categoryData->pluck('count')->toArray(),
                'colors' => $categoryData->pluck('color')->map(function($color) {
                    return $color ?: '#6c757d'; // Default color if null
                })->toArray()
            ],
            'timeline' => $timelineData
        ];
        
        Log::info('Final Result:', $result);
        
        return $result;
    }

    private function getTimelineData($filter, $dateRange)
    {
        try {
            $groupFormat = match($filter) {
                'daily' => '%H:00',
                'monthly' => '%Y-%m-%d', 
                'yearly' => '%Y-%m',
                default => '%Y-%m-%d'
            };

            $timeline = DB::table('tickets')
                ->select(
                    DB::raw("DATE_FORMAT(created_at, '$groupFormat') as period"),
                    DB::raw('COUNT(*) as count')
                )
                ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                ->groupBy('period')
                ->orderBy('period')
                ->get()
                ->map(function($item) {
                    return [
                        'period' => $item->period,
                        'count' => (int) $item->count
                    ];
                })
                ->toArray();

            return $timeline;
        } catch (\Exception $e) {
            Log::error('Timeline data error: ' . $e->getMessage());
            return []; // Return empty array if error
        }
    }

    private function getStatistics($dateRange)
    {
        // Hitung semua statistik berdasarkan date range filter
        $totalTickets = Ticket::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])->count();
        
        $openTickets = Ticket::whereHas('status', function($query) {
            $query->where('name', 'Open');
        })->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])->count();
        
        $closedTickets = Ticket::whereHas('status', function($query) {
            $query->where('name', 'Closed');
        })->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])->count();
        
        $onProgressTickets = Ticket::whereHas('status', function($query) {
            $query->where('name', 'On Progress');
        })->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])->count();
        
        $pendingTickets = Ticket::whereHas('status', function($query) {
            $query->where('name', 'Pending');
        })->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])->count();

        // Data yang tidak perlu filter tanggal (data master)
        $totalUsers = User::count();
        $totalCategories = Category::count();
        $totalStatuses = Status::count();
        
        return [
            'total_tickets' => $totalTickets,
            'open_tickets' => $openTickets,
            'closed_tickets' => $closedTickets,
            'on_progress_tickets' => $onProgressTickets,
            'pending_tickets' => $pendingTickets,
            'total_users' => $totalUsers,
            'total_categories' => $totalCategories,
            'total_statuses' => $totalStatuses,
        ];
    }

    public function getChartData()
    {
        $filter = request('filter', 'all');
        $dateRange = $this->getDateRange($filter);
        $data = $this->getTicketsData($filter, $dateRange);
        
        return response()->json($data);
    }
}