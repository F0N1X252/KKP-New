@extends('layouts.admin')

@section('page-title', 'Edit Ticket #' . $ticket->id)

@section('styles')
<!-- SweetAlert2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<!-- Lightbox CSS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css" rel="stylesheet">
<style>
    /* --- LIGHT MODE VARIABLES --- */
    :root[data-bs-theme="light"] {
        --ticket-bg: #ffffff;
        --ticket-border: #e5e7eb;
        --ticket-text: #1f2937;
        --ticket-text-muted: #6b7280;
        --ticket-input-bg: #ffffff;
        --ticket-input-border: #e5e7eb;
        --ticket-shadow: 0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -1px rgba(0,0,0,0.03);
    }

    /* --- DARK MODE VARIABLES --- */
    :root[data-bs-theme="dark"] {
        --ticket-bg: #1f2937;
        --ticket-border: #374151;
        --ticket-text: #f8fafc;
        --ticket-text-muted: #94a3b8;
        --ticket-input-bg: #374151;
        --ticket-input-border: #4b5563;
        --ticket-shadow: 0 4px 6px -1px rgba(0,0,0,0.3), 0 2px 4px -1px rgba(0,0,0,0.2);
    }

    /* --- EDITOR STYLES --- */
    .ck-editor__editable {
        min-height: 200px;
        max-height: 600px;
        background: var(--ticket-input-bg);
        color: var(--ticket-text);
        border: 1px solid var(--ticket-input-border);
    }

    .ck-editor__editable:focus {
        border-color: #4f46e5;
        box-shadow: 0 0 0 0.2rem rgba(79, 70, 229, 0.1);
    }

    .ck-toolbar {
        background: var(--ticket-bg);
        border-bottom: 1px solid var(--ticket-border);
    }

    .ck-toolbar .ck-toolbar__items > :not(.ck-dropdown):not(.ck-splitbutton):not(.ck-button:disabled) > .ck-button:not(:disabled):not(.ck-on):hover {
        background: rgba(79, 70, 229, 0.1);
    }

    /* --- DARK MODE TEXT --- */
    [data-bs-theme="dark"] .ck-editor__editable {
        color: var(--ticket-text);
    }

    [data-bs-theme="dark"] .ck-toolbar {
        color: var(--ticket-text-muted);
    }

    [data-bs-theme="dark"] .required:after {
        content: " *";
        color: #ef4444;
    }

    /* --- BREADCRUMB DARK MODE --- */
    [data-bs-theme="dark"] .breadcrumb {
        --bs-breadcrumb-bg: transparent;
    }

    [data-bs-theme="dark"] .breadcrumb-item a {
        color: #60a5fa;
    }

    [data-bs-theme="dark"] .text-dark {
        color: var(--ticket-text) !important;
    }

    [data-bs-theme="dark"] .text-muted {
        color: var(--ticket-text-muted) !important;
    }

    /* --- CARD DARK MODE --- */
    [data-bs-theme="dark"] .card {
        background: var(--ticket-bg);
        border-color: var(--ticket-border);
        color: var(--ticket-text);
    }

    [data-bs-theme="dark"] .card-header {
        background: var(--ticket-bg);
        border-bottom-color: var(--ticket-border);
        color: var(--ticket-text);
    }

    [data-bs-theme="dark"] .card-body {
        background: var(--ticket-bg);
        color: var(--ticket-text);
    }

    /* --- FORM CONTROLS DARK MODE --- */
    [data-bs-theme="dark"] .form-control,
    [data-bs-theme="dark"] .form-select {
        background: var(--ticket-input-bg);
        border-color: var(--ticket-input-border);
        color: var(--ticket-text);
    }

    [data-bs-theme="dark"] .form-control:focus,
    [data-bs-theme="dark"] .form-select:focus {
        background: var(--ticket-input-bg);
        border-color: #4f46e5;
        color: var(--ticket-text);
        box-shadow: 0 0 0 0.2rem rgba(79, 70, 229, 0.1);
    }

    [data-bs-theme="dark"] .form-label {
        color: var(--ticket-text);
    }

    /* --- INPUT GROUP DARK MODE --- */
    [data-bs-theme="dark"] .input-group-text {
        background: var(--ticket-input-bg);
        border-color: var(--ticket-input-border);
        color: var(--ticket-text-muted);
    }

    /* --- BADGE & TEXT HELPERS --- */
    [data-bs-theme="dark"] .badge {
        background-color: rgba(79, 70, 229, 0.2);
        color: #93c5fd;
    }

    /* --- FILE CONTAINER DARK MODE --- */
    [data-bs-theme="dark"] .bg-light {
        background-color: #374151 !important;
        color: var(--ticket-text);
    }

    [data-bs-theme="dark"] .current-file-container {
        border-color: var(--ticket-border);
    }

    /* --- VIDEO PLAYER STYLES --- */
    .edit-video-preview {
        width: 100%;
        max-width: 450px;
        border-radius: 8px;
        background: #000;
        margin-top: 15px;
        display: block;
        position: relative;
        z-index: 50;
        pointer-events: auto;
        cursor: pointer;
        box-shadow: var(--ticket-shadow);
    }

    /* --- CONTAINER STYLES --- */
    .current-file-container {
        position: relative;
        z-index: 1;
    }

    /* --- REQUIRED FIELD INDICATOR --- */
    .required:after {
        content: " *";
        color: #ef4444;
    }

    /* --- STICKY SIDEBAR DARK MODE --- */
    [data-bs-theme="dark"] .sticky-top {
        background: var(--ticket-bg);
    }

    /* --- BUTTON DARK MODE --- */
    [data-bs-theme="dark"] .btn-white {
        background: var(--ticket-input-bg);
        border-color: var(--ticket-border);
        color: var(--ticket-text);
    }

    [data-bs-theme="dark"] .btn-white:hover {
        background: #4b5563;
        border-color: #6b7280;
        color: var(--ticket-text);
    }

    [data-bs-theme="dark"] .btn-outline-secondary {
        color: var(--ticket-text-muted);
        border-color: var(--ticket-border);
    }

    [data-bs-theme="dark"] .btn-outline-secondary:hover {
        background: #4b5563;
        border-color: #6b7280;
        color: var(--ticket-text);
    }

    /* --- HR DARK MODE --- */
    [data-bs-theme="dark"] hr {
        border-color: var(--ticket-border);
        opacity: 0.5;
    }

    /* --- SMALL TEXT DARK MODE --- */
    [data-bs-theme="dark"] .small,
    [data-bs-theme="dark"] .form-text {
        color: var(--ticket-text-muted);
    }

    /* --- ICON DARK MODE --- */
    [data-bs-theme="dark"] .bi {
        color: inherit;
    }

    /* --- SELECT2 DARK MODE INTEGRATION --- */
    [data-bs-theme="dark"] .select2-container--bootstrap-5 .select2-selection {
        background-color: var(--ticket-input-bg);
        border-color: var(--ticket-input-border);
    }

    [data-bs-theme="dark"] .select2-container--bootstrap-5.select2-container--open .select2-selection {
        border-color: #4f46e5;
    }

    [data-bs-theme="dark"] .select2-dropdown {
        background-color: var(--ticket-bg);
        border-color: var(--ticket-border);
    }

    [data-bs-theme="dark"] .select2-results__option {
        color: var(--ticket-text);
    }

    [data-bs-theme="dark"] .select2-results__option--highlighted {
        background-color: rgba(79, 70, 229, 0.2);
    }

    /* --- COLLAPSE STYLES --- */
    [data-bs-theme="dark"] .bg-light {
        background-color: #1f2937 !important;
    }

    [data-bs-theme="dark"] .collapsed-card .card-header {
        background: #374151;
    }

    /* --- ERROR FEEDBACK --- */
    [data-bs-theme="dark"] .invalid-feedback {
        color: #fca5a5;
    }

    [data-bs-theme="dark"] .is-invalid,
    [data-bs-theme="dark"] .is-invalid:focus {
        border-color: #ef4444;
        background-color: var(--ticket-input-bg);
    }

    /* --- SMOOTH TRANSITIONS --- */
    .form-control, .form-select, .btn, .card {
        transition: all 0.2s ease;
    }
