<?php
  $brand = 'Inventory System';
  $pageTitle = 'Admin Panel';
?>

<?php $__env->startSection('sidebar'); ?>
    <?php echo $__env->make('partials.admin-sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <h2 style="margin:0 0 10px;">Welcome, <?php echo e(auth()->user()->name); ?></h2>

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
                <?php if($pendingRequests > 0): ?>
                    <span style="display:inline-block; background:#ef4444; color:white; font-size:11px; font-weight:700; padding:2px 8px; border-radius:999px; min-width:24px; text-align:center;">
                        <?php echo e($pendingRequests); ?>

                    </span>
                <?php endif; ?>
            </div>
            <a href="/admin/requests" style="display:inline-block; margin-top:10px; color:#22c55e; text-decoration:none;">Open →</a>
        </div>

        <div style="padding:14px; border:1px solid rgba(255,255,255,.08); border-radius:14px; background:rgba(255,255,255,.02);">
            <div style="color:#9ca3af; font-size:12px;">Quick Access</div>
            <div style="font-weight:700; margin-top:6px; display:flex; align-items:center; gap:8px;">
                Password Reset
                <?php if($pendingPasswordResets > 0): ?>
                    <span style="display:inline-block; background:#ef4444; color:white; font-size:11px; font-weight:700; padding:2px 8px; border-radius:999px; min-width:24px; text-align:center;">
                        <?php echo e($pendingPasswordResets); ?>

                    </span>
                <?php endif; ?>
            </div>
            <a href="/admin/password-reset" style="display:inline-block; margin-top:10px; color:#22c55e; text-decoration:none;">Open →</a>
        </div>
    </div>

    <style>
        /* Enhanced charts grid: 3 columns for better layout */
        .charts-grid{
            margin-top:32px;
            display:grid;
            grid-template-columns: repeat(3, 1fr);
            gap:20px;
            align-items:start;
        }
        @media (max-width:1200px){ .charts-grid{ grid-template-columns: 1fr 1fr; } }
        @media (max-width:900px){ .charts-grid{ grid-template-columns: 1fr; } }
        
        .chart-card{
            padding:20px;
            border:1px solid rgba(255,255,255,.08);
            border-radius:16px;
            background:linear-gradient(180deg, rgba(255,255,255,0.03), rgba(255,255,255,0.01));
            transition:transform .2s ease, box-shadow .2s ease;
            cursor:pointer;
            position:relative;
            overflow:hidden;
        }
        .chart-card:hover{ 
            transform: translateY(-6px); 
            box-shadow:0 20px 40px rgba(59,130,246,0.15); 
            border-color: rgba(59,130,246,0.3);
        }
        .chart-card::before{
            content:'';
            position:absolute;
            top:0;
            left:0;
            right:0;
            height:3px;
            background:linear-gradient(90deg, #3b82f6, #8b5cf6);
        }

        .chart-header{ display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:16px; }
        .chart-title{ font-weight:800; color:#000; margin:0; font-size:18px; }
        .chart-sub{ color:#9ca3af; font-size:13px; margin:0; }
        .chart-icon{ font-size:24px; opacity:0.8; }

        /* fullscreen modal for charts */
        .chart-modal{
            position:fixed;
            top:0; left:0; width:100vw; height:100vh;
            background:rgba(0,0,0,0.7);
            backdrop-filter: blur(8px);
            display:flex;
            align-items:center;
            justify-content:center;
            padding:20px;
            z-index:1000;
            overflow:auto;
        }
        .chart-modal.hidden{ display:none; }
        .chart-modal .modal-content{
            position:relative;
            width:95vw;
            max-width:1400px;
            max-height:90vh;
            background:#fff;
            border-radius:16px;
            padding:24px;
            box-shadow:0 25px 50px rgba(0,0,0,.3);
            overflow:auto;
        }
        .chart-modal .modal-content canvas{
            width:100% !important;
            height:70vh !important;
        }
        .chart-modal .close-btn{
            position:absolute;
            top:16px;
            right:16px;
            background:#f3f4f6;
            border:none;
            border-radius:50%;
            width:40px;
            height:40px;
            font-size:20px;
            line-height:0;
            cursor:pointer;
            transition:all 0.2s ease;
        }
        .chart-modal .close-btn:hover{
            background:#e5e7eb;
            transform:scale(1.1);
        }

        /* Loading spinner styles */
        .chart-loading-overlay{
            position:absolute;
            top:0;
            left:0;
            width:100%;
            height:100%;
            background:rgba(255,255,255,0.9);
            display:flex;
            align-items:center;
            justify-content:center;
            z-index:10;
            border-radius:16px;
            opacity:0;
            visibility:hidden;
            transition:opacity 0.3s ease, visibility 0.3s ease;
        }
        .chart-loading-overlay.active{
            opacity:1;
            visibility:visible;
        }
        .spinner{
            width:40px;
            height:40px;
            border:4px solid #f3f4f6;
            border-top:4px solid #3b82f6;
            border-radius:50%;
            animation:spin 1s linear infinite;
        }
        @keyframes spin{
            0%{ transform:rotate(0deg); }
            100%{ transform:rotate(360deg); }
        }
        .loading-text{
            position:absolute;
            bottom:20px;
            left:50%;
            transform:translateX(-50%);
            color:#6b7280;
            font-size:14px;
            font-weight:500;
        }
    </style>

    <div class="charts-grid" id="chartsGrid">
        <!-- Category Analytics Chart -->
        <div class="chart-card" id="categoryCard">
            <div class="chart-header">
                <div>
                    <h3 class="chart-title">Category Analytics</h3>
                    <div class="chart-sub">Stock availability vs. approved requests by category</div>
                </div>
                <div class="chart-icon">📊</div>
            </div>
            <div style="position:relative; height:320px;">
                <canvas id="categoryChart"></canvas>
                <div class="chart-loading-overlay" id="categoryLoading">
                    <div class="spinner"></div>
                    <div class="loading-text">Loading data...</div>
                </div>
            </div>
        </div>

        <!-- Requests by Office Chart -->
        <div class="chart-card" id="officeCard">
            <div class="chart-header">
                <div>
                    <h3 class="chart-title">Requests by Office</h3>
                    <div class="chart-sub">Which offices submit the most requests</div>
                </div>
                <div class="chart-icon">🏢</div>
            </div>
            <div style="position:relative; height:320px; width:100%;">
                <canvas id="officeChart"></canvas>
                <div class="chart-loading-overlay" id="officeLoading">
                    <div class="spinner"></div>
                    <div class="loading-text">Loading data...</div>
                </div>
            </div>
            <div id="officeTop" style="margin-top:12px; color:#9ca3af; font-size:13px; text-align:center;"></div>
        </div>

        <!-- Most Requested Items Chart -->
        <div class="chart-card" id="itemsCard">
            <div class="chart-header">
                <div>
                    <h3 class="chart-title">Most Requested Items</h3>
                    <div class="chart-sub">Top 10 most requested inventory items</div>
                </div>
                <div class="chart-icon">🔥</div>
            </div>
            <div style="position:relative; height:320px; width:100%;">
                <canvas id="itemsChart"></canvas>
                <div class="chart-loading-overlay" id="itemsLoading">
                    <div class="spinner"></div>
                    <div class="loading-text">Loading data...</div>
                </div>
            </div>
            <div id="itemsTop" style="margin-top:12px; color:#9ca3af; font-size:13px; text-align:center;"></div>
        </div>
    </div>

    <!-- fullscreen modal overlay -->
    <div id="chartModal" class="chart-modal hidden">
        <div class="modal-content">
            <button id="closeModal" class="close-btn">&times;</button>
            <div id="modalControls" style="margin-bottom:16px; display:flex; flex-wrap:wrap; align-items:center; gap:12px; padding-bottom:16px; border-bottom:1px solid #e5e7eb;">
                <label for="startDateModal" style="margin:0; font-size:14px; color:#374151; font-weight:600;">Start Date:</label>
                <input type="date" id="startDateModal" style="padding:6px 10px; font-size:14px; border:1px solid #d1d5db; border-radius:6px; min-width:140px;">
                <label for="endDateModal" style="margin:0; font-size:14px; color:#374151; font-weight:600;">End Date:</label>
                <input type="date" id="endDateModal" style="padding:6px 10px; font-size:14px; border:1px solid #d1d5db; border-radius:6px; min-width:140px;">
                <button id="applyModalFilter" style="padding:8px 16px; border:1px solid #3b82f6; border-radius:6px; background:#3b82f6; color:#fff; font-size:14px; font-weight:600; cursor:pointer;">Apply</button>
                <button id="resetModalFilter" style="padding:8px 16px; border:1px solid #6b7280; border-radius:6px; background:#f3f4f6; color:#374151; font-size:14px; font-weight:600; cursor:pointer;">Reset</button>
            </div>
            <div id="modalMessage" style="display:none; position:absolute; top:50%; left:50%; transform:translate(-50%, -50%);
                    font-size:18px; color:#6b7280; text-align:center; pointer-events:none;">
                No data for selected date range
            </div>
            <div id="modalChartWrapper" style="overflow-x:auto;">
                <canvas id="modalChart" style="width:100%; height:70vh;"></canvas>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chartsGrid = document.getElementById('chartsGrid');
            
            // Set default date range for modal (last 30 days)
            const today = new Date();
            const thirtyDaysAgo = new Date(today.getTime() - (30 * 24 * 60 * 60 * 1000));
            
            // --- Enhanced Category Chart ---
            const catCtx = document.getElementById('categoryChart').getContext('2d');
            const categories = <?php echo json_encode($categoryAnalytics, 15, 512) ?>;
            const catLabels = categories.map(c => c.name);
            const catAvailability = categories.map(c => c.availability);
            const catRequested = categories.map(c => c.requested);

            // Enhanced gradients
            const gAvail = catCtx.createLinearGradient(0,0,0,300);
            gAvail.addColorStop(0, 'rgba(34,197,94,0.6)');
            gAvail.addColorStop(0.5, 'rgba(34,197,94,0.4)');
            gAvail.addColorStop(1, 'rgba(34,197,94,0.3)');
            const gReq = catCtx.createLinearGradient(0,0,0,300);
            gReq.addColorStop(0, 'rgba(59,130,246,0.6)');
            gReq.addColorStop(0.5, 'rgba(59,130,246,0.4)');
            gReq.addColorStop(1, 'rgba(59,130,246,0.3)');

            const catChart = new Chart(catCtx, {
                type: 'bar',
                data: {
                    labels: catLabels,
                    datasets: [
                        { 
                            label: 'Available Stock', 
                            data: catAvailability, 
                            backgroundColor: gAvail, 
                            borderColor: 'rgba(34,197,94,1)', 
                            borderWidth: 2, 
                            borderRadius: 8, 
                            borderSkipped: false,
                            barPercentage: 0.8
                        },
                        { 
                            label: 'Approved Requests', 
                            data: catRequested, 
                            backgroundColor: gReq, 
                            borderColor: 'rgba(59,130,246,1)', 
                            borderWidth: 2, 
                            borderRadius: 8, 
                            borderSkipped: false,
                            barPercentage: 0.8
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: { duration: 1200, easing: 'easeOutQuart' },
                    plugins: { 
                        legend: { 
                            labels: { 
                                color: '#000',
                                font: { size: 12, weight: '600' },
                                padding: 15
                            } 
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0,0,0,0.8)',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            borderColor: 'rgba(255,255,255,0.2)',
                            borderWidth: 1,
                            cornerRadius: 8,
                            padding: 12
                        }
                    },
                    scales: {
                        y: { 
                            beginAtZero:true, 
                            ticks:{ color:'#000', font: { size: 11 } }, 
                            grid:{ color:'rgba(255,255,255,0.1)' },
                            title: {
                                display: true,
                                text: 'Quantity',
                                color: '#000',
                                font: { size: 12, weight: '600' }
                            }
                        },
                        x: { 
                            ticks:{ color:'#000', font: { size: 11 } }, 
                            grid:{ display: false },
                            title: {
                                display: true,
                                text: 'Categories',
                                color: '#000',
                                font: { size: 12, weight: '600' }
                            }
                        }
                    },
                    interaction: { intersect: false, mode: 'index' }
                }
            });

            // --- Enhanced Office Chart ---
            const officeCtx = document.getElementById('officeChart').getContext('2d');
            const offices = <?php echo json_encode($officeAnalytics, 15, 512) ?>;
            const officeLabels = offices.map(o => o.office);
            const officeCounts = offices.map(o => o.count);

            // Dynamic color gradients
            const officeGradients = officeCounts.map((v,i) => {
                const g = officeCtx.createLinearGradient(0,0,300,0);
                if(i===0){ 
                    g.addColorStop(0,'rgba(249,115,22,0.6)'); 
                    g.addColorStop(0.5,'rgba(249,115,22,0.4)'); 
                    g.addColorStop(1,'rgba(249,115,22,0.3)'); 
                }
                else if(i===1){ 
                    g.addColorStop(0,'rgba(168,85,247,0.6)'); 
                    g.addColorStop(0.5,'rgba(168,85,247,0.4)'); 
                    g.addColorStop(1,'rgba(168,85,247,0.3)'); 
                }
                else { 
                    g.addColorStop(0,'rgba(59,130,246,0.5)'); 
                    g.addColorStop(0.5,'rgba(59,130,246,0.4)'); 
                    g.addColorStop(1,'rgba(59,130,246,0.3)'); 
                }
                return g;
            });

            const officeChart = new Chart(officeCtx, {
                type: 'bar',
                data: { 
                    labels: officeLabels, 
                    datasets: [{ 
                        label: 'Requests', 
                        data: officeCounts, 
                        backgroundColor: officeGradients, 
                        borderRadius: 8, 
                        borderSkipped: false,
                        barPercentage: 0.7
                    }] 
                },
                options: {
                    indexAxis: 'y', 
                    responsive:true, 
                    maintainAspectRatio:false,
                    animation:{ duration:1000, easing:'easeOutQuart' },
                    plugins:{ 
                        legend:{ display:false }, 
                        tooltip:{ 
                            backgroundColor: 'rgba(0,0,0,0.8)',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            borderColor: 'rgba(255,255,255,0.2)',
                            borderWidth: 1,
                            cornerRadius: 8,
                            padding: 12,
                            callbacks:{ 
                                label: ctx => ctx.parsed.x + ' requests' 
                            } 
                        } 
                    },
                    scales:{
                        x:{
                            beginAtZero:true,
                            ticks:{ color:'#000', font: { size: 11 } },
                            grid:{ color:'rgba(255,255,255,0.1)' },
                            title: {
                                display: true,
                                text: 'Number of Requests',
                                color: '#000',
                                font: { size: 12, weight: '600' }
                            }
                        },
                        y:{ 
                            ticks:{ color:'#000', font: { size: 11 } },
                            grid:{ display: false },
                            title: {
                                display: true,
                                text: 'Offices',
                                color: '#000',
                                font: { size: 12, weight: '600' }
                            }
                        }
                    }
                }
            });

            // --- Most Requested Items Chart ---
            const itemsCtx = document.getElementById('itemsChart').getContext('2d');
            const items = <?php echo json_encode($itemAnalytics, 15, 512) ?>;
            const itemLabels = items.map(i => i.id_no + ' - ' + (i.description.length > 20 ? i.description.substring(0,20) + '...' : i.description));
            const itemCounts = items.map(i => i.total_requested);

            // Enhanced gradients for items
            const itemGradients = itemCounts.map((v,i) => {
                const g = itemsCtx.createLinearGradient(0,0,0,300);
                const hue = (i * 360 / itemCounts.length) % 360;
                g.addColorStop(0, `hsla(${hue}, 70%, 60%, 0.6)`);
                g.addColorStop(0.5, `hsla(${hue}, 70%, 60%, 0.4)`);
                g.addColorStop(1, `hsla(${hue}, 70%, 60%, 0.3)`);
                return g;
            });

            const itemsChart = new Chart(itemsCtx, {
                type: 'bar',
                data: {
                    labels: itemLabels,
                    datasets: [{
                        label: 'Times Requested',
                        data: itemCounts,
                        backgroundColor: itemGradients,
                        borderColor: 'rgba(255,255,255,0.2)',
                        borderWidth: 1,
                        borderRadius: 8,
                        borderSkipped: false,
                        barPercentage: 0.8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: { duration: 1100, easing: 'easeOutQuart' },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0,0,0,0.8)',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            borderColor: 'rgba(255,255,255,0.2)',
                            borderWidth: 1,
                            cornerRadius: 8,
                            padding: 12,
                            callbacks: {
                                title: function(context) {
                                    const index = context[0].dataIndex;
                                    const item = items[index];
                                    return `${item.id_no} - ${item.description}`;
                                },
                                label: function(context) {
                                    const index = context.dataIndex;
                                    const item = items[index];
                                    return [
                                        `Requested: ${context.parsed.y} times`,
                                        `Category: ${item.category}`,
                                        `Unit: ${item.unit}`
                                    ];
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { color: '#000', font: { size: 11 } },
                            grid: { color: 'rgba(255,255,255,0.1)' },
                            title: {
                                display: true,
                                text: 'Times Requested',
                                color: '#000',
                                font: { size: 12, weight: '600' }
                            }
                        },
                        x: {
                            ticks: { color: '#000', font: { size: 10 }, maxRotation: 45, minRotation: 45 },
                            grid: { display: false },
                            title: {
                                display: true,
                                text: 'Items (ID - Description)',
                                color: '#000',
                                font: { size: 12, weight: '600' }
                            }
                        }
                    },
                    interaction: { intersect: false, mode: 'index' }
                }
            });

            // Show top items summary
            const topItem = items[0];
            const topEl = document.getElementById('itemsTop');
            if(topItem && topEl){ 
                topEl.innerHTML = `<strong style="color:#000;">Top: ${topItem.id_no} - ${topItem.description}</strong><br><span style="color:#9ca3af; font-size:11px;">Requested ${topItem.total_requested} times</span>`; 
            }

            // Show top office summary
            const topOffice = offices[0];
            const topOfficeEl = document.getElementById('officeTop');
            if(topOffice && topOfficeEl){ 
                topOfficeEl.innerHTML = `<strong style="color:#000;">Top: ${topOffice.office}</strong><br><span style="color:#9ca3af; font-size:11px;">${topOffice.count} requests</span>`; 
            }

            // --- Filter functionality (only for modal) ---
            function updateChartsWithFilter(startDate, endDate) {
                // Show loading overlays
                showLoadingStates();
                
                const url = '/admin/dashboard/chart-data' + '?start_date=' + encodeURIComponent(startDate) + '&end_date=' + encodeURIComponent(endDate);
                
                fetch(url)
                    .then(resp => resp.json())
                    .then(data => {
                        // Update category chart
                        catChart.data.labels = data.categories.map(c => c.name);
                        catChart.data.datasets[0].data = data.categories.map(c => c.availability);
                        catChart.data.datasets[1].data = data.categories.map(c => c.requested);
                        catChart.update('active');

                        // Update office chart
                        officeChart.data.labels = data.offices.map(o => o.office);
                        officeChart.data.datasets[0].data = data.offices.map(o => o.count);
                        officeChart.update('active');

                        // Update items chart
                        itemsChart.data.labels = data.items.map(i => i.id_no + ' - ' + (i.description.length > 20 ? i.description.substring(0,20) + '...' : i.description));
                        itemsChart.data.datasets[0].data = data.items.map(i => i.total_requested);
                        itemsChart.update('active');

                        // Update summaries
                        if(data.items.length > 0) {
                            const newTopItem = data.items[0];
                            topEl.innerHTML = `<strong style="color:#000;">Top: ${newTopItem.id_no} - ${newTopItem.description}</strong><br><span style="color:#9ca3af; font-size:11px;">Requested ${newTopItem.total_requested} times</span>`;
                        }
                        if(data.offices.length > 0) {
                            const newTopOffice = data.offices[0];
                            topOfficeEl.innerHTML = `<strong style="color:#000;">Top: ${newTopOffice.office}</strong><br><span style="color:#9ca3af; font-size:11px;">${newTopOffice.count} requests</span>`;
                        }
                        
                        // Hide loading overlays
                        hideLoadingStates();
                    })
                    .catch(error => {
                        console.error('Error updating charts:', error);
                        hideLoadingStates();
                    });
            }

            // Loading state management functions
            function showLoadingStates() {
                document.getElementById('categoryLoading').classList.add('active');
                document.getElementById('officeLoading').classList.add('active');
                document.getElementById('itemsLoading').classList.add('active');
            }

            function hideLoadingStates() {
                document.getElementById('categoryLoading').classList.remove('active');
                document.getElementById('officeLoading').classList.remove('active');
                document.getElementById('itemsLoading').classList.remove('active');
            }

            // --- Modal functionality ---
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
                            const copy = { ...ds };
                            if(Array.isArray(ds.data)) copy.data = [...ds.data];
                            return copy;
                        })
                    },
                    options: src.config.options ? { ...src.config.options } : {}
                };
                if(src.config.plugins) cfg.plugins = { ...src.config.plugins };
                return cfg;
            }

            let currentChartType = null;
            let originalChartForModal = null;

            function openChartFullscreen(chart, type) {
                if(modalChartInstance) return;
                currentChartType = type;
                originalChartForModal = chart;
                
                // Set modal date inputs to default range
                document.getElementById('startDateModal').value = thirtyDaysAgo.toISOString().split('T')[0];
                document.getElementById('endDateModal').value = today.toISOString().split('T')[0];
                document.body.style.overflow = 'hidden';
                modal.classList.remove('hidden');
                window.addEventListener('resize', handleModalResize);
                
                // Only load data if user has actually selected a date range
                // Don't auto-load with default dates to avoid showing data when user hasn't selected anything
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
                document.body.style.overflow = '';
            }

            function handleModalResize(){
                if(modalChartInstance){
                    try{ modalChartInstance.resize(); }catch(e){}
                }
            }

            function loadModalChartData(startDate, endDate) {
                if(!startDate || !endDate) {
                    const msg = document.getElementById('modalMessage');
                    if(msg) {
                        msg.textContent = 'Please select both start and end dates';
                        msg.style.display = 'block';
                    }
                    return;
                }

                // Show loading state in modal
                const msg = document.getElementById('modalMessage');
                if(msg) {
                    msg.innerHTML = '<div style="display:flex; flex-direction:column; align-items:center; gap:10px;"><div class="spinner" style="width:30px; height:30px; border-width:3px;"></div><div>Loading chart data...</div></div>';
                    msg.style.display = 'block';
                }

                const url = '/admin/dashboard/chart-data' + '?start_date=' + encodeURIComponent(startDate) + '&end_date=' + encodeURIComponent(endDate);
                fetch(url)
                    .then(resp => resp.json())
                    .then(data => {
                        rebuildModalChart(originalChartForModal, data);
                    })
                    .catch(error => {
                        console.error('Error loading modal chart:', error);
                        if(msg) {
                            msg.textContent = 'Error loading chart data';
                            msg.style.display = 'block';
                        }
                    });
            }

            function rebuildModalChart(sourceChart, data) {
                if(modalChartInstance) {
                    modalChartInstance.destroy();
                    modalChartInstance = null;
                }

                const messageEl = document.getElementById('modalMessage');

                // Check if we have data
                let hasData = false;
                if(currentChartType === 'category') {
                    hasData = data.categories && data.categories.length > 0;
                } else if(currentChartType === 'office') {
                    hasData = data.offices && data.offices.length > 0;
                } else if(currentChartType === 'items') {
                    hasData = data.items && data.items.length > 0;
                }

                if(!hasData) {
                    if(messageEl){
                        messageEl.textContent = 'No data for selected date range';
                        messageEl.style.display = 'block';
                    }
                    const ctx = modalCanvas.getContext('2d');
                    ctx.clearRect(0, 0, modalCanvas.width, modalCanvas.height);
                    return;
                }

                if(messageEl) messageEl.style.display = 'none';

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
                } else if(currentChartType === 'items') {
                    cfg.data.labels = data.items.map(i => i.id_no + ' - ' + (i.description.length > 30 ? i.description.substring(0,30) + '...' : i.description));
                    cfg.data.datasets[0].data = data.items.map(i => i.total_requested);
                    labelsCount = data.items.length;
                }

                const wrapper = document.getElementById('modalChartWrapper');
                if(wrapper && modalCanvas) {
                    const minWidth = wrapper.clientWidth;
                    const extra = labelsCount * 80;
                    modalCanvas.style.width = Math.max(minWidth, extra) + 'px';
                    wrapper.scrollLeft = 0;
                }

                modalChartInstance = new Chart(modalCanvas.getContext('2d'), cfg);
                modalChartInstance.resize();
            }

            // Modal filter controls
            document.getElementById('applyModalFilter').addEventListener('click', function() {
                const startDate = document.getElementById('startDateModal').value;
                const endDate = document.getElementById('endDateModal').value;
                loadModalChartData(startDate, endDate);
            });

            document.getElementById('resetModalFilter').addEventListener('click', function() {
                const today = new Date();
                const thirtyDaysAgo = new Date(today.getTime() - (30 * 24 * 60 * 60 * 1000));
                document.getElementById('startDateModal').value = thirtyDaysAgo.toISOString().split('T')[0];
                document.getElementById('endDateModal').value = today.toISOString().split('T')[0];
                loadModalChartData(
                    thirtyDaysAgo.toISOString().split('T')[0],
                    today.toISOString().split('T')[0]
                );
            });

            closeBtn.addEventListener('click', closeModal);
            modal.addEventListener('click', function(e){ if(e.target === modal) closeModal(); });

            // Make charts clickable
            function makeClickable(card, chart, type) {
                card.style.cursor = 'pointer';
                card.addEventListener('click', function(){ openChartFullscreen(chart, type); });
                const canvas = card.querySelector('canvas');
                if(canvas){ canvas.addEventListener('click', function(e){ e.stopPropagation(); openChartFullscreen(chart, type); }); }
            }

            makeClickable(document.getElementById('categoryCard'), catChart, 'category');
            makeClickable(document.getElementById('officeCard'), officeChart, 'office');
            makeClickable(document.getElementById('itemsCard'), itemsChart, 'items');
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/admin/dashboard.blade.php ENDPATH**/ ?>