@extends('layouts.admin')

@section('page-title', 'User Management')

@section('styles')
<!-- SweetAlert2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<style>
    /* --- Table & Card Styles --- */
    .table-card {
        overflow: hidden;
        border: none;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }

    /* --- Avatar Style --- */
    .user-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 14px;
        background-color: rgba(79, 70, 229, 0.1);
        color: #4f46e5;
        margin-right: 12px;
    }

    /* --- Badge Styles --- */
    .badge-soft {
        padding: 0.35em 0.65em;
        font-size: 0.75em;
        font-weight: 600;
        border-radius: 6px;
        display: inline-block;
        margin-right: 4px;
    }
    .badge-soft-success { background-color: rgba(16, 185, 129, 0.1); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.2); }
    .badge-soft-warning { background-color: rgba(245, 158, 11, 0.1); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.2); }
    .badge-soft-info    { background-color: rgba(56, 189, 248, 0.1); color: #0ea5e9; border: 1px solid rgba(56, 189, 248, 0.2); }

    /* --- Action Buttons --- */
    .btn-icon {
        width: 32px; height: 32px;
        display: inline-flex; align-items: center; justify-content: center;
        border-radius: 8px;
        color: #64748b;
        transition: all 0.2s;
        background: transparent;
        border: none;
    }
    .btn-icon:hover { background-color: #f1f5f9; transform: translateY(-2px); }
    .btn-icon.view:hover { color: #4f46e5; }
    .btn-icon.edit:hover { color: #f59e0b; }
    .btn-icon.delete:hover { color: #ef4444; }

    /* --- Bulk Action Bar --- */
    #bulkActions {
        background-color: var(--card-bg);
        border-top: 1px solid var(--border-color);
        transition: all 0.3s ease;
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
                <i class="bi bi-people-fill fs-3"></i>
            </div>
            <div>
                <div class="text-muted small">Administration</div>
                <h4 class="fw-bold m-0">Users</h4>
            </div>
        </div>
        
        @can('user_create')
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm rounded-pill px-4">
            <i class="bi bi-person-plus-fill"></i>
            <span>Add User</span>
        </a>
        @endcan
    </div>

    <!-- 2. Data Table Card -->
    <div class="card table-card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle w-100 mb-0" id="usersTable">
                    <thead class="bg-light">
                        <tr>
                            <th width="40" class="text-center ps-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="selectAll">
                                </div>
                            </th>
                            <th class="text-uppercase text-muted small fw-bold ps-3" width="80">ID</th>
                            <th class="text-uppercase text-muted small fw-bold">User</th>
                            <th class="text-uppercase text-muted small fw-bold">Email</th>
                            <th class="text-uppercase text-muted small fw-bold">Verified</th>
                            <th class="text-uppercase text-muted small fw-bold">Roles</th>
                            <th class="text-end text-uppercase text-muted small fw-bold pe-4" width="150">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $key => $user)
                            <tr data-entry-id="{{ $user->id }}">
                                <td class="text-center ps-4">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input dt-checkboxes" value="{{ $user->id }}">
                                    </div>
                                </td>
                                <td class="ps-3 fw-bold text-muted">#{{ $user->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="user-avatar">
                                            {{ substr($user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-bold text-dark">{{ $user->name }}</h6>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-muted">{{ $user->email }}</td>
                                <td>
                                    @if($user->email_verified_at)
                                        <span class="badge badge-soft-success">Verified</span>
                                    @else
                                        <span class="badge badge-soft-warning">Pending</span>
                                    @endif
                                </td>
                                <td>
                                    @foreach($user->roles as $role)
                                        <span class="badge badge-soft-info">{{ $role->title }}</span>
                                    @endforeach
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-flex justify-content-end gap-1">
                                        @can('user_show')
                                            <a href="{{ route('admin.users.show', $user->id) }}" class="btn-icon view" data-bs-toggle="tooltip" title="View Details">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        @endcan

                                        @can('user_edit')
                                            <a href="{{ route('admin.users.edit', $user->id) }}" class="btn-icon edit" data-bs-toggle="tooltip" title="Edit User">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        @endcan

                                        @can('user_delete')
                                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline delete-form">
                                                <input type="hidden" name="_method" value="DELETE">
                                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                <button type="submit" class="btn-icon delete" data-bs-toggle="tooltip" title="Delete User">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Bulk Action Bar -->
        <div class="card-footer p-3 d-none" id="bulkActions">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <div class="bg-primary bg-opacity-10 text-primary px-3 py-1 rounded-pill fw-bold small">
                        <span id="selectedCount">0</span> Selected
                    </div>
                    <span class="text-muted small">items selected</span>
                </div>
                
                @can('user_delete')
                <button type="button" class="btn btn-danger btn-sm rounded-pill px-4" id="massDeleteBtn">
                    <i class="bi bi-trash me-1"></i> Delete Selection
                </button>
                @endcan
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@parent
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(function () {
        // 1. Initialize DataTable
        let table = $('#usersTable').DataTable({
            language: {
                search: "",
                searchPlaceholder: "Search users..."
            },
            dom: '<"d-flex justify-content-between align-items-center p-3"f>rt<"d-flex justify-content-between align-items-center p-3"ip>',
            columnDefs: [
                { orderable: false, targets: 0 }, 
                { orderable: false, targets: -1 } 
            ],
            order: [[ 1, 'asc' ]],
            pageLength: 25
        });

        // Style Search Input
        $('.dataTables_filter input').addClass('form-control').css('width', '250px');

        // 2. Select All Logic
        $('#selectAll').on('click', function() {
            let checked = $(this).prop('checked');
            $('.dt-checkboxes').prop('checked', checked);
            toggleBulkActions();
        });

        $(document).on('change', '.dt-checkboxes', function() {
            toggleBulkActions();
            if (!$(this).prop('checked')) {
                $('#selectAll').prop('checked', false);
            }
        });

        function toggleBulkActions() {
            let count = $('.dt-checkboxes:checked').length;
            $('#selectedCount').text(count);
            if(count > 0) {
                $('#bulkActions').removeClass('d-none');
            } else {
                $('#bulkActions').addClass('d-none');
            }
        }

        // 3. Mass Delete Logic (SweetAlert)
        $('#massDeleteBtn').on('click', function() {
            let ids = [];
            $('.dt-checkboxes:checked').each(function() {
                ids.push($(this).val());
            });

            if (ids.length > 0) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: `You are about to delete ${ids.length} users. This cannot be undone!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#94a3b8',
                    confirmButtonText: 'Yes, delete them!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            headers: {'x-csrf-token': "{{ csrf_token() }}"},
                            method: 'POST',
                            url: "{{ route('admin.users.massDestroy') }}",
                            data: { ids: ids, _method: 'DELETE' }
                        })
                        .done(function () {
                            Swal.fire('Deleted!', 'Selected users have been deleted.', 'success').then(() => {
                                location.reload();
                            });
                        });
                    }
                });
            }
        });

        // 4. Single Delete Confirmation (SweetAlert)
        $(document).on('submit', '.delete-form', function(e) {
            e.preventDefault();
            let form = this;

            Swal.fire({
                title: 'Delete User?',
                text: "This action cannot be undone.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });

        // Initialize Tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    })
</script>
@endsection