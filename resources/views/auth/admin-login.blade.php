<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Login - Inventory System</title>

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
        background-image: url('/images/building.jpg.JPG?v={{ time() }}');
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

    .login-logo{
        text-align:center;
        margin-bottom:32px;
    }

    .login-logo img{
        max-width:140px;
        height:auto;
        display:block;
        margin:0 auto;
    }

    .login-logo a{
        text-align:center;
        margin-bottom:32px;
    }

    .logo-label{
        text-align:center;
        font-size:22px;
        font-weight:800;
        color:var(--blue);
        margin-top:12px;
        letter-spacing:1px;
    }

    .switch-login{
        text-align:center;
        margin-top:24px;
        padding-top:20px;
        border-top:1px solid var(--line);
    }

    .switch-login-label{
        display:block;
        color:var(--muted);
        font-size:13px;
        margin-bottom:10px;
    }

    .switch-login a{
        display:inline-block;
        transform: scale(1.05);
        padding:8px 16px;
        background:var(--blue-soft);
        color:var(--blue);
        text-decoration:none;
        border-radius:6px;
        font-weight:700;
        font-size:13px;
        transition:background .2s;
    }

    .switch-login a:hover{
        background:rgba(37,99,235,.18);
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
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(37,99,235,.3);
    }

    .btn-submit:active{
        background:rgba(37,99,235,.8);
        transform: translateY(0
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
    color: var(--blue);
    
    .login-footer a:hover{
        text-decoration:underline;
    }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-logo">
            <img src="/images/logo.png.png?v={{ time() }}" alt="Logo">
            <div class="logo-label">Inventory System</div>
        </div>



        @if(session('error'))
            <div class="error-message">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('admin.login.submit') }}">
            @csrf

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>

            <button type="submit" class="btn-submit">Sign In</button>
        </form>

        <div class="login-footer">
            <p style="text-align: center;"><a href="{{ route('admin.password-reset-self.form') }}">Forgot your password?</a></p>

            <div class="switch-login">
                <span class="switch-login-label">Login as Client?</span>
                <a href="{{ route('client.login') }}">Switch to Client Login</a>
            </div>
        </div>
    </div>
</body>
</html>
