@extends('layouts.admin')

@section('page-title', 'Create ' . trans('cruds.ticket.title_singular'))

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.tickets.index') }}">{{ trans('cruds.ticket.title') }}</a></li>
                <li class="breadcrumb-item active">Create</li>
            </ol>
        </nav>
        <h2>Create {{ trans('cruds.ticket.title_singular') }}</h2>
    </div>

    <form action="{{ route('admin.tickets.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="row">
            <!-- Main Form -->
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="bi bi-pencil-square me-2"></i>Ticket Informations
                    </div>
                    <div class="card-body">
                        <!-- Title -->
                        <div class="mb-3">
                            <label class="form-label required">{{ trans('cruds.ticket.fields.title') }}</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   name="title" value="{{ old('title') }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Content -->
                        <div class="mb-3">
                            <label class="form-label required">{{ trans('cruds.ticket.fields.content') }}</label>
                            <textarea class="form-control @error('content') is-invalid @enderror" 
                                      name="content" rows="6" required>{{ old('content') }}</textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Describe the issue in detail</div>
                        </div>

                        <!-- Attachment -->
                        <div class="mb-3">
                            <label class="form-label">{{ trans('cruds.ticket.fields.attachment') }}</label>
                            <input type="file" class="form-control @error('attachment') is-invalid @enderror" 
                                   name="attachment" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                            @error('attachment')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Accepted: JPG, PNG, PDF, DOC, DOCX (Max: 10MB)</div>
                        </div>
                    </div>
                </div>

                <!-- Author Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="bi bi-person me-2"></i>Author Information
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label required">{{ trans('cruds.ticket.fields.author_name') }}</label>
                                <input type="text" class="form-control @error('author_name') is-invalid @enderror" 
                                       name="author_name" value="{{ old('author_name', auth()->user()->name) }}" required>
                                @error('author_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label required">{{ trans('cruds.ticket.fields.author_email') }}</label>
                                <input type="email" class="form-control @error('author_email') is-invalid @enderror" 
                                       name="author_email" value="{{ old('author_email', auth()->user()->email) }}" required>
                                @error('author_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Status & Priority -->
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="bi bi-gear me-2"></i>Settings
                    </div>
                    <div class="card-body">
                        <!-- Status -->
                        <div class="mb-3">
                            <label class="form-label required">{{ trans('cruds.ticket.fields.status') }}</label>
                            <select class="form-select select2 @error('status_id') is-invalid @enderror" name="status_id" required>
                                <option value="">{{ trans('global.pleaseSelect') }}</option>
                                @foreach($statuses as $id => $status)
                                    <option value="{{ $id }}" {{ old('status_id') == $id ? 'selected' : '' }}>
                                        {{ $status }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Priority -->
                        <div class="mb-3">
                            <label class="form-label required">{{ trans('cruds.ticket.fields.priority') }}</label>
                            <select class="form-select select2 @error('priority_id') is-invalid @enderror" name="priority_id" required>
                                <option value="">{{ trans('global.pleaseSelect') }}</option>
                                @foreach($priorities as $id => $priority)
                                    <option value="{{ $id }}" {{ old('priority_id') == $id ? 'selected' : '' }}>
                                        {{ $priority }}
                                    </option>
                                @endforeach
                            </select>
                            @error('priority_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Category -->
                        <div class="mb-3">
                            <label class="form-label required">{{ trans('cruds.ticket.fields.category') }}</label>
                            <select class="form-select select2 @error('category_id') is-invalid @enderror" name="category_id" required>
                                <option value="">{{ trans('global.pleaseSelect') }}</option>
                                @foreach($categories as $id => $category)
                                    <option value="{{ $id }}" {{ old('category_id') == $id ? 'selected' : '' }}>
                                        {{ $category }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Assigned To -->
                        <div class="mb-3">
                            <label class="form-label">{{ trans('cruds.ticket.fields.assigned_to_user') }}</label>
                            <select class="form-select select2 @error('assigned_to_user_id') is-invalid @enderror" name="assigned_to_user_id">
                                <option value="">{{ trans('global.pleaseSelect') }}</option>
                                @foreach($assigned_to_users as $id => $user)
                                    <option value="{{ $id }}" {{ old('assigned_to_user_id') == $id ? 'selected' : '' }}>
                                        {{ $user }}
                                    </option>
                                @endforeach
                            </select>
                            @error('assigned_to_user_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="card">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary w-100 mb-2">
                            <i class="bi bi-check-circle me-2"></i>{{ trans('global.save') }}
                        </button>
                        <a href="{{ route('admin.tickets.index') }}" class="btn btn-outline-secondary w-100">
                            <i class="bi bi-x-circle me-2"></i>{{ trans('global.cancel') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

