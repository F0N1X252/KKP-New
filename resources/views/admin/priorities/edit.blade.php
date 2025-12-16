@extends('layouts.admin')

@section('page-title', 'Edit Priority')

@section('styles')
<!-- Bootstrap Colorpicker CSS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/2.5.3/css/bootstrap-colorpicker.min.css" rel="stylesheet">
<style>
    .required:after {
        content: " *";
        color: #ef4444;
    }
    /* Style untuk input group color picker */
    .color-preview-addon {
        background-color: #fff;
        border: 1px solid #dee2e6;
        border-left: none;
    }
    .color-preview-box {
        width: 20px;
        height: 20px;
        border-radius: 4px;
        border: 1px solid rgba(0,0,0,0.1);
        display: inline-block;
        vertical-align: middle;
    }
</style>
@endsection

@section('content')
<div class="container-fluid p-0">
    
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center gap-3">
            <div class="bg-primary bg-opacity-10 text-primary p-2 rounded d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                <i class="bi bi-pencil-square fs-3"></i>
            </div>
            <div>
                <h4 class="fw-bold m-0">Edit Priority</h4>
                <div class="text-muted small">Update priority level definition</div>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.priorities.index') }}" class="btn btn-light border shadow-sm">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <!-- Form Card -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <form action="{{ route("admin.priorities.update", [$priority->id]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-8">
                        <!-- Name Input -->
                        <div class="mb-4">
                            <label for="name" class="form-label fw-bold required">{{ trans('cruds.priority.fields.name') }}</label>
                            <input type="text" id="name" name="name" class="form-control form-control-lg {{ $errors->has('name') ? 'is-invalid' : '' }}" value="{{ old('name', $priority->name) }}" required>
                            @if($errors->has('name'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('name') }}
                                </div>
                            @endif
                            <div class="form-text text-muted">
                                {{ trans('cruds.priority.fields.name_helper') }}
                            </div>
                        </div>

                        <!-- Color Input -->
                        <div class="mb-4">
                            <label for="color" class="form-label fw-bold">{{ trans('cruds.priority.fields.color') }}</label>
                            <div class="input-group">
                                <input type="text" id="color" name="color" class="form-control form-control-lg colorpicker {{ $errors->has('color') ? 'is-invalid' : '' }}" value="{{ old('color', $priority->color) }}">
                                <span class="input-group-text color-preview-addon">
                                    <!-- Preview box langsung menggunakan warna dari database -->
                                    <i class="color-preview-box" style="background-color: {{ old('color', $priority->color ?? '#FFFFFF') }};"></i>
                                </span>
                            </div>
                            @if($errors->has('color'))
                                <div class="invalid-feedback d-block">
                                    {{ $errors->first('color') }}
                                </div>
                            @endif
                            <div class="form-text text-muted">
                                {{ trans('cruds.priority.fields.color_helper') }}
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <!-- Submit Button -->
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.priorities.index') }}" class="btn btn-light border">Cancel</a>
                    <button class="btn btn-primary px-4" type="submit">
                        <i class="bi bi-save me-1"></i> Update Priority
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Bootstrap Colorpicker JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/2.5.3/js/bootstrap-colorpicker.min.js"></script>
<script>
    $(document).ready(function() {
        // Init Colorpicker
        $('.colorpicker').colorpicker();

        // Update preview box on change
        $('.colorpicker').on('changeColor', function(event) {
            $('.color-preview-box').css('background-color', event.color.toString());
        });
    });
</script>
@endsection