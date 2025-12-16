@extends('layouts.admin')

@section('page-title', 'Edit Comment #' . $comment->id)

@section('styles')
<style>
    /* --- EDITOR STYLES --- */
    .ck-editor__editable {
        min-height: 250px;
        max-height: 500px;
        background: var(--bs-body-bg);
        color: var(--bs-body-color);
    }
    
    /* --- TICKET CONTEXT CARD --- */
    .ticket-context {
        border-left: 4px solid var(--primary-color);
        background: rgba(79, 70, 229, 0.04);
    }
    
    [data-bs-theme="dark"] .ticket-context {
        background: rgba(79, 70, 229, 0.1);
    }

    .required:after { content: " *"; color: #ef4444; }
    
    /* --- SELECT2 CUSTOM --- */
    .select2-container--bootstrap-5 .select2-selection {
        min-height: 45px;
        padding-top: 5px;
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
                    <li class="breadcrumb-item active">Edit #{{ $comment->id }}</li>
                </ol>
            </nav>
            <h3 class="fw-bold text-dark mb-0 mt-1">Edit Comment</h3>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.comments.index') }}" class="btn btn-light border shadow-sm">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <form action="{{ route("admin.comments.update", [$comment->id]) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- Hidden Ticket ID (Agar controller tetap menerima ID tiket tanpa user bisa mengubahnya) -->
        <input type="hidden" name="ticket_id" value="{{ $comment->ticket_id }}">

        <div class="row g-4">
            <!-- LEFT COLUMN: Editor -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-pencil-square me-2"></i>Content</h6>
                    </div>
                    <div class="card-body p-4">
                        <!-- Comment Text -->
                        <div class="mb-3">
                            <label class="form-label fw-bold required">{{ trans('cruds.comment.fields.comment_text') }}</label>
                            <textarea id="editor" name="comment_text" class="form-control">{{ old('comment_text', $comment->comment_text) }}</textarea>
                            @if($errors->has('comment_text'))
                                <div class="text-danger small mt-1">
                                    {{ $errors->first('comment_text') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN: Context & Author -->
            <div class="col-lg-4">
                
                <!-- Ticket Context (Read Only) -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-ticket-perforated me-2"></i>Related Ticket</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="ticket-context p-3 rounded-3 mb-3">
                            <div class="d-flex align-items-center mb-1">
                                <span class="badge bg-primary me-2">#{{ $comment->ticket->id }}</span>
                                <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Currently Editing Comment For:</small>
                            </div>
                            <h6 class="fw-bold mb-0 text-dark">
                                <a href="{{ route('admin.tickets.show', $comment->ticket->id) }}" target="_blank" class="text-decoration-none text-body hover-primary">
                                    {{ $comment->ticket->title ?? 'Deleted Ticket' }} <i class="bi bi-box-arrow-up-right small ms-1 text-muted"></i>
                                </a>
                            </h6>
                        </div>
                        
                        <div class="alert alert-info border-0 bg-info bg-opacity-10 text-info d-flex align-items-center mb-0">
                            <i class="bi bi-info-circle-fill me-2"></i>
                            <small>You cannot move a comment to another ticket. Delete and recreate if necessary.</small>
                        </div>
                    </div>
                </div>

                <!-- Author Information -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-person me-2"></i>Author Info</h6>
                    </div>
                    <div class="card-body p-4">
                        <!-- Author Name -->
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted text-uppercase required">{{ trans('cruds.comment.fields.author_name') }}</label>
                            <input type="text" name="author_name" class="form-control" value="{{ old('author_name', $comment->author_name) }}" required>
                            @if($errors->has('author_name'))
                                <div class="text-danger small">{{ $errors->first('author_name') }}</div>
                            @endif
                        </div>

                        <!-- Author Email -->
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted text-uppercase required">{{ trans('cruds.comment.fields.author_email') }}</label>
                            <input type="text" name="author_email" class="form-control" value="{{ old('author_email', $comment->author_email) }}" required>
                            @if($errors->has('author_email'))
                                <div class="text-danger small">{{ $errors->first('author_email') }}</div>
                            @endif
                        </div>

                        <!-- Registered User Link -->
                        <div class="mb-0">
                            <label class="form-label small fw-bold text-muted text-uppercase">{{ trans('cruds.comment.fields.user') }} Link</label>
                            <select name="user_id" class="form-control select2">
                                <option value="">-- Guest / No Account --</option>
                                @foreach($users as $id => $user)
                                    <option value="{{ $id }}" {{ (old('user_id') ? old('user_id') : $comment->user->id ?? '') == $id ? 'selected' : '' }}>
                                        {{ $user }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text small">Link this comment to a registered system user (optional).</div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-grid gap-2">
                    <button class="btn btn-primary btn-lg shadow-sm" type="submit">
                        <i class="bi bi-save me-2"></i> Update Comment
                    </button>
                    <a href="{{ route('admin.comments.index') }}" class="btn btn-light btn-lg text-muted border">Cancel</a>
                </div>

            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Init CKEditor
        ClassicEditor.create(document.querySelector('#editor'), {
            toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', '|', 'undo', 'redo']
        }).catch(error => { console.error(error); });

        // Init Select2
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Select user...'
        });
    });
</script>
@endsection