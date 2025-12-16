@extends('layouts.admin')

@section('page-title', 'Edit Role')

@section('styles')
<style>
    /* --- SELECT2 CUSTOM STYLING --- */
    /* Membuat tag permission terlihat modern (pill style) */
    .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__rendered .select2-selection__choice {
        background-color: rgba(79, 70, 229, 0.1);
        border: 1px solid rgba(79, 70, 229, 0.2);
        color: var(--primary-color);
        border-radius: 6px;
        padding: 4px 10px;
        font-size: 0.85rem;
        font-weight: 500;
        margin-top: 6px;
    }
    
    .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__rendered .select2-selection__choice .select2-selection__choice__remove {
        color: var(--primary-color);
        margin-right: 8px;
        border-right: 1px solid rgba(79, 70, 229, 0.2);
    }

    .select2-container--bootstrap-5 .select2-selection {
        border-color: var(--border-color);
        padding: 0.5rem;
    }

    /* Dark Mode Support for Select2 */
    [data-bs-theme="dark"] .select2-container--bootstrap-5 .select2-selection {
        background-color: var(--bs-body-bg);
        border-color: #374151;
        color: var(--bs-body-color);
    }
    [data-bs-theme="dark"] .select2-container--bootstrap-5 .select2-dropdown {
        background-color: #1f2937;
        border-color: #374151;
        color: var(--bs-body-color);
    }
    [data-bs-theme="dark"] .select2-results__option--highlighted {
        background-color: rgba(79, 70, 229, 0.2) !important; 
        color: #a5b4fc !important;
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
                <i class="bi bi-shield-check fs-3"></i>
            </div>
            <div>
                <h4 class="fw-bold m-0">Edit Role</h4>
                <div class="text-muted small">Update role definition and access control</div>
            </div>
        </div>
        
        <div class="d-flex gap-2">
            <a href="{{ route('admin.roles.index') }}" class="btn btn-light border shadow-sm">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
            @can('role_show')
            <a href="{{ route('admin.roles.show', $role->id) }}" class="btn btn-light border text-primary shadow-sm">
                <i class="bi bi-eye me-1"></i> View
            </a>
            @endcan
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            
            <!-- 2. Form Card -->
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0 fw-bold text-primary">
                        <i class="bi bi-pencil-square me-2"></i> Role Configuration
                    </h6>
                </div>
                
                <div class="card-body p-4">
                    <form action="{{ route("admin.roles.update", [$role->id]) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <!-- Title Input -->
                        <div class="mb-4">
                            <label for="title" class="form-label fw-bold required">{{ trans('cruds.role.fields.title') }}</label>
                            <input type="text" id="title" name="title" 
                                   class="form-control form-control-lg {{ $errors->has('title') ? 'is-invalid' : '' }}" 
                                   value="{{ old('title', $role->title) }}" 
                                   required>
                            @if($errors->has('title'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('title') }}
                                </div>
                            @endif
                            <div class="form-text text-muted">
                                {{ trans('cruds.role.fields.title_helper') }}
                            </div>
                        </div>

                        <!-- Permissions Input -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-end mb-2">
                                <div>
                                    <label for="permissions" class="form-label fw-bold required mb-0">{{ trans('cruds.role.fields.permissions') }}</label>
                                    <div class="form-text text-muted small mt-0">Select capabilities for this role</div>
                                </div>
                                
                                <!-- Bulk Action Buttons -->
                                <div class="btn-group btn-group-sm shadow-sm">
                                    <button type="button" class="btn btn-outline-primary select-all">
                                        <i class="bi bi-check-all me-1"></i> Select All
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary deselect-all">
                                        <i class="bi bi-x-lg me-1"></i> Deselect All
                                    </button>
                                </div>
                            </div>

                            <select name="permissions[]" id="permissions" class="form-control select2 {{ $errors->has('permissions') ? 'is-invalid' : '' }}" multiple="multiple" required>
                                @foreach($permissions as $id => $permission)
                                    <option value="{{ $id }}" 
                                        {{ (in_array($id, old('permissions', [])) || $role->permissions->contains($id)) ? 'selected' : '' }}>
                                        {{ $permission }}
                                    </option>
                                @endforeach
                            </select>
                            
                            @if($errors->has('permissions'))
                                <div class="invalid-feedback d-block mt-2">
                                    {{ $errors->first('permissions') }}
                                </div>
                            @endif
                        </div>

                        <hr class="my-4">

                        <!-- Actions -->
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                @if($role->users->count() > 0)
                                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning px-3 py-2 rounded-pill">
                                        <i class="bi bi-exclamation-circle me-1"></i> {{ $role->users->count() }} Users assigned to this role
                                    </span>
                                @endif
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.roles.index') }}" class="btn btn-light border">Cancel</a>
                                <button class="btn btn-primary px-4 shadow-sm" type="submit">
                                    <i class="bi bi-save me-1"></i> Update Role
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@section('scripts')
@parent
<script>
    $(document).ready(function() {
        // Init Select2
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Search and select permissions...',
            closeOnSelect: false,
            allowClear: true
        });

        // 1. Function Select All
        $('.select-all').click(function() {
            let $select2 = $('#permissions');
            $select2.find('option').prop('selected', 'selected');
            $select2.trigger('change');
        });

        // 2. Function Deselect All
        $('.deselect-all').click(function() {
            let $select2 = $('#permissions');
            $select2.find('option').prop('selected', false);
            $select2.trigger('change');
        });
    });
</script>
@endsection