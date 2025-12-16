@extends('layouts.admin')

@section('page-title', 'Role Details')

@section('styles')
<!-- SweetAlert2 -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<style>
    /* --- INFO LABELS --- */
    .detail-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        color: var(--text-muted);
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .detail-value {
        font-size: 1rem;
        font-weight: 500;
        color: var(--text-main);
    }

    /* --- ROLE AVATAR CARD --- */
    .role-icon-box {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, var(--primary-color), #818cf8);
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        color: white;
        box-shadow: 0 10px 20px rgba(79, 70, 229, 0.2);
        margin: 0 auto 1.5rem;
    }

    /* --- PERMISSION BADGES --- */
    .permission-badge {
        font-size: 0.85rem;
        padding: 6px 12px;
        border-radius: 8px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        background-color: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
        margin-bottom: 8px;
        margin-right: 4px;
        transition: all 0.2s;
    }

    .permission-badge:hover {
        background-color: #eef2ff;
        color: var(--primary-color);
        border-color: rgba(79, 70, 229, 0.2);
        transform: translateY(-1px);
    }
    
    .permission-badge i {
        font-size: 0.7rem;
        margin-right: 6px;
        opacity: 0.5;
    }

    /* Dark Mode Adjustments */
    [data-bs-theme="dark"] .bg-light {
        background-color: #1f2937 !important;
    }
    [data-bs-theme="dark"] .permission-badge {
        background-color: rgba(255,255,255,0.05);
        border-color: rgba(255,255,255,0.1);
        color: #cbd5e1;
    }
    [data-bs-theme="dark"] .permission-badge:hover {
        background-color: rgba(79, 70, 229, 0.2);
        color: #a5b4fc;
    }
</style>
@endsection

@section('content')
<div class="container-fluid p-0">

    <!-- 1. Header & Actions -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="bg-primary bg-opacity-10 text-primary p-2 rounded d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                <i class="bi bi-shield-lock-fill fs-3"></i>
            </div>
            <div>
                <h4 class="fw-bold m-0">Role Details</h4>
                <div class="text-muted small">Viewing role configuration</div>
            </div>
        </div>
        
        <div class="d-flex gap-2">
            <a href="{{ route('admin.roles.index') }}" class="btn btn-light border shadow-sm">
                <i class="bi bi-arrow-left me-1"></i> Back to List
            </a>
            @can('role_edit')
            <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-warning text-white shadow-sm">
                <i class="bi bi-pencil-square me-1"></i> Edit
            </a>
            @endcan
        </div>
    </div>

    <div class="row g-4">
        <!-- LEFT COLUMN: Role Overview -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4 text-center">
                    
                    <div class="role-icon-box">
                        <i class="bi bi-person-badge"></i>
                    </div>

                    <h3 class="fw-bold text-dark mb-1">{{ $role->title }}</h3>
                    <span class="badge bg-light text-muted border mb-4">ID: #{{ $role->id }}</span>

                    <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded-3 border text-start mb-2">
                        <div>
                            <div class="detail-label mb-1">Total Permissions</div>
                            <div class="h5 mb-0 fw-bold text-primary">{{ $role->permissions->count() }}</div>
                        </div>
                        <i class="bi bi-key fs-1 text-muted opacity-25"></i>
                    </div>

                    <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded-3 border text-start">
                        <div>
                            <div class="detail-label mb-1">Created At</div>
                            <div class="detail-value text-muted">{{ $role->created_at ? $role->created_at->format('d M Y') : '-' }}</div>
                        </div>
                        <i class="bi bi-calendar-event fs-1 text-muted opacity-25"></i>
                    </div>

                    <!-- Delete Button -->
                    @can('role_delete')
                    <div class="mt-4 pt-3 border-top">
                        <button class="btn btn-outline-danger w-100" id="deleteBtn">
                            <i class="bi bi-trash me-2"></i> Delete Role
                        </button>
                        <form id="deleteForm" action="{{ route('admin.roles.destroy', $role->id) }}" method="POST" class="d-none">
                            @csrf @method('DELETE')
                        </form>
                    </div>
                    @endcan

                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: Permissions List -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-dark">
                        <i class="bi bi-shield-check me-2 text-primary"></i> Assigned Permissions
                    </h6>
                    
                    <!-- Search Filter (Client Side) -->
                    <div class="input-group input-group-sm" style="width: 200px;">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search"></i></span>
                        <input type="text" id="permissionSearch" class="form-control bg-light border-start-0" placeholder="Filter...">
                    </div>
                </div>
                
                <div class="card-body p-4">
                    @if($role->permissions->count() > 0)
                        <div id="permissionContainer">
                            @foreach($role->permissions as $permission)
                                <div class="permission-badge" title="{{ $permission->title }}">
                                    <i class="bi bi-key-fill"></i>
                                    <span class="perm-text">{{ $permission->title }}</span>
                                </div>
                            @endforeach
                        </div>
                        
                        <div id="noResults" class="text-center py-4 d-none">
                            <i class="bi bi-search display-6 text-muted opacity-25"></i>
                            <p class="text-muted mt-2">No permissions found matching your search.</p>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="bg-light rounded-circle d-inline-flex p-3 mb-3">
                                <i class="bi bi-shield-x fs-1 text-muted"></i>
                            </div>
                            <h6 class="fw-bold text-muted">No Permissions Assigned</h6>
                            <p class="text-muted small">This role does not have any special access rights.</p>
                            @can('role_edit')
                            <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-sm btn-primary mt-2">
                                Assign Permissions
                            </a>
                            @endcan
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        
        // 1. Client-side Permission Filter
        const searchInput = document.getElementById('permissionSearch');
        const container = document.getElementById('permissionContainer');
        
        if(searchInput && container) {
            const badges = container.querySelectorAll('.permission-badge');
            const noResults = document.getElementById('noResults');

            searchInput.addEventListener('keyup', function(e) {
                const term = e.target.value.toLowerCase();
                let hasVisible = false;

                badges.forEach(badge => {
                    const text = badge.querySelector('.perm-text').textContent.toLowerCase();
                    if(text.includes(term)) {
                        badge.style.display = 'inline-flex';
                        hasVisible = true;
                    } else {
                        badge.style.display = 'none';
                    }
                });

                if(hasVisible) {
                    noResults.classList.add('d-none');
                } else {
                    noResults.classList.remove('d-none');
                }
            });
        }

        // 2. Delete Confirmation
        const deleteBtn = document.getElementById('deleteBtn');
        if(deleteBtn) {
            deleteBtn.addEventListener('click', function() {
                Swal.fire({
                    title: 'Delete Role?',
                    text: "Users assigned to this role will lose their access rights!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#94a3b8',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('deleteForm').submit();
                    }
                });
            });
        }
    });
</script>
@endsection