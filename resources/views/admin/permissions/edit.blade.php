@extends('layouts.admin')

@section('page-title', 'Edit Permission')

@section('styles')
<style>
    .form-label {
        font-weight: 600;
        color: var(--text-dark);
        font-size: 0.9rem;
    }
    
    .input-group-text {
        background-color: var(--bs-body-bg);
        border-color: var(--border-color);
        color: var(--text-muted);
    }

    /* Warning Box */
    .alert-warning-soft {
        background-color: rgba(245, 158, 11, 0.1);
        border: 1px solid rgba(245, 158, 11, 0.2);
        color: #b45309;
    }
    
    [data-bs-theme="dark"] .alert-warning-soft {
        color: #fbbf24;
    }
    
    .required:after {
        content: " *";
        color: #ef4444;
    }
</style>
@endsection

@section('content')
<div class="container-fluid p-0">
    
    <!-- 1. Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center gap-3">
            <div class="bg-primary bg-opacity-10 text-primary p-2 rounded d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                <i class="bi bi-key-fill fs-3"></i>
            </div>
            <div>
                <h4 class="fw-bold m-0">Edit Permission</h4>
                <div class="text-muted small">Update system access control</div>
            </div>
        </div>
        <a href="{{ route('admin.permissions.index') }}" class="btn btn-light border shadow-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to List
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-6">
            
            <!-- 2. Form Card -->
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0 fw-bold text-primary">
                        <i class="bi bi-pencil-square me-2"></i> Permission Details
                    </h6>
                </div>
                
                <div class="card-body p-4">
                    
                    <!-- Warning Alert -->
                    <div class="alert alert-warning-soft d-flex align-items-start rounded-3 mb-4 p-3" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2 mt-1 fs-5"></i>
                        <div>
                            <strong>Caution:</strong> Changing permissions may break existing features if the code relies on these specific strings.
                        </div>
                    </div>

                    <form action="{{ route("admin.permissions.update", [$permission->id]) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <!-- Title Input -->
                        <div class="mb-4">
                            <label for="title" class="form-label required">{{ trans('cruds.permission.fields.title') }}</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-key"></i></span>
                                <input type="text" id="title" name="title" 
                                       class="form-control form-control-lg {{ $errors->has('title') ? 'is-invalid' : '' }}" 
                                       value="{{ old('title', $permission->title) }}" 
                                       required>
                                @if($errors->has('title'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('title') }}
                                    </div>
                                @endif
                            </div>
                            <div class="form-text text-muted mt-1">
                                Unique identifier (e.g., <code>user_create</code>, <code>ticket_delete</code>).
                            </div>
                        </div>

                        <!-- Info Metadata -->
                        <div class="d-flex justify-content-between p-3 bg-light rounded-3 mb-4 border border-dashed">
                            <div>
                                <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.7rem;">Created At</small>
                                <span class="fw-medium">{{ $permission->created_at->format('d M Y') }}</span>
                            </div>
                            <div class="text-end">
                                <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.7rem;">ID</small>
                                <span class="font-monospace">#{{ $permission->id }}</span>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Actions -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.permissions.index') }}" class="btn btn-light border">Cancel</a>
                            <button class="btn btn-primary px-4 shadow-sm" type="submit">
                                <i class="bi bi-save me-1"></i> Update Permission
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Simple script to auto-format text to snake_case if user wants (Optional)
    // document.getElementById('title').addEventListener('keyup', function(e) {
    //     this.value = this.value.toLowerCase().replace(/ /g, '_');
    // });
</script>
@endsection