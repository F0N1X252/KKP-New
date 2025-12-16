@extends('layouts.admin')

@section('page-title', 'Add New Comment')

@section('styles')
<!-- SweetAlert2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<style>
    /* --- EDITOR STYLES --- */
    .ck-editor__editable {
        min-height: 200px;
        max-height: 500px;
        background: var(--bs-body-bg);
        color: var(--bs-body-color);
    }
    
    .required:after { content: " *"; color: #ef4444; }

    /* --- SELECT2 CUSTOM --- */
    .select2-container--bootstrap-5 .select2-selection {
        min-height: 45px;
        padding-top: 5px;
    }
    
    /* --- DARK MODE TWEAKS --- */
    [data-bs-theme="dark"] .bg-light {
        background-color: #1f2937 !important;
    }
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
                    <li class="breadcrumb-item"><a href="{{ route('admin.comments.index') }}" class="text-decoration-none">Comments</a></li>
                    <li class="breadcrumb-item active">Create</li>
                </ol>
            </nav>
            <h3 class="fw-bold text-dark mb-0 mt-1">Add Comment</h3>
        </div>
        <a href="{{ route('admin.comments.index') }}" class="btn btn-light border shadow-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to List
        </a>
    </div>

    <!-- Form ID untuk AJAX -->
    <form id="createCommentForm" enctype="multipart/form-data">
        @csrf
        
        <div class="row g-4">
            <!-- LEFT COLUMN: Main Content -->
            <div class="col-lg-8">
                
                <!-- Ticket & Content -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-chat-text-fill me-2"></i>Comment Details</h6>
                    </div>
                    <div class="card-body p-4">
                        
                        <!-- Select Ticket -->
                        <div class="mb-4">
                            <label for="ticket_id" class="form-label fw-bold required">{{ trans('cruds.comment.fields.ticket') }}</label>
                            <select name="ticket_id" id="ticket_id" class="form-select select2" required data-placeholder="Choose a ticket...">
                                <option value=""></option>
                                @foreach($tickets as $id => $ticket)
                                    <option value="{{ $id }}">{{ $ticket }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback d-block" id="error-ticket_id"></div>
                            <div class="form-text text-muted">Which ticket is this comment related to?</div>
                        </div>

                        <!-- Comment Body -->
                        <div class="mb-3">
                            <label for="comment_text" class="form-label fw-bold required">{{ trans('cruds.comment.fields.comment_text') }}</label>
                            <textarea id="editor" name="comment_text" class="form-control"></textarea>
                            <div class="invalid-feedback d-block" id="error-comment_text"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN: Author Info -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 mb-4 sticky-top" style="top: 90px; z-index: 1;">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-person-circle me-2"></i>Author Information</h6>
                    </div>
                    <div class="card-body p-4">
                        
                        <!-- Author Name -->
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted text-uppercase required">{{ trans('cruds.comment.fields.author_name') }}</label>
                            <input type="text" name="author_name" class="form-control" 
                                   value="{{ old('author_name', auth()->check() ? auth()->user()->name : '') }}" required>
                            <div class="invalid-feedback d-block" id="error-author_name"></div>
                        </div>

                        <!-- Author Email -->
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted text-uppercase required">{{ trans('cruds.comment.fields.author_email') }}</label>
                            <input type="email" name="author_email" class="form-control" 
                                   value="{{ old('author_email', auth()->check() ? auth()->user()->email : '') }}" required>
                            <div class="invalid-feedback d-block" id="error-author_email"></div>
                        </div>

                        <!-- Registered User Link -->
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-muted text-uppercase">{{ trans('cruds.comment.fields.user') }} Link</label>
                            <select name="user_id" class="form-control select2" data-placeholder="Link to user (optional)">
                                <option value=""></option>
                                @foreach($users as $id => $user)
                                    <option value="{{ $id }}" {{ (auth()->check() && auth()->id() == $id) ? 'selected' : '' }}>{{ $user }}</option>
                                @endforeach
                            </select>
                        </div>

                        <hr class="my-4">

                        <!-- Actions -->
                        <div class="d-grid gap-2">
                            <button type="submit" id="btnSave" class="btn btn-primary btn-lg shadow-sm">
                                <span id="btnText"><i class="bi bi-save me-2"></i> Save Comment</span>
                                <span id="btnSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            </button>
                            <a href="{{ route('admin.comments.index') }}" class="btn btn-light text-muted border">Cancel</a>
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

<script>
    let editorInstance;

    document.addEventListener("DOMContentLoaded", function() {
        // 1. Init CKEditor
        ClassicEditor.create(document.querySelector('#editor'), {
            toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', '|', 'undo', 'redo']
        })
        .then(editor => { editorInstance = editor; })
        .catch(error => { console.error(error); });

        // 2. Init Select2
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%',
            allowClear: true,
            placeholder: function() {
                $(this).data('placeholder');
            }
        });

        // 3. HANDLE AJAX SUBMIT (SweetAlert Logic)
        $('#createCommentForm').on('submit', function(e) {
            e.preventDefault();

            // Reset Errors
            $('.invalid-feedback').text('');
            $('.form-control, .form-select').removeClass('is-invalid');

            // Update CKEditor Data
            if (editorInstance) editorInstance.updateSourceElement();

            // Button Loading State
            let btn = $('#btnSave');
            let btnText = $('#btnText');
            let btnSpinner = $('#btnSpinner');
            
            btn.prop('disabled', true);
            btnText.text('Saving...');
            btnSpinner.removeClass('d-none');

            // Form Data
            let formData = new FormData(this);

            $.ajax({
                url: "{{ route('admin.comments.store') }}",
                method: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    // --- SWEETALERT SUKSES ---
                    Swal.fire({
                        title: 'Success!',
                        text: 'Comment has been added successfully.',
                        icon: 'success',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#4f46e5',
                        allowOutsideClick: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Redirect ke index atau halaman detail
                            window.location.href = "{{ route('admin.comments.index') }}";
                        }
                    });
                },
                error: function(xhr) {
                    // Reset Button
                    btn.prop('disabled', false);
                    btnText.html('<i class="bi bi-save me-2"></i> Save Comment');
                    btnSpinner.addClass('d-none');

                    if (xhr.status === 422) {
                        // Validation Errors
                        let errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            let input = $('[name="'+key+'"]');
                            let errorDiv = $('#error-'+key);
                            
                            input.addClass('is-invalid');
                            if(key === 'comment_text') {
                                // Highlight CKEditor
                                $('.ck.ck-editor').addClass('border border-danger');
                            }
                            
                            // Handle Select2 Validation Style
                            if(input.hasClass('select2-hidden-accessible')) {
                                input.next('.select2-container').find('.select2-selection').addClass('border-danger');
                            }

                            if(errorDiv.length) {
                                errorDiv.text(value[0]);
                            }
                        });

                        // SweetAlert Validation Error
                        Swal.fire({
                            title: 'Validation Error',
                            text: 'Please check the highlighted fields.',
                            icon: 'error',
                            confirmButtonColor: '#d33'
                        });
                    } else {
                        // General Error
                        Swal.fire({
                            title: 'Error!',
                            text: 'Something went wrong. Please try again.',
                            icon: 'error',
                            confirmButtonColor: '#d33'
                        });
                    }
                }
            });
        });

        // Hapus styling error saat user mengetik/memilih
        $('input, textarea, select').on('input change', function() {
            $(this).removeClass('is-invalid');
            if($(this).hasClass('select2-hidden-accessible')) {
                $(this).next('.select2-container').find('.select2-selection').removeClass('border-danger');
            }
        });
    });
</script>
@endsection