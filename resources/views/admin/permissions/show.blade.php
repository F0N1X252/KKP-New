@extends('layouts.admin')

@section('page-title', 'Permission Details')

@section('styles')
<!-- SweetAlert2 -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<style>
    /* --- LABEL & VALUE TYPOGRAPHY --- */
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

    /* --- PERMISSION CODE BOX --- */
    .permission-code-box {
        background-color: rgba(79, 70, 229, 0.05);
        border: 1px dashed rgba(79, 70, 229, 0.3);
        border-radius: 12px;
        padding: 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: all 0.2s;
    }
    
    .permission-code-box:hover {
        background-color: rgba(79, 70, 229, 0.08);
        border-color: var(--primary-color);
    }

    .code-text {
        font-family: 'Courier New', Courier, monospace;
        font-weight: 700;
        font-size: 1.2rem;
        color: var(--primary-color);
    }

    /* --- ACTION BUTTONS --- */
    .btn-copy {
        width: 36px; height: 36px;
        border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        background: #fff;
        border: 1px solid var(--border-color);
        color: var(--text-muted);
        transition: all 0.2s;
    }
    .btn-copy:hover {
        color: var(--primary-color);
        border-color: var(--primary-color);
        transform: translateY(-2px);
    }

    /* Dark Mode Adjustments */
    [data-bs-theme="dark"] .btn-copy {
        background: #1f2937;
        border-color: #4b5563;
    }
    [data-bs-theme="dark"] .bg-light {
        background-color: #1f2937 !important;
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
                <i class="bi bi-shield-lock-fill fs-3"></i>
            </div>
            <div>
                <h4 class="fw-bold m-0">Permission Details</h4>
                <div class="text-muted small">Viewing access control definition</div>
            </div>
        </div>
        
        <div class="d-flex gap-2">
            <a href="{{ route('admin.permissions.index') }}" class="btn btn-light border shadow-sm">
                <i class="bi bi-arrow-left me-1"></i> Back to List
            </a>
            @can('permission_edit')
            <a href="{{ route('admin.permissions.edit', $permission->id) }}" class="btn btn-warning text-white shadow-sm">
                <i class="bi bi-pencil-square me-1"></i> Edit
            </a>
            @endcan
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
                        <h6 class="fw-bold m-0 text-dark">General Information</h6>
                        <span class="badge bg-light text-muted border">ID: #{{ $permission->id }}</span>
                    </div>

                    <!-- Permission Title (Highlighted) -->
                    <div class="mb-4">
                        <div class="detail-label">Permission Title / Key</div>
                        <div class="permission-code-box">
                            <span class="code-text" id="permTitle">{{ $permission->title }}</span>
                            <button class="btn-copy shadow-sm" onclick="copyToClipboard()" data-bs-toggle="tooltip" title="Copy to clipboard">
                                <i class="bi bi-clipboard"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Timestamps -->
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 border h-100">
                                <div class="detail-label"><i class="bi bi-calendar-plus me-1"></i> Created At</div>
                                <div class="detail-value">{{ $permission->created_at ? $permission->created_at->format('d M Y, H:i') : '-' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 border h-100">
                                <div class="detail-label"><i class="bi bi-calendar-check me-1"></i> Updated At</div>
                                <div class="detail-value">{{ $permission->updated_at ? $permission->updated_at->format('d M Y, H:i') : '-' }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Danger Zone (Delete) -->
                    @can('permission_delete')
                    <div class="mt-5 pt-3 border-top">
                        <button class="btn btn-outline-danger btn-sm" id="deleteBtn">
                            <i class="bi bi-trash me-1"></i> Delete Permission
                        </button>
                        <form id="deleteForm" action="{{ route('admin.permissions.destroy', $permission->id) }}" method="POST" class="d-none">
                            @csrf @method('DELETE')
                        </form>
                    </div>
                    @endcan

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Copy to Clipboard Function
    function copyToClipboard() {
        const text = document.getElementById('permTitle').innerText;
        navigator.clipboard.writeText(text).then(() => {
            // Show toast notification
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: false,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            Toast.fire({
                icon: 'success',
                title: 'Copied to clipboard'
            });
            
            // Change icon momentarily
            const btn = document.querySelector('.btn-copy i');
            btn.classList.remove('bi-clipboard');
            btn.classList.add('bi-check-lg');
            setTimeout(() => {
                btn.classList.remove('bi-check-lg');
                btn.classList.add('bi-clipboard');
            }, 2000);
        });
    }

    document.addEventListener("DOMContentLoaded", function() {
        // Init Tooltip
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        // Delete Confirmation
        const deleteBtn = document.getElementById('deleteBtn');
        if(deleteBtn) {
            deleteBtn.addEventListener('click', function() {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "Deleting a permission might break system functionality!",
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