@extends('layouts.admin')

@section('page-title', 'System Audit Logs')

@section('styles')
<!-- DataTables Buttons CSS -->
<link href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css" rel="stylesheet" />
<style>
    /* --- Unique Page Styling (System Monitor Look) --- */
    .audit-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        overflow: hidden;
    }

    .audit-header {
        background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
        color: white;
        padding: 2rem;
        position: relative;
    }

    .audit-icon-bg {
        position: absolute;
        right: 20px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 5rem;
        opacity: 0.1;
        color: white;
    }

    /* --- Custom Badges for Actions --- */
    .log-badge {
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .log-created { background: rgba(16, 185, 129, 0.15); color: #059669; border: 1px solid rgba(16, 185, 129, 0.2); }
    .log-updated { background: rgba(245, 158, 11, 0.15); color: #d97706; border: 1px solid rgba(245, 158, 11, 0.2); }
    .log-deleted { background: rgba(239, 68, 68, 0.15); color: #dc2626; border: 1px solid rgba(239, 68, 68, 0.2); }
    .log-accessed { background: rgba(99, 102, 241, 0.15); color: #4f46e5; border: 1px solid rgba(99, 102, 241, 0.2); }

    /* --- Host Pill --- */
    .host-pill {
        font-family: 'Courier New', monospace;
        background: #f1f5f9;
        padding: 4px 8px;
        border-radius: 4px;
        color: #475569;
        font-size: 0.85rem;
        border: 1px solid #e2e8f0;
    }

    /* --- Detail Properties (JSON) --- */
    .json-key { color: #64748b; font-weight: 600; }
    .json-val { color: #0f172a; font-family: monospace; }
    
    /* --- Action Buttons --- */
    .btn-view-log {
        width: 32px; height: 32px;
        border-radius: 50%;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #64748b;
        display: inline-flex; align-items: center; justify-content: center;
        transition: all 0.2s;
    }
    .btn-view-log:hover { background: #4f46e5; color: white; border-color: #4f46e5; }

    /* --- Export Buttons Styling --- */
    .dt-buttons .btn {
        border-radius: 6px !important;
        font-size: 0.85rem;
        margin-right: 5px;
        border: 1px solid #e2e8f0;
        background: white;
        color: #475569;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }
    .dt-buttons .btn:hover { background: #f8fafc; color: #1e293b; }

    /* Dark Mode */
    [data-bs-theme="dark"] .audit-card { background: var(--card-bg); }
    [data-bs-theme="dark"] .host-pill { background: #1e293b; border-color: #374151; color: #cbd5e1; }
    [data-bs-theme="dark"] .btn-view-log { background: #1e293b; border-color: #374151; color: #94a3b8; }
</style>
@endsection

@section('content')
<div class="container-fluid p-0">

    <div class="audit-card bg-white mb-4">
        <!-- Unique Header -->
        <div class="audit-header">
            <i class="bi bi-activity audit-icon-bg"></i>
            <h4 class="fw-bold m-0 text-white">System Audit Logs</h4>
            <p class="mb-0 text-white-50 small mt-1">Track comprehensive user activities and system events.</p>
        </div>

        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle w-100" id="auditTable">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-3 text-uppercase text-muted small fw-bold">ID</th>
                            <th class="text-uppercase text-muted small fw-bold">Action</th>
                            <th class="text-uppercase text-muted small fw-bold">Subject</th>
                            <th class="text-uppercase text-muted small fw-bold">User</th>
                            <th class="text-uppercase text-muted small fw-bold">IP Host</th>
                            <th class="text-uppercase text-muted small fw-bold">Timestamp</th>
                            <th class="text-end pe-4 text-uppercase text-muted small fw-bold">Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- DataTables will populate this -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Details Modal -->
<div class="modal fade" id="auditDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title fw-bold"><i class="bi bi-code-square me-2 text-primary"></i>Log Properties</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-secondary border-0 d-flex align-items-center mb-3">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    <small>Below shows the state of the data during this event.</small>
                </div>
                <div class="bg-dark text-light p-3 rounded-3 font-monospace small" style="max-height: 400px; overflow-y: auto;">
                    <pre id="modalJsonContent" class="m-0 text-light"></pre>
                </div>
            </div>
            <div class="modal-footer border-top-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
@parent
<!-- Load DataTables Buttons & JSZip/PdfMake for Export -->
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

<script>
    $(function () {
        let dtButtons = [
            {
                extend: 'copyHtml5',
                text: '<i class="bi bi-clipboard me-1"></i> Copy',
                className: 'btn btn-light',
                exportOptions: { columns: [0, 1, 2, 3, 4, 5] } // Exclude Actions column
            },
            {
                extend: 'excelHtml5',
                text: '<i class="bi bi-file-earmark-excel me-1 text-success"></i> Excel',
                className: 'btn btn-light',
                title: 'System_Audit_Logs_' + new Date().toISOString().slice(0,10),
                exportOptions: { 
                    columns: [0, 1, 2, 3, 4, 5],
                    format: {
                        body: function(data, row, column, node) {
                            if (data === null || data === undefined) return '';
                            return String(data).replace(/<(?:.|\n)*?>/gm, '').trim();
                        }
                    }
                }
            },
            {
                extend: 'pdfHtml5',
                text: '<i class="bi bi-file-earmark-pdf me-1 text-danger"></i> PDF',
                className: 'btn btn-light',
                orientation: 'landscape',
                pageSize: 'A4',
                title: 'System Audit Logs Report',
                exportOptions: { 
                    columns: [0, 1, 2, 3, 4, 5],
                    stripHtml: true 
                },
                customize: function (doc) {
                    // Improve PDF styling
                    doc.content[1].table.widths = ['5%', '10%', '20%', '20%', '15%', '30%'];
                    doc.styles.tableHeader.fillColor = '#334155';
                    doc.styles.tableHeader.color = 'white';
                    doc.defaultStyle.fontSize = 9;
                }
            },
            {
                extend: 'print',
                text: '<i class="bi bi-printer me-1"></i> Print',
                className: 'btn btn-light',
                exportOptions: { columns: [0, 1, 2, 3, 4, 5] },
                customize: function (win) {
                    $(win.document.body).css('font-size', '10pt');
                    $(win.document.body).find('table')
                        .addClass('compact')
                        .css('font-size', 'inherit');
                }
            }
        ];

        let table = $('#auditTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.audit-logs.index') }}",
            columns: [
                { data: 'id', name: 'id', className: 'ps-3 fw-bold text-muted' },
                { 
                    data: 'description', 
                    name: 'description',
                    render: function(data) {
                        let badgeClass = 'log-accessed';
                        let icon = 'bi-eye';
                        
                        if(data.includes('created')) { badgeClass = 'log-created'; icon = 'bi-plus-lg'; }
                        if(data.includes('updated')) { badgeClass = 'log-updated'; icon = 'bi-pencil'; }
                        if(data.includes('deleted')) { badgeClass = 'log-deleted'; icon = 'bi-trash'; }

                        return `<span class="log-badge ${badgeClass}"><i class="bi ${icon}"></i> ${data}</span>`;
                    }
                },
                { 
                    data: 'subject_type', 
                    name: 'subject_type',
                    render: function(data, type, row) {
                        let id = row.subject_id ? `#${row.subject_id}` : '';
                        let cleanName = data.replace('App\\', '');
                        return `<span class="fw-semibold text-dark">${cleanName}</span> <small class="text-muted">${id}</small>`;
                    }
                },
                { 
                    data: 'user_id', 
                    name: 'user_id',
                    render: function(data, type, row) {
                        // Asumsi backend mengirim data user object atau kita hanya punya ID
                        // Untuk contoh ini kita pakai ID, idealnya backend kirim nama user via relation
                        return `<div class="d-flex align-items-center">
                                    <div class="bg-light border rounded-circle d-flex align-items-center justify-content-center me-2" style="width:24px; height:24px; font-size: 10px;">
                                        <i class="bi bi-person-fill"></i>
                                    </div>
                                    <span>User #${data}</span>
                                </div>`;
                    }
                },
                { 
                    data: 'host', 
                    name: 'host',
                    render: function(data) {
                        return `<span class="host-pill">${data || 'Unknown'}</span>`;
                    }
                },
                { 
                    data: 'created_at', 
                    name: 'created_at',
                    render: function(data) {
                        // Format tanggal sederhana
                        return `<span class="text-muted small"><i class="bi bi-clock me-1"></i>${data}</span>`;
                    }
                },
                { 
                    data: 'actions', 
                    name: '{{ trans('global.actions') }}',
                    orderable: false, 
                    searchable: false,
                    className: 'text-end pe-4',
                    render: function(data, type, row) {
                        // Encode JSON untuk keamanan di HTML attribute
                        // Pastikan backend mengirim kolom 'properties' (bisa hidden)
                        let props = row.properties ? encodeURIComponent(JSON.stringify(row.properties)) : '';

                        return `<button class="btn-view-log view-details-btn" 
                                    data-json="${props}" 
                                    data-bs-toggle="tooltip" 
                                    title="View Details">
                                    <i class="bi bi-chevron-right"></i>
                                </button>`;
                    }
                }
            ],
            order: [[ 0, 'desc' ]],
            pageLength: 25,
            dom: '<"d-flex justify-content-between align-items-center flex-wrap gap-2 p-3"Bf>rt<"d-flex justify-content-between align-items-center p-3"lip>',
            buttons: dtButtons
        });

        // Handle Modal View Details
        $(document).on('click', '.view-details-btn', function() {
            let rawData = $(this).data('json');
            let jsonContent = "No changes recorded or properties empty.";

            if(rawData) {
                try {
                    let decodedData = decodeURIComponent(rawData);
                    let jsonObj = JSON.parse(decodedData);
                    jsonContent = JSON.stringify(jsonObj, null, 4); // Pretty print JSON
                } catch(e) {
                    jsonContent = "Error parsing JSON data.";
                }
            }

            $('#modalJsonContent').text(jsonContent);
            
            // Show Bootstrap Modal manually via JS
            let myModal = new bootstrap.Modal(document.getElementById('auditDetailModal'));
            myModal.show();
        });

        // Styling Search Input
        $('.dataTables_filter input').addClass('form-control').css('width', '250px').attr('placeholder', 'Search logs...');
    });
</script>
@endsection