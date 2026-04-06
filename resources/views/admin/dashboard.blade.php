@extends('layouts.app')

@php
  $brand = 'Inventory System';
  $pageTitle = 'Admin Panel';
  $pageSubtitle = 'Manage categories, stocks, inbound/outbound, and requests.';
@endphp

@section('sidebar')
    @include('partials.admin-sidebar')
@endsection

@section('content')
    <h2 style="margin:0 0 10px;">Welcome, {{ auth()->user()->name }} 👋</h2>

    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap:12px; margin-top:14px;">
        <div style="padding:14px; border:1px solid rgba(255,255,255,.08); border-radius:14px; background:rgba(255,255,255,.02);">
            <div style="color:#9ca3af; font-size:12px;">Quick Access</div>
            <div style="font-weight:700; margin-top:6px;">Stocks</div>
            <a href="/admin/stocks" style="display:inline-block; margin-top:10px; color:#22c55e; text-decoration:none;">Open →</a>
        </div>

        <div style="padding:14px; border:1px solid rgba(255,255,255,.08); border-radius:14px; background:rgba(255,255,255,.02); position:relative;">
            <div style="color:#9ca3af; font-size:12px;">Quick Access</div>
            <div style="font-weight:700; margin-top:6px; display:flex; align-items:center; gap:8px;">
                Requests
                @if($pendingRequests > 0)
                    <span style="display:inline-block; background:#ef4444; color:white; font-size:11px; font-weight:700; padding:2px 8px; border-radius:999px; min-width:24px; text-align:center;">
                        {{ $pendingRequests }}
                    </span>
                @endif
            </div>
            <a href="/admin/requests" style="display:inline-block; margin-top:10px; color:#22c55e; text-decoration:none;">Open →</a>
        </div>

        <div style="padding:14px; border:1px solid rgba(255,255,255,.08); border-radius:14px; background:rgba(255,255,255,.02);">
            <div style="color:#9ca3af; font-size:12px;">Quick Access</div>
            <div style="font-weight:700; margin-top:6px; display:flex; align-items:center; gap:8px;">
                Password Reset
                @if($pendingPasswordResets > 0)
                    <span style="display:inline-block; background:#ef4444; color:white; font-size:11px; font-weight:700; padding:2px 8px; border-radius:999px; min-width:24px; text-align:center;">
                        {{ $pendingPasswordResets }}
                    </span>
                @endif
            </div>
            <a href="/admin/password-reset" style="display:inline-block; margin-top:10px; color:#22c55e; text-decoration:none;">Open →</a>
        </div>
    </div>

    <style>
        /* Charts grid: 50/50 default; when expanded the clicked card spans full width and the other stacks below */
        .charts-grid{
            margin-top:32px;
            display:grid;
            grid-template-columns: 1fr 1fr;
            gap:16px;
            align-items:start;
        }
        .chart-card{
            padding:18px;
            border:1px solid rgba(255,255,255,.08);
            border-radius:14px;
            background:linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01));
            transition:transform .18s ease, box-shadow .18s ease;
            cursor:pointer;
        }
        .chart-card:hover{ transform: translateY(-4px); box-shadow:0 18px 40px rgba(2,6,23,0.06); }

        .chart-header{ display:flex; align-items:center; justify-content:space-between; gap:8px; margin-bottom:12px; }
        .chart-title{ font-weight:800; color:#000; margin:0; }
        .chart-sub{ color:#000; font-size:13px; margin:0; }

        @media (max-width:900px){ .charts-grid{ grid-template-columns: 1fr; } }

        /* fullscreen modal for charts */
        .chart-modal{
            position:fixed;
            top:0; left:0; width:100vw; height:100vh;
            background:rgba(0,0,0,0.45);
            backdrop-filter: blur(6px);
            display:flex;
            align-items:center;
            justify-content:center;
            padding:10px;
            z-index:1000;
            overflow:auto;
        }
        .chart-modal.hidden{ display:none; }
        .chart-modal .modal-content{
            position:relative;
            width:90vw;
            max-width:1200px;
            max-height:90vh;
            background:#fff;
            border-radius:8px;
            padding:12px;
            box-shadow:0 8px 24px rgba(0,0,0,.2);
            overflow:auto;
        }
        .chart-modal .modal-content canvas{
            width:100% !important;
            /* reduce height to avoid overflowing tall screens */
            height:60vh !important;
        }
        .chart-modal .close-btn{
            position:absolute;
            top:12px;
            right:12px;
            background:#fff;
            border:none;
            border-radius:50%;
            width:32px;
            height:32px;
            font-size:18px;
            line-height:0;
            cursor:pointer;
        }
    
    </style>

    <div class="charts-grid" id="chartsGrid">
        <div class="chart-card" id="categoryCard">
            <div class="chart-header">
                <div>
                    <h3 class="chart-title">Category Analytics</h3>
                    <div class="chart-sub">Total availability vs. approved requests by category</div>
                </div>
            </div>
            <div style="position:relative; height:360px;"><canvas id="categoryChart"></canvas></div>
        </div>

        <div class="chart-card" id="officeCard">
            <div class="chart-header">
                <div>
                    <h3 class="chart-title">Requests by Office</h3>
                    <div class="chart-sub">Which offices submit the most requests</div>
                </div>
            </div>
            <div style="position:relative; height:300px; width:100%; max-width:600px;"><canvas id="officeChart"></canvas></div>
            <div id="officeTop" style="margin-top:12px; color:#9ca3af; font-size:13px;"></div>
        </div>
    </div>

    <!-- fullscreen modal overlay -->
    <div id="chartModal" class="chart-modal hidden">
        <div class="modal-content">
            <button id="closeModal" class="close-btn">&times;</button>
            <!-- month filter will appear here when a chart is zoomed -->
            <div id="modalControls" style="margin-bottom:12px; display:flex; flex-wrap:wrap; align-items:center; gap:8px;">
                <label for="monthFilter" style="margin:0; font-size:14px; color:#333;">Month:</label>
                <select id="monthFilter" style="padding:4px 6px; font-size:14px; min-width:120px;">
                    <!-- options populated by script -->
                </select>
            </div>
            <div id="modalMessage" style="display:none; position:absolute; top:50%; left:50%; transform:translate(-50%, -50%);
                    font-size:18px; color:#333; text-align:center; pointer-events:none;">
                No data for selected month
            </div>
            <div id="modalChartWrapper" style="overflow-x:auto;">
                <canvas id="modalChart" style="width:100%; height:80vh;"></canvas>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chartsGrid = document.getElementById('chartsGrid');

            // --- Chart: Category (bar) — keep data, make visuals punchy (gradients, rounded bars) ---
            const catCtx = document.getElementById('categoryChart').getContext('2d');
            const categories = @json($categoryAnalytics);
            const catLabels = categories.map(c => c.name);
            const catAvailability = categories.map(c => c.availability);
            const catRequested = categories.map(c => c.requested);

            // create gradients
            const gAvail = catCtx.createLinearGradient(0,0,0,300);
            gAvail.addColorStop(0, 'rgba(34,197,94,0.95)');
            gAvail.addColorStop(1, 'rgba(34,197,94,0.55)');
            const gReq = catCtx.createLinearGradient(0,0,0,300);
            gReq.addColorStop(0, 'rgba(59,130,246,0.95)');
            gReq.addColorStop(1, 'rgba(59,130,246,0.45)');

            const catChart = new Chart(catCtx, {
                type: 'bar',
                data: {
                    labels: catLabels,
                    datasets: [
                        { label: 'Available', data: catAvailability, backgroundColor: gAvail, borderColor: 'rgba(34,197,94,1)', borderWidth: 0, borderRadius: 8, borderSkipped: false },
                        { label: 'Outbound/Approved Request', data: catRequested, backgroundColor: gReq, borderColor: 'rgba(59,130,246,1)', borderWidth: 0, borderRadius: 8, borderSkipped: false }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: { duration: 900, easing: 'easeOutQuart' },
                    plugins: { legend: { labels: { color: '#000' } } },
                    scales: {
                        y: { beginAtZero:true, ticks:{ color:'#000' }, grid:{ color:'rgba(0,0,0,.06)' } },
                        x: { ticks:{ color:'#000' }, grid:{ display:false } }
                    },
                    interaction: { intersect: false, mode: 'index' }
                }
            });

            // --- Chart: Office (horizontal bar) ---
            const officeCtx = document.getElementById('officeChart').getContext('2d');
            const offices = @json($officeAnalytics);
            const officeLabels = offices.map(o => o.office);
            const officeCounts = offices.map(o => o.count);

            // make first bar orange, others blue; use rounded bars
            const officeGradients = officeCounts.map((v,i) => {
                const g = officeCtx.createLinearGradient(0,0,300,0);
                if(i===0){ g.addColorStop(0,'rgba(249,115,22,0.95)'); g.addColorStop(1,'rgba(249,115,22,0.55)'); }
                else { g.addColorStop(0,'rgba(59,130,246,0.9)'); g.addColorStop(1,'rgba(59,130,246,0.5)'); }
                return g;
            });

            const officeChart = new Chart(officeCtx, {
                type: 'bar',
                data: { labels: officeLabels, datasets: [{ label: 'Requests', data: officeCounts, backgroundColor: officeGradients, borderRadius: 8, borderSkipped: false }] },
                options: {
                    indexAxis: 'y', responsive:true, maintainAspectRatio:false,
                    animation:{ duration:800, easing:'easeOutQuart' },
                    plugins:{ legend:{ display:false }, tooltip:{ callbacks:{ label: ctx => ctx.parsed.x + ' requests' } } },
                    scales:{
                        x:{
                            beginAtZero:true,
                            ticks:{
                                color:'#000',
                                precision:0,
                                stepSize:1
                            },
                            grid:{ color:'rgba(0,0,0,.06)' }
                        },
                        y:{ ticks:{ color:'#000' }, grid:{ display:false } }
                    }
                }
            });

            // show top office summary (office name only)
            const top = offices[0];
            const topEl = document.getElementById('officeTop');
            if(top && topEl){ topEl.innerHTML = `<strong style="color:#fff;">Top: ${top.office}</strong>`; }

            // simple resize handler (no expand/collapse behavior)
            function resizeCharts(){
                try{ if(catChart && typeof catChart.resize === 'function') catChart.resize(); if(catChart && typeof catChart.update === 'function') catChart.update(); }catch(e){}
                try{ if(officeChart && typeof officeChart.resize === 'function') officeChart.resize(); if(officeChart && typeof officeChart.update === 'function') officeChart.update(); }catch(e){}
            }

            // reflow charts on window resize
            window.addEventListener('resize', function(){ resizeCharts(); });

            // ----- fullscreen modal functionality (config cloning) -----
            let modalChartInstance = null;
            const modal = document.getElementById('chartModal');
            const modalCanvas = document.getElementById('modalChart');
            const closeBtn = document.getElementById('closeModal');

            function cloneChartConfig(src) {
                const cfg = {
                    type: src.config.type,
                    data: {
                        labels: Array.isArray(src.config.data.labels) ? [...src.config.data.labels] : src.config.data.labels,
                        datasets: src.config.data.datasets.map(ds => {
                            // shallow copy dataset; preserve gradient/background references
                            const copy = { ...ds };
                            if(Array.isArray(ds.data)) copy.data = [...ds.data];
                            return copy;
                        })
                    },
                    options: src.config.options ? { ...src.config.options } : {}
                };
                // plugins may have callbacks; copy by reference
                if(src.config.plugins) cfg.plugins = { ...src.config.plugins };
                return cfg;
            }

            // keep track of which chart is currently displayed in the modal
            let currentChartType = null;
            let originalChartForModal = null;

            function openChartFullscreen(chart, type) {
                if(modalChartInstance) return;

                currentChartType = type;
                originalChartForModal = chart;

                // populate month selector and set default to current month
                const monthSelect = document.getElementById('monthFilter');
                if(monthSelect){
                    populateMonthOptions(monthSelect);
                    // start with no selection; user must choose month manually
                    monthSelect.value = '';
                    monthSelect.onchange = function(){ loadChartData(this.value); };
                }

                // show initial instruction message
                const msg = document.getElementById('modalMessage');
                if(msg){
                    msg.textContent = 'Please select a month';
                    msg.style.display = 'block';
                }

                // lock scroll on background page
                document.body.style.overflow = 'hidden';

                modal.classList.remove('hidden');
                window.addEventListener('resize', handleModalResize);
            }

            function closeModal() {
                if(modalChartInstance) {
                    modalChartInstance.destroy();
                    modalChartInstance = null;
                }
                currentChartType = null;
                originalChartForModal = null;
                modal.classList.add('hidden');
                window.removeEventListener('resize', handleModalResize);
                // restore body scrolling
                document.body.style.overflow = '';
            }

            function handleModalResize(){
                if(modalChartInstance){
                    try{ modalChartInstance.resize(); }catch(e){}
                }
            }

            // generate the last 12 months options for the selector
            function populateMonthOptions(select) {
                select.innerHTML = '';
                // add blank placeholder
                const placeholder = document.createElement('option');
                placeholder.value = '';
                placeholder.textContent = '-- select month --';
                select.appendChild(placeholder);
                const now = new Date();
                for(let i=0;i<12;i++){
                    const d = new Date(now.getFullYear(), now.getMonth()-i, 1);
                    const val = d.toISOString().slice(0,7);
                    const label = d.toLocaleString('default', { month:'long', year:'numeric' });
                    const opt = document.createElement('option');
                    opt.value = val;
                    opt.textContent = label;
                    select.appendChild(opt);
                }
            }

            // request analytics data from the server for a particular month and redraw modal chart
            function loadChartData(month) {
                const messageEl = document.getElementById('modalMessage');
                // if user cleared selection or hasn't chosen yet
                if(!month) {
                    if(messageEl) {
                        messageEl.textContent = 'Please select a month';
                        messageEl.style.display = 'block';
                    }
                    if(modalChartInstance) {
                        modalChartInstance.destroy();
                        modalChartInstance = null;
                    }
                    return;
                }

                if(messageEl) {
                    messageEl.style.display = 'none';
                }

                const url = '/admin/dashboard/chart-data' + '?month=' + encodeURIComponent(month);
                fetch(url)
                    .then(resp => resp.json())
                    .then(data => {
                        if(!originalChartForModal) return;
                        rebuildModalChart(originalChartForModal, data);
                    })
                    .catch(console.error);
            }

            function rebuildModalChart(sourceChart, data) {
                // clear existing modal chart instance if any
                if(modalChartInstance) {
                    modalChartInstance.destroy();
                    modalChartInstance = null;
                }

                const messageEl = document.getElementById('modalMessage');

                // determine whether any request-related data exists for this month
                let hasRequestData = false;
                if(currentChartType === 'category') {
                    if(Array.isArray(data.categories)) {
                        hasRequestData = data.categories.some(c => (c.requested || 0) > 0);
                    }
                } else if(currentChartType === 'office') {
                    if(Array.isArray(data.offices)) {
                        hasRequestData = data.offices.some(o => (o.count || 0) > 0);
                    }
                }

                if(!hasRequestData) {
                    // no relevant monthly data – show a clear message and clear canvas
                    if(messageEl){
                        messageEl.textContent = 'No data for this month';
                        messageEl.style.display = 'block';
                    }
                    const ctx = modalCanvas.getContext('2d');
                    ctx.clearRect(0, 0, modalCanvas.width, modalCanvas.height);
                    return;
                }

                // hide message if it was shown
                if(messageEl) messageEl.style.display = 'none';

                // build config normally when there is data
                const cfg = cloneChartConfig(sourceChart);
                let labelsCount = 0;
                if(currentChartType === 'category') {
                    cfg.data.labels = data.categories.map(c=>c.name);
                    cfg.data.datasets[0].data = data.categories.map(c=>c.availability);
                    cfg.data.datasets[1].data = data.categories.map(c=>c.requested);
                    labelsCount = data.categories.length;
                } else if(currentChartType === 'office') {
                    cfg.data.labels = data.offices.map(o=>o.office);
                    cfg.data.datasets[0].data = data.offices.map(o=>o.count);
                    labelsCount = data.offices.length;
                }

                // if there are many labels, expand canvas width to allow horizontal scroll
                const wrapper = document.getElementById('modalChartWrapper');
                if(wrapper && modalCanvas) {
                    const minWidth = wrapper.clientWidth;
                    const extra = labelsCount * 60; // ~60px per label
                    modalCanvas.style.width = Math.max(minWidth, extra) + 'px';
                    wrapper.scrollLeft = 0;
                }

                modalChartInstance = new Chart(modalCanvas.getContext('2d'), cfg);
                modalChartInstance.resize();
            }

            function handleModalResize(){
                if(modalChartInstance){
                    try{ modalChartInstance.resize(); }catch(e){}
                }
            }

            closeBtn.addEventListener('click', closeModal);
            modal.addEventListener('click', function(e){ if(e.target === modal) closeModal(); });

            // helper to enable clicking anywhere inside card (including canvas)
            function makeClickable(card, chart, type) {
                card.style.cursor = 'pointer';
                card.addEventListener('click', function(){ openChartFullscreen(chart, type); });
                const canvas = card.querySelector('canvas');
                if(canvas){ canvas.addEventListener('click', function(e){ e.stopPropagation(); openChartFullscreen(chart, type); }); }
            }

            makeClickable(document.getElementById('categoryCard'), catChart, 'category');
            makeClickable(document.getElementById('officeCard'), officeChart, 'office');
        });
    </script>
@endsection
