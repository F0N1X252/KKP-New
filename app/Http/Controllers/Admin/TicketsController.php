<?php

namespace App\Http\Controllers\Admin;

use App\User;
// Models
use App\Status;
use App\Ticket;
use ZipArchive;
use App\Category;
use App\Priority;
// Requests
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
// Helpers
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\StoreTicketRequest;
// DataTables (Pastikan huruf besar T sesuai)
use Maatwebsite\Excel\Concerns\FromArray;
// Excel
use App\Http\Requests\UpdateTicketRequest;
use Maatwebsite\Excel\Concerns\WithStyles;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\MassDestroyTicketRequest;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use App\Http\Controllers\Traits\MediaUploadingTrait;

class TicketsController extends Controller
{
    use MediaUploadingTrait;

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Ticket::with(['status', 'priority', 'category', 'assigned_to_user', 'comments'])
                ->filterTickets($request)
                // Fix: Menggunakan getTable()
                ->select(sprintf('%s.*', (new Ticket)->getTable())); 

            // Fix: Penulisan DataTables (T besar) harus sama dengan use di atas
            // $table = DataTables::of($query);
            $table = DataTables::of($query); 

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'ticket_show';
                $editGate      = 'ticket_edit';
                $deleteGate    = 'ticket_delete';
                $crudRoutePart = 'tickets';

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
            $table->editColumn('title', function ($row) {
                return $row->title ? $row->title : "";
            });
            $table->addColumn('status_name', function ($row) {
                return $row->status ? $row->status->name : '';
            });
            $table->addColumn('priority_name', function ($row) {
                return $row->priority ? $row->priority->name : '';
            });
            $table->addColumn('category_name', function ($row) {
                return $row->category ? $row->category->name : '';
            });
            $table->editColumn('author_name', function ($row) {
                return $row->author_name ? $row->author_name : "";
            });
            $table->editColumn('author_email', function ($row) {
                return $row->author_email ? $row->author_email : "";
            });
            $table->addColumn('assigned_to_user_name', function ($row) {
                return $row->assigned_to_user ? $row->assigned_to_user->name : '';
            });

            $table->rawColumns(['actions', 'placeholder']);
// dd($table);
            return $table->make(true);
        }

        $priorities = Priority::all();
        $statuses   = Status::all();
        $categories = Category::all();
