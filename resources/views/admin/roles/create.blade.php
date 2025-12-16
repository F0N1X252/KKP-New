@extends('layouts.admin')

@section('page-title', 'Create Role')

@section('styles')
<style>
    .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__rendered .select2-selection__choice {
        background-color: #eef2ff;
        border: 1px solid #c7d2fe;
        color: #4f46e5;
        border-radius: 4px;
        padding: 2px 8px;
        font-size: 0.85rem;
    }
    .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__rendered .select2-selection__choice .select2-selection__choice__remove {
        color: #4f46e5;
        margin-right: 5px;
    }
    .required:after {
        content: " *";
        color: #ef4444;
    }
</style>
@endsection

@section('content')
<div class="container-fluid p-0">
    
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center gap-3">
            <div class="bg-primary bg-opacity-10 text-primary p-2 rounded d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                <i class="bi bi-shield-plus fs-3"></i>
            </div>
            <div>
                <h4 class="fw-bold m-0">Create New Role</h4>
                <div class="text-muted small">Define a new role and its permissions</div>
            </div>
        </div>
        <a href="{{ route('admin.roles.index') }}" class="btn btn-light border shadow-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to List
        </a>
    </div>

    <!-- Form Card -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <form action="{{ route("admin.roles.store") }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <!-- Title Input -->
                <div class="mb-4">
                    <label for="title" class="form-label fw-bold required">{{ trans('cruds.role.fields.title') }}</label>
                    <input type="text" id="title" name="title" class="form-control form-control-lg {{ $errors->has('title') ? 'is-invalid' : '' }}" value="{{ old('title', isset($role) ? $role->title : '') }}" required placeholder="e.g. Manager">
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
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label for="permissions" class="form-label fw-bold required mb-0">{{ trans('cruds.role.fields.permissions') }}</label>
                        
                        <!-- Select/Deselect Buttons -->
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-primary select-all">
                                <i class="bi bi-check-all me-1"></i> {{ trans('global.select_all') }}
                            </button>
                            <button type="button" class="btn btn-outline-secondary deselect-all">
                                <i class="bi bi-x-lg me-1"></i> {{ trans('global.deselect_all') }}
                            </button>
                        </div>
                    </div>

                    <select name="permissions[]" id="permissions" class="form-control select2 {{ $errors->has('permissions') ? 'is-invalid' : '' }}" multiple="multiple" required>
                        @foreach($permissions as $id => $permission)
                            <option value="{{ $id }}" {{ in_array($id, old('permissions', [])) ? 'selected' : '' }}>{{ $permission }}</option>
                        @endforeach
                    </select>
                    
                    @if($errors->has('permissions'))
                        <div class="invalid-feedback d-block">
                            {{ $errors->first('permissions') }}
                        </div>
                    @endif
                    <div class="form-text text-muted">
                        {{ trans('cruds.role.fields.permissions_helper') }}
                    </div>
                </div>

                <hr class="my-4">

                <!-- Submit Button -->
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.roles.index') }}" class="btn btn-light border">Cancel</a>
                    <button class="btn btn-primary px-4" type="submit">
                        <i class="bi bi-save me-1"></i> {{ trans('global.save') }}
                    </button>
                </div>
            </form>
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
            placeholder: 'Select permissions...'
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