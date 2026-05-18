<?php $__empty_1 = true; $__currentLoopData = $shown; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $req): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <?php $rid = 'req-'.$req->id; ?>

    <div class="req-card">
        <div class="req-header" onclick="toggleReq('<?php echo e($rid); ?>')">
            <div>
                <div class="req-title">
                    Request from <span style="color:#2563eb;"><?php echo e($req->office); ?></span>
                    <span style="color:#000000;">•</span>
                    <span style="color:#000000;"><?php echo e($req->client?->name ?? 'Client'); ?></span>
                    <span style="color:#000000;">•</span>
                    <span style="color:#000000;"><?php echo e($req->created_at?->format('M d, Y')); ?></span>
                </div>

                <div class="req-sub">
                    <span style="color:#000000;">Status:</span>
                    <span class="status-pill"><?php echo e(strtoupper(str_replace('_',' ', $req->status))); ?></span>
                    <span style="color:#000000; margin-left:10px;">Request ID:</span>
                    <b style="color:#000000;">#<?php echo e($req->id); ?></b>
                </div>
            </div>

            <div class="req-right">
                Ref. No:
                <span style="color:#0f172a;">#<?php echo e($req->id); ?></span>
                <div style="font-size:12px; font-weight:600; margin-top:4px; color:#000000;">Click to view details</div>
            </div>
        </div>

        <div id="<?php echo e($rid); ?>" class="req-body">
            <!-- <div class="muted" style="margin-bottom:10px;">Approve partially by setting Approved Qty per item (0 = rejected item).</div> -->

            <form method="POST" action="<?php echo e(route('admin.requests.decision', $req->id)); ?>">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>

                <div style="overflow:auto; border-radius:12px; border:1px solid #e2e8f0;">
                    <table>
                        <tr>
                            <th style="min-width:200px;">Item</th>
                            <th style="min-width:140px;">Requested</th>
                            <th style="min-width:140px;">Available</th>
                            <th style="min-width:160px;">Approved Qty</th>
                        </tr>

                        <?php $__empty_2 = true; $__currentLoopData = $req->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                            <tr>
                                <td style="text-align:left;">
                                    <b style="color:#000000;"><?php echo e($item->stock?->id_no ?? ''); ?></b> — <?php echo e($item->stock?->description ?? 'N/A'); ?>

                                    <div style="font-size:12px; color:#000000;">
                                        Unit: <?php echo e($item->stock?->unit ?? '—'); ?>

                                        <?php if(isset($item->stock->price)): ?> • Price: ₱<?php echo e(number_format($item->stock->price, 2)); ?><?php endif; ?>
                                    </div>
                                </td>

                                <td style="color:#000000;"><?php echo e($item->requested_qty); ?></td>
                                <td style="color:#000000;"><?php echo e($item->stock?->stock ?? 0); ?></td>

                                <td style="min-width:160px;">
                                    <div style="display:flex; gap:6px; align-items:center;">
                                        <input
                                            type="number"
                                            name="approved_qty[<?php echo e($item->id); ?>]"
                                            min="0"
                                            max="<?php echo e($item->stock?->stock ?? 0); ?>"
                                            value="<?php echo e($item->approved_qty ?? 0); ?>"
                                            <?php echo e($activeTab !== 'pending' ? 'readonly' : ''); ?>

                                            style="flex:1; text-align:center;"
                                        >
                                        <?php if($activeTab === 'pending'): ?>
                                            <button type="button" class="btn-max" onclick="setMax(this, <?php echo e($item->requested_qty); ?>)" style="padding:8px 10px; border-radius:10px; border:1px solid #2563eb; background:#2563eb; color:#fff; cursor:pointer; font-weight:700; white-space:nowrap; flex-shrink:0;">Max</button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                            <tr>
                                <td colspan="4" style="color:#000000;">No request items found for this request.</td>
                            </tr>
                        <?php endif; ?>
                    </table>
                </div>

                <?php if($req->status !== 'ready_to_receive'): ?>
                    <div style="display:flex; gap:10px; flex-wrap:wrap; margin-top:12px;">
                        <?php if($req->status !== 'approved'): ?>
                            <button class="btn-ghost" type="button" onclick="confirmAction(event, null, 'Save Decision', 'Save: approval quantities for this request?', '<?php echo e($req->id); ?>')">
                                Save Decision
                            </button>
                        <?php endif; ?>

                        <?php if($req->status !== 'pending'): ?>
                            <button class="btn-ghost" type="button" onclick="confirmAction(event, 'rejected', 'Reject Entire Request', 'This request will be rejected. This action cannot be undone.', '<?php echo e($req->id); ?>')">
                                Reject Whole Request
                            </button>

                            <button class="btn-ghost" type="button" onclick="confirmAction(event, 'ready_to_receive', 'Generate Code', 'Proceed to generate a verification code for the client to claim these items.', '<?php echo e($req->id); ?>')">
                                Ready to Receive
                            </button>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </form>

            <?php if($req->status === 'ready_to_receive'): ?>
                <hr style="border:none; border-top:1px solid #e2e8f0; margin:16px 0;">

                <form method="POST" action="<?php echo e(route('admin.requests.release', $req->id)); ?>">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>

                    <div style="background:#eff6ff; border:1px solid #2563eb; border-radius:10px; padding:12px; display:flex; gap:10px; align-items:flex-end;">
                        <div style="flex:1; min-width:200px;">
                            <label style="font-size:12px; font-weight:700; color:#0f172a; display:block; margin-bottom:6px;">🔐 Client Code</label>
                            <input type="text" name="verification_code" placeholder="Enter code" required style="padding:10px;">
                        </div>

                        <button class="btn" type="submit" style="padding:10px 16px;">Release</button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>

<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <div class="muted">No requests found.</div>
<?php endif; ?>
<?php /**PATH /var/www/resources/views/admin/requests/_list.blade.php ENDPATH**/ ?>