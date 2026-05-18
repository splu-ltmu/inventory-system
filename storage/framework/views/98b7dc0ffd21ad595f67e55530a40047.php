<?php
  $brand = 'Inventory System';
  $pageTitle = 'Edit Account';
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

    .form-container{ max-width:600px; background:#fff; border:1px solid var(--line); border-radius:14px; padding:20px; }
    .form-group{ margin-bottom:20px; }
    .form-group label{ display:block; margin-bottom:8px; color: var(--text); font-weight:700; }
    .form-group input{ width:100%; padding:10px; border:1px solid var(--line); border-radius:8px; font-size:14px; }
    .form-group input:focus{ outline:none; border-color: var(--blue); box-shadow: 0 0 0 3px rgba(37,99,235,.1); }
    
    .form-actions{ display:flex; gap:12px; margin-top:24px; }
    .btn-submit{
        display:inline-block;
        padding:10px 20px;
        border-radius:10px;
        border:none;
        background: var(--blue);
        color: white;
        text-decoration:none;
        font-weight:700;
        cursor:pointer;
        transition: all 0.3s ease;
    }
    .btn-submit:hover{ 
        background: rgba(37,99,235,.9);
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(37,99,235,.2);
    }
    .btn-submit:active{
        transform: translateY(0);
    }
    .btn-cancel{
        display:inline-block;
        padding:10px 20px;
        border-radius:10px;
        border:1px solid var(--line);
        background: transparent;
        color: var(--text);
        text-decoration:none;
        font-weight:700;
        cursor:pointer;
        transition: all 0.3s ease;
    }
    .btn-cancel:hover{ 
        background: var(--line);
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0,0,0,.08);
    }
    .btn-cancel:active{
        transform: translateY(0);
    }

    .error-message{ color: var(--red); margin-bottom:16px; padding:12px; background: rgba(239,68,68,.1); border:1px solid rgba(239,68,68,.3); border-radius:8px; }
    .error-message ul{ margin:0; padding-left:20px; }
    .error-message li{ margin:4px 0; }

    .error-text{ color: var(--danger); font-size: 12px; margin-top: 4px; }
</style>

<div class="toolbar">
    <h2 style="margin:0;">Edit Client Account</h2>
    <a class="btn-link" href="<?php echo e(route('admin.users.index')); ?>">Back to Accounts</a>
</div>

<div class="form-container">
    <?php if($errors->any()): ?>
        <div class="error-message">
            <ul>
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <li><?php echo e($error); ?></li> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="<?php echo e(route('admin.users.update', $user->id)); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>
        
        <div class="form-group">
            <label for="name">Full Name:</label>
            <input type="text" name="name" id="name" placeholder="Enter client full name" required value="<?php echo e(old('name', $user->name)); ?>">
            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <span class="error-text"><?php echo e($message); ?></span>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div class="form-group">
            <label for="email">Email Address:</label>
            <input type="email" name="email" id="email" placeholder="Enter email address" required value="<?php echo e(old('email', $user->email)); ?>">
            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <span class="error-text"><?php echo e($message); ?></span>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div class="form-group">
            <label for="role">Role:</label>
            <select name="role" id="role" required style="width:100%; padding:10px; border:1px solid var(--line); border-radius:8px; font-size:14px;">
                <option value="client" <?php echo e(old('role', $user->role) == 'client' ? 'selected' : ''); ?>>Client</option>
                <option value="admin" <?php echo e(old('role', $user->role) == 'admin' ? 'selected' : ''); ?>>Admin</option>
            </select>
            <?php $__errorArgs = ['role'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <span class="error-text"><?php echo e($message); ?></span>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div class="form-group">
            <label for="office">Office / Department (optional):</label>
            <input type="text" name="office" id="office" placeholder="e.g. Purchasing" value="<?php echo e(old('office', $user->office)); ?>">
            <?php $__errorArgs = ['office'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <span class="error-text"><?php echo e($message); ?></span>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div style="padding:12px; background: rgba(37,99,235,.06); border-radius:8px; margin-bottom:20px; color:var(--muted); font-size:12px;">
            Note: To change the password, direct the client to use the "Request Password Reset" feature from the login page.
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-submit">Update Account</button>
            <a href="<?php echo e(route('admin.users.index')); ?>" class="btn-cancel">Cancel</a>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/admin/users/edit.blade.php ENDPATH**/ ?>