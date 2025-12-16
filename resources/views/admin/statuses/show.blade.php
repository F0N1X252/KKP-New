@extends('layouts.admin')

@section('page-title', 'Status Details')

@section('styles')
<style>
    /* --- Visual Styles --- */
    .color-preview-card {
        width: 100px;
        height: 100px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        border: 1px solid rgba(0,0,0,0.05);
        color: #fff;
        font-weight: bold;
        font-size: 1.5rem;
        text-shadow: 0 2px 4px rgba(0,0,0,0.3);
    }

    .detail-label {
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .detail-value {
        font-size: 1.1rem;
        font-weight: 500;
        color: #1e293b;
    }
</style>
@endsection

@section('content')
<div class="container-fluid p-0">

    <!-- 1. Header & Actions -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="bg-primary bg-opacity-10 text-primary p-2 rounded d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                <i class="bi bi-toggle-on fs-3"></i>
            </div>
            <div>
                <h4 class="fw-bold m-0">Status Details</h4>
                <div class="text-muted small">Viewing status definition</div>
            </div>
        </div>
        
        <div class="d-flex gap-2">
            <a href="{{ route('admin.statuses.index') }}" class="btn btn-light border shadow-sm">
                <i class="bi bi-arrow-left me-1"></i> Back to List
            </a>
            @can('status_edit')
            <a href="{{ route('admin.statuses.edit', $status->id) }}" class="btn btn-warning text-white shadow-sm">
                <i class="bi bi-pencil-square me-1"></i> Edit Status
            </a>
            @endcan
        </div>
    </div>

    <div class="row g-4">
        <!-- Left Column: Main Details -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4 border-bottom pb-3">General Information</h5>
                    
                    <div class="row g-4">
                        <!-- ID -->
                        <div class="col-md-6">
                            <div class="detail-label">ID</div>
                            <div class="detail-value">#{{ $status->id }}</div>
                        </div>

                        <!-- Created At -->
                        <div class="col-md-6">
                            <div class="detail-label">Created At</div>
                            <div class="detail-value">{{ $status->created_at ? $status->created_at->format('d M Y, H:i') : '-' }}</div>
                        </div>

                        <!-- Name -->
                        <div class="col-12">
                            <div class="detail-label">Status Name</div>
                            <div class="detail-value">{{ $status->name }}</div>
                        </div>

                        <!-- Live Preview (Badge) -->
                        <div class="col-12">
                            <div class="detail-label">Badge Preview (In Tables)</div>
                            <div class="p-3 bg-light rounded-3 border d-flex align-items-center justify-content-center" style="height: 100px;">
                                <span class="badge rounded-pill shadow-sm" 
                                      style="background-color: {{ $status->color }}; color: #fff; font-size: 1rem; padding: 10px 20px;">
                                    {{ $status->name }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Visual & Stats -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4 border-bottom pb-3">Visual Appearance</h5>
                    
                    <!-- Color Block -->
                    <div class="d-flex flex-column align-items-center justify-content-center mb-4">
                        <div class="color-preview-card mb-3" style="background-color: {{ $status->color }};">
                            <i class="bi bi-palette"></i>
                        </div>
                        <h4 class="font-monospace fw-bold mb-0">{{ $status->color }}</h4>
                        <span class="text-muted small">Hex Color Code</span>
                    </div>

                    <!-- Usage Stats (Optional: Jika relasi tickets ada) -->
                    @if(isset($status->tickets_count))
                    <div class="alert alert-light border d-flex align-items-center justify-content-between mb-0">
                        <div>
                            <span class="fw-bold d-block">Related Tickets</span>
                            <small class="text-muted">Tickets using this status</small>
                        </div>
                        <span class="badge bg-primary rounded-pill fs-6">{{ $status->tickets_count }}</span>
                    </div>
                    @else
                    <div class="alert alert-info border-0 bg-info bg-opacity-10 text-info d-flex align-items-center mb-0">
                        <i class="bi bi-info-circle-fill me-2"></i>
                        <small>This status is active and ready to use.</small>
                    </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>
@endsection