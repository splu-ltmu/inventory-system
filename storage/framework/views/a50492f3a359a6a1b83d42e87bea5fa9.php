<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Password Reset</title>
  <style>
    body { font-family: Arial, sans-serif; color: #333; }
    .container { max-width: 600px; margin: 24px auto; padding: 16px; border: 1px solid #eee; }
    .footer { font-size: 12px; color: #888; margin-top: 16px; }
  </style>
</head>
<body>
  <div class="container">
    <h2>Your password has been reset</h2>

    <p>Hello <?php echo e($user->name); ?>,</p>

    <p>An administrator has reset your account password. Use the temporary password below to log in, then change your password from your account settings.</p>

    <p><strong>Temporary password:</strong> <?php echo e($tempPassword); ?></p>

    <p>If you did not request this, please contact support immediately.</p>

    <div class="footer">
      <p><?php echo e(config('app.name')); ?> — <?php echo e(url('/')); ?></p>
    </div>
  </div>
</body>
</html>
<?php /**PATH /var/www/resources/views/emails/admin-approved-reset.blade.php ENDPATH**/ ?>