</style>
@endsection

@section('content')
<div class="container-fluid p-0">

    <!-- Header & Actions -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent p-0 m-0 small text-muted">
                    <li class="breadcrumb-item"><a href="{{ route('admin.home') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.tickets.index') }}" class="text-decoration-none">Tickets</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit #{{ $ticket->id }}</li>
                </ol>
            </nav>
            <h3 class="fw-bold text-dark mb-0 mt-1">Edit Ticket</h3>
        </div>
        
        <div class="d-flex gap-2">
            <a href="{{ route('admin.tickets.index') }}" class="btn btn-white border shadow-sm">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
            <a href="{{ route('admin.tickets.show', $ticket->id) }}" class="btn btn-white border shadow-sm text-primary">
                <i class="bi bi-eye me-1"></i> View Ticket
            </a>
        </div>
    </div>

    <!-- Form dengan ID khusus untuk AJAX -->
    <form id="ticketEditForm" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <!-- LEFT COLUMN: Main Form -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-pencil-square me-2 text-primary"></i>Ticket Details</h6>
                    </div>
                    
                    <div class="card-body p-4">
                        <!-- Title Field -->
                        <div class="mb-4">
                            <label class="form-label fw-bold required">{{ trans('cruds.ticket.fields.title') }}</label>
                            <input type="text" class="form-control form-control-lg" id="title" name="title" value="{{ old('title', $ticket->title) }}" required>
                            <div class="invalid-feedback" id="error-title"></div>
                        </div>

                        <!-- Content Field (CKEditor) -->
                        <div class="mb-4">
                            <label class="form-label fw-bold required">{{ trans('cruds.ticket.fields.content') }}</label>
                            <textarea name="content" id="editor" class="form-control">{{ old('content', $ticket->content) }}</textarea>
                            <div class="invalid-feedback d-block" id="error-content"></div>
                        </div>

                        <!-- Attachment Section -->
                        <div class="mb-2">
                            <label class="form-label fw-bold">{{ trans('cruds.ticket.fields.attachment') }}</label>
                            
                            <!-- Existing File Preview -->
                            @if($ticket->attachment)
                                <div class="mb-3 p-3 bg-light rounded-3 border current-file-container" id="currentAttachmentSection">
                                    <div class="d-flex flex-column">
                                        <!-- Header Info File -->
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary bg-opacity-10 text-primary p-2 rounded me-3">
                                                    @php
                                                        $ext = strtolower(pathinfo($ticket->attachment, PATHINFO_EXTENSION));
                                                        $isVid = in_array($ext, ['mp4', 'webm', 'ogg', 'mov', 'qt']);
                                                        $isImg = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                                        $isPdf = $ext === 'pdf';
                                                        $fileUrl = asset('storage/' . $ticket->attachment);
                                                    @endphp
                                                    <i class="bi {{ $isVid ? 'bi-film' : ($isImg ? 'bi-image' : ($isPdf ? 'bi-file-pdf' : 'bi-file-earmark-check')) }} fs-4"></i>
                                                </div>
                                                <div>
                                                    <div class="small text-muted text-uppercase fw-bold">Current File</div>
                                                    <!-- Link Download -->
                                                    <a href="{{ $fileUrl }}" target="_blank" class="fw-bold text-dark text-decoration-none hover-primary">
                                                        {{ basename($ticket->attachment) }}
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="d-flex gap-2">
                                                <span class="badge bg-success bg-opacity-10 text-success">Uploaded</span>
                                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteAttachmentEdit({{ $ticket->id }})" data-bs-toggle="tooltip" title="Delete Attachment">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- PREVIEW IMAGE -->
                                        @if($isImg)
                                            <div class="mt-2 position-relative" style="z-index: 50;">
                                                <a href="{{ $fileUrl }}" target="_blank" data-lightbox="ticket-edit">
                                                    <img src="{{ $fileUrl }}" class="img-fluid rounded border" alt="Attachment" style="max-height: 300px; width: 100%; object-fit: cover;">
                                                    <div class="position-absolute top-0 end-0 m-2">
                                                        <span class="badge bg-dark bg-opacity-75">
                                                            <i class="bi bi-zoom-in me-1"></i> Click to enlarge
                                                        </span>
                                                    </div>
                                                </a>
                                            </div>
                                        @endif

                                        <!-- PREVIEW VIDEO PLAYER -->
                                        @if($isVid)
                                            <div class="mt-2 position-relative" style="z-index: 50;">
                                                <video 
                                                    controls 
                                                    playsinline 
                                                    preload="metadata" 
                                                    class="edit-video-preview"
                                                    onclick="this.paused ? this.play() : this.pause();"
                                                >
                                                    <source src="{{ $fileUrl }}" type="video/{{ in_array($ext, ['mov', 'qt']) ? 'mp4' : $ext }}">
                                                    Your browser does not support the video tag.
                                                </video>
                                                <div class="text-muted small mt-1 fst-italic">
                                                    <i class="bi bi-play-circle me-1"></i> Click video to play/pause preview
                                                </div>
                                            </div>
                                        @endif

                                        <!-- PREVIEW PDF -->
                                        @if($isPdf)
                                            <div class="mt-2 text-center py-4 bg-white rounded border">
                                                <i class="bi bi-file-pdf text-danger fs-1"></i>
                                                <div class="mt-2 fw-bold">PDF Document</div>
                                                <small class="text-muted">{{ basename($ticket->attachment) }}</small>
                                                <div class="mt-2">
                                                    <a href="{{ $fileUrl }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-eye me-1"></i> Preview PDF
                                                    </a>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <!-- Upload New File -->
                            <div class="input-group">
                                <input type="file" class="form-control" name="attachment" id="attachmentInput" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.mp4,.webm,.ogg">
                                <label class="input-group-text bg-white text-muted" for="attachmentInput">
                                    <i class="bi bi-cloud-upload me-2"></i> {{ $ticket->attachment ? 'Replace File' : 'Upload File' }}
                                </label>
                            </div>
                            <div class="invalid-feedback d-block" id="error-attachment"></div>
                            <div class="form-text text-muted small mt-2">
                                <i class="bi bi-info-circle"></i> 
                                @if($ticket->attachment)
                                    Uploading a new file will replace the current one. Or delete the current file first.
                                @else
                                    Allowed files: JPG, PNG, PDF, DOC, MP4, WebM (Max: 10MB)
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Author Info -->
                <div class="card border-0 shadow-sm rounded-4 collapsed-card">
                    <div class="card-header bg-light border-bottom py-3" data-bs-toggle="collapse" href="#authorCollapse" role="button" aria-expanded="false">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold text-muted"><i class="bi bi-person me-2"></i>Author Information</h6>
                            <i class="bi bi-chevron-down small"></i>
                        </div>
                    </div>
                    <div class="collapse" id="authorCollapse">
                        <div class="card-body p-4">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold text-uppercase text-muted">{{ trans('cruds.ticket.fields.author_name') }}</label>
                                    <input type="text" class="form-control" name="author_name" value="{{ old('author_name', $ticket->author_name) }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold text-uppercase text-muted">{{ trans('cruds.ticket.fields.author_email') }}</label>
                                    <input type="email" class="form-control" name="author_email" value="{{ old('author_email', $ticket->author_email) }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN: Properties -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 sticky-top" style="top: 80px; z-index: 10;">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-sliders me-2 text-primary"></i>Properties</h6>
                    </div>
                    <div class="card-body p-4">
                        
                        <!-- Status -->
                        <div class="mb-4">
                            <label class="form-label fw-bold required">{{ trans('cruds.ticket.fields.status') }}</label>
                            <select name="status_id" id="status_id" class="form-select select2" required>
                                @foreach($statuses as $id => $status)
                                    <option value="{{ $id }}" {{ (old('status_id') ? old('status_id') : $ticket->status->id ?? '') == $id ? 'selected' : '' }}>
                                        {{ $status }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback d-block" id="error-status_id"></div>
                        </div>

                        <!-- Priority -->
                        <div class="mb-4">
                            <label class="form-label fw-bold required">{{ trans('cruds.ticket.fields.priority') }}</label>
                            <select name="priority_id" id="priority_id" class="form-select select2" required>
                                @foreach($priorities as $id => $priority)
                                    <option value="{{ $id }}" {{ (old('priority_id') ? old('priority_id') : $ticket->priority->id ?? '') == $id ? 'selected' : '' }}>
                                        {{ $priority }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback d-block" id="error-priority_id"></div>
                        </div>

                        <!-- Category -->
                        <div class="mb-4">
                            <label class="form-label fw-bold required">{{ trans('cruds.ticket.fields.category') }}</label>
                            <select name="category_id" id="category_id" class="form-select select2" required>
                                @foreach($categories as $id => $category)
                                    <option value="{{ $id }}" {{ (old('category_id') ? old('category_id') : $ticket->category->id ?? '') == $id ? 'selected' : '' }}>
                                        {{ $category }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback d-block" id="error-category_id"></div>
                        </div>

                        <!-- Assigned To -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">{{ trans('cruds.ticket.fields.assigned_to_user') }}</label>
                            <select name="assigned_to_user_id" class="form-select select2">
                                <option value="">Unassigned</option>
                                @foreach($assigned_to_users as $id => $user)
                                    <option value="{{ $id }}" {{ (old('assigned_to_user_id') ? old('assigned_to_user_id') : $ticket->assigned_to_user->id ?? '') == $id ? 'selected' : '' }}>
                                        {{ $user }}
                                    </option>
                                @endforeach
                            </select>
                            @error('assigned_to_user_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <hr class="my-4">

                        <!-- Action Buttons -->
                        <div class="d-grid gap-2">
                            <button type="submit" id="btnUpdate" class="btn btn-primary btn-lg shadow-sm">
                                <span id="btnText"><i class="bi bi-save me-2"></i> Save Changes</span>
                                <span id="btnSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            </button>
                            <a href="{{ route('admin.tickets.index') }}" class="btn btn-outline-secondary">
                                Cancel
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Lightbox JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>

<script>
    let editorInstance;

    document.addEventListener("DOMContentLoaded", function() {
        // 1. Init CKEditor 5 dengan Media Embed
        if (typeof ClassicEditor !== 'undefined') {
            ClassicEditor
                .create(document.querySelector('#editor'), {
                    // Konfigurasi Toolbar
                    toolbar: [
                        'heading', '|', 
                        'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 
                        '|', 'mediaEmbed', '|', 
                        'undo', 'redo'
                    ],
                    mediaEmbed: {
                        previewsInData: true 
                    }
                })
                .then(editor => {
                    editorInstance = editor;
                })
                .catch(error => {
                    console.error(error);
                });
        }

        // 2. Init Select2
        if ($.fn.select2) {
            $('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: "Select an option"
            });
        }

        // 3. HANDLE AJAX UPDATE
        $('#ticketEditForm').on('submit', function(e) {
            e.preventDefault();

            // Clear previous errors
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').text('');

            // Loading state
            let btn = $('#btnUpdate');
            let btnText = $('#btnText');
            let btnSpinner = $('#btnSpinner');
            
            btn.prop('disabled', true);
            btnText.text('Updating...');
            btnSpinner.removeClass('d-none');

            // Sync CKEditor data
            if (editorInstance) {
                editorInstance.updateSourceElement();
            }

            let formData = new FormData(this);

            $.ajax({
                url: "{{ route('admin.tickets.update', $ticket->id) }}",
                method: "POST", 
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    if(response.success) {
                        Swal.fire({
                            title: 'Success!',
                            text: response.message,
                            icon: 'success',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#4f46e5',
                            allowOutsideClick: false
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = response.redirect_url;
                            }
                        });
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false);
                    btnText.html('<i class="bi bi-save me-2"></i> Save Changes');
                    btnSpinner.addClass('d-none');

                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            let input = $('[name="'+key+'"]');
                            let errorDiv = $('#error-'+key);
                            
                            if(key === 'content') {
                                $('.ck.ck-editor').addClass('border border-danger');
                            } else {
                                input.addClass('is-invalid');
                            }
                            
                            if(input.hasClass('select2-hidden-accessible')) {
                                input.next('.select2-container').find('.select2-selection').addClass('border-danger');
                            }

                            if(errorDiv.length) {
                                errorDiv.text(value[0]);
                            }
                        });

                        Swal.fire({
                            title: 'Validation Error',
                            text: 'Please check the form fields marked in red.',
                            icon: 'error',
                            confirmButtonColor: '#d33',
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Something went wrong. Please try again.',
                            icon: 'error',
                            confirmButtonColor: '#d33',
                        });
                    }
                }
            });
        });

        // Remove error state on change
        $('input, textarea, select').on('input change', function() {
            $(this).removeClass('is-invalid');
            if($(this).hasClass('select2-hidden-accessible')) {
                $(this).next('.select2-container').find('.select2-selection').removeClass('border-danger');
            }
        });
    });

    // Global function for delete attachment in edit page
    function deleteAttachmentEdit(ticketId) {
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
                            
                            // Remove attachment section
                            $('#currentAttachmentSection').remove();
                            
                            // Show file input for new upload
                            $('#attachmentInput').prop('disabled', false);
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
</script>
@endsection