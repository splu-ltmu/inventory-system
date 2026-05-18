<?php
  $brand = 'Inventory System';
  $activeTab = request('tab', 'details');
  $pageTitle = $activeTab === 'inventory' ? 'Available Items' : ($activeTab === 'members' ? 'Monitor Members' : 'Subaccount Details');
?>

<?php $__env->startSection('sidebar'); ?>
    <?php echo $__env->make('client.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<style>
    .account-container{ width: 100%; max-width: none; margin: 24px 0; padding: 0 16px; box-sizing: border-box; }
    .card, .card-body, .table-wrap { width: 100%; }
    .card { background: var(--surface); border:1px solid var(--line); border-radius:18px; margin-bottom:18px; overflow:hidden; }
    .card-header { display:flex; justify-content:space-between; align-items:center; padding:18px; background:rgba(37,99,235,.08); }
    .card-header h2 { margin:0; font-size:18px; }
    .card-body { padding:18px; }
    .grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:18px; }
    .form-group { margin-bottom:14px; }
    .form-group label { display:block; margin-bottom:8px; font-weight:700; color:var(--text); }
    .form-group input, .form-group select { width:100%; padding:10px; border:1px solid var(--line); border-radius:10px; font-size:14px; }
    .btn-primary { display:inline-flex; align-items:center; justify-content:center; padding:10px 18px; border:none; border-radius:10px; background:#2563eb; color:#fff; font-weight:700; cursor:pointer; }
    .btn-primary:hover{ background:#1d4ed8; }
    .table-wrap{ overflow-x:auto; }
    table{ width:100%; border-collapse:collapse; margin-top:16px; }
    th,td{ padding:12px; border:1px solid var(--line); text-align:left; }
    th{ background:rgba(37,99,235,.06); }
    .pill{ display:inline-flex; padding:4px 10px; border-radius:999px; background:rgba(37,99,235,.08); color:var(--blue); font-size:12px; font-weight:700; }
    .distribution-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-top: 20px; }
    .distribution-card { background: var(--surface); border: 1px solid var(--line); border-radius: 12px; padding: 20px; transition: all 0.2s ease; }
    .distribution-card:hover { border-color: #2563eb; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.1); }
    .item-header { display: flex; align-items: center; gap: 12px; margin-bottom: 16px; }
    .item-icon { width: 40px; height: 40px; background: linear-gradient(135deg, #2563eb, #3b82f6); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 18px; }
    .item-info h4 { margin: 0; font-size: 16px; color: var(--text); }
    .item-info p { margin: 4px 0 0 0; color: var(--muted); font-size: 13px; }
    .progress-bar { width: 100%; height: 8px; background: rgba(37, 99, 235, 0.1); border-radius: 4px; overflow: hidden; margin: 12px 0; }
    .progress-fill { height: 100%; background: linear-gradient(90deg, #2563eb, #3b82f6); border-radius: 4px; transition: width 0.3s ease; }
    .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-bottom: 16px; }
    .stat-item { text-align: center; padding: 8px; background: rgba(37, 99, 235, 0.05); border-radius: 6px; }
    .stat-value { font-size: 18px; font-weight: bold; color: #2563eb; display: block; }
    .stat-label { font-size: 11px; color: var(--muted); text-transform: uppercase; letter-spacing: 0.5px; }
    .quick-actions { display: flex; gap: 8px; margin-top: 16px; }
    .btn-quick { padding: 6px 12px; border: 1px solid var(--line); background: white; border-radius: 6px; font-size: 12px; cursor: pointer; transition: all 0.2s ease; }
    .btn-quick:hover { background: #2563eb; color: white; border-color: #2563eb; }
    .btn-quick:disabled { opacity: 0.5; cursor: not-allowed; }
    .member-selector { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 12px; margin-bottom: 20px; }
    .member-card { display: flex; align-items: center; gap: 12px; padding: 12px; border: 2px solid transparent; border-radius: 8px; cursor: pointer; transition: all 0.2s ease; background: rgba(37, 99, 235, 0.02); }
    .member-card:hover { border-color: #2563eb; background: rgba(37, 99, 235, 0.05); }
    .member-card.selected { border-color: #2563eb; background: rgba(37, 99, 235, 0.08); }
    .member-avatar { width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, #2563eb, #3b82f6); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 14px; }
    .member-details h5 { margin: 0; font-size: 14px; color: var(--text); }
    .member-details p { margin: 2px 0 0 0; font-size: 12px; color: var(--muted); }
    .distribution-form { background: var(--surface); border: 1px solid var(--line); border-radius: 12px; padding: 24px; margin-top: 20px; }
    .form-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; }
    .form-header h3 { margin: 0; color: var(--text); }
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
    .form-group-enhanced { position: relative; }
    .form-group-enhanced label { display: block; margin-bottom: 8px; font-weight: 600; color: var(--text); font-size: 14px; }
    .form-group-enhanced input, .form-group-enhanced select { width: 100%; padding: 12px 16px; border: 2px solid var(--line); border-radius: 8px; font-size: 14px; transition: all 0.2s ease; }
    .form-group-enhanced input:focus, .form-group-enhanced select:focus { border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1); outline: none; }
    .quantity-input { position: relative; }
    .quantity-input input { padding-right: 60px; }
    .quantity-max { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); font-size: 12px; color: var(--muted); }
    .btn-submit { 
        display: inline-flex; 
        align-items: center; 
        gap: 8px; 
        padding: 12px 24px; 
        border: none; 
        border-radius: 8px; 
        background: linear-gradient(135deg, #6366f1, #4f46e5); 
        color: white; 
        font-weight: 600; 
        cursor: pointer; 
        transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        box-shadow: 0 4px 15px rgba(99, 102, 241, 0.2);
        position: relative;
        overflow: hidden;
    }
    .btn-submit::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s ease;
    }
    .btn-submit:hover { 
        transform: translateY(-1px) scale(1.02); 
        background: linear-gradient(135deg, #4f46e5, #374151); 
        box-shadow: 0 6px 20px rgba(99, 102, 241, 0.3); 
    }
    .btn-submit:hover::before {
        left: 100%;
    }
    .btn-submit:active {
        transform: translateY(0) scale(0.98);
        box-shadow: 0 2px 8px rgba(99, 102, 241, 0.2);
    }
    .btn-submit:disabled { 
        opacity: 0.6; 
        cursor: not-allowed; 
        transform: none; 
        background: linear-gradient(135deg, #94a3b8, #6b7280);
        box-shadow: none;
    }
    .btn-submit:disabled::before {
        display: none;
    }
    .bulk-actions { background: rgba(37, 99, 235, 0.05); border: 1px solid rgba(37, 99, 235, 0.2); border-radius: 8px; padding: 16px; margin-top: 20px; }
    .bulk-actions h4 { margin: 0 0 12px 0; color: var(--text); font-size: 16px; }
    .usage-warning { margin-top: 12px; padding: 8px; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 6px; font-size: 12px; color: #d97706; }
    .usage-error { margin-top: 12px; padding: 8px; background: rgba(220, 38, 38, 0.1); border: 1px solid rgba(220, 38, 38, 0.2); border-radius: 6px; font-size: 12px; color: #dc2626; }
    .usage-input { position: relative; }
    .usage-input input:focus { border-color: #f59e0b; box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1); }
</style>

<div class="account-container">
    <?php if(session('success')): ?>
        <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="alert alert-error"><?php echo e(session('error')); ?></div>
    <?php endif; ?>

    <?php if($activeTab === 'details'): ?>
    <div class="card">
        <div class="card-header">
            <div>
                <h2><?php echo e($subaccount->name); ?></h2>
                <div style="color: var(--muted); font-size:13px; margin-top:4px;"><?php echo e($subaccount->description ?: 'No description added.'); ?></div>
                <div style="color: var(--muted); font-size:13px; margin-top:4px;">Login Email: <?php echo e(optional($subaccount->user)->email ?? 'Not configured'); ?></div>
            </div>
        </div>
        <div class="card-body">
            <div class="grid-2">
                <div>
                    <div class="pill">Members: <?php echo e($subaccount->members()->count()); ?></div>
                </div>
                <div style="text-align:right;">Created <?php echo e($subaccount->created_at->format('M d, Y')); ?></div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h2>👤 Add New Member</h2>
        </div>
        <div class="card-body">
            <form method="POST" action="<?php echo e(route('client.account.subaccounts.members.store', $subaccount)); ?>">
                <?php echo csrf_field(); ?>
                <div class="form-grid">
                    <div class="form-group-enhanced">
                        <label for="member_name">Member Name *</label>
                        <input type="text" id="member_name" name="name" required value="<?php echo e(old('name')); ?>" placeholder="Enter full name">
                        <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span style="color:#dc2626;font-size:12px;display:block;margin-top:4px;"><?php echo e($message); ?></span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="form-group-enhanced">
                        <label for="member_email">Member Email</label>
                        <input type="email" id="member_email" name="email" placeholder="Optional email address" value="<?php echo e(old('email')); ?>">
                        <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span style="color:#dc2626;font-size:12px;display:block;margin-top:4px;"><?php echo e($message); ?></span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>
                <div style="display: flex; gap: 12px; align-items: center;">
                    <button type="submit" class="btn-submit">
                        <span>👤</span> Add Member
                    </button>
                    <small style="color: var(--muted);">* Required field</small>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <?php if($activeTab === 'members'): ?>
    <div class="card">
        <div class="card-header">
            <h2>👥 Member Supply Overview</h2>
        </div>
        <div class="card-body">
            <?php if($members->isEmpty()): ?>
                <div class="alert-info">
                    <strong>No members yet.</strong> Add members to start distributing items to them.
                </div>
            <?php else: ?>
                <div class="distribution-grid">
                    <?php $__currentLoopData = $members; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $totalDistributed = $member->distributions->sum('distributed_qty');
                            $itemCount = $member->distributions->count();
                        ?>
                        <div class="distribution-card">
                            <div class="item-header">
                                <div class="member-avatar" style="width: 40px; height: 40px; font-size: 18px;">
                                    <?php echo e(strtoupper(substr($member->name, 0, 1))); ?>

                                </div>
                                <div class="item-info">
                                    <h4><?php echo e($member->name); ?></h4>
                                    <p><?php echo e($member->email ?: 'No email provided'); ?></p>
                                </div>
                            </div>

                            <div class="stats-grid" style="grid-template-columns: repeat(2, 1fr);">
                                <div class="stat-item">
                                    <span class="stat-value"><?php echo e($itemCount); ?></span>
                                    <span class="stat-label">Items</span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-value"><?php echo e($totalDistributed); ?></span>
                                    <span class="stat-label">Total Qty</span>
                                </div>
                            </div>

                            <?php if(!$member->distributions->isEmpty()): ?>
                                <?php
                                    $remainingByItem = $allocatedItems->keyBy('stock_request_item_id')->map->remaining_qty;
                                ?>
                                <div style="margin-top: 16px;">
                                    <h5 style="margin: 0 0 8px 0; font-size: 14px; color: var(--text);">Current Items:</h5>
                                    <div style="max-height: 120px; overflow-y: auto;">
                                        <?php
                                    $groupedDistributions = $member->distributions->groupBy('stock_request_item_id');
                                ?>
                                <?php $__currentLoopData = $groupedDistributions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stockRequestItemId => $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $firstDistribution = $group->first();
                                        $totalDistributedForItem = $group->sum('distributed_qty');
                                        $usedQtyForItem = $group->sum('used_qty');
                                        $itemName = $firstDistribution->stockRequestItem->stock->description ?? $firstDistribution->stockRequestItem->stock->name ?? 'Item';
                                    ?>
                                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 6px 0; border-bottom: 1px solid rgba(0,0,0,0.05);">
                                        <div>
                                            <div style="font-size: 13px; color: var(--text);">
                                                <?php echo e($itemName); ?>

                                            </div>
                                            <div style="font-size: 11px; color: var(--muted); margin-top: 2px;">Current Qty: <?php echo e($totalDistributedForItem); ?></div>
                                        </div>
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <form method="POST" action="<?php echo e(route('client.account.subaccounts.distributions.update', [$subaccount, $firstDistribution])); ?>" style="display:flex; align-items:center; gap:6px; margin:0;">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('PUT'); ?>
                                                <input type="hidden" name="tab" value="members">
                                                <input type="number" class="member-update-qty" data-current="<?php echo e($totalDistributedForItem); ?>" name="updated_qty" value="" placeholder="New qty" min="0" max="<?php echo e(max(0, $totalDistributedForItem - 1)); ?>" style="width:80px; padding:4px 8px; border:1px solid var(--line); border-radius:6px; font-size:12px;">
                                                <button type="submit" style="padding:6px 10px; border:none; border-radius:6px; background:#2563eb; color:#fff; font-size:12px; cursor:pointer;">Apply</button>
                                            </form>
                                            <span class="used-count" style="font-size:11px; color: var(--muted);">Used: <?php echo e($usedQtyForItem); ?></span>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div style="text-align: center; padding: 20px; color: var(--muted); font-style: italic;">
                                    No items distributed yet
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if($activeTab === 'inventory'): ?>
    <div class="card">
        <div class="card-header">
            <h2> Available Items for Distribution</h2>
        </div>
        <div class="card-body">
            <?php if($allocatedItems->isEmpty()): ?>
                <div class="alert-info">
                    <strong>No items allocated yet.</strong> Items need to be allocated to this subaccount before they can be distributed to members.
                </div>
            <?php else: ?>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Allocated</th>
                                <th>Available</th>
                                <th>Distributed</th>
                                <th>Progress</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $allocatedItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $allocation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $item = $allocation->stockRequestItem;
                                    $distributed = $allocation->distributed_qty ?? 0;
                                    $remaining = $allocation->remaining_qty ?? max(0, $allocation->allocated_qty - $distributed);
                                    $memberQty = $allocation->member_qty ?? 0;
                                    $progressPercent = $allocation->allocated_qty > 0 ? (($allocation->allocated_qty - $remaining) / $allocation->allocated_qty) * 100 : 0;
                                    $itemName = $item->stock->description ?? $item->stock->name ?? 'Unknown Item';
                                    $initials = strtoupper(substr($itemName, 0, 2));
                                ?>
                                <tr>
                                    <td>
                                        <div class="item-header" style="margin: 0; gap: 12px;">
                                            <div class="item-icon" style="width: 32px; height: 32px; font-size: 14px;"><?php echo e($initials); ?></div>
                                            <div class="item-info" style="text-align: left;">
                                                <div style="font-weight: 600; color: var(--text);"><?php echo e($itemName); ?></div>
                                                <div style="font-size: 12px; color: var(--muted);">₱<?php echo e(number_format($item->stock->price ?? 0, 2)); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="text-align: center; font-weight: 600; color: #2563eb;"><?php echo e($allocation->allocated_qty); ?></td>
                                    <td style="text-align: center; font-weight: 600; color: #059669;"><?php echo e($memberQty); ?></td>
                                    <td style="text-align: center; font-weight: 600; color: #dc2626;"><?php echo e($distributed); ?></td>
                                    <td style="min-width: 120px;">
                                        <div style="display: flex; flex-direction: column; gap: 4px;">
                                            <div class="progress-bar" style="width: 100%; margin: 0;">
                                                <div class="progress-fill" style="width: <?php echo e($progressPercent); ?>%"></div>
                                            </div>
                                            <div style="font-size: 11px; color: var(--muted); text-align: center;"><?php echo e(number_format($progressPercent, 1)); ?>% used</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="quick-actions" style="margin: 0; flex-direction: column; gap: 6px;">
                                            <button class="btn-quick" onclick="selectItem(<?php echo e($item->id); ?>, '<?php echo e(addslashes($itemName)); ?>', <?php echo e($remaining); ?>)" <?php echo e($remaining <= 0 ? 'disabled' : ''); ?> style="width: 100%; padding: 6px 8px; font-size: 11px;">
                                                Quick Distribute
                                            </button>
                                            <button class="btn-quick" onclick="viewDistributionHistory(<?php echo e($allocation->id); ?>)" style="width: 100%; padding: 6px 8px; font-size: 11px;">
                                                View History
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="distribution-form" id="distributionForm" style="display: none;">
        <div class="form-header">
            <h3>🚀 Distribute Item to Member</h3>
            <button type="button" onclick="closeDistributionForm()" style="background: none; border: none; font-size: 24px; cursor: pointer; color: var(--muted);">&times;</button>
        </div>

        <form method="POST" action="<?php echo e(route('client.account.subaccounts.distributions.store', $subaccount)); ?>" id="distributionFormElement">
            <?php echo csrf_field(); ?>
            <input type="hidden" id="selected_item_id" name="stock_request_item_id">

            <?php if($members->isEmpty()): ?>
                <div class="alert-error">
                    <strong>No members available.</strong> Add members to this subaccount before distributing items.
                </div>
            <?php else: ?>
                <div class="form-group-enhanced">
                    <label for="selected_item_display">Selected Item</label>
                    <input type="text" id="selected_item_display" readonly style="background: rgba(37, 99, 235, 0.05);">
                </div>

                <div class="form-group-enhanced">
                    <label>Select Member</label>
                    <div class="member-selector">
                        <?php $__currentLoopData = $members; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="member-card" onclick="selectMember(<?php echo e($member->id); ?>, this)">
                                <div class="member-avatar">
                                    <?php echo e(strtoupper(substr($member->name, 0, 1))); ?>

                                </div>
                                <div class="member-details">
                                    <h5><?php echo e($member->name); ?></h5>
                                    <p><?php echo e($member->email ?: 'No email'); ?></p>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <input type="hidden" id="selected_member_id" name="member_id" required>
                </div>

                <div class="form-grid">
                    <div class="form-group-enhanced">
                        <label for="distributed_qty">Quantity to Distribute</label>
                        <div class="quantity-input">
                            <input type="number" id="distributed_qty" name="distributed_qty" min="1" max="" required>
                            <span class="quantity-max" id="maxQuantity">Max: 0</span>
                        </div>
                        <?php $__errorArgs = ['distributed_qty'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span style="color:#dc2626;font-size:12px;display:block;margin-top:4px;"><?php echo e($message); ?></span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="form-group-enhanced">
                        <label for="distribution_notes">Notes (Optional)</label>
                        <input type="text" id="distribution_notes" name="notes" placeholder="e.g., For project X, emergency supply">
                    </div>
                </div>

                <div style="display: flex; gap: 12px; align-items: center;">
                    <button type="submit" class="btn-submit" id="submitBtn">
                        <span>📦</span> Distribute Item
                    </button>
                    <button type="button" onclick="closeDistributionForm()" class="btn-quick" style="margin: 0;">
                        Cancel
                    </button>
                </div>
            <?php endif; ?>
        </form>
    </div>

    <?php if(!$allocatedItems->isEmpty() && !$members->isEmpty()): ?>
    <div class="bulk-actions">
        <h4>⚡ Bulk Actions</h4>
        <p style="margin: 8px 0 16px 0; color: var(--muted); font-size: 14px;">
            Need to distribute the same item to multiple members? Use the quick distribute buttons above for faster allocation.
        </p>
        <button class="btn-quick" onclick="showBulkDistribution()" style="background: #2563eb; color: white; border-color: #2563eb;">
            Bulk Distribution Mode
        </button>
    </div>
    <?php endif; ?>
    <?php endif; ?>
</div>

<script>
let selectedItem = null;
let selectedMember = null;
let maxQuantity = 0;

function selectItem(itemId, itemName, remainingQty) {
    if (remainingQty <= 0) return;

    selectedItem = { id: itemId, name: itemName, maxQty: remainingQty };
    document.getElementById('selected_item_display').value = itemName;
    document.getElementById('selected_item_id').value = itemId;
    document.getElementById('maxQuantity').textContent = `Max: ${remainingQty}`;
    document.getElementById('distributed_qty').max = remainingQty;
    document.getElementById('distributed_qty').value = Math.min(1, remainingQty);

    // Reset member selection
    selectedMember = null;
    document.getElementById('selected_member_id').value = '';
    document.querySelectorAll('.member-card').forEach(card => card.classList.remove('selected'));

    document.getElementById('distributionForm').style.display = 'block';
    document.getElementById('distributionForm').scrollIntoView({ behavior: 'smooth' });
}

function selectMember(memberId, element) {
    selectedMember = memberId;
    document.getElementById('selected_member_id').value = memberId;

    // Update UI
    document.querySelectorAll('.member-card').forEach(card => card.classList.remove('selected'));
    element.classList.add('selected');

    updateSubmitButton();
}

function closeDistributionForm() {
    document.getElementById('distributionForm').style.display = 'none';
    selectedItem = null;
    selectedMember = null;
    document.getElementById('selected_item_display').value = '';
    document.getElementById('selected_item_id').value = '';
    document.getElementById('selected_member_id').value = '';
    document.getElementById('distributed_qty').value = '';
    document.querySelectorAll('.member-card').forEach(card => card.classList.remove('selected'));
}

function updateSubmitButton() {
    const submitBtn = document.getElementById('submitBtn');
    const isValid = selectedItem && selectedMember && document.getElementById('distributed_qty').value > 0;

    submitBtn.disabled = !isValid;
    submitBtn.style.opacity = isValid ? '1' : '0.6';
}

function viewDistributionHistory(allocationId) {
    // This could open a modal or redirect to a detailed view
    alert('Distribution history feature coming soon! Allocation ID: ' + allocationId);
}

function updateUsagePreview(allocationId, newValue, maxAvailable) {
    const preview = document.getElementById(`usage-preview-${allocationId}`);
    const value = parseInt(newValue) || 0;
    const clampedValue = Math.max(0, Math.min(value, maxAvailable));

    if (value !== clampedValue) {
        // Auto-correct the input value
        event.target.value = clampedValue;
    }

    preview.textContent = `Will update usage to ${clampedValue} of ${maxAvailable} available`;

    // Update progress bar preview
    const progressBar = preview.closest('.distribution-card').querySelector('.progress-fill');
    const percent = maxAvailable > 0 ? (clampedValue / maxAvailable) * 100 : 0;
    progressBar.style.width = `${percent}%`;
}

// Event listeners
document.getElementById('distributed_qty').addEventListener('input', function() {
    const value = parseInt(this.value) || 0;
    if (selectedItem && value > selectedItem.maxQty) {
        this.value = selectedItem.maxQty;
    }
    updateSubmitButton();
});

// Form validation
document.getElementById('distributionFormElement').addEventListener('submit', function(e) {
    const qty = parseInt(document.getElementById('distributed_qty').value) || 0;

    if (!selectedItem || !selectedMember) {
        e.preventDefault();
        alert('Please select both an item and a member.');
        return;
    }

    if (qty <= 0) {
        e.preventDefault();
        alert('Please enter a valid quantity.');
        return;
    }

    if (qty > selectedItem.maxQty) {
        e.preventDefault();
        alert(`Cannot distribute more than ${selectedItem.maxQty} items.`);
        return;
    }
});

function updateUsedCounts() {
    document.querySelectorAll('.member-update-qty').forEach(input => {
        const currentQty = parseInt(input.dataset.current) || 0;
        const value = Math.max(0, Math.min(parseInt(input.value) || 0, currentQty));
        const used = Math.max(0, currentQty - value);
        const usedLabel = input.closest('div').querySelector('.used-count');
        if (usedLabel) {
            usedLabel.textContent = `Used: ${used}`;
        }
    });
}

document.querySelectorAll('.member-update-qty').forEach(input => {
    input.addEventListener('input', updateUsedCounts);
});

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    updateSubmitButton();
    updateUsedCounts();
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/client/account/subaccounts/show.blade.php ENDPATH**/ ?>