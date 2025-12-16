@extends('layouts.admin')

@section('page-title', 'Create Permission')

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
    
    .required:after {
        content: " *";
        color: #ef4444;
    }

    /* Visual Hint Card */
    .hint-card {
        background-color: rgba(79, 70, 229, 0.03);
        border: 1px dashed rgba(79, 70, 229, 0.2);
        border-radius: 8px;
    }
    
    [data-bs-theme="dark"] .hint-card {
        background-color: rgba(79, 70, 229, 0.1);
        border-color: rgba(79, 70, 229, 0.3);
    }
</style>
@endsection

@section('content')
<div class="container-fluid p-0">
    
    <!-- 1. Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center gap-3">
            <div class="bg-primary bg-opacity-10 text-primary p-2 rounded d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                <i class="bi bi-shield-plus fs-3"></i>
            </div>
            <div>
                <h4 class="fw-bold m-0">Create Permission</h4>
                <div class="text-muted small">Define new system capabilities</div>
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
                        <i class="bi bi-pencil-square me-2"></i> New Permission Details
                    </h6>
                </div>
                
                <div class="card-body p-4">
                    
                    <form action="{{ route("admin.permissions.store") }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- Title Input -->
                        <div class="mb-4">
                            <label for="title" class="form-label required">{{ trans('cruds.permission.fields.title') }}</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-key"></i></span>
                                <input type="text" id="title" name="title" 
                                       class="form-control form-control-lg {{ $errors->has('title') ? 'is-invalid' : '' }}" 
                                       value="{{ old('title') }}" 
                                       placeholder="e.g. user_management_access"
                                       required autofocus>
                                @if($errors->has('title'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('title') }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Helper Hint -->
                        <div class="hint-card p-3 mb-4 d-flex align-items-start">
                            <i class="bi bi-lightbulb-fill text-warning me-3 mt-1"></i>
                            <div>
                                <h6 class="fw-bold small text-dark mb-1">Naming Convention</h6>
                                <p class="small text-muted mb-0">
                                    It is recommended to use <strong>lowercase</strong> and <strong>underscores</strong> for permission titles. 
                                    <br>Example: <code>ticket_create</code>, <code>ticket_delete</code>.
                                </p>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Actions -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.permissions.index') }}" class="btn btn-light border">Cancel</a>
                            <button class="btn btn-primary px-4 shadow-sm" type="submit">
                                <i class="bi bi-save me-1"></i> Save Permission
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
    // Optional: Auto-format to snake_case while typing
    const titleInput = document.getElementById('title');
    
    titleInput.addEventListener('input', function(e) {
        // Hanya visual suggestion, tidak memaksa replace agar user tetap punya kontrol
        // Jika ingin memaksa replace spasi dengan underscore, uncomment baris bawah:
        // this.value = this.value.toLowerCase().replace(/ /g, '_');
    });
</script>
@endsection