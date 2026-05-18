<?php $__env->startSection('content'); ?>
<div class="container">
  <h2>Reset password for <?php echo e($email); ?></h2>

  <form method="POST" action="<?php echo e(route('password-reset.admin.reset')); ?>">
    <?php echo csrf_field(); ?>
    <input type="hidden" name="token" value="<?php echo e($token); ?>">

    <div class="mb-3">
      <label for="password">New Password</label>
      <input id="password" type="password" name="password" required class="form-control">
    </div>

    <div class="mb-3">
      <label for="password_confirmation">Confirm Password</label>
      <input id="password_confirmation" type="password" name="password_confirmation" required class="form-control">
    </div>

    <button class="btn btn-primary">Set Password</button>
  </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/admin/password-reset/form.blade.php ENDPATH**/ ?>