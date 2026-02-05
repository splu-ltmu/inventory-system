<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $title ?? 'Inventory System' }}</title>

    <style>
    :root{
        --bg: #f8fafc;                 /* page background (white-ish) */
        --panel: #ffffff;              /* cards / sidebar */
        --panel2: #f1f5f9;             /* subtle secondary panel */

        --text: #0f172a;               /* main text (dark blue) */
        --muted: #475569;              /* secondary text */

        --line: #e2e8f0;               /* borders */

        --blue: #2563eb;               /* primary blue */
        --blue-soft: #eff6ff;

        --orange: #f97316;             /* accent orange */
        --orange-soft: #fff7ed;

        --success: #16a34a;
        --danger: #dc2626;
    }


    *{ box-sizing:border-box; }

    body{
        margin:0;
        font-family: Arial, Helvetica, sans-serif;
        background: var(--bg);
        color: var(--text);
    }
    

    .app{
        display:flex;
        min-height:100vh;
    }

    /* Sidebar */
    .sidebar{
        width:260px;
        background: linear-gradient(180deg, var(--panel), var(--panel2));
        border-right:1px solid var(--line);
        padding:18px 14px;
        position:sticky;
        top:0;
        height:100vh;
        overflow:auto;
    }

    .brand{
        padding:12px 12px 16px;
        border-bottom:1px solid var(--line);
        margin-bottom:14px;
    }

    .brand .name{
        font-weight:700;
        font-size:16px;
        color:var(--blue);
    }

    .brand .role{
        color:var(--muted);
        font-size:12px;
        margin-top:6px;
    }

    .nav{
        margin-top:12px;
        display:flex;
        flex-direction:column;
        gap:8px;
    }

    .nav a{
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:10px;
        text-decoration:none;
        color:var(--text);
        padding:11px 12px;
        border-radius:10px;
        border:1px solid transparent;
        background:#ffffff;
    }

    .nav a:hover{
        border-color:var(--blue);
        background:var(--blue-soft);
    }

    /* 🔵 ACTIVE TAB */
    .nav a.active{
        border-color:var(--blue);
        background:var(--blue-soft);
        color:var(--blue);
        font-weight:600;
    }

    .nav small{
        color:var(--muted);
        font-size:11px;
    }

    .sidebar-footer{
        margin-top:18px;
        padding-top:14px;
        border-top:1px solid var(--line);
    }

    .logout-btn{
        width:100%;
        padding:10px 12px;
        border-radius:10px;
        border:1px solid var(--orange);
        background:var(--orange-soft);
        color:var(--orange);
        font-weight:600;
        cursor:pointer;
    }

    .logout-btn:hover{
        background:rgba(249,115,22,.25);
    }

    /* Main */
    .main{
        flex:1;
        padding:22px;
    }

    

    .topbar{
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:12px;
        padding:14px 16px;
        background:#ffffff;
        border:1px solid var(--line);
        border-radius:14px;
        margin-bottom:16px;
    }

    .topbar .title{
        font-size:16px;
        font-weight:700;
        color:var(--blue);
    }

    .topbar .sub{
        color:var(--muted);
        font-size:12px;
        margin-top:4px;
    }

    .content{
        background:#ffffff;
        border:1px solid var(--line);
        border-radius:14px;
        padding:16px;
        min-height:calc(100vh - 22px - 16px - 16px - 22px);
    }

    /* FLASH MESSAGES */
    .flash-success{
        padding:10px 12px;
        border-radius:10px;
        border:1px solid var(--success);
        background:#ecfdf5;
        color:var(--success);
        margin-bottom:12px;
        transition: opacity .35s ease, transform .2s ease;
    }

    .flash-error{
        padding:10px 12px;
        border-radius:10px;
        border:1px solid var(--orange);
        background:var(--orange-soft);
        color:var(--orange);
        margin-bottom:12px;
    }

    /* RESPONSIVE DESIGN */
    @media (max-width: 768px){
        .app{
            flex-direction:column;
        }

        .sidebar{
            width:100%;
            height:auto;
            position:relative;
            top:auto;
            border-right:none;
            border-bottom:1px solid var(--line);
            padding:12px 8px;
            overflow:visible;
        }

        .brand{
            padding:8px 8px 12px;
            margin-bottom:10px;
        }

        .brand .name{
            font-size:14px;
        }

        .brand .role{
            font-size:11px;
        }

        .nav{
            display:flex;
            flex-direction:row;
            flex-wrap:wrap;
            gap:6px;
        }

        .nav a{
            flex:1 1 calc(50% - 3px);
            min-width:120px;
            padding:8px 10px;
            font-size:13px;
        }

        .nav a small{
            display:none;
        }

        .main{
            padding:12px;
            flex:1;
        }

        .topbar{
            flex-direction:column;
            align-items:flex-start;
            gap:8px;
            padding:10px 12px;
            margin-bottom:12px;
        }

        .topbar .title{
            font-size:14px;
        }

        .topbar .sub{
            font-size:11px;
        }

        .topbar > div:last-child{
            width:100%;
            display:flex;
            justify-content:space-between;
            align-items:center;
            font-size:11px;
        }

        .content{
            padding:12px;
            min-height:auto;
        }
    }

    @media (max-width: 480px){
        .sidebar{
            padding:8px 4px;
        }

        .brand{
            padding:6px 6px 10px;
        }

        .brand .name{
            font-size:12px;
        }

        .nav a{
            flex:1 1 100%;
            min-width:auto;
        }

        .main{
            padding:8px;
        }

        .topbar{
            padding:8px 10px;
            margin-bottom:8px;
        }

        .topbar .title{
            font-size:13px;
        }

        .content{
            padding:10px;
        }
    }
