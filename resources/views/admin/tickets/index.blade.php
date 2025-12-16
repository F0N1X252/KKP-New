@extends('layouts.admin')

@section('page-title', 'Ticket Management')

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
        overflow: hidden; /* Penting untuk border-radius */
    }

    /* Responsive Table Scroll */
    .table-responsive {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch; /* Smooth scroll di mobile */
    }

    /* Scrollbar Styling untuk tabel (agar terlihat modern) */
    .table-responsive::-webkit-scrollbar {
        height: 8px;
    }
    .table-responsive::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 4px;
    }
    .table-responsive::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }
    .table-responsive::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    /* Force min-width agar tabel tidak gepeng di layar kecil */
    #ticketsTable {
        min-width: 1000px; /* Sesuaikan dengan kebutuhan kolom */
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
        white-space: nowrap; /* Mencegah header wrap */
    }
    
    table.dataTable tbody td {
        padding: 1.25rem 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.9rem;
        color: var(--text-main);
    }

    /* --- PAGINATION CUSTOMIZATION --- */
    .dataTables_wrapper .dataTables_paginate {
        padding-top: 1rem;
        display: flex;
        justify-content: flex-end;
        gap: 0.5rem;
        flex-wrap: wrap; /* Agar pagination turun ke bawah jika layar sangat sempit */
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

    .dataTables_wrapper .page-item.disabled .page-link {
        opacity: 0.4;
        cursor: not-allowed;
    }

    .dataTables_info {
        padding-top: 1.5rem;
        color: #94a3b8;
        font-size: 0.85rem;
        font-weight: 500;
    }

    /* --- FILTER TABS STYLE --- */
    .ticket-filter-tabs {
        display: flex;
        gap: 8px;
        margin-bottom: 0;
        flex-wrap: wrap; /* Agar tab turun ke bawah di mobile */
    }

    .filter-tab {
        padding: 8px 20px;
        border: 1px solid transparent;
        background: transparent;
        color: #64748b;
        font-weight: 600;
        font-size: 0.9rem;
        border-radius: 50px;
        transition: all 0.2s;
        position: relative;
        white-space: nowrap;
    }

    .filter-tab:hover {
        color: var(--primary-color);
        background: #f8fafc;
    }
    
    .filter-tab.active {
        background-color: #eef2ff;
        color: var(--primary-color);
        border-color: rgba(79, 70, 229, 0.1);
        box-shadow: 0 2px 5px rgba(79, 70, 229, 0.05);
    }

    /* --- COMPONENTS --- */
    .avatar-sm {
        width: 32px; height: 32px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.75rem; font-weight: 700;
        color: #fff;
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
    }

    .search-box .form-control {
        border-radius: 50px;
        padding-left: 45px;
        height: 45px;
        border: 1px solid #e2e8f0;
        background-color: #fff;
        font-size: 0.9rem;
    }
    
    .search-box i {
        position: absolute;
        left: 18px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 1.1rem;
    }

    /* Dark Mode Adjustments */
    [data-bs-theme="dark"] .table-card { background: var(--card-bg); }
    [data-bs-theme="dark"] table.dataTable thead th { background: #1e293b; border-bottom-color: #334155; }
    [data-bs-theme="dark"] table.dataTable tbody td { border-bottom-color: #334155; }
    [data-bs-theme="dark"] .search-box .form-control { background: #1e293b; border-color: #334155; color: #fff; }
    [data-bs-theme="dark"] .filter-tab:hover { background: #334155; color: #fff; }
    [data-bs-theme="dark"] .filter-tab.active { background: #3730a3; color: #fff; border-color: #4338ca; }
    [data-bs-theme="dark"] .page-link { color: #94a3b8; }
    [data-bs-theme="dark"] .page-link:hover { background: #334155; }
    [data-bs-theme="dark"] .table-responsive::-webkit-scrollbar-track { background: #374151; }
    [data-bs-theme="dark"] .table-responsive::-webkit-scrollbar-thumb { background: #4b5563; }
</style>
@endsection

@section('content')
<div class="container-fluid p-0">

    <!-- 1. Header & Actions -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="bg-primary bg-opacity-10 text-primary p-2 rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                <i class="bi bi-ticket-detailed-fill fs-3"></i>
            </div>
            <div>
                <h4 class="fw-bold m-0">Tickets</h4>
                <div class="text-muted small">Manage and track support requests</div>
            </div>
        </div>
        
        <div class="d-flex gap-2">
            @can('ticket_create')
            <a href="{{ route('admin.tickets.create') }}" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm rounded-pill px-4">
                <i class="bi bi-plus-lg"></i>
                <span>Create Ticket</span>
            </a>
            @endcan
        </div>
    </div>

    <!-- 2. Controls Area (Filter Tabs & Search) -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <div class="row align-items-center g-3">
                <div class="col-lg-7">
                    <div class="ticket-filter-tabs">
                        <button class="filter-tab active" data-status="">
                            All Tickets
                        </button>
                        <button class="filter-tab" data-status="1">
                            <i class="bi bi-envelope-open me-1"></i> Open
                        </button>
                        <button class="filter-tab" data-status="4">
                            <i class="bi bi-hourglass-split me-1"></i> Pending
                        </button>
                        <button class="filter-tab" data-status="2">
                            <i class="bi bi-check-circle me-1"></i> Closed
                        </button>
                        <button class="filter-tab" data-status="3">
                            <i class="bi bi-time-circle me-1"></i> On Progress
                        </button>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="position-relative search-box">
                        <i class="bi bi-search"></i>
                        <input type="text" class="form-control" id="customSearch" placeholder="Search by ticket ID, subject, or requester...">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Data Table -->
    <div class="card table-card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover w-100 mb-0" id="ticketsTable">
                    <thead>
                        <tr>
                            <th width="40" class="text-center ps-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="selectAll">
                                </div>
                            </th>
                            <th class="ps-3">Ticket ID</th>
                            <th style="min-width: 250px;">Subject</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th>Category</th>
                            <th>Requester</th>
                            <th>Assigned</th>
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
                @can('ticket_delete')
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
        let currentStatusFilter = ''; // Variable untuk menyimpan status aktif

        // 1. Initialize DataTable
        let table = $('#ticketsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "/api/v1/tickets",
                type: "GET",
                headers: {
                    "Authorization": "Bearer " + apiToken,
                    "Accept": "application/json"
                },
                data: function(d) {
                    d.page = (d.start / d.length) + 1;
                    d.per_page = d.length;
                    d.search = $('#customSearch').val();
                    if(currentStatusFilter !== '') {
                        d.status_id = currentStatusFilter;
                    }
                },
                dataSrc: function(json) {
                    if (json.meta) {
                        json.recordsTotal = json.meta.total;
                        json.recordsFiltered = json.meta.total;
                    } else {
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
                    render: function(data, type, row) {
                        let cleanContent = row.content ? row.content.replace(/<[^>]*>?/gm, '') : '';
                        let snippet = cleanContent.length > 50 ? cleanContent.substring(0, 50) + "..." : cleanContent;
                        
                        return `<div class="d-flex flex-column">
                                    <a href="/admin/tickets/${row.id}" class="text-decoration-none fw-bold text-body hover-primary mb-1 text-truncate" style="max-width:250px" title="${data}">${data}</a>
                                    <small class="text-muted text-truncate" style="max-width: 250px;">${snippet}</small>
                                </div>`;
                    }
                },
                { 
                    data: 'status.name', 
                    render: function(data) {
                        let color = 'secondary';
                        let bg = 'bg-secondary';
                        let label = data || 'Unknown';
                        
                        if(label === 'Open') { color = 'success'; bg = 'bg-success'; }
                        if(label === 'On Progress') { color = 'info'; bg = 'bg-info'; }
                        if(label === 'Pending') { color = 'warning'; bg = 'bg-warning'; }
                        if(label === 'Closed') { color = 'dark'; bg = 'bg-light'; }
                        
                        return `<span class="badge ${bg} bg-opacity-10 text-${color} border border-${color} border-opacity-10 px-2 py-1 rounded-pill text-nowrap">${label}</span>`;
                    }
                },
                { 
                    data: 'priority.name', 
                    render: function(data) {
                        let icon = 'bi-dash-lg';
                        let color = 'text-muted';
                        let label = data || 'None';

                        if(label === 'High' || label === 'Critical') { icon = 'bi-arrow-up-circle-fill'; color = 'text-danger'; }
                        if(label === 'Medium') { icon = 'bi-dash-circle-fill'; color = 'text-warning'; }
                        if(label === 'Low') { icon = 'bi-arrow-down-circle-fill'; color = 'text-success'; }

                        return `<div class="d-flex align-items-center ${color} fw-medium text-nowrap" style="font-size: 0.85rem;"><i class="bi ${icon} me-2"></i> ${label}</div>`;
                    }
                },
                { 
                    data: 'category.name', 
                    render: function(data) { return `<span class="badge bg-light text-dark border fw-normal text-nowrap">${data || '-'}</span>`; }
                },
                { 
                    data: 'author_name',
                    render: function(data, type, row) {
                        return `<div class="d-flex flex-column">
                                    <span class="fw-semibold text-dark text-truncate" style="max-width: 150px;">${data}</span>
                                    <span class="small text-muted text-truncate" style="font-size: 0.75rem; max-width: 150px;">${row.author_email}</span>
                                </div>`;
                    }
                },
                { 
                    data: 'assigned_to_user.name', 
                    defaultContent: '',
                    render: function(data) {
                        if(!data) return '<span class="badge bg-light text-muted border border-dashed fst-italic fw-normal text-nowrap">Unassigned</span>';
                        let initial = data.charAt(0).toUpperCase();
                        return `<div class="d-flex align-items-center" data-bs-toggle="tooltip" title="${data}">
                                    <div class="avatar-sm me-2 shadow-sm flex-shrink-0">${initial}</div>
                                    <span class="d-none d-lg-inline small fw-medium text-truncate" style="max-width:100px">${data}</span>
                                </div>`;
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
                                    <li><a class="dropdown-item py-2" href="/admin/tickets/${data}"><i class="bi bi-eye me-2 text-primary"></i> View Details</a></li>
                                    <li><a class="dropdown-item py-2" href="/admin/tickets/${data}/edit"><i class="bi bi-pencil me-2 text-warning"></i> Edit Ticket</a></li>
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
                info: "Showing _START_ to _END_ of _TOTAL_ tickets",
                paginate: { next: '<i class="bi bi-chevron-right"></i>', previous: '<i class="bi bi-chevron-left"></i>' },
                emptyTable: "No tickets found matching your criteria"
            },
            drawCallback: function() {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl)
                })
            }
        });

        // 2. Custom Search
        let searchTimeout;
        $('#customSearch').on('keyup', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                table.draw();
            }, 400); 
        });

        // 3. Filter Tabs
        $('.filter-tab').on('click', function() {
            $('.filter-tab').removeClass('active');
            $(this).addClass('active');
            currentStatusFilter = $(this).data('status');
            table.draw();
        });

        // 4. Bulk Delete Logic
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

        // 5. Delete Logic
        $(document).on('click', '.delete-btn', function() { deleteConfirm([$(this).data('id')]); });
        $('#massDeleteBtn').on('click', function() {
            let ids = [];
            $('.dt-checkboxes:checked').each(function() { ids.push($(this).val()); });
            deleteConfirm(ids);
        });

        function deleteConfirm(ids) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Yes, delete it!',
                customClass: { confirmButton: 'btn btn-danger', cancelButton: 'btn btn-light border me-2' },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('api.tickets.bulkDelete') }}",
                        type: "DELETE",
                        headers: { "Authorization": "Bearer " + apiToken },
                        data: { ids: ids },
                        success: function() {
                            Swal.fire({ icon: 'success', title: 'Deleted!', showConfirmButton: false, timer: 1500 });
                            table.draw();
                            $('#bulkActions').addClass('d-none');
                            $('#selectAll').prop('checked', false);
                        },
                        error: function() {
                            Swal.fire('Error!', 'Failed to delete tickets.', 'error');
                        }
                    });
                }
            });
        }
    });
</script>
@endsection