@extends('layouts.admin')

@section('page-title', 'Ticket Details #' . $ticket->id)

@section('styles')
<!-- SweetAlert2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<style>
    /* --- CONTENT & TYPOGRAPHY --- */
    .ticket-content {
        font-size: 1rem;
        line-height: 1.7;
        color: var(--text-main);
    }
    
    /* CKEditor Content Reset */
    .ck-content blockquote {
        border-left: 4px solid #e5e7eb;
        padding-left: 1rem;
        font-style: italic;
        color: #6b7280;
    }
    .ck-content img {
        max-width: 100%;
        border-radius: 8px;
        margin: 10px 0;
    }

    /* --- ATTACHMENTS --- */
    .attachment-card {
        border: 1px solid var(--border-color);
        border-radius: 12px;
        transition: all 0.2s ease;
        background: var(--card-bg);
        overflow: hidden;
    }
    .attachment-card:hover {
        border-color: var(--primary-color);
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        transform: translateY(-2px);
    }
    .video-preview {
        width: 100%;
        border-radius: 8px;
        background: #000;
        max-height: 300px;
    }

    /* --- COMMENTS TIMELINE --- */
    .timeline-wrapper {
        position: relative;
        padding-left: 20px;
    }
    .timeline-wrapper::before {
        content: '';
        position: absolute;
        top: 0;
        bottom: 0;
        left: 20px;
        width: 2px;
        background: #e5e7eb;
        z-index: 0;
    }
    [data-bs-theme="dark"] .timeline-wrapper::before { background: #374151; }

    .comment-card {
        position: relative;
        z-index: 1;
        border: 1px solid var(--border-color);
        border-radius: 12px;
        background: var(--card-bg);
        transition: all 0.2s;
    }
    .comment-card.is-admin {
        border-left: 4px solid var(--primary-color);
        background: rgba(79, 70, 229, 0.02);
    }
    [data-bs-theme="dark"] .comment-card.is-admin { background: rgba(79, 70, 229, 0.05); }

    .avatar-circle {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1rem;
        border: 2px solid var(--card-bg);
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    /* --- SIDEBAR INFO --- */
    .info-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--text-muted);
        font-weight: 600;
        margin-bottom: 4px;
    }
    .info-value {
        font-size: 0.95rem;
        font-weight: 500;
        color: var(--text-main);
    }

    .status-dot {
        height: 10px;
        width: 10px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 6px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid p-0">
    
    <!-- 1. Header Navigation & Actions -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent p-0 m-0 small text-muted">
                    <li class="breadcrumb-item"><a href="{{ route('admin.home') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.tickets.index') }}" class="text-decoration-none">Tickets</a></li>
                    <li class="breadcrumb-item active">Ticket #{{ $ticket->id }}</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-2 mt-1">
                <h3 class="fw-bold text-dark mb-0">{{ $ticket->title }}</h3>
                @php
                    $statusColor = match(strtolower($ticket->status->name ?? '')) {
                        'open' => 'success',
                        'closed' => 'secondary',
                        'pending' => 'warning',
                        default => 'primary'
                    };
                @endphp
                <span class="badge bg-{{ $statusColor }} bg-opacity-10 text-{{ $statusColor }} border border-{{ $statusColor }} border-opacity-20 rounded-pill px-3 ms-2">
                    {{ $ticket->status->name ?? 'Unknown' }}
                </span>
            </div>
        </div>
        
        <div class="d-flex gap-2">
            <a href="{{ route('admin.tickets.index') }}" class="btn btn-light border shadow-sm">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
            
            <div class="btn-group shadow-sm">
                @can('ticket_edit')
                <a href="{{ route('admin.tickets.edit', $ticket->id) }}" class="btn btn-white border text-primary">
                    <i class="bi bi-pencil-square me-1"></i> Edit
                </a>
                @endcan
                
                <a href="{{ route('admin.tickets.exportDetail', $ticket->id) }}" class="btn btn-white border text-success">
                    <i class="bi bi-file-earmark-arrow-down me-1"></i> Export
                </a>

                @can('ticket_delete')
                <button type="button" class="btn btn-white border text-danger delete-ticket-btn" data-id="{{ $ticket->id }}">
                    <i class="bi bi-trash"></i>
                </button>
                @endcan
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- LEFT COLUMN: Ticket Content & Discussion -->
        <div class="col-lg-8">
            
            <!-- Ticket Description Card -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                    <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-file-text me-2"></i>Description</h6>
                    <span class="text-muted small"><i class="bi bi-clock me-1"></i> {{ $ticket->created_at->format('d M Y, H:i') }}</span>
                </div>
                <div class="card-body p-4">
                    <div class="ticket-content ck-content">
                        {!! $ticket->content !!}
                    </div>

                    <!-- Attachments Section -->
                    @if($ticket->attachment)
                        <div class="mt-4 pt-4 border-top" id="attachmentSection">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="fw-bold mb-0 small text-uppercase text-muted">
                                    <i class="bi bi-paperclip me-1"></i> Attachment
                                </h6>
                                @can('ticket_edit')
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteAttachment({{ $ticket->id }})" data-bs-toggle="tooltip" title="Delete Attachment">
                                        <i class="bi bi-trash me-1"></i> Delete
                                    </button>
                                @endcan
                            </div>
                            
                            @php
                                // Handle JSON or String attachment
                                $files = is_string($ticket->attachment) && is_array(json_decode($ticket->attachment, true)) 
                                    ? json_decode($ticket->attachment, true) 
                                    : [$ticket->attachment];
                            @endphp

                            <div class="row g-3">
                                @foreach($files as $file)
                                    @php
                                        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                                        $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                        $isVideo = in_array($ext, ['mp4', 'webm', 'ogg', 'mov']);
                                        $isPdf = $ext === 'pdf';
                                        $fileUrl = asset('storage/' . $file);
                                        $fileSize = file_exists(storage_path('app/public/' . $file)) 
                                            ? number_format(filesize(storage_path('app/public/' . $file)) / 1024, 2) 
                                            : 0;
                                    @endphp

                                    <div class="col-md-6">
                                        <div class="attachment-card p-3">
                                            <!-- Image Preview -->
                                            @if($isImage)
                                                <div class="position-relative">
                                                    <a href="{{ $fileUrl }}" target="_blank" data-lightbox="ticket-{{ $ticket->id }}">
                                                        <img src="{{ $fileUrl }}" class="img-fluid rounded mb-2 border" alt="Attachment" style="max-height: 300px; width: 100%; object-fit: cover;">
                                                        <div class="position-absolute top-0 end-0 m-2">
                                                            <span class="badge bg-dark bg-opacity-75">
                                                                <i class="bi bi-zoom-in me-1"></i> Click to enlarge
                                                            </span>
                                                        </div>
                                                    </a>
                                                </div>
                                            
                                            <!-- Video Preview -->
                                            @elseif($isVideo)
                                                <div class="position-relative">
                                                    <video controls class="video-preview mb-2" preload="metadata" style="width: 100%; max-height: 300px;">
                                                        <source src="{{ $fileUrl }}" type="video/{{ $ext == 'mov' ? 'mp4' : $ext }}">
                                                        Your browser does not support the video tag.
                                                    </video>
                                                    <div class="position-absolute top-0 start-0 m-2">
                                                        <span class="badge bg-dark bg-opacity-75">
                                                            <i class="bi bi-play-circle me-1"></i> Video
                                                        </span>
                                                    </div>
                                                </div>
                                            
                                            <!-- PDF Preview -->
                                            @elseif($isPdf)
                                                <div class="text-center py-4 bg-light rounded mb-2">
                                                    <i class="bi bi-file-pdf text-danger fs-1"></i>
                                                    <div class="mt-2 fw-bold">PDF Document</div>
                                                    <small class="text-muted">{{ $fileSize }} KB</small>
                                                </div>
                                            
                                            <!-- Other Files -->
                                            @else
                                                <div class="d-flex align-items-center mb-2 p-3 bg-light rounded">
                                                    <div class="bg-primary bg-opacity-10 text-primary p-3 rounded me-3">
                                                        <i class="bi bi-file-earmark-text fs-2"></i>
                                                    </div>
                                                    <div class="overflow-hidden flex-grow-1">
                                                        <div class="fw-bold text-truncate">{{ basename($file) }}</div>
                                                        <small class="text-uppercase text-muted">{{ $ext }} File • {{ $fileSize }} KB</small>
                                                    </div>
                                                </div>
                                            @endif

                                            <!-- File Actions -->
                                            <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">
                                                <div class="text-truncate flex-grow-1 me-2">
                                                    <small class="text-muted d-block">
                                                        <i class="bi bi-file-earmark me-1"></i>
                                                        {{ basename($file) }}
                                                    </small>
                                                </div>
                                                <div class="d-flex gap-1">
                                                    <!-- Preview Button (for PDF and images) -->
                                                    @if($isPdf || $isImage)
                                                        <a href="{{ $fileUrl }}" target="_blank" class="btn btn-sm btn-light border text-primary" data-bs-toggle="tooltip" title="Preview">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                    @endif
                                                    
                                                    <!-- Download Button -->
                                                    <a href="{{ $fileUrl }}" download class="btn btn-sm btn-light border text-success" data-bs-toggle="tooltip" title="Download">
                                                        <i class="bi bi-download"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="mt-4 pt-4 border-top">
                            <div class="text-center py-4 text-muted">
                                <i class="bi bi-paperclip fs-1 opacity-25"></i>
                                <p class="mb-0 mt-2">No attachment</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Comments Section -->
            <div class="d-flex align-items-center justify-content-between mb-3 mt-5">
                <h5 class="fw-bold m-0"><i class="bi bi-chat-dots-fill me-2 text-secondary"></i>Discussion History</h5>
                <span class="badge bg-secondary rounded-pill">{{ $ticket->comments->count() }} Comments</span>
            </div>

            <div class="timeline-wrapper">
                @forelse($ticket->comments as $comment)
                    @php
                        $isAgent = $comment->user_id && $comment->user && $comment->user->roles->contains(2); // Asumsi Role ID 2 = Agent
                        $isAdmin = $comment->user_id && $comment->user && $comment->user->roles->contains(1);
                        $bgAvatar = $isAdmin ? 'bg-danger' : ($isAgent ? 'bg-primary' : 'bg-secondary');
                        $cardClass = ($isAdmin || $isAgent) ? 'is-admin' : '';
                        
                        // Check if current user can edit/delete this comment
                        $canEditComment = auth()->user() && $comment->user_id && 
                                         (auth()->user()->roles->contains(1) || // Admin can edit any comment
                                          auth()->user()->id == $comment->user_id); // User can edit own comment
                        
                        $canDeleteComment = auth()->user() && $comment->user_id && 
                                           (auth()->user()->roles->contains(1) || // Admin can delete any comment
                                            auth()->user()->id == $comment->user_id); // User can delete own comment
                    @endphp
                    
                    <div class="d-flex mb-4 position-relative">
                        <!-- Avatar -->
                        <div class="avatar-circle {{ $bgAvatar }} text-white flex-shrink-0 position-relative z-1">
                            {{ substr($comment->author_name, 0, 1) }}
                        </div>
                        
                        <!-- Bubble -->
                        <div class="ms-3 flex-grow-1">
                            <div class="comment-card p-3 p-md-4 {{ $cardClass }}">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="fw-bold mb-0 text-dark">
                                            {{ $comment->author_name }}
                                            @if($isAdmin) <i class="bi bi-patch-check-fill text-danger ms-1" title="Admin"></i>
                                            @elseif($isAgent) <i class="bi bi-headset text-primary ms-1" title="Agent"></i>
                                            @endif
                                        </h6>
                                        <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                                    </div>
                                    
                                    <!-- Actions -->
                                    @if($canEditComment || $canDeleteComment)
                                    <div class="dropdown">
                                        <button class="btn btn-link text-muted p-0" type="button" data-bs-toggle="dropdown">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3">
                                            @if($canEditComment && Gate::allows('comment_edit'))
                                            <li>
                                                <button class="dropdown-item edit-comment-btn" 
                                                    data-id="{{ $comment->id }}" 
                                                    data-text="{{ $comment->comment_text }}" 
                                                    data-action="{{ route('admin.comments.update', $comment->id) }}">
                                                    <i class="bi bi-pencil me-2 text-warning"></i> Edit
                                                </button>
                                            </li>
                                            @endif
                                            @if($canDeleteComment && Gate::allows('comment_delete'))
                                            <li>
                                                <form action="{{ route('admin.comments.destroy', $comment->id) }}" method="POST" class="delete-comment-form">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="bi bi-trash me-2"></i> Delete
                                                    </button>
                                                </form>
                                            </li>
                                            @endif
                                        </ul>
                                    </div>
                                    @endif
                                </div>

                                <div class="text-secondary" style="white-space: pre-wrap;">{{ $comment->comment_text }}</div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5 bg-light rounded-4 border border-dashed">
                        <i class="bi bi-chat-square-dots display-4 text-muted opacity-25"></i>
                        <p class="text-muted mt-2">No comments yet. Be the first to reply.</p>
                    </div>
                @endforelse
            </div>

            <!-- Reply Form -->
            <div class="card border-0 shadow-sm rounded-4 mt-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3">Post a Reply</h6>
                    <form action="{{ route('admin.tickets.storeComment', $ticket->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <textarea class="form-control bg-light" name="comment_text" rows="4" placeholder="Write your response here..." required></textarea>
                        </div>
                        <div class="d-flex justify-content-end align-items-center">
                            <button type="submit" class="btn btn-primary px-4 rounded-pill">
                                <i class="bi bi-send-fill me-1"></i> Send Reply
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>

        <!-- RIGHT COLUMN: Sidebar Info -->
        <div class="col-lg-4">
            
            <!-- Requester Info -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="avatar-circle bg-dark text-white me-3" style="width: 50px; height: 50px; font-size: 1.2rem;">
                            {{ substr($ticket->author_name, 0, 1) }}
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0 text-dark">{{ $ticket->author_name }}</h6>
                            <small class="text-muted">Requester</small>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="info-label">Email Address</div>
                        <div class="info-value d-flex align-items-center">
                            <i class="bi bi-envelope me-2 text-muted"></i>
                            <a href="mailto:{{ $ticket->author_email }}" class="text-decoration-none">{{ $ticket->author_email }}</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Properties Info -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0 fw-bold text-dark">Ticket Properties</h6>
                </div>
                <div class="card-body p-4">
                    
                    <!-- Priority -->
                    <div class="mb-4">
                        <div class="info-label">Priority</div>
                        <div class="info-value d-flex align-items-center">
                            <span class="status-dot" style="background-color: {{ $ticket->priority->color ?? '#ccc' }}"></span>
                            {{ $ticket->priority->name ?? 'None' }}
                        </div>
                    </div>

                    <!-- Category -->
                    <div class="mb-4">
                        <div class="info-label">Category</div>
                        <div class="info-value">
                            <span class="badge bg-light text-dark border">
                                {{ $ticket->category->name ?? 'Uncategorized' }}
                            </span>
                        </div>
                    </div>

                    <!-- Assigned Agent -->
                    <div class="mb-4">
                        <div class="info-label">Assigned Agent</div>
                        @if($ticket->assigned_to_user)
                            <div class="d-flex align-items-center mt-2 p-2 bg-light rounded border">
                                <div class="avatar-circle bg-primary bg-opacity-20 text-primary me-2" style="width: 32px; height: 32px; font-size: 0.8rem; border:none;">
                                    {{ substr($ticket->assigned_to_user->name, 0, 1) }}
                                </div>
                                <span class="fw-medium small">{{ $ticket->assigned_to_user->name }}</span>
                            </div>
                        @else
                            <div class="text-muted fst-italic">Unassigned</div>
                        @endif
                    </div>

                    <!-- Dates -->
                    <div class="row">
                        <div class="col-6">
                            <div class="info-label">Created</div>
                            <small class="text-muted">{{ $ticket->created_at->format('M d, Y') }}</small>
                        </div>
                        <div class="col-6">
                            <div class="info-label">Last Update</div>
                            <small class="text-muted">{{ $ticket->updated_at->diffForHumans() }}</small>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

<!-- Modal Edit Comment -->
<div class="modal fade" id="editCommentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="editCommentForm" method="POST" class="w-100">
            @csrf @method('PUT')
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold">Edit Comment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <label class="form-label fw-semibold">Content</label>
                    <textarea class="form-control" id="edit_comment_text" name="comment_text" rows="5" required></textarea>
                    
                    <!-- Hidden inputs to preserve author info if needed by controller validation -->
                    <input type="hidden" name="author_name" value="{{ auth()->user()->name }}">
                    <input type="hidden" name="author_email" value="{{ auth()->user()->email }}">
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
@parent
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Global function for delete attachment (called from onclick)
    function deleteAttachment(ticketId) {
        Swal.fire({
            title: 'Are you sure?',
            text: "This attachment will be permanently deleted!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/admin/tickets/' + ticketId + '/attachment',
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if(response.success) {
                            Swal.fire({
                                title: 'Deleted!',
                                text: response.message,
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            });
                            
                            // Remove attachment section and show "No attachment" message
                            $('#attachmentSection').html('<p class="text-muted mb-0">No attachment</p>');
                        }
                    },
                    error: function(xhr) {
                        let errorMsg = 'Failed to delete attachment';
                        if(xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            title: 'Error!',
                            text: errorMsg,
                            icon: 'error',
                            confirmButtonColor: '#dc3545'
                        });
                    }
                });
            }
        });
    }

    document.addEventListener("DOMContentLoaded", function() {
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // --- 2. Delete Ticket Confirmation ---
        const deleteBtn = document.querySelector('.delete-ticket-btn');
        if(deleteBtn){
            deleteBtn.addEventListener('click', function() {
                let id = this.getAttribute('data-id');
                Swal.fire({
                    title: 'Delete Ticket?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#9ca3af',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Create form programmatically
                        let form = document.createElement('form');
                        form.action = `/admin/tickets/${id}`;
                        form.method = 'POST';
                        form.innerHTML = `@csrf @method('DELETE')`;
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            });
        }

        // --- 3. Edit Comment Modal ---
        const editButtons = document.querySelectorAll('.edit-comment-btn');
        const editModal = new bootstrap.Modal(document.getElementById('editCommentModal'));
        const editForm = document.getElementById('editCommentForm');
        const editTextarea = document.getElementById('edit_comment_text');

        editButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const commentText = this.getAttribute('data-text');
                const actionUrl = this.getAttribute('data-action');

                editTextarea.value = commentText;
                editForm.action = actionUrl;
                editModal.show();
            });
        });

        // --- 4. Delete Comment Confirmation ---
        const deleteCommentForms = document.querySelectorAll('.delete-comment-form');
        deleteCommentForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Delete Comment?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
            });
        });
    });
</script>
@endsection