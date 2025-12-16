@extends('layouts.admin')

@section('page-title', 'User Profile: ' . $user->name)

@section('styles')
<!-- SweetAlert2 -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<style>
    /* --- PROFILE CARD STYLES --- */
    .profile-card-header {
        height: 120px;
        background: linear-gradient(135deg, var(--primary-color), #818cf8);
        position: relative;
    }

    .avatar-profile {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        border: 4px solid var(--card-bg);
        background-color: #fff;
        color: var(--primary-color);
        font-size: 2.5rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        position: absolute;
        bottom: -50px;
        left: 50%;
        transform: translateX(-50%);
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }

    /* --- INFO LABELS --- */
    .detail-label {
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: var(--text-muted);
        font-weight: 700;
        margin-bottom: 0.25rem;
    }

    .detail-value {
        font-size: 0.95rem;
        font-weight: 500;
        color: var(--text-main);
    }

    /* --- ROLE BADGES --- */
    .role-badge {
        padding: 8px 16px;
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 600;
        background-color: rgba(99, 102, 241, 0.1);
        color: #4f46e5;
        border: 1px solid rgba(99, 102, 241, 0.2);
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
    }
    
    .role-badge:hover {
        background-color: #4f46e5;
        color: white;
        transform: translateY(-2px);
    }

    /* Dark Mode Support */
    [data-bs-theme="dark"] .avatar-profile {
        background-color: #1f2937;
        border-color: #1f2937;
    }
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
            <div class="bg-primary bg-opacity-10 text-primary p-2 rounded d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                <i class="bi bi-person-badge fs-3"></i>
            </div>
            <div>
                <h4 class="fw-bold m-0">User Profile</h4>
                <div class="text-muted small">Manage user details and access</div>
            </div>
        </div>
        
        <div class="d-flex gap-2">
            <a href="{{ route('admin.users.index') }}" class="btn btn-light border shadow-sm">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
            @can('user_edit')
            <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-primary shadow-sm">
                <i class="bi bi-pencil-square me-1"></i> Edit User
            </a>
            @endcan
        </div>
    </div>

    <div class="row g-4">
        <!-- LEFT COLUMN: Profile Card -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                <!-- Visual Header -->
                <div class="profile-card-header">
                    <div class="avatar-profile">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                </div>
                
                <div class="card-body pt-5 text-center mt-3">
                    <h4 class="fw-bold text-dark mb-1">{{ $user->name }}</h4>
                    <p class="text-muted mb-3">{{ $user->email }}</p>

                    <!-- Verification Badge -->
                    <div class="mb-4">
                        @if($user->email_verified_at)
                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-20 px-3 py-2 rounded-pill">
                                <i class="bi bi-patch-check-fill me-1"></i> Verified Account
                            </span>
                        @else
                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-20 px-3 py-2 rounded-pill">
                                <i class="bi bi-exclamation-circle-fill me-1"></i> Email Unverified
                            </span>
                        @endif
                    </div>

                    <!-- Short Stats -->
                    <div class="row border-top pt-4">
                        <div class="col-6 border-end">
                            <div class="detail-label">Joined Date</div>
                            <div class="fw-bold text-dark">{{ $user->created_at->format('M Y') }}</div>
                        </div>
                        <div class="col-6">
                            <div class="detail-label">User ID</div>
                            <div class="fw-bold text-dark">#{{ $user->id }}</div>
                        </div>
                    </div>

                    <!-- Delete Button -->
                    @can('user_delete')
                    <div class="mt-4 pt-3 border-top">
                        <button class="btn btn-outline-danger w-100" id="deleteBtn">
                            <i class="bi bi-trash me-2"></i> Delete User
                        </button>
                        <form id="deleteForm" action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-none">
                            @csrf @method('DELETE')
                        </form>
                    </div>
                    @endcan
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: Detailed Info -->
        <div class="col-lg-8">
            <!-- 1. Assigned Roles -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-shield-lock me-2 text-primary"></i>Assigned Roles</h6>
                </div>
                <div class="card-body p-4">
                    @if($user->roles->count() > 0)
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($user->roles as $role)
                                <a href="{{ route('admin.roles.show', $role->id) }}" class="text-decoration-none">
                                    <div class="role-badge">
                                        <i class="bi bi-person-gear"></i> {{ $role->title }}
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-light border-0 d-flex align-items-center mb-0">
                            <i class="bi bi-info-circle-fill me-2 text-muted"></i>
                            <span class="text-muted">This user has no roles assigned.</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- 2. System Information -->
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-info-circle me-2 text-primary"></i>System Information</h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 border h-100">
                                <div class="detail-label">Full Name</div>
                                <div class="detail-value fs-6">{{ $user->name }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 border h-100">
                                <div class="detail-label">Email Address</div>
                                <div class="detail-value fs-6">{{ $user->email }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 border h-100">
                                <div class="detail-label">Email Verified At</div>
                                <div class="detail-value">
                                    {{ $user->email_verified_at ? $user->email_verified_at->format('d M Y, H:i:s') : 'Not Verified' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 border h-100">
                                <div class="detail-label">Last Updated</div>
                                <div class="detail-value">
                                    {{ $user->updated_at->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                    </div>
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
        // Delete Confirmation
        const deleteBtn = document.getElementById('deleteBtn');
        if(deleteBtn) {
            deleteBtn.addEventListener('click', function() {
                Swal.fire({
                    title: 'Delete User?',
                    text: "All associated data like comments might be affected.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#94a3b8',
                    confirmButtonText: 'Yes, delete user!'
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