</style>

</head>

<body>
<div class="app">

    <aside class="sidebar">
        <div class="brand">
            <div style="display: flex; flex-direction: column; align-items: center; gap: 8px; margin-bottom: 16px;">
                <img src="/images/logo.png.png" alt="Logo" style="width: 120px; height: 120px; object-fit: contain;">
                <div class="name" style="text-align: center;">{{ $brand ?? 'Inventory System' }}</div>
            </div>
            <div class="role" style="position: relative; text-align: center;">
                Logged in as: <b>{{ auth()->user()->name ?? 'User' }}</b>
                <small>({{ auth()->user()->role ?? 'role' }})</small>
            </div>
        </div>

        <nav class="nav">
            {{-- Put your links here from child pages --}}
            @yield('sidebar')
        </nav>

        <div class="sidebar-footer">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="logout-btn" type="submit">Logout</button>
            </form>
        </div>
    </aside>

    <main class="main">
        <div class="topbar">
            <div>
                <div class="title">{{ $pageTitle ?? 'Dashboard' }}</div>
                <div class="sub">{{ $pageSubtitle ?? 'Choose a tab on the left.' }}</div>
            </div>
            <div style="display:flex; align-items:center; gap:12px;">
                <div style="color: var(--muted); font-size:12px;">{{ now()->format('M d, Y h:i A') }}</div>
                @include('partials.top-notifications')
            </div>
        </div>

        <section class="content">
                @if(session('success'))
                    <div class="flash-success flash-auto">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="flash-error">{{ session('error') }}</div>
            @endif

            @yield('content')
        </section>
    </main>

</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
    setTimeout(function(){
        document.querySelectorAll('.flash-auto').forEach(function(el){
            el.style.transition = 'opacity .45s ease, transform .25s ease';
            el.style.opacity = '0';
            el.style.transform = 'translateY(-6px)';
            setTimeout(function(){ if(el.parentNode) el.parentNode.removeChild(el); }, 500);
        });
    }, 3000);
});
</script>

</body>
</html>
