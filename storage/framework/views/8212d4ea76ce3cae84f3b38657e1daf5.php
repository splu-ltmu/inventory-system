<?php
  $brand = 'Inventory System';
  $pageTitle = 'Notification Preferences';
?>

<?php $__env->startSection('sidebar'); ?>
    <?php echo $__env->make('partials.admin-sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <h2>Notification Preferences</h2>
    
    <p style="color:var(--muted); margin-bottom:24px;">
        Customize which notifications you want to see and receive via email. Toggle notifications on/off based on your preferences.
    </p>

    <form action="<?php echo e(route('admin.notification-preferences.update')); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>
        
        <div style="display:grid; gap:16px;">
            <?php $__currentLoopData = $preferences; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type => $preference): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div style="border:1px solid var(--border); border-radius:8px; padding:16px; background:var(--panel1);">
                    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:12px;">
                        <h4 style="margin:0; color:var(--text);"><?php echo e($preference['label']); ?></h4>
                        <div style="display:flex; gap:12px; align-items:center;">
                            <label style="display:flex; align-items:center; gap:6px; cursor:pointer;">
                                <input type="checkbox" 
                                       name="preferences[<?php echo e($type); ?>][enabled]" 
                                       value="1"
                                       <?php echo e($preference['enabled'] ? 'checked' : ''); ?>

                                       onchange="this.closest('form').submit()">
                                <span style="font-size:14px;">Show in notifications</span>
                            </label>
                            
                            <label style="display:flex; align-items:center; gap:6px; cursor:pointer;">
                                <input type="checkbox" 
                                       name="preferences[<?php echo e($type); ?>][email_enabled]" 
                                       value="1"
                                       <?php echo e($preference['email_enabled'] ? 'checked' : ''); ?>

                                       onchange="this.closest('form').submit()">
                                <span style="font-size:14px;">Email alerts</span>
                            </label>
                        </div>
                    </div>
                    
                    <div style="font-size:13px; color:var(--muted);">
                        <?php switch($type):
                            case ('pending_requests'): ?>
                                Get notified when clients submit new stock requests that need your approval.
                                <?php break; ?>
                            <?php case ('password_resets'): ?>
                                Get notified when users request password resets.
                                <?php break; ?>
                            <?php case ('low_stock'): ?>
                                Get notified when inventory items are running low (≤5 units).
                                <?php break; ?>
                            <?php case ('out_of_stock'): ?>
                                Get notified when items are completely out of stock.
                                <?php break; ?>
                            <?php case ('urgent_outbounds'): ?>
                                Get notified when urgent outbound requests are submitted and need immediate attention.
                                <?php break; ?>
                            <?php case ('expiring_items'): ?>
                                Get notified when items are approaching their expiry date (within 7 days).
                                <?php break; ?>
                            <?php case ('new_clients'): ?>
                                Get notified when new clients register in the system.
                                <?php break; ?>
                            <?php case ('system_health'): ?>
                                Get notified about system issues like failed jobs or other health alerts.
                                <?php break; ?>
                        <?php endswitch; ?>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <div style="margin-top:24px; padding:16px; background:var(--panel2); border-radius:8px;">
            <h4 style="margin:0 0 8px 0;">Email Notification Settings</h4>
            <p style="margin:0; font-size:14px; color:var(--muted);">
                Email notifications will be sent to: <strong><?php echo e(auth()->user()->email); ?></strong>
            </p>
            <p style="margin:8px 0 0 0; font-size:13px; color:var(--muted);">
                Note: Make sure your email configuration is properly set up in the system settings to receive email notifications.
            </p>
        </div>
    </form>

    <?php if(session('success')): ?>
        <div style="position:fixed; top:20px; right:20px; background:var(--green); color:white; padding:12px 16px; border-radius:8px; box-shadow:0 4px 12px rgba(0,0,0,0.15); z-index:1000;">
            <?php echo e(session('success')); ?>

        </div>
        
        <script>
            setTimeout(() => {
                const successMsg = document.querySelector('div[style*="position:fixed"]');
                if(successMsg) successMsg.remove();
            }, 3000);
        </script>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/admin/notification-preferences.blade.php ENDPATH**/ ?>