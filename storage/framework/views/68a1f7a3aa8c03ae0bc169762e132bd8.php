<?php
  $brand = 'Inventory System';
  $pageTitle = 'Notification Preferences';
?>

<?php $__env->startSection('sidebar'); ?>
    <?php echo $__env->make('client.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <h2>Notification Preferences</h2>
    
    <p style="color:var(--muted); margin-bottom:24px;">
        Customize which notifications you want to see. Toggle notifications on/off based on your preferences.
    </p>

    <form action="<?php echo e(route('client.notification-preferences.update')); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>
        
        <div style="display:grid; gap:16px;">
            <?php
                $clientTypes = [
                    'request_updates' => 'Request Status Updates',
                    'inventory_alerts' => 'Inventory Alerts', 
                    'member_activity' => 'Member Activity',
                ];
                
                // Get current preferences (simplified approach)
                $currentPreferences = [
                    'request_updates' => true,
                    'inventory_alerts' => true,
                    'member_activity' => true,
                ];
            ?>
            
            <?php $__currentLoopData = $clientTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if(isset($currentPreferences[$type])): ?>
                    <div style="border:1px solid var(--border); border-radius:8px; padding:16px; background:var(--panel1);">
                        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:12px;">
                            <h4 style="margin:0; color:var(--text);"><?php echo e($label); ?></h4>
                            <label style="display:flex; align-items:center; gap:6px; cursor:pointer;">
                                <input type="checkbox" 
                                       name="preferences[<?php echo e($type); ?>][enabled]" 
                                       value="1"
                                       <?php echo e($currentPreferences[$type] ? 'checked' : ''); ?>

                                       onchange="this.closest('form').submit()">
                                <span style="font-size:14px;">Enable notifications</span>
                            </label>
                        </div>
                        
                        <div style="font-size:13px; color:var(--muted);">
                            <?php switch($type):
                                case ('request_updates'): ?>
                                    Get notified when your requests are approved, rejected, or need attention.
                                    <?php break; ?>
                                <?php case ('inventory_alerts'): ?>
                                    Get notified when your inventory items are running low.
                                    <?php break; ?>
                                <?php case ('member_activity'): ?>
                                    Get notified when new members are added to your team.
                                    <?php break; ?>
                            <?php endswitch; ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <div style="margin-top:24px; padding:16px; background:var(--panel2); border-radius:8px;">
            <h4 style="margin:0 0 8px 0;">About Client Notifications</h4>
            <p style="margin:0; font-size:14px; color:var(--muted);">
                Client notifications help you stay informed about your inventory requests, stock levels, and team activities. 
                You'll receive real-time updates in the notification bell icon and can view detailed history on this page.
            </p>
            <p style="margin:8px 0 0 0; font-size:13px; color:var(--muted);">
                <strong>Note:</strong> As a client, you'll receive notifications for all your requests, 
                inventory changes, and member management activities.
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/client/notification-preferences.blade.php ENDPATH**/ ?>