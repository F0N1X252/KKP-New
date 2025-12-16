@extends('layouts.admin')

@section('page-title', 'Permissions Management')

@section('styles')
<!-- DataTables Bootstrap 5 & SweetAlert -->
<link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

<style>
    /* --- TABLE CARD --- */
    .table-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        overflow: hidden;
    }
    
    /* --- TABLE HEADER & BODY --- */
    table.dataTable thead th {
        background-color: #f8fafc;
        border-bottom: 2px solid #e2e8f0;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        font-weight: 700;
        padding: 1.25rem 1rem;
    }
    
    table.dataTable tbody td {
        padding: 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.9rem;
        color: var(--text-main);
    }

    /* --- PERMISSION BADGE --- */
    .badge-code {
        font-family: 'Courier New', Courier, monospace;
        font-size: 0.85rem;
        background-color: rgba(99, 102, 241, 0.1);
        color: var(--primary-color);
        border: 1px solid rgba(99, 102, 241, 0.2);
        padding: 4px 8px;
        border-radius: 6px;
        font-weight: 600;
    }

    /* --- PAGINATION & SEARCH --- */
    .search-box .form-control {
        border-radius: 50px;
        padding-left: 45px;
        height: 45px;
        border: 1px solid #e2e8f0;
        background-color: #fff;
    }
    
    .search-box i {
        position: absolute;
        left: 18px; top: 50%; transform: translateY(-50%);
        color: #94a3b8; font-size: 1.1rem;
    }

    .dataTables_wrapper .dataTables_paginate {
        padding-top: 1rem;
        display: flex;
        justify-content: flex-end;
    }

    .dataTables_wrapper .page-item .page-link {
        border: none;
        border-radius: 8px;
        color: #64748b;
        background: transparent;
        padding: 0.5rem 1rem;
        font-weight: 600;
        font-size: 0.85rem;
        margin: 0 2px;
        transition: all 0.2s;
    }

    .dataTables_wrapper .page-item.active .page-link {
        background-color: var(--primary-color);
        color: #fff;
        box-shadow: 0 4px 10px rgba(79, 70, 229, 0.2);
    }

    /* Dark Mode Support */
    [data-bs-theme="dark"] .table-card { background: var(--card-bg); }
    [data-bs-theme="dark"] table.dataTable thead th { background: #1e293b; border-bottom-color: #334155; }
    [data-bs-theme="dark"] table.dataTable tbody td { border-bottom-color: #334155; }
    [data-bs-theme="dark"] .search-box .form-control { background: #1e293b; border-color: #334155; color: #fff; }
    [data-bs-theme="dark"] .badge-code { background: rgba(99, 102, 241, 0.2); color: #a5b4fc; border-color: rgba(99, 102, 241, 0.3); }
</style>
@endsection

@section('content')
<div class="container-fluid p-0">

    <!-- 1. Header & Actions -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="bg-primary bg-opacity-10 text-primary p-2 rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                <i class="bi bi-key-fill fs-3"></i>
            </div>
            <div>
                <h4 class="fw-bold m-0">Permissions</h4>
                <div class="text-muted small">System Access Control List</div>
            </div>
        </div>
        
        @can('permission_create')
        <a href="{{ route('admin.permissions.create') }}" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm rounded-pill px-4">
            <i class="bi bi-plus-lg"></i>
            <span>Add Permission</span>
        </a>
        @endcan
    </div>

    <!-- 2. Search Box -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <div class="position-relative search-box">
                <i class="bi bi-search"></i>
                <input type="text" class="form-control" id="customSearch" placeholder="Search permissions (e.g. user_create)...">
            </div>
        </div>
    </div>

    <!-- 3. Data Table -->
    <div class="card table-card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover w-100 mb-0" id="permissionsTable">
                    <thead>
                        <tr>
                            <th width="40" class="text-center ps-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="selectAll">
                                </div>
                            </th>
                            <th class="ps-3" width="80">ID</th>
                            <th>Permission Key</th>
                            <th>Created At</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
        
        <!-- Bulk Actions Sticky Footer -->
        <div class="card-footer bg-white border-top p-3 d-none animate__animated animate__fadeInUp" id="bulkActions">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-primary bg-opacity-10 text-primary px-3 py-1 rounded-pill fw-bold small">
                        <span id="selectedCount">0</span> Selected
                    </div>
                    <span class="text-muted small">items selected</span>
                </div>
                @can('permission_delete')
                <button type="button" class="btn btn-danger btn-sm rounded-pill px-4 shadow-sm" id="massDeleteBtn">
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(function () {
        const apiToken = document.querySelector('meta[name="api-token"]').getAttribute('content');
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // 1. Initialize DataTable
        let table = $('#permissionsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('api.permissions.index') }}", // Menggunakan API Route
                type: "GET",
                headers: {
                    "Authorization": "Bearer " + apiToken,
                    "Accept": "application/json"
                },
                data: function(d) {
                    d.search = $('#customSearch').val();
                },
                dataSrc: function(json) {
                    // Mapping API Laravel Resource ke DataTables
                    // Jika API mengembalikan array langsung (Resource Collection), bungkus dalam format DT
                    if(!json.recordsTotal) {
                        json.recordsTotal = json.data.length;
                        json.recordsFiltered = json.data.length;
                    }
                    return json.data;
                },
                error: function (xhr) {
                    if(xhr.status == 401) window.location.href = "{{ route('login') }}";
                }
            },
            columns: [
                { 
                    data: 'id', orderable: false, searchable: false, className: 'text-center ps-4',
                    render: function(data) {
                        return `<div class="form-check"><input type="checkbox" class="form-check-input dt-checkboxes" value="${data}"></div>`;
                    }
                },
                { 
                    data: 'id', className: 'ps-3',
                    render: function(data) { return `<span class="fw-bold text-muted">#${data}</span>`; }
                },
                { 
                    data: 'title',
                    render: function(data) {
                        return `<span class="badge-code">${data}</span>`;
                    }
                },
                {
                    data: 'created_at',
                    render: function(data) {
                        // Format tanggal sederhana jika data ada
                        return data ? new Date(data).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' }) : '-';
                    }
                },
                { 
                    data: 'id', orderable: false, className: 'text-end pe-4',
                    render: function(data) {
                        return `
                            <div class="dropdown">
                                <button class="btn btn-light btn-sm rounded-circle shadow-sm border" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots text-muted"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end border-0 shadow rounded-3">
                                    <li><a class="dropdown-item py-2" href="/admin/permissions/${data}"><i class="bi bi-eye me-2 text-primary"></i> View</a></li>
                                    <li><a class="dropdown-item py-2" href="/admin/permissions/${data}/edit"><i class="bi bi-pencil me-2 text-warning"></i> Edit</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><button class="dropdown-item py-2 text-danger delete-btn" data-id="${data}"><i class="bi bi-trash me-2"></i> Delete</button></li>
                                </ul>
                            </div>
                        `;
                    }
                }
            ],
            order: [[ 1, 'desc' ]],
            pageLength: 10,
            dom: 'rt<"row align-items-center justify-content-between p-3"<"col-md-6"i><"col-md-6"p>>',
            language: {
                paginate: { next: '<i class="bi bi-chevron-right"></i>', previous: '<i class="bi bi-chevron-left"></i>' },
                emptyTable: "No permissions found"
            }
        });

        // 2. Custom Search
        let searchTimeout;
        $('#customSearch').on('keyup', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => { table.draw(); }, 400); 
        });

        // 3. Bulk Actions Logic
        $('#selectAll').on('click', function() {
            $('.dt-checkboxes').prop('checked', this.checked);
            toggleBulkActions();
        });

        $(document).on('change', '.dt-checkboxes', function() {
            toggleBulkActions();
            if(!this.checked) $('#selectAll').prop('checked', false);
        });

        function toggleBulkActions() {
            let count = $('.dt-checkboxes:checked').length;
            $('#selectedCount').text(count);
            count > 0 ? $('#bulkActions').removeClass('d-none') : $('#bulkActions').addClass('d-none');
        }

        // 4. Delete Logic
        $(document).on('click', '.delete-btn', function() {
            let id = $(this).data('id');
            deleteConfirm([id]);
        });

        $('#massDeleteBtn').on('click', function() {
            let ids = [];
            $('.dt-checkboxes:checked').each(function() { ids.push($(this).val()); });
            deleteConfirm(ids);
        });

        function deleteConfirm(ids) {
            if(ids.length === 0) return;

            Swal.fire({
                title: 'Are you sure?',
                text: "Removing permissions may affect user access!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Yes, delete it!',
                customClass: { confirmButton: 'btn btn-danger', cancelButton: 'btn btn-light border me-2' },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('admin.permissions.massDestroy') }}",
                        method: 'POST',
                        data: { 
                            _token: csrfToken,
                            ids: ids, 
                            _method: 'DELETE' 
                        },
                        success: function() {
                            Swal.fire({ icon: 'success', title: 'Deleted!', showConfirmButton: false, timer: 1500 });
                            table.draw();
                            $('#bulkActions').addClass('d-none');
                            $('#selectAll').prop('checked', false);
                        },
                        error: function() {
                            Swal.fire('Error!', 'Failed to delete permission.', 'error');
                        }
                    });
                }
            });
        }
    });
</script>
@endsection