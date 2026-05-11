<?php
  $brand = 'Inventory System';
  $pageTitle = 'Outbound Details';
?>

<?php $__env->startSection('sidebar'); ?>
    <?php echo $__env->make('client.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div style="margin-top:16px;">
        <!-- Header -->
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <div>
                <h2 style="color:#1e40af; font-size:24px; font-weight:700; margin:0;">Outbound #<?php echo e($outbound->id); ?></h2>
                <p style="color:#64748b; font-size:14px; margin:4px 0 0 0;">Created on <?php echo e($outbound->created_at->format('F j, Y, g:i A')); ?></p>
            </div>
            <a href="<?php echo e(route('client.outbounds.index')); ?>" 
               style="padding:10px 20px; background:#f8fafc; color:#64748b; text-decoration:none; border:2px solid #e2e8f0; border-radius:8px; font-weight:600; font-size:14px; display:inline-block; transition:all 0.3s ease;"
               onmouseover="this.style.background='#e2e8f0'" 
               onmouseout="this.style.background='#f8fafc'">
                ← Back to Outbounds
            </a>
        </div>

        <!-- Outbound Details Card -->
        <div style="background:linear-gradient(135deg, #ffffff, #f8fafc); border-radius:16px; box-shadow:0 8px 25px rgba(59,130,246,0.15); border:1px solid #e2e8f0; overflow:hidden;">
            
            <!-- Status Header -->
            <div style="padding:20px 24px; background:linear-gradient(135deg, #10b981, #059669); border-bottom:1px solid #e2e8f0;">
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <div>
                        <div style="color:#fff; font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">Status</div>
                        <div style="color:#fff; font-size:18px; font-weight:700; margin-top:2px;"><?php echo e(ucfirst($outbound->status)); ?></div>
                    </div>
                    <div style="text-align:right;">
                        <div style="color:#fff; font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">Outbound ID</div>
                        <div style="color:#fff; font-size:18px; font-weight:700; margin-top:2px;">#<?php echo e($outbound->id); ?></div>
                    </div>
                </div>
            </div>

            <!-- Details Grid -->
            <div style="padding:24px;">
                <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap:20px;">
                    
                    <!-- Item Information -->
                    <div style="padding:16px; background:#f8fafc; border-radius:12px; border:1px solid #e2e8f0;">
                        <div style="color:#64748b; font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:8px;">Item Information</div>
                        <div style="font-weight:700; color:#1e40af; font-size:16px; margin-bottom:4px;"><?php echo e($outbound->stock->id_no); ?></div>
                        <div style="color:#475569; font-size:14px;"><?php echo e($outbound->stock->description ?? $outbound->stock->name ?? 'Item'); ?></div>
                        <?php if($outbound->stock->price): ?>
                            <div style="color:#64748b; font-size:12px; margin-top:8px;">Unit Price: ₱<?php echo e(number_format($outbound->stock->price, 2)); ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Quantity Information -->
                    <div style="padding:16px; background:#f8fafc; border-radius:12px; border:1px solid #e2e8f0;">
                        <div style="color:#64748b; font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:8px;">Quantity</div>
                        <div style="font-weight:700; color:#1e40af; font-size:24px; margin-bottom:4px;"><?php echo e($outbound->total); ?></div>
                        <div style="color:#475569; font-size:14px;">items distributed</div>
                        <?php if($outbound->stock->price): ?>
                            <div style="color:#64748b; font-size:12px; margin-top:8px;">Total Value: ₱<?php echo e(number_format($outbound->total * $outbound->stock->price, 2)); ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Recipient Information -->
                    <div style="padding:16px; background:#f8fafc; border-radius:12px; border:1px solid #e2e8f0;">
                        <div style="color:#64748b; font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:8px;">Recipient</div>
                        <?php if($outbound->member): ?>
                            <div style="font-weight:700; color:#1e40af; font-size:16px; margin-bottom:4px;"><?php echo e($outbound->member->name); ?></div>
                            <div style="color:#475569; font-size:14px;"><?php echo e($outbound->member->email); ?></div>
                        <?php else: ?>
                            <div style="font-weight:700; color:#059669; font-size:16px; margin-bottom:4px;">Direct Distribution</div>
                            <div style="color:#475569; font-size:14px;"><?php echo e($outbound->office); ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Office Information -->
                    <div style="padding:16px; background:#f8fafc; border-radius:12px; border:1px solid #e2e8f0;">
                        <div style="color:#64748b; font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:8px;">Office</div>
                        <div style="font-weight:700; color:#1e40af; font-size:16px; margin-bottom:4px;"><?php echo e($outbound->office); ?></div>
                        <div style="color:#475569; font-size:14px;">Distribution location</div>
                    </div>

                </div>

                <!-- Reason Section -->
                <?php if($outbound->reason): ?>
                    <div style="margin-top:20px; padding:16px; background:#fef3c7; border-radius:12px; border:1px solid #fbbf24;">
                        <div style="color:#92400e; font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:8px;">Reason</div>
                        <div style="color:#78350f; font-size:14px; line-height:1.5;"><?php echo e($outbound->reason); ?></div>
                    </div>
                <?php endif; ?>

                <!-- Timeline Information -->
                <div style="margin-top:20px; padding:16px; background:#f0fdf4; border-radius:12px; border:1px solid #86efac;">
                    <div style="color:#166534; font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:12px;">Timeline</div>
                    <div style="display:flex; flex-direction:column; gap:8px;">
                        <div style="display:flex; justify-content:space-between; align-items:center;">
                            <div style="color:#475569; font-size:14px;">Created:</div>
                            <div style="color:#1e40af; font-size:14px; font-weight:600;"><?php echo e($outbound->created_at->format('M j, Y g:i A')); ?></div>
                        </div>
                        <?php if($outbound->deducted_at): ?>
                            <div style="display:flex; justify-content:space-between; align-items:center;">
                                <div style="color:#475569; font-size:14px;">Inventory Deducted:</div>
                                <div style="color:#059669; font-size:14px; font-weight:600;"><?php echo e($outbound->deducted_at->format('M j, Y g:i A')); ?></div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/client/outbounds/show.blade.php ENDPATH**/ ?>