// dd($priorities, $statuses, $categories);
        return view('admin.tickets.index', compact('priorities', 'statuses', 'categories'));
    }

    // public function create()
    // {
    //     abort_if(Gate::denies('ticket_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

    //     $statuses = Status::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
    //     $priorities = Priority::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
    //     $categories = Category::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

    //     $assigned_to_users = User::whereHas('roles', function($query) {
    //             $query->whereId(2);
    //         })
    //         ->pluck('name', 'id')
    //         ->prepend(trans('global.pleaseSelect'), '');

    //     return view('admin.tickets.create', compact('statuses', 'priorities', 'categories', 'assigned_to_users'));
    // }
    public function create()
    {
        abort_if(Gate::denies('ticket_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // Menggunakan pluck agar formatnya [id => name] untuk Select2
        $statuses = Status::all()->pluck('name', 'id');
        $priorities = Priority::all()->pluck('name', 'id');
        $categories = Category::all()->pluck('name', 'id');

        $assigned_to_users = User::whereHas('roles', function($query) {
                $query->whereId(2); // Pastikan Role ID 2 (Agent) ada di DB
            })
            ->pluck('name', 'id');

        return view('admin.tickets.create', compact('statuses', 'priorities', 'categories', 'assigned_to_users'));
    }

    public function store(StoreTicketRequest $request)
{
    \Illuminate\Support\Facades\DB::beginTransaction();
    try {
        $data = $request->all();
        
        // Handle Multiple Files
        $attachmentPaths = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                // Simpan file dan ambil path-nya
                $path = $file->store('attachments', 'public');
                $attachmentPaths[] = $path;
            }
        }
        
        // Simpan sebagai JSON string jika kolom di DB mendukung
        // Jika kolom di DB 'attachment' hanya string biasa dan anda ingin multiple,
        // sebaiknya buat tabel terpisah 'media' atau 'ticket_attachments'.
        // Untuk solusi cepat (disimpan di kolom attachment):
        $data['attachment'] = !empty($attachmentPaths) ? json_encode($attachmentPaths) : null;

        $ticket = Ticket::create($data);

        \Illuminate\Support\Facades\DB::commit();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Ticket created successfully!',
                'redirect_url' => route('admin.tickets.index')
            ]);
        }

        return redirect()->route('admin.tickets.index');

    } catch (\Exception $e) {
            // Jika ada error, batalkan penyimpanan tiket
            \Illuminate\Support\Facades\DB::rollback();

            if ($request->ajax()) {
                // Kembalikan pesan error asli untuk debugging (hapus $e->getMessage() di production)
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to save ticket: ' . $e->getMessage()
                ], 500);
            }

            return back()->withErrors(['msg' => 'Something went wrong: ' . $e->getMessage()]);
        }
    }

    public function edit(Ticket $ticket)
    {
        abort_if(Gate::denies('ticket_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $statuses = Status::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $priorities = Priority::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $categories = Category::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $assigned_to_users = User::whereHas('roles', function($query) {
                $query->whereId(2);
            })
            ->pluck('name', 'id')
            ->prepend(trans('global.pleaseSelect'), '');

        $ticket->load('status', 'priority', 'category', 'assigned_to_user');

        return view('admin.tickets.edit', compact('statuses', 'priorities', 'categories', 'assigned_to_users', 'ticket'));
    }

    public function update(UpdateTicketRequest $request, Ticket $ticket)
{
    $ticket->update($request->all());

    // Logic Upload File (Jika pakai manual storage seperti di create sebelumnya)
    if ($request->hasFile('attachment')) {
        // Hapus file lama jika ada
        if ($ticket->attachment && \Illuminate\Support\Facades\Storage::disk('public')->exists($ticket->attachment)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($ticket->attachment);
        }
        
        $path = $request->file('attachment')->store('attachments', 'public');
        $ticket->attachment = $path;
        $ticket->save();
    }

    // --- PERUBAHAN DI SINI (Return JSON) ---
    if ($request->ajax()) {
        return response()->json([
            'success' => true,
            'message' => 'Ticket updated successfully!',
            'redirect_url' => route('admin.tickets.edit', $ticket->id)
        ]);
    }

    // Fallback jika javascript mati
    return redirect()->route('admin.tickets.edit', $ticket->id);
}

    public function show($id)
    {
        $ticket = Ticket::findOrFail($id);
        return view('admin.tickets.show', compact('ticket'));
    }

    // public function destroy(Ticket $ticket)
    // {
    //     abort_if(Gate::denies('ticket_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

    //     // 1. Hapus File Attachment Fisik (PENTING: Agar storage tidak penuh sampah)
    //     if ($ticket->attachment && \Illuminate\Support\Facades\Storage::disk('public')->exists($ticket->attachment)) {
    //         \Illuminate\Support\Facades\Storage::disk('public')->delete($ticket->attachment);
    //     }

    //     // 2. Hapus Komentar Terkait (Opsional: Jika tidak di-cascade di database)
    //     // $ticket->comments()->forceDelete(); 

    //     // 3. HARD DELETE (Hapus Permanen dari Database)
    //     // Gunakan forceDelete() menggantikan delete()
    //     $ticket->forceDelete();

    //     if (request()->ajax()) {
    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Ticket deleted permanently.'
    //         ]);
    //     }

    //     return back();
    // }
    
    public function destroy(Ticket $ticket)
    {
        abort_if(Gate::denies('ticket_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // 1. Hapus File Fisik
        if ($ticket->attachment) {
            // Cek jika attachment berupa array JSON (multiple files)
            $files = json_decode($ticket->attachment, true);
            
            if (is_array($files)) {
                foreach ($files as $file) {
                    if (\Illuminate\Support\Facades\Storage::disk('public')->exists($file)) {
                        \Illuminate\Support\Facades\Storage::disk('public')->delete($file);
                    }
                }
            } 
            // Cek jika attachment berupa string tunggal (single file - legacy)
            elseif (is_string($ticket->attachment)) {
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($ticket->attachment)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($ticket->attachment);
                }
            }
        }

        // 2. Hapus Komentar Terkait (Manual delete agar bersih)
        $ticket->comments()->delete();

        // 3. Hapus Permanen Tiket
        $ticket->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Ticket deleted permanently.'
            ]);
        }

        return back();
    }

     public function massDestroy(MassDestroyTicketRequest $request)
    {
        // Kita harus loop satu per satu untuk menghapus file fisiknya
        $tickets = Ticket::whereIn('id', request('ids'))->get();

        foreach ($tickets as $ticket) {
            // 1. Hapus File Fisik (Sama seperti destroy)
            if ($ticket->attachment) {
                $files = json_decode($ticket->attachment, true);
                if (is_array($files)) {
                    foreach ($files as $file) {
                        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($file)) {
                            \Illuminate\Support\Facades\Storage::disk('public')->delete($file);
                        }
                    }
                } elseif (is_string($ticket->attachment)) {
                    if (\Illuminate\Support\Facades\Storage::disk('public')->exists($ticket->attachment)) {
                        \Illuminate\Support\Facades\Storage::disk('public')->delete($ticket->attachment);
                    }
                }
            }

            // 2. Hapus Komentar
            $ticket->comments()->delete();

            // 3. Hapus Tiket
            $ticket->delete();
        }

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Selected tickets deleted permanently.'
            ]);
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
    // public function massDestroy(MassDestroyTicketRequest $request)
    // {
    //     // Ambil tiket yang akan dihapus untuk membersihkan file attachmentnya juga
    //     $tickets = Ticket::whereIn('id', request('ids'))->get();

    //     foreach ($tickets as $ticket) {
    //         // Hapus file fisik
    //         if ($ticket->attachment && \Illuminate\Support\Facades\Storage::disk('public')->exists($ticket->attachment)) {
    //             \Illuminate\Support\Facades\Storage::disk('public')->delete($ticket->attachment);
    //         }
            
    //         // Hapus Permanen
    //         $ticket->forceDelete();
    //     }

    //     if (request()->ajax()) {
    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Selected tickets deleted permanently.'
    //         ]);
    //     }

    //     return response(null, Response::HTTP_NO_CONTENT);
    // }

    public function storeComment(Request $request, Ticket $ticket)
    {
        $request->validate([
            'comment_text' => 'nullable|required'
        ]);
        
        $user = auth()->user();
        // dd($user);
        $comment = $ticket->comments()->create([
            'author_name'   => $user->name,
            'author_email'  => $user->email,
            'user_id'       => $user->id,
            'comment_text'  => $request->comment_text
        ]);

        $ticket->sendCommentNotification($comment);

        return redirect()->back()->withStatus('Your comment added successfully');
    }

    public function exportDetail($id)
    {
        $ticket = Ticket::with(['status', 'priority', 'category', 'assigned_to_user', 'comments'])->findOrFail($id);

        // 1. Siapkan Data Excel (Sama seperti sebelumnya)
        $attachmentsInfo = $ticket->attachment ? basename($ticket->attachment) : 'No attachment';
        $comments = $ticket->comments->pluck('comment_text')->implode("\n---\n");

        $data = [
            ['ID', $ticket->id],
            ['Created at', $ticket->created_at],
            ['Title', $ticket->title],
            ['Content', strip_tags($ticket->content)], // Strip tags agar rapi di excel
            ['Attachments', $attachmentsInfo],
            ['Status', $ticket->status->name ?? ''],
            ['Priority', $ticket->priority->name ?? ''],
            ['Category', $ticket->category->name ?? ''],
            ['Author Name', $ticket->author_name],
            ['Author Email', $ticket->author_email],
            ['Assigned To User', $ticket->assigned_to_user->name ?? ''],
            ['Comments', $comments],
        ];

        // 2. Generate Excel ke dalam variable (RAW Data)
        $exportObject = new class($data) implements FromArray, WithStyles, WithColumnWidths {
            protected $data;
            public function __construct($data) { $this->data = $data; }
            public function array(): array { return $this->data; }
            public function styles(Worksheet $sheet) {
                $rowCount = count($this->data);
                for ($i = 1; $i <= $rowCount; $i++) {
                    $sheet->getStyle("A{$i}")->getFont()->setBold(true);
                    $sheet->getStyle("B{$i}")->getAlignment()->setWrapText(true);
                    $sheet->getRowDimension($i)->setRowHeight(-1);
                }
            }
            public function columnWidths(): array {
                return ['A' => 22, 'B' => 60];
            }
        };

        // Simpan Excel ke Temporary File
        $excelFileName = 'ticket_' . $ticket->id . '_report.xlsx';
        $excelContent = Excel::raw($exportObject, \Maatwebsite\Excel\Excel::XLSX);

        // 3. Buat File ZIP
        $zipFileName = 'ticket_' . $ticket->id . '_complete.zip';
        $zipPath = storage_path('app/public/' . $zipFileName); // Simpan sementara di storage

        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            
            // A. Masukkan File Excel ke dalam ZIP
            $zip->addFromString($excelFileName, $excelContent);

            // B. Masukkan File Attachment (Jika Ada)
            if ($ticket->attachment && Storage::disk('public')->exists($ticket->attachment)) {
                $absolutePath = storage_path('app/public/' . $ticket->attachment);
                $fileNameInZip = 'attachment_' . basename($ticket->attachment);
                $zip->addFile($absolutePath, $fileNameInZip);
            }

            $zip->close();
        }

        // 4. Download ZIP lalu Hapus File ZIP dari server setelah dikirim
        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    public function export(Request $request)
    {
        // 1. Ambil data (gunakan filter yang sama dengan index)
        $query = Ticket::with(['status', 'priority', 'category', 'assigned_to_user']);
        
        // Gunakan scope filter agar hasil export sesuai dengan apa yang difilter user
        $query->filterTickets($request); 
        
        $tickets = $query->get();

        // 2. Siapkan Header dan Data
        $data = [];
        
        // Header Row
        $data[] = [
            'ID', 
            'Title', 
            'Status', 
            'Priority', 
            'Category', 
            'Author Name', 
            'Author Email', 
            'Assigned Agent', 
            'Content',
            'Created At'
        ];

        // Data Rows
        foreach ($tickets as $ticket) {
            $data[] = [
                $ticket->id,
                $ticket->title,
                $ticket->status->name ?? '',
                $ticket->priority->name ?? '',
                $ticket->category->name ?? '',
                $ticket->author_name,
                $ticket->author_email,
                $ticket->assigned_to_user->name ?? 'Unassigned',
                strip_tags($ticket->content), // Bersihkan tag HTML dari CKEditor
                $ticket->created_at->format('Y-m-d H:i:s'),
            ];
        }

        // 3. Download Excel
        return Excel::download(new class($data) implements FromArray, WithStyles, WithColumnWidths {
            protected $data;
            public function __construct($data) { $this->data = $data; }
            public function array(): array { return $this->data; }

            // Style Header Bold
            public function styles(Worksheet $sheet) {
                $sheet->getStyle('A1:J1')->getFont()->setBold(true);
            }

            // Lebar Kolom Otomatis/Manual
            public function columnWidths(): array {
                return [
                    'A' => 8,  // ID
                    'B' => 30, // Title
                    'G' => 25, // Email
                    'I' => 50, // Content
                    'J' => 20, // Created At
                ];
            }
        }, 'tickets_export_' . date('Y-m-d_H-i') . '.xlsx');
    }

    /**
     * Delete attachment from ticket
     */
    public function deleteAttachment(Ticket $ticket)
    {
        try {
            if (!$ticket->attachment) {
                return response()->json([
                    'success' => false,
                    'message' => 'No attachment found'
                ], 404);
            }

            // Delete file from storage
            if (Storage::exists('public/' . $ticket->attachment)) {
                Storage::delete('public/' . $ticket->attachment);
            }

            // Update database
            $ticket->attachment = null;
            $ticket->save();

            return response()->json([
                'success' => true,
                'message' => 'Attachment deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting attachment: ' . $e->getMessage()
            ], 500);
        }
    }
}