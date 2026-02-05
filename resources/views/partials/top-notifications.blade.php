@php
    // Only compute and render notifications for admin users
    $isAdmin = auth()->check() && (auth()->user()->role ?? '') === 'admin';
    if($isAdmin) {
        $pendingRequests = class_exists(\App\Models\StockRequest::class) ? \App\Models\StockRequest::where('status','pending')->count() : 0;
        $pendingPR = class_exists(\App\Models\PasswordResetRequest::class) ? \App\Models\PasswordResetRequest::where('status','pending')->count() : 0;
        $lowThreshold = 5;
        $lowStock = class_exists(\App\Models\Stock::class) ? \App\Models\Stock::where('stock','>',0)->where('stock','<=',$lowThreshold)->count() : 0;
        $outStock = class_exists(\App\Models\Stock::class) ? \App\Models\Stock::where('stock','<=',0)->count() : 0;
        $totalPending = $pendingRequests + $pendingPR + $lowStock + $outStock;
    }
@endphp

@if($isAdmin && request()->is('admin*'))
<style>
.top-notif{ display:flex; align-items:center; gap:8px; position:relative; }
.top-notif button{ background:transparent;border:none;cursor:pointer;font-size:18px;padding:6px 8px; border-radius:8px; display:inline-flex; align-items:center; justify-content:center; }
.top-notif button:hover{ background:rgba(2,6,23,.03); }
.top-notif .icon{ width:20px; height:20px; display:inline-block; }
.top-notif .badge{ position:absolute; top:-6px; right:-6px; background:#ef4444;color:#fff;padding:2px 6px;border-radius:999px;font-weight:700;font-size:11px; }
.top-notif .dropdown{ display:none; position:absolute; right:0; top:calc(100% + 8px); width:320px; background:var(--panel, #fff); border:1px solid var(--line); border-radius:10px; padding:12px; box-shadow:0 8px 20px rgba(2,6,23,.08); z-index:9999; }
.top-notif .dropdown.open{ display:block; }
.top-notif .dropdown .item{ display:flex; align-items:center; gap:8px; padding:8px 6px; border-radius:6px; color:var(--text); text-decoration:none; }
.top-notif .dropdown .item:hover{ background:rgba(2,6,23,.03); }
.top-notif .dropdown .muted{ color:var(--muted); font-size:13px; }
.top-notif .dropdown .footer{ border-top:1px solid var(--line); padding-top:8px; margin-top:8px; text-align:right; }
</style>

<div class="top-notif" aria-live="polite">
    <button id="topNotifBtn" title="Notifications" aria-haspopup="true" aria-expanded="false">
        <svg class="icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M15 17H9a3 3 0 006 0z" fill="currentColor" opacity="0.9"/>
            <path d="M18 8a6 6 0 10-12 0v4l-2 2v1h16v-1l-2-2V8z" stroke="currentColor" stroke-width="0" fill="currentColor" />
        </svg>
        <span id="notif_badge" class="badge" style="display:{{ $totalPending ? 'inline-block' : 'none' }}">{{ $totalPending }}</span>
    </button>

    <div id="topNotifDropdown" class="dropdown" role="menu" aria-hidden="true">
        <strong>Notifications</strong>
        <div style="margin-top:8px; color:var(--text);">
            <a class="item" href="/admin/requests">
                <div style="font-weight:700;"><span id="notif_pendingRequests">{{ $pendingRequests }}</span></div>
                <div class="muted">pending request<span id="notif_pendingRequests_s">{{ $pendingRequests !== 1 ? 's' : '' }}</span></div>
            </a>

            <a class="item" href="/admin/password-reset">
                <div style="font-weight:700;"><span id="notif_pendingPR">{{ $pendingPR }}</span></div>
                <div class="muted">password reset<span id="notif_pendingPR_s">{{ $pendingPR !== 1 ? 's' : '' }}</span></div>
            </a>

            <a class="item" href="/admin/stocks">
                <div style="font-weight:700;"><span id="notif_lowStock">{{ $lowStock }}</span></div>
                <div class="muted">low stock<span id="notif_lowStock_s">{{ $lowStock !== 1 ? ' items' : ' item' }}</span></div>
            </a>

            <a class="item" href="/admin/stocks">
                <div style="font-weight:700;"><span id="notif_outStock">{{ $outStock }}</span></div>
                <div class="muted">out of stock<span id="notif_outStock_s">{{ $outStock !== 1 ? ' items' : ' item' }}</span></div>
            </a>

            {{-- "View all" removed to simplify notification actions. --}}
        </div>
    </div>
</div>

<script>
(function(){
    const btn = document.getElementById('topNotifBtn');
    const dd = document.getElementById('topNotifDropdown');
    if(!btn || !dd) return;
    function close(){ dd.classList.remove('open'); btn.setAttribute('aria-expanded','false'); dd.setAttribute('aria-hidden','true'); }
    function open(){ dd.classList.add('open'); btn.setAttribute('aria-expanded','true'); dd.setAttribute('aria-hidden','false'); }
    btn.addEventListener('click', function(e){ e.stopPropagation(); dd.classList.contains('open') ? close() : open(); });
    document.addEventListener('click', function(){ close(); });
    document.addEventListener('keydown', function(e){ if(e.key === 'Escape') close(); });
})();
</script>

<script>
(function(){
    const badge = document.getElementById('notif_badge');
    const url = '/admin/notifications/counts';
    async function fetchCounts(){
        try{
            const res = await fetch(url, { credentials: 'same-origin' });
            if(!res.ok) return;
            const data = await res.json();
            // update badge
            if(badge){
                if(data.total && data.total > 0){ badge.style.display = 'inline-block'; badge.textContent = data.total; }
                else{ badge.style.display = 'none'; }
            }
            // update items
            function setText(id, val){ const el = document.getElementById(id); if(el) el.textContent = val; }
            function pluralSuffix(id, val){ const el = document.getElementById(id); if(!el) return; el.textContent = (val !== 1) ? 's' : ''; }

            setText('notif_pendingRequests', data.pendingRequests ?? 0);
            pluralSuffix('notif_pendingRequests_s', data.pendingRequests ?? 0);
            setText('notif_pendingPR', data.pendingPasswordResets ?? 0);
            pluralSuffix('notif_pendingPR_s', data.pendingPasswordResets ?? 0);
            setText('notif_lowStock', data.lowStock ?? 0);
            document.getElementById('notif_lowStock_s') && (document.getElementById('notif_lowStock_s').textContent = (data.lowStock !== 1 ? ' items' : ' item'));
            setText('notif_outStock', data.outStock ?? 0);
            document.getElementById('notif_outStock_s') && (document.getElementById('notif_outStock_s').textContent = (data.outStock !== 1 ? ' items' : ' item'));

        }catch(e){ console.error('Notif counts error', e); }
    }

    // initial fetch and interval
    fetchCounts();
    setInterval(fetchCounts, 30000); // every 30s
})();
</script>
@endif