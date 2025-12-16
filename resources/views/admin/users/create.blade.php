@extends('layouts.admin')

@section('page-title', 'Create User')

@section('styles')
<style>
    /* Styling khusus untuk tag Select2 agar sesuai tema modern */
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
                <i class="bi bi-person-plus-fill fs-3"></i>
            </div>
            <div>
                <h4 class="fw-bold m-0">Create User</h4>
                <div class="text-muted small">Add a new user and assign roles</div>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.users.index') }}" class="btn btn-light border shadow-sm">
                <i class="bi bi-arrow-left me-1"></i> Back to List
            </a>
        </div>
    </div>

    <!-- Form Card -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <form action="{{ route("admin.users.store") }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <!-- Name Input -->
                <div class="mb-4">
                    <label for="name" class="form-label fw-bold required">{{ trans('cruds.user.fields.name') }}</label>
                    <input type="text" id="name" name="name" class="form-control form-control-lg {{ $errors->has('name') ? 'is-invalid' : '' }}" value="{{ old('name') }}" required placeholder="Full Name">
                    @if($errors->has('name'))
                        <div class="invalid-feedback">
                            {{ $errors->first('name') }}
                        </div>
                    @endif
                    <div class="form-text text-muted">
                        {{ trans('cruds.user.fields.name_helper') }}
                    </div>
                </div>

                <!-- Email Input -->
                <div class="mb-4">
                    <label for="email" class="form-label fw-bold required">{{ trans('cruds.user.fields.email') }}</label>
                    <input type="email" id="email" name="email" class="form-control form-control-lg {{ $errors->has('email') ? 'is-invalid' : '' }}" value="{{ old('email') }}" required placeholder="name@example.com">
                    @if($errors->has('email'))
                        <div class="invalid-feedback">
                            {{ $errors->first('email') }}
                        </div>
                    @endif
                    <div class="form-text text-muted">
                        {{ trans('cruds.user.fields.email_helper') }}
                    </div>
                </div>

                <!-- Password Input -->
                <div class="mb-4">
                    <label for="password" class="form-label fw-bold required">{{ trans('cruds.user.fields.password') }}</label>
                    <input type="password" id="password" name="password" class="form-control form-control-lg {{ $errors->has('password') ? 'is-invalid' : '' }}" required>
                    @if($errors->has('password'))
                        <div class="invalid-feedback">
                            {{ $errors->first('password') }}
                        </div>
                    @endif
                    <div class="form-text text-muted">
                        {{ trans('cruds.user.fields.password_helper') }}
                    </div>
                </div>

                <!-- Roles Select with Select All/Deselect All -->
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label for="roles" class="form-label fw-bold required mb-0">{{ trans('cruds.user.fields.roles') }}</label>
                        
                        <!-- Tombol Aksi -->
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-primary select-all">
                                <i class="bi bi-check-all me-1"></i> {{ trans('global.select_all') }}
                            </button>
                            <button type="button" class="btn btn-outline-secondary deselect-all">
                                <i class="bi bi-x-lg me-1"></i> {{ trans('global.deselect_all') }}
                            </button>
                        </div>
                    </div>

                    <select name="roles[]" id="roles" class="form-control select2 {{ $errors->has('roles') ? 'is-invalid' : '' }}" multiple="multiple" required>
                        @foreach($roles as $id => $role)
                            <option value="{{ $id }}" {{ in_array($id, old('roles', [])) ? 'selected' : '' }}>{{ $role }}</option>
                        @endforeach
                    </select>
                    
                    @if($errors->has('roles'))
                        <div class="invalid-feedback d-block">
                            {{ $errors->first('roles') }}
                        </div>
                    @endif
                    <div class="form-text text-muted">
                        {{ trans('cruds.user.fields.roles_helper') }}
                    </div>
                </div>

                <hr class="my-4">

                <!-- Submit Button -->
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-light border">Cancel</a>
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
        // Init Select2 dengan tema Bootstrap 5
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Select roles...'
        });

        // 1. Fungsi Select All
        // Mencari elemen select dengan ID 'roles', set properti selected ke true untuk semua option, lalu trigger change
        $('.select-all').click(function() {
            let $select2 = $('#roles');
            $select2.find('option').prop('selected', 'selected');
            $select2.trigger('change');
        });

        // 2. Fungsi Deselect All
        // Menghapus properti selected, lalu trigger change agar tampilan Select2 diperbarui
        $('.deselect-all').click(function() {
            let $select2 = $('#roles');
            $select2.find('option').prop('selected', false);
            $select2.trigger('change');
        });
    });
</script>
@endsection