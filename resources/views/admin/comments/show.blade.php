@extends('layouts.admin')

@section('page-title', 'View Comment #' . $comment->id)

@section('styles')
<!-- SweetAlert2 -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<style>
    /* --- AVATAR STYLE --- */
    .avatar-lg {
        width: 80px;
        height: 80px;
        font-size: 2rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        background: linear-gradient(135deg, var(--primary-color), #818cf8);
        color: white;
    }

    /* --- COMMENT CONTENT --- */
    .comment-body {
        font-size: 1rem;
        line-height: 1.7;
        color: var(--text-main);
    }
    
    /* Handling CKEditor/HTML Content */
    .comment-content img { max-width: 100%; border-radius: 8px; margin: 10px 0; }
    .comment-content blockquote { border-left: 4px solid #e5e7eb; padding-left: 1rem; font-style: italic; color: #6b7280; }
    .comment-content pre { background: #1e293b; color: #f8fafc; padding: 15px; border-radius: 8px; }

    /* --- LABELS --- */
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

    /* --- TICKET CARD --- */
    .ticket-link-card {
        transition: all 0.2s;
        border: 1px solid var(--border-color);
        background: var(--bs-body-bg);
    }
    .ticket-link-card:hover {
        background: rgba(79, 70, 229, 0.05);
        border-color: var(--primary-color);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }

    /* Dark Mode Adjustments */
    [data-bs-theme="dark"] .bg-light {
        background-color: #1f2937 !important;
    }
    [data-bs-theme="dark"] .comment-content blockquote { border-left-color: #4b5563; color: #9ca3af; }
</style>
@endsection

@section('content')
<div class="container-fluid p-0">

    <!-- 1. Header & Actions -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent p-0 m-0 small text-muted">
                    <li class="breadcrumb-item"><a href="{{ route('admin.home') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.comments.index') }}" class="text-decoration-none">Comments</a></li>
                    <li class="breadcrumb-item active">Detail #{{ $comment->id }}</li>
                </ol>
            </nav>
            <h3 class="fw-bold text-dark mb-0 mt-1">Comment Details</h3>
        </div>
        
        <div class="d-flex gap-2">
            <a href="{{ route('admin.comments.index') }}" class="btn btn-light border shadow-sm">
                <i class="bi bi-arrow-left me-1"></i> Back to List
            </a>
            
            <div class="btn-group shadow-sm">
                @can('comment_edit')
                <a href="{{ route('admin.comments.edit', $comment->id) }}" class="btn btn-white border text-warning">
                    <i class="bi bi-pencil-square me-1"></i> Edit
                </a>
                @endcan

                @can('comment_delete')
                <button type="button" class="btn btn-white border text-danger delete-btn" data-id="{{ $comment->id }}">
                    <i class="bi bi-trash me-1"></i> Delete
                </button>
                @endcan
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- LEFT COLUMN: Author & Context -->
        <div class="col-lg-4">
            
            <!-- Author Profile Card -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4 text-center">
                    <div class="d-flex justify-content-center mb-3">
                        <div class="avatar-lg">
                            {{ substr($comment->author_name, 0, 1) }}
                        </div>
                    </div>
                    
                    <h5 class="fw-bold mb-1">{{ $comment->author_name }}</h5>
                    <p class="text-muted mb-3">{{ $comment->author_email }}</p>
                    
                    @if($comment->user)
                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-10 rounded-pill px-3 py-2">
                            <i class="bi bi-patch-check-fill me-1"></i> Registered User
                        </span>
                    @else
                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-10 rounded-pill px-3 py-2">
                            Guest / External
                        </span>
                    @endif
                </div>
                <div class="card-footer bg-light border-top p-3">
                    <div class="row text-center">
                        <div class="col-6 border-end">
                            <div class="info-label">Comment ID</div>
                            <div class="fw-bold">#{{ $comment->id }}</div>
                        </div>
                        <div class="col-6">
                            <div class="info-label">Created</div>
                            <div class="fw-bold" data-bs-toggle="tooltip" title="{{ $comment->created_at }}">
                                {{ $comment->created_at->format('d M Y') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ticket Context Card -->
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-ticket-perforated me-2 text-primary"></i>Related Ticket</h6>
                </div>
                <div class="card-body p-4">
                    @if($comment->ticket)
                        <a href="{{ route('admin.tickets.show', $comment->ticket->id) }}" class="text-decoration-none text-dark">
                            <div class="ticket-link-card p-3 rounded-3 d-flex align-items-center">
                                <div class="bg-primary bg-opacity-10 text-primary p-2 rounded me-3">
                                    <i class="bi bi-box-arrow-up-right fs-4"></i>
                                </div>
                                <div class="overflow-hidden">
                                    <div class="fw-bold text-truncate">{{ $comment->ticket->title }}</div>
                                    <small class="text-muted">Ticket #{{ $comment->ticket->id }}</small>
                                </div>
                                <i class="bi bi-chevron-right ms-auto text-muted"></i>
                            </div>
                        </a>
                        <div class="mt-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="small text-muted">Ticket Status:</span>
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-10">
                                    {{ $comment->ticket->status->name ?? 'Unknown' }}
                                </span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="small text-muted">Priority:</span>
                                <span class="fw-bold small d-flex align-items-center" style="color: {{ $comment->ticket->priority->color ?? '#000' }}">
                                    <span style="width:8px; height:8px; border-radius:50%; background-color:{{ $comment->ticket->priority->color ?? '#000' }}; margin-right:6px; display:inline-block;"></span>
                                    {{ $comment->ticket->priority->name ?? 'None' }}
                                </span>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-danger border-0 bg-danger bg-opacity-10 text-danger d-flex align-items-center mb-0">
                            <i class="bi bi-exclamation-circle-fill me-2"></i>
                            <small>Ticket deleted or unavailable.</small>
                        </div>
                    @endif
                </div>
            </div>

        </div>

        <!-- RIGHT COLUMN: Comment Content -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-primary">
                        <i class="bi bi-chat-left-quote-fill me-2"></i>Comment Content
                    </h6>
                    <small class="text-muted d-flex align-items-center">
                        <i class="bi bi-clock me-1"></i> Posted {{ $comment->created_at->diffForHumans() }}
                    </small>
                </div>
                <div class="card-body p-4">
                    <div class="bg-light p-4 rounded-3 border border-light-subtle h-100">
                        <!-- Class 'comment-content' handles styling for HTML elements from CKEditor -->
                        <div class="comment-body comment-content">
                            {!! $comment->comment_text !!}
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white border-top p-3 text-end">
                    <small class="text-muted fst-italic">
                        <i class="bi bi-pencil-square me-1"></i> Last updated: {{ $comment->updated_at->format('d M Y, H:i:s') }}
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden Form for Delete -->
    <form id="deleteForm" action="{{ route('admin.comments.destroy', $comment->id) }}" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Init Tooltip
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        // Delete Confirmation
        const deleteBtn = document.querySelector('.delete-btn');
        if(deleteBtn){
            deleteBtn.addEventListener('click', function() {
                Swal.fire({
                    title: 'Delete Comment?',
                    text: "This action cannot be undone!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#9ca3af',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('deleteForm').submit();
                    }
                });
            });
        }
    });
</script>
@endsection