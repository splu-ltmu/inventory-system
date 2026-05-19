<?php
  $brand = 'Inventory System';
  $pageTitle = 'Password Reset';
?>

<?php $__env->startSection('sidebar'); ?>
    <?php echo $__env->make('partials.admin-sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<style>
    .toolbar{ display:flex; justify-content:space-between; align-items:center; gap:12px; margin-bottom:12px; }
    .btn-link{
        display:inline-block;
        padding:10px 12px;
        border-radius:10px;
        border:1px solid var(--blue);
        background: var(--blue-soft);
        color: var(--blue);
        text-decoration:none;
        font-weight:700;
    }
    .btn-link:hover{ background: rgba(37,99,235,.18); }

    .table-wrap{ overflow-x:auto; border-radius:16px; box-shadow:0 8px 25px rgba(59,130,246,0.15); background:linear-gradient(135deg, #eff6ff, #dbeafe); }
    table{ width:100%; border-collapse:collapse; }
    th,td{ border:1px solid #e0e7ff; padding:10px; text-align:left; }
    th{ background:linear-gradient(135deg, #3b82f6, #1d4ed8); color: #ffffff; font-weight:700; font-size:12px; border-bottom:2px solid #1e40af; }
    td{ color: #475569; font-size:13px; border-bottom:1px solid #e0e7ff; }
    .muted{ color: var(--muted); }

    .status-badge{
        display:inline-block;
        padding:4px 10px;
        border-radius:999px;
        font-size:12px;
        font-weight:700;
    }
    .status-pending{
        background: rgba(249,115,22,.10);
        color: var(--orange);
        border:1px solid rgba(249,115,22,.3);
    }
    .status-approved{
        background: rgba(22,163,74,.10);
        color: var(--success);
        border:1px solid rgba(22,163,74,.3);
    }
    .status-rejected{
        background: rgba(220,38,38,.10);
        color: var(--danger);
        border:1px solid rgba(220,38,38,.3);
    }

    .btn-action{
        display:inline-block;
        padding:6px 12px;
        border-radius:6px;
        border:none;
        font-size:12px;
        font-weight:700;
        cursor:pointer;
        text-decoration:none;
        margin-right:6px;
    }
    .btn-approve{
        background: rgba(22,163,74,.15);
        color: var(--success);
        border:1px solid rgba(22,163,74,.3);
    }
    .btn-approve:hover{
        background: rgba(22,163,74,.25);
    }
    .btn-reject{
        background: rgba(220,38,38,.15);
        color: var(--danger);
        border:1px solid rgba(220,38,38,.3);
    }
    .btn-reject:hover{
        background: rgba(220,38,38,.25);
    }

    .alert{
        padding:12px;
        border-radius:8px;
        margin-bottom:16px;
        border:1px solid;
    }
    .alert-success{
        background: rgba(22,163,74,.1);
        border-color: rgba(22,163,74,.3);
        color: var(--success);
    }
</style>

<?php if(session('success')): ?>
    <div class="alert alert-success"><?php echo e(session('success')); ?></div>
<?php endif; ?>

<div class="toolbar">
    <h2 style="margin:0;">Password Reset Requests</h2>
</div>

<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th style="min-width:160px;">Client Name</th>
                <th style="min-width:200px;">Email</th>
                <th style="min-width:140px;">Requested At</th>
                <th style="min-width:100px;">Status</th>
                <th style="min-width:200px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $requests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $req): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr style="border-bottom:1px solid #e0e7ff; background:linear-gradient(135deg, #ffffff, #f8fafc);">
                    <td style="padding:14px 10px; border-bottom:1px solid #e0e7ff;">
                        <div style="font-weight:700; color:#1e40af; font-size:14px;"><?php echo e($req->user->name ?? '—'); ?></div>
                    </td>
                    <td style="padding:14px 10px; border-bottom:1px solid #e0e7ff; color:#64748b; font-size:14px;"><?php echo e($req->user->email ?? '—'); ?></td>
                    <td style="padding:14px 10px; border-bottom:1px solid #e0e7ff; color:#475569; font-weight:600;"><?php echo e($req->requested_at->format('M d, Y H:i')); ?></td>
                    <td style="padding:14px 10px; border-bottom:1px solid #e0e7ff;">
                        <?php if($req->status === 'pending'): ?>
                            <span style="padding:4px 10px; border-radius:999px; font-size:12px; font-weight:700; border:1px solid #fed7aa; background:#fff7ed; color:#ea580c;"><?php echo e(ucfirst($req->status)); ?></span>
                        <?php elseif($req->status === 'sent'): ?>
                            <span style="padding:4px 10px; border-radius:999px; font-size:12px; font-weight:700; border:1px solid #bbf7d0; background:#ecfdf5; color:#059669;"><?php echo e(ucfirst($req->status)); ?></span>
                        <?php elseif($req->status === 'completed'): ?>
                            <span style="padding:4px 10px; border-radius:999px; font-size:12px; font-weight:700; border:1px solid #bbf7d0; background:#ecfdf5; color:#059669;"><?php echo e(ucfirst($req->status)); ?></span>
                        <?php elseif($req->status === 'rejected'): ?>
                            <span style="padding:4px 10px; border-radius:999px; font-size:12px; font-weight:700; border:1px solid #fecaca; background:#fef2f2; color:#dc2626;"><?php echo e(ucfirst($req->status)); ?></span>
                        <?php endif; ?>
                    </td>
                    <td style="padding:14px 10px; border-bottom:1px solid #e0e7ff;">
                        <?php if($req->status === 'pending'): ?>
                            <form method="POST" action="<?php echo e(route('password-reset.approve', $req->id)); ?>" style="display:inline;">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="btn-action btn-approve" style="padding:8px 16px; border-radius:8px; border:2px solid #3b82f6; background:linear-gradient(135deg, #3b82f6, #1d4ed8); color:#ffffff; font-weight:700; transition:all 0.3s ease; box-shadow:0 4px 12px rgba(59,130,246,0.2);">Approve</button>
                            </form>
                            <button type="button" onclick="showRejectModal(<?php echo e($req->id); ?>)" class="btn-action btn-reject" style="padding:8px 16px; border-radius:8px; border:2px solid #ef4444; background:linear-gradient(135deg, #ef4444, #dc2626); color:#ffffff; font-weight:700; transition:all 0.3s ease; box-shadow:0 4px 12px rgba(239,68,68,0.2);">Reject</button>
                        <?php elseif($req->status === 'sent'): ?>
                            <span style="color:#64748b; font-size:14px;">Link sent</span>
                        <?php elseif($req->status === 'completed'): ?>
                            <span style="color:#059669; font-size:14px; font-weight:600;">✓ Completed</span>
                        <?php elseif($req->status === 'rejected'): ?>
                            <span style="color:#dc2626; font-size:14px; font-weight:600;">✕ Rejected</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr style="background:linear-gradient(135deg, #f8fafc, #f1f5f9);">
                    <td colspan="4" style="padding:20px 10px; text-align:center; color:#64748b; font-size:14px;">No password reset requests.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Reject Modal -->
<div id="rejectModal" style="display:none !important; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,.5); z-index:999; justify-content:center; align-items:center;">
    <div style="background:white; border-radius:12px; padding:24px; max-width:400px; width:90%;">
        <h3 style="margin-top:0;">Reject Request</h3>
        <form id="rejectForm" method="POST">
            <?php echo csrf_field(); ?>
            <div style="margin-bottom:16px;">
                <label style="display:block; margin-bottom:8px; font-weight:700;">Notes (optional):</label>
                <textarea name="notes" style="width:100%; padding:10px; border:1px solid var(--line); border-radius:8px; font-family:Arial;" rows="4"></textarea>
            </div>
            <div style="display:flex; gap:12px;">
                <button type="submit" class="btn-action btn-reject" style="flex:1;">Reject</button>
                <button type="button" onclick="closeRejectModal()" style="flex:1; padding:6px 12px; border-radius:6px; border:1px solid var(--line); background:white; cursor:pointer;">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
function showRejectModal(requestId) {
    const form = document.getElementById('rejectForm');
    form.action = '/admin/password-reset/' + requestId + '/reject';
    document.getElementById('rejectModal').style.display = 'flex';
}

function closeRejectModal() {
    document.getElementById('rejectModal').style.display = 'none';
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/admin/password-reset/index.blade.php ENDPATH**/ ?>