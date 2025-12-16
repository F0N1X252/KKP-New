@extends('layouts.admin')

@section('page-title', 'Create ' . trans('cruds.ticket.title_singular'))

@section('styles')
<!-- SweetAlert2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<style>
    /* --- EDITOR STYLES --- */
    .ck-editor__editable {
        min-height: 250px;
        max-height: 600px;
        border-bottom-left-radius: 12px !important;
        border-bottom-right-radius: 12px !important;
    }
    .ck-toolbar {
        border-top-left-radius: 12px !important;
        border-top-right-radius: 12px !important;
    }

    /* --- UPLOAD BOX STYLES --- */
    .upload-box {
        transition: all 0.3s ease;
        min-height: 180px;
        border: 2px dashed var(--border-color);
        background-color: rgba(0,0,0,0.01);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }
    .upload-box:hover, .upload-box.dragover {
        background-color: rgba(79, 70, 229, 0.05);
        border-color: var(--primary-color);
    }
    
    .file-preview-item {
        background: #fff;
        border: 1px solid var(--border-color);
        transition: all 0.2s;
    }
    
    /* --- DARK MODE ADJUSTMENTS --- */
    [data-bs-theme="dark"] .file-preview-item {
        background: #1f2937;
        border-color: #374151;
    }
    [data-bs-theme="dark"] .upload-box {
        background-color: rgba(255,255,255,0.02);
    }

    .required:after { content: " *"; color: #ef4444; }
</style>
@endsection

@section('content')
<div class="container-fluid p-0">
    
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent p-0 m-0 small text-muted">
                    <li class="breadcrumb-item"><a href="{{ route('admin.home') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.tickets.index') }}" class="text-decoration-none">Tickets</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Create</li>
                </ol>
            </nav>
            <h3 class="fw-bold text-dark mb-0 mt-1">New Ticket</h3>
        </div>
        <a href="{{ route('admin.tickets.index') }}" class="btn btn-light border shadow-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to List
        </a>
    </div>

    <form id="ticketForm" action="{{ route('admin.tickets.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="row g-4">
            <!-- LEFT COLUMN: Main Content -->
            <div class="col-lg-8">
                
                <!-- Main Ticket Info -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-pencil-square me-2 text-primary"></i>Ticket Details</h6>
                    </div>
                    <div class="card-body p-4">
                        <!-- Title -->
                        <div class="mb-4">
                            <label class="form-label fw-bold required">{{ trans('cruds.ticket.fields.title') }}</label>
                            <input type="text" class="form-control form-control-lg @error('title') is-invalid @enderror" 
                                   name="title" id="title" placeholder="Brief summary of the issue..." value="{{ old('title') }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Content (CKEditor) -->
                        <div class="mb-4">
                            <label class="form-label fw-bold required">{{ trans('cruds.ticket.fields.content') }}</label>
                            <textarea name="content" id="editor" class="form-control @error('content') is-invalid @enderror">{{ old('content') }}</textarea>
                            @error('content')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <div class="form-text text-muted mt-2">
                                <i class="bi bi-info-circle me-1"></i> Please describe the issue in detail. You can paste images directly.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Attachment Card -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-paperclip me-2 text-primary"></i>Attachments</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="upload-box rounded-4 position-relative" id="dropArea">
                            <input type="file" name="attachments[]" id="attachments" class="position-absolute w-100 h-100 opacity-0 cursor-pointer" multiple onchange="handleFiles(this.files)">
                            
                            <div class="text-center p-4" id="uploadPlaceholder">
                                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">
                                    <i class="bi bi-cloud-arrow-up fs-2"></i>
                                </div>
                                <h6 class="fw-bold">Click or Drag files here</h6>
                                <p class="text-muted small mb-0">Supported: JPG, PNG, PDF, DOC (Max 10MB)</p>
                            </div>
                        </div>

                        <!-- File List Preview -->
                        <div id="fileList" class="mt-3 d-flex flex-column gap-2"></div>
                        
                        @error('attachments')
                            <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

            </div>

            <!-- RIGHT COLUMN: Sidebar Settings -->
            <div class="col-lg-4">
                <div class="sticky-top" style="top: 90px; z-index: 1;">
                    
                    <!-- Properties Card -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="mb-0 fw-bold"><i class="bi bi-sliders me-2 text-primary"></i>Properties</h6>
                        </div>
                        <div class="card-body p-4">
                            
                            <!-- Priority -->
                            <div class="mb-4">
                                <label class="form-label fw-bold required">{{ trans('cruds.ticket.fields.priority') }}</label>
                                <select class="form-select select2 @error('priority_id') is-invalid @enderror" name="priority_id" required data-placeholder="Select Priority">
                                    <option value="">Select Priority</option>
                                    @foreach($priorities as $id => $priority)
                                        <option value="{{ $id }}" {{ old('priority_id') == $id ? 'selected' : '' }}>{{ $priority }}</option>
                                    @endforeach
                                </select>
                                @error('priority_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <!-- Status -->
                            <div class="mb-4">
                                <label class="form-label fw-bold required">{{ trans('cruds.ticket.fields.status') }}</label>
                                <select class="form-select select2 @error('status_id') is-invalid @enderror" name="status_id" required data-placeholder="Select Status">
                                    <option value="">Select Status</option>
                                    @foreach($statuses as $id => $status)
                                        <option value="{{ $id }}" {{ old('status_id') == $id ? 'selected' : '' }}>{{ $status }}</option>
                                    @endforeach
                                </select>
                                @error('status_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <!-- Category -->
                            <div class="mb-4">
                                <label class="form-label fw-bold required">{{ trans('cruds.ticket.fields.category') }}</label>
                                <select class="form-select select2 @error('category_id') is-invalid @enderror" name="category_id" required data-placeholder="Select Category">
                                    <option value="">Select Category</option>
                                    @foreach($categories as $id => $category)
                                        <option value="{{ $id }}" {{ old('category_id') == $id ? 'selected' : '' }}>{{ $category }}</option>
                                    @endforeach
                                </select>
                                @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <!-- Assigned Agent -->
                            <div class="mb-2">
                                <label class="form-label fw-bold">{{ trans('cruds.ticket.fields.assigned_to_user') }}</label>
                                <select class="form-select select2 @error('assigned_to_user_id') is-invalid @enderror" name="assigned_to_user_id" data-placeholder="Select Agent">
                                    <option value="">Select Agent</option>
                                    @foreach($assigned_to_users as $id => $user)
                                        <option value="{{ $id }}" {{ old('assigned_to_user_id') == $id ? 'selected' : '' }}>{{ $user }}</option>
                                    @endforeach
                                </select>
                                @error('assigned_to_user_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                        </div>
                    </div>

                    <!-- Requester Info Card -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                    <i class="bi bi-person-fill fs-5"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-0">Requester Info</h6>
                                    <small class="text-muted">Who is asking for help?</small>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small text-muted fw-bold">NAME</label>
                                <input type="text" class="form-control" name="author_name" value="{{ old('author_name', auth()->user()->name) }}" required>
                            </div>
                            <div class="mb-0">
                                <label class="form-label small text-muted fw-bold">EMAIL</label>
                                <input type="email" class="form-control" name="author_email" value="{{ old('author_email', auth()->user()->email) }}" required>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Actions -->
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg shadow-sm" id="btnSubmit">
                            <i class="bi bi-send-fill me-2"></i> Submit Ticket
                        </button>
                        <a href="{{ route('admin.tickets.index') }}" class="btn btn-light text-muted border">Cancel</a>
                    </div>

                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
@parent
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // File Handling Logic (Sama seperti sebelumnya)
    const fileDataTransfer = new DataTransfer();
    const MAX_FILE_SIZE_MB = 10;
    const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx'];

    function handleFiles(files) {
        const container = document.getElementById('fileList');
        const placeholder = document.getElementById('uploadPlaceholder');
        
        for (let i = 0; i < files.length; i++) {
            let file = files[i];
            let extension = file.name.split('.').pop().toLowerCase();

            // Validation
            if (file.size > MAX_FILE_SIZE_MB * 1024 * 1024) {
                Swal.fire('Error', `File "${file.name}" too large (Max ${MAX_FILE_SIZE_MB}MB)`, 'error');
                continue;
            }
            if (!ALLOWED_EXTENSIONS.includes(extension)) {
                Swal.fire('Error', `File type ".${extension}" not supported`, 'error');
                continue;
            }

            fileDataTransfer.items.add(file);
        }

        document.getElementById('attachments').files = fileDataTransfer.files;
        renderFileList();
    }

    function renderFileList() {
        const container = document.getElementById('fileList');
        const placeholder = document.getElementById('uploadPlaceholder');
        container.innerHTML = '';

        if (fileDataTransfer.files.length > 0) {
            placeholder.style.display = 'none';
            
            Array.from(fileDataTransfer.files).forEach((file, index) => {
                let size = (file.size / 1024 / 1024).toFixed(2) + ' MB';
                let icon = 'bi-file-earmark';
                if(file.type.includes('image')) icon = 'bi-file-earmark-image';
                if(file.type.includes('pdf')) icon = 'bi-file-earmark-pdf';

                let html = `
                    <div class="file-preview-item p-3 rounded-3 d-flex align-items-center justify-content-between animate__animated animate__fadeIn">
                        <div class="d-flex align-items-center overflow-hidden">
                            <div class="bg-light rounded p-2 me-3 text-secondary border">
                                <i class="bi ${icon} fs-4"></i>
                            </div>
                            <div class="d-flex flex-column overflow-hidden">
                                <span class="fw-bold text-truncate" style="max-width: 200px;">${file.name}</span>
                                <small class="text-muted">${size}</small>
                            </div>
                        </div>
                        <button type="button" class="btn btn-link text-danger p-0" onclick="removeFile(${index})">
                            <i class="bi bi-x-circle-fill fs-5"></i>
                        </button>
                    </div>
                `;
                container.innerHTML += html;
            });
        } else {
            placeholder.style.display = 'block';
        }
    }

    function removeFile(index) {
        const newDataTransfer = new DataTransfer();
        Array.from(fileDataTransfer.files).forEach((file, i) => {
            if (i !== index) newDataTransfer.items.add(file);
        });
        fileDataTransfer.items.clear();
        Array.from(newDataTransfer.files).forEach(file => fileDataTransfer.items.add(file));
        
        document.getElementById('attachments').files = fileDataTransfer.files;
        renderFileList();
    }

    $(document).ready(function() {
        // 1. Init CKEditor
        ClassicEditor.create(document.querySelector('#editor'), {
            toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|', 'blockQuote', 'insertTable', 'undo', 'redo']
        }).catch(error => { console.error(error); });

        // 2. Init Select2 dengan Placeholder
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%',
            // Placeholder diatur via atribut data-placeholder di HTML select
            placeholder: function() {
                $(this).data('placeholder');
            },
            allowClear: true // Memungkinkan user menghapus pilihan kembali ke kosong
        });

        // 3. Upload Box Drag & Drop Effect
        const dropArea = document.getElementById('dropArea');
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, preventDefaults, false);
        });
        function preventDefaults(e) { e.preventDefault(); e.stopPropagation(); }
        
        ['dragenter', 'dragover'].forEach(eventName => {
            dropArea.addEventListener(eventName, () => dropArea.classList.add('dragover'), false);
        });
        ['dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, () => dropArea.classList.remove('dragover'), false);
        });
        
        dropArea.addEventListener('drop', handleDrop, false);
        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            handleFiles(files);
        }

        // 4. Form Submit Animation
        $('#ticketForm').on('submit', function() {
            let btn = $('#btnSubmit');
            btn.prop('disabled', true);
            btn.html('<span class="spinner-border spinner-border-sm me-2"></span> Sending...');
        });
    });
</script>
@endsection