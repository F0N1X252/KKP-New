<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use App\Http\Resources\Admin\TicketResource;
use App\Ticket;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Storage;

class TicketsApiController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('ticket_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // FIX: Hapus withoutGlobalScopes() jika tidak diperlukan, atau gunakan dengan hati-hati
        $query = Ticket::with(['status', 'priority', 'category', 'assigned_to_user', 'comments']);
        
        // Add search functionality
        if ($request->has('search') && $request->get('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('content', 'LIKE', "%{$search}%")
                  ->orWhere('author_name', 'LIKE', "%{$search}%");
            });
        }

        // Add filtering
        if ($request->has('status_id') && $request->get('status_id')) {
            $query->where('status_id', $request->get('status_id'));
        }

        if ($request->has('priority_id') && $request->get('priority_id')) {
            $query->where('priority_id', $request->get('priority_id'));
        }

        if ($request->has('category_id') && $request->get('category_id')) {
            $query->where('category_id', $request->get('category_id'));
        }

        // FIX: Handle pagination properly untuk DataTables
        $perPage = $request->get('length', 10); // DataTables menggunakan 'length'
        $page = ($request->get('start', 0) / $perPage) + 1; // Konversi start ke page number
        
        try {
            $tickets = $query->latest('id')->paginate($perPage, ['*'], 'page', $page);
            
            // FIX: Format response untuk DataTables
            if ($request->has('draw')) {
                // Ini adalah request dari DataTables
                return response()->json([
                    'draw' => intval($request->get('draw')),
                    'recordsTotal' => $tickets->total(),
                    'recordsFiltered' => $tickets->total(),
                    'data' => $tickets->items()
                ]);
            }
            
            return TicketResource::collection($tickets);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading tickets: ' . $e->getMessage(),
                'draw' => intval($request->get('draw', 1)),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => []
            ], 500);
        }
    }

    public function store(StoreTicketRequest $request)
    {
        try {
            $data = $request->validated();
            
            // Handle file uploads - store all files as JSON array in 'attachment' column
            $attachments = [];
            
            // Handle single attachment
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('attachments', $filename, 'public');
                $attachments[] = $path;
            }

            // Handle multiple attachments
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $filename = time() . '_' . rand(1000, 9999) . '_' . $file->getClientOriginalName();
                    $path = $file->storeAs('attachments', $filename, 'public');
                    $attachments[] = $path;
                }
            }

            // Store all attachments as JSON or single path
            if (!empty($attachments)) {
                $data['attachment'] = count($attachments) === 1 ? $attachments[0] : json_encode($attachments);
            }

            // Remove the 'attachments' key if it exists (since we're storing everything in 'attachment')
            unset($data['attachments']);

            $ticket = Ticket::create($data);

            return (new TicketResource($ticket->load(['status', 'priority', 'category', 'assigned_to_user'])))
                ->response()
                ->setStatusCode(Response::HTTP_CREATED);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create ticket: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(Ticket $ticket)
    {
        abort_if(Gate::denies('ticket_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new TicketResource($ticket->load(['status', 'priority', 'category', 'assigned_to_user', 'comments.user']));
    }

    public function update(UpdateTicketRequest $request, Ticket $ticket)
    {
        try {
            $data = $request->validated();

            // Handle file uploads
            $attachments = [];
            
            // Get existing attachments
            if ($ticket->attachment) {
                $existing = json_decode($ticket->attachment, true);
                if (is_array($existing)) {
                    $attachments = $existing;
                } else {
                    $attachments = [$ticket->attachment];
                }
            }

            // Handle new single attachment
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('attachments', $filename, 'public');
                $attachments[] = $path;
            }

            // Handle new multiple attachments
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $filename = time() . '_' . rand(1000, 9999) . '_' . $file->getClientOriginalName();
                    $path = $file->storeAs('attachments', $filename, 'public');
                    $attachments[] = $path;
                }
            }

            // Store updated attachments
            if (!empty($attachments)) {
                $data['attachment'] = count($attachments) === 1 ? $attachments[0] : json_encode($attachments);
            }

            // Remove the 'attachments' key if it exists
            unset($data['attachments']);

            $ticket->update($data);

            return (new TicketResource($ticket->load(['status', 'priority', 'category', 'assigned_to_user'])))
                ->response()
                ->setStatusCode(Response::HTTP_ACCEPTED);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update ticket: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(Ticket $ticket)
    {
        abort_if(Gate::denies('ticket_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // Hapus File
        if ($ticket->attachment) {
            $files = json_decode($ticket->attachment, true);
            if(is_array($files)){
                foreach($files as $f) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($f);
                }
            } else {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($ticket->attachment);
            }
        }
        
        // Hapus Komentar
        $ticket->comments()->delete();

        // Hapus Tiket
        $ticket->delete();

        return response()->json([
            'success' => true,
            'message' => 'Ticket deleted permanently'
        ], Response::HTTP_OK);
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        
        try {
            $tickets = Ticket::whereIn('id', $ids)->get();
            
            foreach ($tickets as $ticket) {
                // Hapus File
                if ($ticket->attachment) {
                    $files = json_decode($ticket->attachment, true);
                    if(is_array($files)){
                        foreach($files as $f) {
                            \Illuminate\Support\Facades\Storage::disk('public')->delete($f);
                        }
                    } else {
                        \Illuminate\Support\Facades\Storage::disk('public')->delete($ticket->attachment);
                    }
                }
                
                // Hapus Komentar
                $ticket->comments()->delete();

                // Hapus Tiket
                $ticket->delete();
            }

            return response()->json([
                'success' => true,
                'message' => 'Tickets deleted permanently'
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete tickets: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}