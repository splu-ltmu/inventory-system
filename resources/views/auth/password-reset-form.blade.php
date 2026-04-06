<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Reset Password - Inventory System</title>

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
        --green: #16a34a;
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

    .reset-container{
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

    .reset-header{
        text-align:center;
        margin-bottom:32px;
    }

    .reset-header h2{
        margin:0 0 8px 0;
        font-size:24px;
        color: var(--text);
    }

    .reset-header p{
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

    .password-info{
        background: rgba(37,99,235,.06);
        border:1px solid rgba(37,99,235,.2);
        padding:12px;
        border-radius:8px;
        font-size:13px;
        color: var(--muted);
        margin-bottom:20px;
        line-height:1.5;
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

    .back-link{
        text-align:center;
        margin-top:20px;
        padding-top:20px;
        border-top:1px solid var(--line);
    }

    .back-link a{
        color: var(--blue);
        text-decoration:none;
        font-weight:700;
        font-size:14px;
    }

    .back-link a:hover{
        text-decoration:underline;
    }
    </style>
</head>
<body>
    <div class="reset-container">
        <div class="reset-header">
            <h2>🔐 Create New Password</h2>
            <p>Enter a strong password for your account</p>
        </div>

        @if($errors->any())
            <div class="error-message">
                <strong>Error:</strong>
                <ul style="margin: 8px 0 0 0; padding-left: 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="password-info">
            <strong>Password Requirements:</strong>
            <ul style="margin: 8px 0 0 0; padding-left: 20px;">
                <li>At least 8 characters long</li>
                <li>Include uppercase and lowercase letters</li>
                <li>Include numbers and special characters</li>
            </ul>
        </div>

        <form method="POST" action="{{ route('password-reset.update', ['token' => $token]) }}">
            @csrf

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" value="{{ $email }}" disabled>
            </div>

            <div class="form-group">
                <label for="password">New Password</label>
                <input type="password" id="password" name="password" placeholder="Enter new password" required>
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirm Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirm your password" required>
            </div>

            <button type="submit" class="btn-submit">Reset Password</button>
        </form>

        <div class="back-link">
            <a href="{{ route('admin.login') }}">← Back to Login</a>
        </div>
    </div>
</body>
</html>
