@extends('layouts.admin')

@section('page-title', 'Dashboard Overview')

@section('styles')
{{-- 1. Load Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

{{-- 2. PENTING: Load FontAwesome (Agar icon muncul) --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<style>
    /* --- Dashboard Card Style --- */
    .dashboard-card {
        border: none;
        border-radius: 16px;
        background: var(--bs-body-bg, #fff);
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        overflow: hidden;
        height: 100%;
        position: relative;
    }
    
    [data-bs-theme="dark"] .dashboard-card {
        background: #1e293b;
        box-shadow: none;
        border: 1px solid rgba(255,255,255,0.05);
    }
    
    .dashboard-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.08);
    }

    /* --- Stats Icons & Text --- */
    .stat-icon {
        width: 50px; height: 50px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.5rem;
    }
    .stat-value {
        font-size: 1.8rem; font-weight: 800; line-height: 1.2;
        margin-bottom: 2px; color: var(--bs-heading-color, #333);
    }
    .stat-label {
        font-size: 0.85rem; color: #6c757d; text-transform: uppercase;
        letter-spacing: 0.5px; font-weight: 600;
    }

    /* --- 3D Chart Container Effects --- */
    .chart-wrapper {
        position: relative;
        height: 260px;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Kunci Efek 3D: Bayangan di bawah chart agar terlihat melayang */
    .chart-3d-container {
        position: relative;
        filter: drop-shadow(0px 10px 8px rgba(0, 0, 0, 0.15)); /* Shadow lembut */
        transition: transform 0.5s ease;
    }
    
    /* Center Text untuk Doughnut Chart */
    .chart-center-text {
        position: absolute; top: 50%; left: 50%;
        transform: translate(-50%, -50%);
        text-align: center; pointer-events: none;
        z-index: 10;
    }
    .chart-center-number { font-size: 1.5rem; font-weight: 800; color: var(--bs-heading-color, #333); }
    .chart-center-label { font-size: 0.75rem; color: #64748b; }

    /* --- Filters --- */
    .filter-group {
        background: rgba(0,0,0,0.04); padding: 4px; border-radius: 12px;
        display: inline-flex; align-items: center; gap: 4px;
    }
    [data-bs-theme="dark"] .filter-group { background: rgba(255,255,255,0.05); }

    .filter-btn {
        border: none; background: transparent; padding: 8px 20px;
        border-radius: 8px; font-weight: 600; font-size: 0.9rem;
        color: #64748b; transition: all 0.2s;
    }
    .filter-btn:hover { color: var(--bs-primary); }
    .filter-btn.active {
        background: var(--bs-body-bg, #fff); color: var(--bs-primary);
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    .filter-divider { width: 1px; height: 20px; background: #94a3b8; opacity: 0.3; margin: 0 4px; }
    
    .filter-reset-btn {
        border: none; background: transparent; color: #ef4444;
        padding: 6px 10px; border-radius: 6px; font-size: 1rem;
        transition: transform 0.2s;
    }
    .filter-reset-btn:hover { background: rgba(239, 68, 68, 0.1); transform: scale(1.1); }

    /* --- Custom Side Legend --- */
    .chart-row { display: flex; align-items: center; height: 220px; }
    .chart-col { width: 55%; position: relative; height: 100%; }
    .legend-col { width: 45%; padding-left: 15px; height: 100%; overflow-y: auto; }
    
    .custom-legend-item {
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 8px; font-size: 0.8rem; color: #64748b;
        padding: 5px 8px; border-radius: 6px;
        transition: background 0.2s;
    }
    .custom-legend-item:hover { background: rgba(0,0,0,0.03); }
    .legend-dot { width: 10px; height: 10px; border-radius: 3px; margin-right: 8px; }
    .legend-text { font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 90px; }
    .legend-val { font-weight: 700; color: var(--bs-heading-color, #333); }
</style>
@endsection

@section('content')
<div class="container-fluid px-0">
    
    <!-- Header & Filter -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-5 gap-3">
        <div>
            <h3 class="fw-bold mb-1" style="color: var(--text-main)">Dashboard</h3>
            <p class="text-muted mb-0">Overview of support performance.</p>
        </div>
        
        <div class="filter-group shadow-sm">
            <a href="{{ route('admin.home', ['filter' => 'daily']) }}" 
               class="filter-btn {{ $filter === 'daily' ? 'active' : '' }}">
                Daily
            </a>
            <a href="{{ route('admin.home', ['filter' => 'monthly']) }}" 
               class="filter-btn {{ $filter === 'monthly' ? 'active' : '' }}">
                Monthly
            </a>
            <a href="{{ route('admin.home', ['filter' => 'yearly']) }}" 
               class="filter-btn {{ $filter === 'yearly' ? 'active' : '' }}">
                Yearly
            </a>
            
            @if(isset($filter) && $filter)
                <div class="filter-divider"></div>
                <button type="button" class="filter-reset-btn" onclick="resetFilter()" 
                        data-bs-toggle="tooltip" title="Reset Filter">
                    <i class="bi bi-x-circle-fill"></i>
                </button>
            @endif
        </div>
    </div>

    <!-- Stats Cards -->
      <div class="row g-4 mb-5">
        @php
            $cards = [
                [
                    'id' => 'val-tickets', 
                    'label' => 'Total Tickets', 
                    'val' => $stats['total_tickets'], 
                    'icon' => 'fas fa-ticket-alt', 
                    'color' => 'primary' // Biru (Netral)
                ],
                [
                    'id' => 'val-open', 
                    'label' => 'Open Tickets', 
                    'val' => $stats['open_tickets'], 
                    'icon' => 'fas fa-envelope-open-text', 
                    'color' => 'danger' // Merah (Perlu Perhatian) - Ganti dari 'success' agar beda dengan Closed
                ],
                [
                    'id' => 'val-pending', 
                    'label' => 'Pending Tickets', 
                    'val' => $stats['pending_tickets'], 
                    'icon' => 'fas fa-clock', // Ganti icon jadi Jam (Waktu)
                    'color' => 'warning' // Kuning/Oranye (Menunggu)
                ],
                [
                    'id' => 'val-closed', 
                    'label' => 'Closed Tickets', 
                    'val' => $stats['closed_tickets'], 
                    'icon' => 'fas fa-check-circle', // Ganti icon jadi Centang (Selesai)
                    'color' => 'success' // Hijau (Berhasil/Selesai) - Ganti dari 'secondary'
                ],
            ];
        @endphp

        @foreach($cards as $card)
        <div class="col-xl-3 col-md-6">
            <div class="dashboard-card p-4 d-flex align-items-center">
                {{-- Icon Container dengan warna bg-opacity --}}
                <div class="stat-icon bg-{{ $card['color'] }} bg-opacity-10 text-{{ $card['color'] }} me-3">
                    <i class="{{ $card['icon'] }}"></i>
                </div>
                <div>
                    <div class="stat-value" id="{{ $card['id'] }}">{{ number_format($card['val']) }}</div>
                    <div class="stat-label">{{ $card['label'] }}</div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Charts Section -->
    <div class="row g-4">
        <div class="col-lg-4">
    <div class="dashboard-card p-4">
        <h6 class="fw-bold mb-4">Tickets by Status</h6>
        
        <!-- GANTI STRUKTUR DI SINI AGAR SAMA DENGAN CATEGORY -->
        <div class="chart-row">
            <!-- Kolom Kiri: Chart -->
            <div class="chart-col">
                <canvas id="statusChart"></canvas>
                <!-- Teks Tengah -->
                <div class="chart-center-text">
                    <div class="chart-center-number" id="total-status">0</div>
                    <div class="chart-center-label">Total</div>
                </div>
            </div>
            
            <!-- Kolom Kanan: Legend Custom -->
            <div class="legend-col" id="status-legend">
                <!-- Legend akan diisi otomatis oleh JS -->
            </div>
        </div>
        
    </div>
</div>

        <div class="col-lg-4">
            <div class="dashboard-card p-4">
                <h6 class="fw-bold mb-4" style="color: var(--text-main)">Priority Distribution</h6>
                <div class="chart-wrapper">
                    <canvas id="priorityChart"></canvas>
                </div>
            </div>
        </div>

         <div class="col-lg-4">
            <div class="dashboard-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold m-0" style="color: var(--text-main)">Top Categories</h6>
                    <button class="btn btn-sm btn-light rounded-circle p-1 lh-1 text-muted">
                        <i class="bi bi-three-dots"></i>
                    </button>
                </div>
                
                <div class="chart-row">
                    <!-- Chart Area -->
                    <div class="chart-col">
                        <canvas id="categoryChart"></canvas>
                        <!-- Center Text -->
                        <div class="chart-center-text">
                            <div class="chart-center-number" id="total-category">0</div>
                            <div class="chart-center-label">Total</div>
                        </div>
                    </div>

                    <!-- Custom HTML Legend Area -->
                    <div class="legend-col" id="category-legend">
                        <!-- Legend items will be injected here by JS -->
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-12">
            <div class="dashboard-card p-4">
                <h6 class="fw-bold mb-4" style="color: var(--text-main)">Ticket Trend</h6>
                <div style="height: 300px;">
                    <canvas id="timelineChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let charts = {};
    const initialData = @json($ticketsData);

    // --- KONFIGURASI TEMA ---
    // Ganti nilai ini untuk mengubah warna dasar seluruh chart
    // 220 = Biru (Default), 260 = Ungu, 150 = Hijau, 10 = Merah
    const BASE_HUE = 225; 

    // Global Chart Defaults
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.color = '#64748b';

    document.addEventListener("DOMContentLoaded", function() {
        initCharts(initialData);
        
        // Init Bootstrap Tooltips
        if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
            [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                .map(el => new bootstrap.Tooltip(el));
        }
    });

    function initCharts(data) {
        // 1. Status Chart (Doughnut 3D)
        renderModernChart({
            canvasId: 'statusChart',
            type: 'doughnut',
            data: data.status,
            totalId: 'total-status',
            legendId: 'status-legend',
            baseHue: BASE_HUE
        });

        // 2. Priority Chart (Pie 3D - Full)
        renderModernChart({
            canvasId: 'priorityChart',
            type: 'pie', // Pie chart agar beda bentuk
            data: data.priority,
            baseHue: BASE_HUE 
        });

        // 3. Category Chart (Doughnut 3D + Legend Samping)
        renderModernChart({
            canvasId: 'categoryChart',
            type: 'doughnut',
            data: data.category,
            totalId: 'total-category',
            legendId: 'category-legend', // ID div untuk custom legend
            baseHue: BASE_HUE
        });

        // 4. Timeline Chart (Area)
        renderTimeline('timelineChart', data.timeline || []);
    }

    /**
     * FUNGSI UTAMA GENERATOR CHART 3D MONOKROM
     */
    function renderModernChart({ canvasId, type, data, totalId = null, legendId = null, baseHue = 220 }) {
        const canvas = document.getElementById(canvasId);
        const ctx = canvas.getContext('2d');
        
        // Bersihkan chart lama
        if (charts[canvasId]) charts[canvasId].destroy();

        // Tambahkan class CSS untuk shadow container
        if(canvas.parentElement) canvas.parentElement.classList.add('chart-3d-container');

        // Hitung Total
        const total = data.data.reduce((a, b) => a + b, 0);
        if(totalId) {
            const el = document.getElementById(totalId);
            if(el) el.innerText = total;
        }

        // Generate Palette (Gradient & Solid)
        const palette = generateMonochromePalette(ctx, data.data.length, baseHue);

        // Opsi Chart
        const options = {
            responsive: true,
            maintainAspectRatio: false,
            layout: { padding: 10 },
            elements: {
                arc: {
                    borderWidth: 2,
                    borderColor: '#ffffff', // Border putih tebal pemisah slice
                    hoverOffset: 15, // Efek Pop-out saat hover
                }
            },
            plugins: {
                legend: {
                    // Jika ada custom legend (HTML), sembunyikan legend bawaan canvas
                    display: !legendId, 
                    position: 'right',
                    labels: { usePointStyle: true, padding: 15, font: { weight: '600' } }
                },
                tooltip: {
                    backgroundColor: 'rgba(15, 23, 42, 0.9)',
                    padding: 12, cornerRadius: 8,
                    titleFont: { size: 13 }, bodyFont: { size: 13, weight: 'bold' },
                    displayColors: true,
                    usePointStyle: true,
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            let value = context.parsed;
                            let percentage = total > 0 ? Math.round((value / total) * 100) + '%' : '0%';
                            return ` ${label}: ${value} (${percentage})`;
                        }
                    }
                }
            }
        };

        // Konfigurasi Cutout (Lubang tengah)
        // Jika Doughnut = 70%, Jika Pie = 0%
        options.cutout = (type === 'doughnut') ? '70%' : '0%';

        // Buat Chart Instance
        charts[canvasId] = new Chart(ctx, {
            type: type,
            data: {
                labels: data.labels,
                datasets: [{
                    data: data.data,
                    backgroundColor: palette.gradients, // Pakai Gradient untuk 3D
                    hoverBackgroundColor: palette.gradients, // Tetap gradient saat hover
                    borderColor: '#ffffff',
                    borderWidth: 3
                }]
            },
            options: options
        });

        // Generate Custom HTML Legend (Jika diminta)
        if (legendId) {
            generateHtmlLegend(legendId, data, palette.solids);
        }
    }

    /**
     * Generator Warna Turunan (Monochromatic Gradients)
     * Menghasilkan array gradient untuk chart dan warna solid untuk legend.
     */
    function generateMonochromePalette(ctx, count, hue) {
        let gradients = [];
        let solids = [];

        // Logika Distribusi Warna:
        // Warna pertama = Gelap (Strong)
        // Warna terakhir = Terang (Light)
        const minLightness = 35; // Paling gelap
        const maxLightness = 85; // Paling terang
        
        // Jarak antar warna
        const step = count > 1 ? (maxLightness - minLightness) / (count - 1) : 0;

        for (let i = 0; i < count; i++) {
            // Hitung lightness
            let l = minLightness + (step * i);

            // 1. Buat Gradient (Efek 3D Metalik/Glossy)
            let grd = ctx.createLinearGradient(0, 0, 0, 400);
            // Bagian atas lebih terang (Highlight)
            grd.addColorStop(0, `hsl(${hue}, 80%, ${l + 15}%)`); 
            // Bagian tengah warna asli
            grd.addColorStop(0.5, `hsl(${hue}, 70%, ${l}%)`); 
            // Bagian bawah lebih gelap (Shadow)
            grd.addColorStop(1, `hsl(${hue}, 70%, ${l - 10}%)`);

            gradients.push(grd);

            // 2. Simpan Warna Solid (Untuk Legend HTML & Tooltip dot)
            solids.push(`hsl(${hue}, 70%, ${l}%)`);
        }

        return { gradients, solids };
    }

    /**
     * Helper render Custom Legend HTML
     */
    function generateHtmlLegend(containerId, data, colors) {
        const container = document.getElementById(containerId);
        if (!container) return;
        
        container.innerHTML = '';
        const total = data.data.reduce((a, b) => a + b, 0);

        if (data.labels.length === 0 || total === 0) {
            container.innerHTML = '<div class="text-center text-muted small py-4">No data</div>';
            return;
        }

        data.labels.forEach((label, index) => {
            if (data.data[index] === 0) return; // Skip data kosong
            
            const html = `
                <div class="custom-legend-item">
                    <div class="legend-info">
                        <div class="legend-dot" style="background-color: ${colors[index]}"></div>
                        <span class="legend-text" title="${label}">${label}</span>
                    </div>
                    <span class="legend-val">${data.data[index]}</span>
                </div>`;
            container.insertAdjacentHTML('beforeend', html);
        });
    }

    // --- CHART TIMELINE (AREA CHART) ---
    function renderTimeline(canvasId, data) {
        const ctx = document.getElementById(canvasId).getContext('2d');
        if (charts[canvasId]) charts[canvasId].destroy();
        
        const labels = data && data.length ? data.map(i => i.period) : [];
        const values = data && data.length ? data.map(i => i.count) : [];

        // Gradient Background Timeline (Senada dengan tema)
        let gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, `hsla(${BASE_HUE}, 70%, 60%, 0.3)`);
        gradient.addColorStop(1, `hsla(${BASE_HUE}, 70%, 60%, 0.0)`);

        charts[canvasId] = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Tickets',
                    data: values,
                    borderColor: `hsl(${BASE_HUE}, 70%, 50%)`, // Warna garis utama
                    borderWidth: 3,
                    backgroundColor: gradient,
                    fill: true,
                    tension: 0.4, // Kurva halus
                    pointBackgroundColor: '#fff',
                    pointBorderColor: `hsl(${BASE_HUE}, 70%, 50%)`,
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { borderDash: [5, 5] }, ticks: { precision: 0 } },
                    x: { grid: { display: false }, ticks: { maxRotation: 0, autoSkip: true, maxTicksLimit: 8 } }
                },
                interaction: { mode: 'index', intersect: false }
            }
        });
    }

    // --- AJAX HANDLER (Sama seperti sebelumnya) ---
    async function updateDashboard(filter, btn) {
        if (btn.classList.contains('active') || btn.disabled) return;
        document.querySelectorAll('.filter-btn').forEach(b => { b.classList.remove('active'); b.disabled = false; });
        btn.classList.add('active', 'loading'); btn.disabled = true;

        try {
            const response = await fetch(`{{ route('admin.dashboard.data') }}?filter=${filter}`);
            const result = await response.json();
            if (result.success) {
                // Update Numbers
                ['tickets', 'open', 'pending', 'closed'].forEach(k => {
                   animateValue(`val-${k}`, result.stats[`${k}_tickets`] || result.stats[`total_${k}`]); 
                });

                // Update Charts dengan data baru
                initCharts(result.ticketsData);
                
                const div = document.getElementById('filter-divider');
                const rst = document.getElementById('filter-reset-btn');
                if(div) div.style.display = (filter === 'all') ? 'none' : 'block';
                if(rst) rst.style.display = (filter === 'all') ? 'none' : 'inline-block';
            }
        } catch (e) { console.error(e); } finally { btn.classList.remove('loading'); btn.disabled = false; }
    }
    
    function resetFilter() { window.location.href = "{{ route('admin.home') }}"; }
    
    function animateValue(id, end) {
        const obj = document.getElementById(id); if(!obj) return;
        const start = parseInt(obj.innerText.replace(/,/g, '')) || 0;
        if(start === end) return;
        let startTimestamp = null;
        const step = (timestamp) => {
            if (!startTimestamp) startTimestamp = timestamp;
            const progress = Math.min((timestamp - startTimestamp) / 500, 1);
            obj.innerHTML = Math.floor(progress * (end - start) + start).toLocaleString();
            if (progress < 1) window.requestAnimationFrame(step); else obj.innerHTML = end.toLocaleString();
        };
        window.requestAnimationFrame(step);
    }
</script>
@endsection