<?php $__env->startSection('content'); ?>
    <?php
        $pageTitle = 'Client Inventory Monitoring';
    ?>

    <style>
        .monitoring-container {
            margin: 20px 0;
        }

        .client-section {
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 12px;
            margin-bottom: 20px;
            overflow: hidden;
        }

        .client-header {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
            padding: 16px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .client-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .client-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 16px;
        }

        .inventory-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        .inventory-table th {
            background: #f8fafc;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            border-bottom: 2px solid #e2e8f0;
            color: #374151;
        }

        .inventory-table td {
            padding: 12px;
            border-bottom: 1px solid #e2e8f0;
        }

        .inventory-table tr:hover {
            background: #f8fafc;
        }

        .stock-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
        }

        .stock-low {
            background: #fef2f2;
            color: #dc2626;
        }

        .stock-medium {
            background: #fef3c7;
            color: #d97706;
        }

        .stock-good {
            background: #ecfdf5;
            color: #059669;
        }

        .search-container {
            margin-bottom: 20px;
            position: relative;
        }

        .search-input {
            width: 100%;
            padding: 12px 16px 12px 44px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 14px;
            background: #ffffff;
            transition: all 0.3s ease;
        }

        .search-input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .search-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #64748b;
            font-size: 16px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 20px;
        }

        .stat-card {
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 16px;
            text-align: center;
        }

        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #3b82f6;
            margin-bottom: 4px;
        }

        .stat-label {
            font-size: 12px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .no-data {
            text-align: center;
            padding: 48px;
            color: #64748b;
            background: #f8fafc;
            border-radius: 12px;
            border: 2px dashed #3b82f6;
        }

        .urgent-badge {
            background: #dc2626;
            color: white;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            display: inline-block;
            margin-left: 8px;
        }
    </style>

    <div class="monitoring-container">
        <h2 style="margin-bottom: 20px; color: #1e293b; font-size: 24px; font-weight: 700;">
            📦 Client Inventory Monitoring
        </h2>

        <!-- Statistics Overview -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value"><?php echo e($totalClients ?? 0); ?></div>
                <div class="stat-label">Total Clients</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo e($totalInventoryItems ?? 0); ?></div>
                <div class="stat-label">Total Inventory Items</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo e($lowStockClients ?? 0); ?></div>
                <div class="stat-label">Low Stock Clients</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo e($urgentItems ?? 0); ?></div>
                <div class="stat-label">Urgent Items</div>
            </div>
        </div>

        <!-- Search -->
        <div class="search-container">
            <span class="search-icon">🔍</span>
            <input type="text" id="clientSearch" class="search-input" placeholder="Search clients by name, email, or item description..." oninput="filterClients()">
        </div>

        <?php if(isset($clientsWithInventory) && $clientsWithInventory->count() > 0): ?>
            <?php $__currentLoopData = $clientsWithInventory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="client-section" data-client-name="<?php echo e(strtolower($client->name ?? '')); ?>" data-client-email="<?php echo e(strtolower($client->email ?? '')); ?>">
                    <div class="client-header">
                        <div class="client-info">
                            <div class="client-avatar">
                                <?php echo e(strtoupper(substr($client->name ?? 'U', 0, 1))); ?>

                            </div>
                            <div>
                                <div style="font-weight: 600; font-size: 16px;">
                                    <?php echo e($client->name ?? 'Unknown Client'); ?>

                                    <span style="font-size: 12px; opacity: 0.8;">(<?php echo e($client->email ?? 'No email'); ?>)</span>
                                </div>
                                <div style="font-size: 12px; opacity: 0.8;">
                                    Office: <?php echo e($client->office ?? 'Not specified'); ?>

                                </div>
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-size: 12px; opacity: 0.8;">Total Items</div>
                            <div style="font-size: 18px; font-weight: bold;">
                                <?php echo e($client->inventory_items_count ?? 0); ?>

                            </div>
                        </div>
                    </div>

                    <?php if(isset($client->inventory_items) && $client->inventory_items->count() > 0): ?>
                        <table class="inventory-table">
                            <thead>
                                <tr>
                                    <th>Item ID</th>
                                    <th>Description</th>
                                    <th>Approved</th>
                                    <th>Available</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $client->inventory_items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $availableQty = isset($item->my_inventory) ? $item->my_inventory : max(0, ($item->approved_qty ?? 0) - ($item->distributed_qty ?? 0));
                                        $stockStatus = $availableQty <= 5 ? 'low' : ($availableQty <= 20 ? 'medium' : 'good');
                                    ?>
                                    <tr data-item-description="<?php echo e(strtolower($item->stock->description ?? $item->stock->name ?? '')); ?>">
                                        <td style="font-weight: 600; color: #1e40af;">
                                            <?php echo e($item->stock->id_no ?? 'N/A'); ?>

                                            <?php if(isset($item->type) && $item->type === 'urgent'): ?>
                                                <span class="urgent-badge">URGENT</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo e($item->stock->description ?? $item->stock->name ?? 'Unknown Item'); ?></td>
                                        <td style="text-align: center; font-weight: 600;"><?php echo e($item->approved_qty ?? 0); ?></td>
                                        <td style="text-align: center; font-weight: 600; color: #059669;"><?php echo e($availableQty); ?></td>
                                        <td style="text-align: center;">
                                            <span class="stock-badge stock-<?php echo e($stockStatus); ?>">
                                                <?php echo e($stockStatus === 'low' ? 'Low Stock' : ($stockStatus === 'medium' ? 'Medium' : 'Good')); ?>

                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div style="padding: 20px; text-align: center; color: #64748b; background: #f8fafc;">
                            No inventory items found for this client.
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php else: ?>
            <div class="no-data">
                <div style="font-size: 48px; margin-bottom: 16px;">📦</div>
                <div style="font-size: 18px; font-weight: 600; color: #1e40af;">No client inventory data available.</div>
                <div style="font-size: 14px; margin-top: 8px;">Clients will appear here once they have approved inventory items.</div>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function filterClients() {
            const searchTerm = document.getElementById('clientSearch').value.toLowerCase();
            const clientSections = document.querySelectorAll('.client-section');
            let visibleCount = 0;

            clientSections.forEach(section => {
                const clientName = section.dataset.clientName || '';
                const clientEmail = section.dataset.clientEmail || '';
                const itemDescriptions = Array.from(section.querySelectorAll('tr[data-item-description]'))
                    .map(row => row.dataset.itemDescription || '')
                    .join(' ');

                const matches = clientName.includes(searchTerm) ||
                               clientEmail.includes(searchTerm) ||
                               itemDescriptions.includes(searchTerm);

                if (matches || searchTerm === '') {
                    section.style.display = 'block';
                    visibleCount++;
                } else {
                    section.style.display = 'none';
                }
            });

            // Update search results count if needed
            if (searchTerm && visibleCount === 0) {
                // Optionally show a "no results" message
            }
        }

        // Auto-refresh every 5 minutes to get latest inventory data
        setInterval(() => {
            location.reload();
        }, 300000);
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/admin/client-inventory.blade.php ENDPATH**/ ?>