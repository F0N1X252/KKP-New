@extends('layouts.admin')

@section('page-title', 'Priority Details')

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
        font-size: 2.5rem;
        text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        transition: transform 0.3s ease;
    }
    
    .color-preview-card:hover {
        transform: scale(1.05);
    }

    .detail-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: var(--text-muted);
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .detail-value {
        font-size: 1rem;
        font-weight: 500;
        color: var(--text-main);
    }
    
    /* Dark Mode Adjustments */
    [data-bs-theme="dark"] .bg-light {
        background-color: #1f2937 !important;
        border-color: #374151 !important;
    }
</style>
@endsection

@section('content')
<div class="container-fluid p-0">

    <!-- 1. Header & Actions -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div class="d-flex align-items-center gap-3">
            <!-- Icon Logo -->
            <div class="bg-primary bg-opacity-10 text-primary p-2 rounded d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                <i class="bi bi-sort-up-alt fs-3"></i>
            </div>
            <div>
                <h4 class="fw-bold m-0">Priority Details</h4>
                <div class="text-muted small">Viewing priority level definition</div>
            </div>
        </div>
        
        <div class="d-flex gap-2">
            <a href="{{ route('admin.priorities.index') }}" class="btn btn-light border shadow-sm">
                <i class="bi bi-arrow-left me-1"></i> Back to List
            </a>
            @can('priority_edit')
            <a href="{{ route('admin.priorities.edit', $priority->id) }}" class="btn btn-warning text-white shadow-sm">
                <i class="bi bi-pencil-square me-1"></i> Edit Priority
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
                            <div class="detail-value font-monospace">#{{ $priority->id }}</div>
                        </div>

                        <!-- Created At -->
                        <div class="col-md-6">
                            <div class="detail-label">Created At</div>
                            <div class="detail-value">
                                {{ $priority->created_at ? $priority->created_at->format('d M Y, H:i') : '-' }}
                            </div>
                        </div>

                        <!-- Name -->
                        <div class="col-12">
                            <div class="detail-label">Priority Name</div>
                            <div class="detail-value fs-5 fw-bold">{{ $priority->name }}</div>
                        </div>

                        <!-- Live Preview (Usage in Table) -->
                        <div class="col-12">
                            <div class="detail-label">Usage Preview</div>
                            <div class="p-4 bg-light rounded-3 border d-flex flex-column flex-md-row align-items-center justify-content-around gap-3">
                                
                                <!-- Style 1: Badge -->
                                <div class="text-center">
                                    <small class="text-muted d-block mb-2">Badge Style</small>
                                    <span class="badge rounded-pill shadow-sm" 
                                          style="background-color: {{ $priority->color }}; color: #fff; font-size: 0.9rem; padding: 8px 16px;">
                                        <i class="bi bi-flag-fill me-1"></i> {{ $priority->name }}
                                    </span>
                                </div>

                                <!-- Style 2: Indicator Text -->
                                <div class="text-center">
                                    <small class="text-muted d-block mb-2">List Style</small>
                                    <div class="d-flex align-items-center fw-bold" style="color: {{ $priority->color }}">
                                        <i class="bi bi-circle-fill me-2" style="font-size: 8px;"></i>
                                        {{ $priority->name }}
                                    </div>
                                </div>

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
                    <div class="d-flex flex-column align-items-center justify-content-center mb-4 py-3">
                        <div class="color-preview-card mb-3" style="background-color: {{ $priority->color }};">
                            <i class="bi bi-flag-fill"></i>
                        </div>
                        <h4 class="font-monospace fw-bold mb-0 text-dark">{{ $priority->color }}</h4>
                        <span class="text-muted small">Hex Color Code</span>
                    </div>

                    <!-- Additional Info -->
                    <div class="alert alert-light border d-flex align-items-start mb-3">
                        <i class="bi bi-info-circle-fill text-primary mt-1 me-2"></i>
                        <div>
                            <span class="fw-bold d-block text-dark">Priority Level</span>
                            <small class="text-muted">Used to determine ticket urgency and SLA tracking.</small>
                        </div>
                    </div>

                    <!-- Usage Stats -->
                    @if(isset($priority->tickets_count))
                        <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded-3 border">
                            <div>
                                <span class="fw-bold d-block text-dark">Total Tickets</span>
                                <small class="text-muted">Current usage count</small>
                            </div>
                            <span class="badge bg-primary rounded-pill fs-5 px-3">
                                {{ $priority->tickets_count }}
                            </span>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>
@endsection