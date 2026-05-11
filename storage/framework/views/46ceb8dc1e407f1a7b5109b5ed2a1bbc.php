<?php
  $brand = 'Inventory System';
  $pageTitle = 'My Outbounds';
?>

<?php $__env->startSection('sidebar'); ?>
    <?php echo $__env->make('client.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div style="margin-top:16px;">
        <!-- Header with Create Button -->
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <div>
                <h2 style="color:#1e40af; font-size:24px; font-weight:700; margin:0;">My Outbounds</h2>
                <p style="color:#64748b; font-size:14px; margin:4px 0 0 0;">Direct distributions from your inventory</p>
            </div>
            <a href="<?php echo e(route('client.outbounds.create')); ?>" 
               style="padding:10px 20px; background:linear-gradient(135deg, #10b981, #059669); color:#fff; text-decoration:none; border-radius:8px; font-weight:600; font-size:14px; display:inline-flex; align-items:center; gap:6px; transition:all 0.3s ease;"
               onmouseover="this.style.background='linear-gradient(135deg, #059669, #047857)'" 
               onmouseout="this.style.background='linear-gradient(135deg, #10b981, #059669)'">
                <span>+</span> Create Outbound
            </a>
        </div>

        <?php if($outbounds->count() > 0): ?>
            <div style="margin-top:20px; overflow-x:auto; border-radius:16px; box-shadow:0 8px 25px rgba(59,130,246,0.15); background:linear-gradient(135deg, #eff6ff, #dbeafe);">
                <table style="width:100%; border-collapse:collapse;">
                    <thead>
                        <tr style="background:linear-gradient(135deg, #3b82f6, #1d4ed8);">
                            <th style="padding:12px 10px; text-align:left; border-bottom:2px solid #1e40af; font-weight:700; color:#374151; font-size:12px; background:#f8fafc;">ID</th>
                            <th style="padding:12px 10px; text-align:left; border-bottom:2px solid #1e40af; font-weight:700; color:#374151; font-size:12px; background:#f8fafc;">Item</th>
                            <th style="padding:12px 10px; text-align:left; border-bottom:2px solid #1e40af; font-weight:700; color:#374151; font-size:12px; background:#f8fafc;">Quantity</th>
                            <th style="padding:12px 10px; text-align:left; border-bottom:2px solid #1e40af; font-weight:700; color:#374151; font-size:12px; background:#f8fafc;">Recipient</th>
                            <th style="padding:12px 10px; text-align:left; border-bottom:2px solid #1e40af; font-weight:700; color:#374151; font-size:12px; background:#f8fafc;">Status</th>
                            <th style="padding:12px 10px; text-align:left; border-bottom:2px solid #1e40af; font-weight:700; color:#374151; font-size:12px; background:#f8fafc;">Date</th>
                            <th style="padding:12px 10px; text-align:left; border-bottom:2px solid #1e40af; font-weight:700; color:#374151; font-size:12px; background:#f8fafc;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $outbounds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $outbound): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr style="border-bottom:1px solid #e0e7ff; background:linear-gradient(135deg, #ffffff, #f8fafc);">
                                <td style="padding:14px 10px; border-bottom:1px solid #e0e7ff;">
                                    <div style="font-weight:700; color:#1e40af; font-size:14px;">#<?php echo e($outbound->id); ?></div>
                                </td>
                                <td style="padding:14px 10px; border-bottom:1px solid #e0e7ff;">
                                    <div style="font-weight:600; color:#1e40af; font-size:13px;"><?php echo e($outbound->stock->id_no); ?></div>
                                    <div style="color:#64748b; font-size:11px; margin-top:2px;"><?php echo e($outbound->stock->description ?? $outbound->stock->name ?? 'Item'); ?></div>
                                </td>
                                <td style="padding:14px 10px; border-bottom:1px solid #e0e7ff; color:#475569; font-weight:600;"><?php echo e($outbound->total); ?></td>
                                <td style="padding:14px 10px; border-bottom:1px solid #e0e7ff;">
                                    <?php if($outbound->member): ?>
                                        <div style="font-weight:600; color:#1e40af; font-size:13px;"><?php echo e($outbound->member->name); ?></div>
                                        <div style="color:#64748b; font-size:11px;"><?php echo e($outbound->member->email); ?></div>
                                    <?php else: ?>
                                        <div style="font-weight:600; color:#059669; font-size:13px;">Direct Distribution</div>
                                        <div style="color:#64748b; font-size:11px;"><?php echo e($outbound->office); ?></div>
                                    <?php endif; ?>
                                </td>
                                <td style="padding:14px 10px; border-bottom:1px solid #e0e7ff;">
                                    <span style="padding:4px 8px; background:#10b981; color:#fff; font-size:11px; font-weight:600; border-radius:4px; display:inline-block;">
                                        <?php echo e(ucfirst($outbound->status)); ?>

                                    </span>
                                </td>
                                <td style="padding:14px 10px; border-bottom:1px solid #e0e7ff; color:#64748b; font-size:12px;">
                                    <?php echo e($outbound->created_at->format('M j, Y')); ?>

                                </td>
                                <td style="padding:14px 10px; border-bottom:1px solid #e0e7ff;">
                                    <a href="<?php echo e(route('client.outbounds.show', $outbound)); ?>" 
                                       style="color:#3b82f6; text-decoration:none; font-size:12px; font-weight:600; padding:4px 8px; border:1px solid #3b82f6; border-radius:4px; display:inline-block;"
                                       onmouseover="this.style.background='#3b82f6'; this.style.color='#fff'" 
                                       onmouseout="this.style.background='transparent'; this.style.color='#3b82f6'">
                                        View
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div style="margin-top:40px; text-align:center; padding:60px 20px; background:linear-gradient(135deg, #f8fafc, #e2e8f0); border-radius:16px; border:2px dashed #cbd5e1;">
                <div style="font-size:48px; color:#cbd5e1; margin-bottom:16px;">📦</div>
                <h3 style="color:#475569; font-size:20px; font-weight:600; margin:0 0 8px 0;">No Outbounds Created</h3>
                <p style="color:#64748b; font-size:14px; margin:0 0 24px 0;">You haven't created any direct outbounds from your inventory yet.</p>
                <a href="<?php echo e(route('client.outbounds.create')); ?>" 
                   style="padding:12px 24px; background:linear-gradient(135deg, #10b981, #059669); color:#fff; text-decoration:none; border-radius:8px; font-weight:600; font-size:14px; display:inline-flex; align-items:center; gap:6px; transition:all 0.3s ease;">
                    <span>+</span> Create Your First Outbound
                </a>
            </div>
        <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/client/outbounds/index.blade.php ENDPATH**/ ?>