<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Inventory System</title>
    <style>
    :root{
        --bg: #f8fafc;
        --panel: #ffffff;
        --panel2: #f1f5f9;
        --text: #0f172a;
        --muted: #475569;
        --line: #e2e8f0;
        --blue: #2563eb;
        --orange: #f97316;
    }

    *{ box-sizing:border-box; }

    body { 
        font-family: Arial, sans-serif; 
        margin: 0;
        padding: 12px;
        background:var(--bg); 
        color:var(--text); 
        display: flex;
        gap: 16px;
    }

    .topbar{ 
        display:flex; 
        align-items:center; 
        justify-content:space-between; 
        gap:12px; 
        padding:12px 14px; 
        background:var(--panel); 
        border:1px solid var(--line); 
        border-radius:10px; 
        margin-bottom:16px; 
        flex-wrap:wrap;
    }

    .topbar .title{ 
        font-size:18px; 
        font-weight:700; 
        color:var(--blue); 
    }

    .topbar .sub{ 
        color:var(--muted); 
        font-size:12px; 
    }

    .sidebar{
        width: 280px;
        background:var(--panel);
        border:1px solid var(--line);
        border-radius:10px;
        padding:16px;
        height: fit-content;
        position: sticky;
        top: 12px;
        flex-shrink: 0;
    }

    .content-wrapper{
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    main{ 
        background:var(--panel); 
        border:1px solid var(--line); 
        border-radius:10px; 
        padding:14px; 
        overflow-x:auto;
    }

    table { 
        border-collapse: collapse; 
        width: 100%; 
        font-size:14px;
    }

    th, td { 
        border: 1px solid var(--line); 
        padding: 8px; 
        text-align: left; 
    }

    th { 
        background-color: var(--panel2); 
        font-weight:600;
    }

    a { 
        margin-right: 5px; 
        text-decoration: none; 
        color: var(--blue); 
    }

    a:hover{
        text-decoration:underline;
    }

    button { 
        cursor: pointer; 
        padding: 6px 10px;
        border-radius: 6px;
        border: 1px solid var(--line);
        background: var(--panel2);
        color: var(--text);
        font-size: 12px;
    }

    button:hover{
        border-color: var(--blue);
        background: var(--blue-soft);
    }

    /* RESPONSIVE DESIGN */
    @media (max-width: 768px){
        body{
            padding: 8px;
            flex-direction: column;
        }

        .sidebar{
            width: 100%;
            position: static;
        }

        .topbar{
            flex-direction:column;
            align-items:flex-start;
            padding:10px 12px;
            margin-bottom:12px;
            gap:8px;
        }

        .topbar > div:last-child{
            width:100%;
            display:flex;
            justify-content:space-between;
            align-items:center;
            font-size:11px;
            flex-wrap:wrap;
        }

        .topbar .title{
            font-size:16px;
        }

        .topbar .sub{
            font-size:11px;
        }

        main{
            padding:10px;
            margin-bottom:12px;
        }

        table{
            font-size:12px;
        }

        th, td{
            padding:6px;
        }

        button{
            padding: 4px 8px;
            font-size: 11px;
        }
    }

    @media (max-width: 480px){
        body{
            padding: 6px;
        }

        .topbar{
            padding:8px 10px;
        }

        .topbar .title{
            font-size:14px;
        }

        main{
            padding:8px;
        }

        table{
            font-size:11px;
        }

        th, td{
            padding:4px;
        }
    }
    </style>
</head>
<body>
    <aside class="sidebar">
        <?php echo $__env->make('partials.admin-sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </aside>
    
    <div class="content-wrapper">
        <header>
            <div class="topbar">
                <div style="display: flex; align-items: center; gap: 16px;">
                    <img src="/images/logo.png.png" alt="Logo" style="width: 60px; height: 60px; object-fit: contain;">
                    <div>
                        <div class="title">Admin Inventory System</div>
                    </div>
                </div>
                <div style="display:flex; align-items:center; gap:12px;">
                    <div style="color: var(--muted); font-size:12px;"><?php echo e(now()->format('M d, Y h:i A')); ?></div>
                    <?php echo $__env->make('partials.top-notifications', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>
            </div>
        </header>

        <main>
            <?php echo $__env->yieldContent('content'); ?>
        </main>
    </div>
</body>
</html>
<?php /**PATH /var/www/resources/views/layouts/admin.blade.php ENDPATH**/ ?>