<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Client Login - Inventory System</title>

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

    .logo-label{
        text-align:center;
        font-size:22px;
        font-weight:800;
        color:var(--blue);
        margin-top:12px;
        letter-spacing:1px;
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
        transition: all 0.3s ease;
        margin-top:8px;
    }

    .btn-submit:hover{
        background:rgba(37,99,235,.9);
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(37,99,235,.2);
    }

    .btn-submit:active{
        background:rgba(37,99,235,.8);
        transform: translateY(0);
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
            <img src="/images/logo.png.png" alt="Logo">
            <div class="logo-label">Inventory System</div>
        </div>

        @if(session('error'))
            <div class="error-message">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('client.login.submit') }}" onsubmit="showLoading('Signing in...')">
            @csrf

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
            </div>

            <div class="form-group" style="position: relative; width: 100%;">
    <label for="password">Password</label>
    
    <input 
        type="password" 
        id="password" 
        name="password" 
        placeholder="Enter your password" 
        required
        style="width: 100%; padding-right: 40px;"
    >

    <!-- Eye Button -->
    <span 
        onclick="togglePassword()" 
        style="
            position: absolute;
            right: 10px;
            top: 38px;
            cursor: pointer;
            user-select: none;
        "
    >
        👁️
    </span>
</div>


            <button type="submit" class="btn-submit">Sign In</button>
        </form>

        <div class="login-footer">
            <p style="text-align: center;"><a href="{{ route('password-reset.request') }}">Forgot your password?</a></p>
        </div>
    </div>
    <!-- Loading overlay for login page -->
    <div id="global-loading" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.65); z-index:9999; align-items:center; justify-content:center;">
        <div style="background:transparent; padding:40px; border-radius:12px; display:flex; flex-direction:column; gap:24px; align-items:center; box-shadow:none;">
            <div style="width:60px; height:60px; border-radius:50%; border:4px solid transparent; border-top-color: #2563eb; border-right-color: #2563eb; animation: spin 1.2s linear infinite;"></div>
            <div style="font-weight:700; color: #fff; font-size:16px;" id="loading-message">Signing in...</div>
        </div>
    </div>

    <style>@keyframes spin{ to { transform: rotate(360deg); } }</style>

    <script>
    function showLoading(msg){
        var overlay = document.getElementById('global-loading');
        var m = document.getElementById('loading-message');
        if(m) m.textContent = msg || 'Loading...';
        if(overlay) overlay.style.display = 'flex';
    }
    function hideLoading(){ var overlay = document.getElementById('global-loading'); if(overlay) overlay.style.display = 'none'; }
    window.addEventListener('pageshow', function(){ hideLoading(); });


function togglePassword() {
    const password = document.getElementById("password");

    if (password.type === "password") {
        password.type = "text";
    } else {
        password.type = "password";
    }
}

    </script>
</body>
</html>
