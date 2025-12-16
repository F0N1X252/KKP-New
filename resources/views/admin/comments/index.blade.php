@extends('layouts.admin')

@section('page-title', 'Comments Management')

@section('styles')
<!-- DataTables Bootstrap 5 & SweetAlert -->
<link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

<style>
    /* --- TABLE STYLING --- */
    .table-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        overflow: hidden;
    }
    
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
        padding: 1.25rem 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.9rem;
        color: var(--text-main);
    }

    /* --- PAGINATION & SEARCH --- */
    .dataTables_wrapper .dataTables_paginate {
        padding-top: 1rem;
        display: flex;
        justify-content: flex-end;
        gap: 0.5rem;
    }

    .dataTables_wrapper .page-item .page-link {
        border: none;
        border-radius: 8px;
        color: #64748b;
        background: transparent;
        padding: 0.5rem 1rem;
        font-weight: 600;
        font-size: 0.85rem;
        transition: all 0.2s ease;
        margin: 0 2px;
    }

    .dataTables_wrapper .page-item .page-link:hover {
        background-color: #f1f5f9;
        color: var(--primary-color);
        transform: translateY(-1px);
    }

    .dataTables_wrapper .page-item.active .page-link {
        background-color: var(--primary-color);
        color: #fff;
        box-shadow: 0 4px 10px rgba(79, 70, 229, 0.2);
    }

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

    /* --- AVATAR --- */
    .avatar-circle {
        width: 38px; height: 38px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; color: #fff;
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        box-shadow: 0 2px 5px rgba(99, 102, 241, 0.2);
    }

    /* Dark Mode Adjustments */
    [data-bs-theme="dark"] .table-card { background: var(--card-bg); }
    [data-bs-theme="dark"] table.dataTable thead th { background: #1e293b; border-bottom-color: #334155; }
    [data-bs-theme="dark"] table.dataTable tbody td { border-bottom-color: #334155; }
    [data-bs-theme="dark"] .search-box .form-control { background: #1e293b; border-color: #334155; color: #fff; }
    [data-bs-theme="dark"] .page-link { color: #94a3b8; }
    [data-bs-theme="dark"] .page-link:hover { background: #334155; }
</style>
@endsection

@section('content')
<div class="container-fluid p-0">

    <!-- 1. Header & Actions -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="bg-primary bg-opacity-10 text-primary p-2 rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                <i class="bi bi-chat-left-quote-fill fs-3"></i>
            </div>
            <div>
                <h4 class="fw-bold m-0">Comments</h4>
                <div class="text-muted small">Manage discussion threads</div>
            </div>
        </div>
        
        <div class="d-flex gap-2">
            @can('comment_create')
            <a href="{{ route('admin.comments.create') }}" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm rounded-pill px-4">
                <i class="bi bi-plus-lg"></i>
                <span>Add Comment</span>
            </a>
            @endcan
        </div>
    </div>

    <!-- 2. Search Box -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <div class="position-relative search-box">
                <i class="bi bi-search"></i>
                <input type="text" class="form-control" id="customSearch" placeholder="Search comments, author, or ticket...">
            </div>
        </div>
    </div>

    <!-- 3. Data Table -->
    <div class="card table-card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover w-100 mb-0" id="commentsTable">
                    <thead>
                        <tr>
                            <th width="40" class="text-center ps-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="selectAll">
                                </div>
                            </th>
                            <th class="ps-3">ID</th>
                            <th>Author</th>
                            <th>Ticket</th>
                            <th width="40%">Content</th>
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
                </div>
                @can('comment_delete')
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
        // PERBAIKAN: Mendefinisikan token dari meta tag
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // 1. Initialize DataTable
        let table = $('#commentsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.comments.index') }}",
                data: function(d) {
                    d.search.value = $('#customSearch').val(); // Override default search
                }
            },
            columns: [
                { 
                    data: 'placeholder', name: 'placeholder', orderable: false, searchable: false, className: 'text-center ps-4',
                    render: function(data, type, row) {
                        return `<div class="form-check"><input type="checkbox" class="form-check-input dt-checkboxes" value="${row.id}"></div>`;
                    }
                },
                { 
                    data: 'id', name: 'id', className: 'ps-3',
                    render: function(data) { return `<span class="fw-bold text-muted">#${data}</span>`; }
                },
                { 
                    data: 'author_name', name: 'author_name',
                    render: function(data, type, row) {
                        let initial = data ? data.charAt(0).toUpperCase() : '?';
                        let email = row.author_email || '-';
                        return `<div class="d-flex align-items-center">
                                    <div class="avatar-circle me-3">${initial}</div>
                                    <div class="d-flex flex-column">
                                        <span class="fw-semibold text-dark">${data}</span>
                                        <span class="small text-muted">${email}</span>
                                    </div>
                                </div>`;
                    }
                },
                { 
                    data: 'ticket.title', name: 'ticket.title',
                    render: function(data, type, row) {
                        if(!data) return '<span class="badge bg-light text-muted border border-dashed">Deleted Ticket</span>';
                        let link = `/admin/tickets/${row.ticket.id}`; // Sesuaikan route show ticket
                        return `<a href="${link}" class="text-decoration-none fw-medium text-primary hover-underline">
                                    <i class="bi bi-ticket-perforated me-1"></i> ${data}
                                </a>`;
                    }
                },
                { 
                    data: 'comment_text', name: 'comment_text',
                    render: function(data) {
                        // Strip HTML and truncate
                        let div = document.createElement("div");
                        div.innerHTML = data;
                        let text = div.textContent || div.innerText || "";
                        return `<div class="text-muted small text-truncate" style="max-width: 400px;">"${text}"</div>`;
                    }
                },
                { 
                    data: 'actions', name: '{{ trans('global.actions') }}', orderable: false, searchable: false, className: 'text-end pe-4',
                    render: function(data, type, row) {
                        // Render manual buttons to ensure style consistency
                        return `
                            <div class="d-flex justify-content-end gap-1">
                                @can('comment_show')
                                <a href="/admin/comments/${row.id}" class="btn btn-light btn-sm text-primary border" title="View"><i class="bi bi-eye"></i></a>
                                @endcan
                                @can('comment_edit')
                                <a href="/admin/comments/${row.id}/edit" class="btn btn-light btn-sm text-warning border" title="Edit"><i class="bi bi-pencil"></i></a>
                                @endcan
                                @can('comment_delete')
                                <button class="btn btn-light btn-sm text-danger border delete-btn" data-id="${row.id}" title="Delete"><i class="bi bi-trash"></i></button>
                                @endcan
                            </div>
                        `;
                    }
                }
            ],
            order: [[ 1, 'desc' ]],
            pageLength: 10,
            dom: 'rt<"row align-items-center justify-content-between p-3"<"col-md-6"i><"col-md-6"p>>',
            language: {
                paginate: { next: '<i class="bi bi-chevron-right"></i>', previous: '<i class="bi bi-chevron-left"></i>' }
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

        // 4. Delete Logic (Mass & Single)
        // Single Delete
        $(document).on('click', '.delete-btn', function() {
            let id = $(this).data('id');
            deleteConfirm([id]);
        });

        // Mass Delete
        $('#massDeleteBtn').on('click', function() {
            let ids = [];
            $('.dt-checkboxes:checked').each(function() { ids.push($(this).val()); });
            deleteConfirm(ids);
        });

        function deleteConfirm(ids) {
            if(ids.length === 0) return;

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Yes, delete it!',
                customClass: { confirmButton: 'btn btn-danger', cancelButton: 'btn btn-light border me-2' },
                buttonsStyling: false,
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('admin.comments.massDestroy') }}",
                        method: 'POST',
                        data: { 
                            _token: csrfToken, // FIX: Menggunakan variabel csrfToken yang sudah didefinisikan
                            ids: ids, 
                            _method: 'DELETE' 
                        },
                        success: function() {
                            Swal.fire({ icon: 'success', title: 'Deleted!', showConfirmButton: true, timer: 1500 });
                            table.draw();
                            $('#bulkActions').addClass('d-none');
                            $('#selectAll').prop('checked', false);
                        },
                        error: function() {
                            Swal.fire('Error!', 'Failed to delete comments.', 'error');
                        }
                    });
                }
            });
        }
    });
</script>
@endsection