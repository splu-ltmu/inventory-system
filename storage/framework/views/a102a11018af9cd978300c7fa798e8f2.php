<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Password Reset - Inventory System</title>

    <style>
    :root{
        --bg: #f8fafc;
        --panel: #ffffff;
        --panel2: #f1f5f9;
        --text: #0f172a;
        --muted: #475569;
        --line: #e2e8f0;
        --blue: #2563eb;
        --blue-soft: #eff6ff;
        --orange: #f97316;
        --red: #dc2626;
    }

    *{ box-sizing:border-box; }

    body{
        margin:0;
        font-family: Arial, Helvetica, sans-serif;
        color: var(--text);
        display:flex;
        justify-content:center;
        align-items:center;
        min-height:100vh;
        padding:20px;
        background-image: url('/images/building.jpg.JPG');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        position: relative;
    }

    /* Global transitions */
    *{
        transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease, box-shadow 0.3s ease, opacity 0.3s ease, transform 0.3s ease;
    }

    body::before{
        content: '';
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(37, 99, 235, 0.6) 0%, rgba(37, 99, 235, 0.3) 50%, rgba(37, 99, 235, 0) 100%);
        pointer-events: none;
        z-index: 1;
    }

    .login-container{
        width:100%;
        max-width:400px;
        background: var(--panel);
        border:1px solid var(--line);
        border-radius:14px;
        padding:40px;
        box-shadow:0 1px 3px rgba(0,0,0,.1);
        position: relative;
        z-index: 2;
        animation: slideUp 0.5s ease;
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .login-header{
        text-align:center;
        margin-bottom:32px;
    }

    .login-header h2{
        margin:0 0 8px 0;
        font-size:28px;
        color: var(--text);
    }

    .login-header p{
        margin:0;
        color: var(--muted);
        font-size:14px;
    }

    .form-group{
        margin-bottom:20px;
    }

    .form-group label{
        display:block;
        margin-bottom:8px;
        color: var(--text);
        font-weight:700;
        font-size:14px;
    }

    .form-group input{
        width:100%;
        padding:12px;
        border:1px solid var(--line);
        border-radius:8px;
        font-size:14px;
        background: white;
        color: var(--text);
        transition:border-color .2s;
    }

    .form-group input:focus{
        outline:none;
        border-color: var(--blue);
        box-shadow:0 0 0 3px rgba(37,99,235,.1);
    }

    .form-group input::placeholder{
        color: var(--muted);
    }

    .form-group textarea{
        width:100%;
        padding:12px;
        border:1px solid var(--line);
        border-radius:8px;
        font-size:14px;
        background: white;
        color: var(--text);
        font-family: Arial, Helvetica, sans-serif;
        resize:vertical;
    }

    .form-group textarea:focus{
        outline:none;
        border-color: var(--blue);
        box-shadow:0 0 0 3px rgba(37,99,235,.1);
    }

    .btn-submit{
        width:100%;
        padding:12px;
        border:none;
        border-radius:8px;
        background: var(--blue);
        color:white;
        font-weight:700;
        font-size:14px;
        cursor:pointer;
        transition:background .2s;
        margin-top:8px;
    }

    .btn-submit:hover{
        background:rgba(37,99,235,.9);
    }

    .btn-submit:active{
        background:rgba(37,99,235,.8);
    }

    .error-message{
        background:rgba(220,38,38,.1);
        border:1px solid rgba(220,38,38,.3);
        color: var(--red);
        padding:12px;
        border-radius:8px;
        margin-bottom:20px;
        font-size:14px;
    }

    .success-message{
        background:rgba(22,163,74,.1);
        border:1px solid rgba(22,163,74,.3);
        color: #16a34a;
        padding:12px;
        border-radius:8px;
        margin-bottom:20px;
        font-size:14px;
    }

    .login-footer{
        text-align:center;
        margin-top:20px;
        color: var(--muted);
        font-size:13px;
    }

    .login-footer a{
        color: var(--blue);
        text-decoration:none;
        font-weight:700;
    }

    .login-footer a:hover{
        text-decoration:underline;
    }

    .info-text{
        background: rgba(37,99,235,.06);
        border:1px solid rgba(37,99,235,.2);
        padding:12px;
        border-radius:8px;
        font-size:13px;
        color: var(--muted);
        margin-bottom:20px;
        line-height:1.5;
    }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h2>Password Reset</h2>
            <p>Inventory System</p>
        </div>

        <div class="info-text">
            Reset your admin password. A password reset link will be sent to your email address.
        </div>

        <?php if(session('success')): ?>
            <div class="success-message"><?php echo e(session('success')); ?></div>
        <?php endif; ?>

        <?php if(session('error')): ?>
            <div class="error-message"><?php echo e(session('error')); ?></div>
        <?php endif; ?>

        <?php if($errors->any()): ?>
            <div class="error-message">
                <ul style="margin:0; padding-left:20px;">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li style="margin:4px 0;"><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo e(route('admin.password-reset-self.send')); ?>">
            <?php echo csrf_field(); ?>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" value="<?php echo e(old('email')); ?>" required>
            </div>

            <button type="submit" class="btn-submit">Send Reset Link</button>
        </form>

        <div class="login-footer">
            <p><a href="<?php echo e(route('admin.login')); ?>">← Back to login</a></p>
        </div>
    </div>
</body>
</html>
<?php /**PATH /var/www/resources/views/admin/password-reset/self-form.blade.php ENDPATH**/ ?>