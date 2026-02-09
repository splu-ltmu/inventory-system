@extends('layouts.app')

@php
  $brand = 'Inventory System';
  $pageTitle = 'Requests';
  $pageSubtitle = 'Flow: Pending → Approved/Rejected → Ready to Receive (generate code) → Release (code) → Outbound';

  $activeTab = request('tab', 'pending');

  $pending   = $requests->where('status', 'pending');
  $approved  = $requests->where('status', 'approved');
  $ready     = $requests->where('status', 'ready_to_receive');
  $rejected  = $requests->where('status', 'rejected');

  $shown = match ($activeTab) {
      'approved' => $approved,
      'ready_to_receive' => $ready,
      'rejected' => $rejected,
      default => $pending,
  };
@endphp

@section('sidebar')
    <a href="{{ route('admin.dashboard') }}" class="{{ request()->is('admin') ? 'active' : '' }}">
        Dashboard <small>Home</small>
    </a>

    <a href="{{ route('categories.index') }}" class="{{ request()->is('admin/categories*') ? 'active' : '' }}">
        Categories <small>Manage</small>
    </a>

    <a href="{{ route('stocks.index') }}" class="{{ request()->is('admin/stocks*') ? 'active' : '' }}">
        Stocks <small>Manage</small>
    </a>

    <a href="{{ route('inbound.index') }}" class="{{ request()->is('admin/inbound*') ? 'active' : '' }}">
        Inbound <small>Records</small>
    </a>

    <a href="{{ route('outbound.index') }}" class="{{ request()->is('admin/outbound*') ? 'active' : '' }}">
        Outbound <small>Released Items</small>
    </a>

    {{-- ✅ This exists in route:list: requests.index --}}
    <a href="{{ route('requests.index') }}" class="{{ request()->is('admin/requests*') ? 'active' : '' }}">
        Requests <small>Workflow</small>
    </a>

    <a href="/admin/password-reset" class="{{ request()->is('admin/password-reset*') ? 'active' : '' }}">
        Password Reset <small>Requests</small>
    </a>

    <a href="{{ route('admin.users.index') }}" class="{{ request()->is('admin/users*') ? 'active' : '' }}">
        Client Accounts <small>Create/Manage</small>
    </a>
@endsection

@section('content')
<style>
    .tabs{ display:flex; gap:10px; flex-wrap:wrap; margin-bottom:14px; }
    .tab{
        display:flex; align-items:center; gap:8px;
        padding:10px 12px; border-radius:12px; text-decoration:none;
        background:#ffffff; border:1px solid #e2e8f0; color:#0f172a;
        font-weight:600; box-shadow:0 1px 2px rgba(15,23,42,.06);
    }
    .tab:hover{ border-color:#93c5fd; background:#eff6ff; color:#2563eb; }
    .tab.active{ border-color:#2563eb; background:#eff6ff; color:#2563eb; }

    .badge{
        padding:2px 8px; border-radius:999px; font-size:12px;
        border:1px solid #e2e8f0; background:#f8fafc; color:#475569;
    }

    .req-card{
        border:1px solid #e2e8f0;
        border-radius:14px;
        background:#fff;
        overflow:hidden;
        margin-bottom:14px;
        box-shadow:0 1px 2px rgba(15,23,42,.06);
    }
    .req-header{
        padding:14px 16px;
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:12px;
        cursor:pointer;
        background:#fff7ed;
        border-bottom:1px solid #e2e8f0;
    }
    .req-title{
        font-weight:800;
        font-size:18px;
        color:#0f172a;
    }
    .req-sub{
        margin-top:4px;
        color:#475569;
        font-size:13px;
    }
    .req-right{
        text-align:right;
        color:#334155;
        font-weight:700;
        white-space:nowrap;
    }
    .req-body{
        display:none;
        padding:14px 16px 16px;
        background:#fff;
    }
    .req-body.open{ display:block; }

    table{ width:100%; border-collapse:collapse; }
    th, td{ border:1px solid #e2e8f0; padding:10px; text-align:center; }
    th{ background:#f8fafc; color:#0f172a; }

    .muted{ color:#64748b; }
    .status-pill{
        display:inline-block;
        padding:4px 10px;
        border-radius:999px;
        border:1px solid #e2e8f0;
        background:#f8fafc;
        font-size:12px;
        font-weight:700;
        color:#334155;
    }
    .btn{
        padding:9px 12px;
        border-radius:10px;
        border:1px solid #2563eb;
        background:#2563eb;
        color:#fff;
        cursor:pointer;
        font-weight:700;
    }
    .btn:hover{ opacity:.92; }
    .btn-ghost{
        padding:9px 12px;
        border-radius:10px;
        border:1px solid #e2e8f0;
        background:#fff;
        color:#0f172a;
        cursor:pointer;
        font-weight:700;
        transition: background-color .2s, border-color .2s, color .2s;
    }
    .btn-ghost:hover{ background:#eff6ff; border-color:#2563eb; color:#2563eb; }

    input[type="number"], input[type="text"], select{
        padding:8px 10px;
        border-radius:10px;
        border:1px solid #e2e8f0;
        background:#fff;
        color:#0f172a;
        outline:none;
        width:100%;
    }
    input:focus, select:focus{ border-color:#93c5fd; }
    /* Spinner and no-results */
    #search-spinner{ border:3px solid rgba(0,0,0,0.08); border-top-color:rgba(37,99,235,0.9); border-radius:50%; width:20px; height:20px; display:inline-block; animation:spin 1s linear infinite; }
    @keyframes spin{ to{ transform: rotate(360deg); } }
    .no-results{ padding:18px; text-align:center; color:#64748b; background:transparent; border-radius:8px; margin-top:8px; }
    /* Confirmation modal */
    #confirmModal{ display:none; position:fixed; inset:0; background:rgba(0,0,0,.5); z-index:5000; align-items:center; justify-content:center; }
    #confirmModal.show{ display:flex; }
    .modal-box{ background:#fff; border-radius:14px; box-shadow:0 10px 40px rgba(0,0,0,.25); max-width:420px; width:90%; padding:24px; }
    .modal-box h3{ margin:0 0 8px 0; font-size:18px; color:#0f172a; font-weight:800; }
    .modal-box p{ margin:0 0 20px 0; color:#475569; font-size:14px; line-height:1.5; }
    .modal-box .modal-buttons{ display:flex; gap:10px; justify-content:flex-end; }
    .modal-box .modal-btn{ padding:10px 16px; border-radius:10px; border:none; font-weight:700; cursor:pointer; font-size:14px; }
    .modal-btn-confirm{ background:#2563eb; color:#fff; }
    .modal-btn-confirm:hover{ opacity:.92; }
    .modal-btn-cancel{ background:#e2e8f0; color:#0f172a; }
    .modal-btn-cancel:hover{ background:#cbd5e1; }
</style>

<div class="tabs">
    <a class="tab {{ $activeTab==='pending'?'active':'' }}" href="/admin/requests?tab=pending">
        Pending <span class="badge">{{ $pending->count() }}</span>
    </a>
    <a class="tab {{ $activeTab==='approved'?'active':'' }}" href="/admin/requests?tab=approved">
        Approved <span class="badge">{{ $approved->count() }}</span>
    </a>
    <a class="tab {{ $activeTab==='ready_to_receive'?'active':'' }}" href="/admin/requests?tab=ready_to_receive">
        Ready to Receive <span class="badge">{{ $ready->count() }}</span>
    </a>
    <a class="tab {{ $activeTab==='rejected'?'active':'' }}" href="/admin/requests?tab=rejected">
        Rejected <span class="badge">{{ $rejected->count() }}</span>
    </a>
</div>

<div id="no-results" class="no-results" style="display:none;">No results found.</div>

{{-- Search bar: search by Ref No. (#123) or client name --}}
<div style="display:flex; gap:8px; align-items:center; margin-bottom:12px;">
    <form method="GET" action="{{ route('requests.index') }}" style="display:flex; gap:8px; width:100%;">
        <input type="hidden" name="tab" value="{{ $activeTab }}">
        <input
            type="text"
            name="q"
            placeholder="Search by Ref No. or client name"
            value="{{ request('q') }}"
            style="padding:10px 12px; border-radius:10px; border:1px solid #e2e8f0; flex:1;"
        >
        <button type="submit" class="btn">Search</button>
        <a href="{{ route('requests.index', ['tab' => $activeTab]) }}" class="btn-ghost" style="display:inline-flex; align-items:center;">Clear</a>
        <span id="search-spinner" style="display:none; margin-left:6px;"></span>
    </form>
</div>

<div id="requests-list">
@forelse($shown as $req)
    @php $rid = 'req-'.$req->id; @endphp

    <div class="req-card">
        <div class="req-header" onclick="toggleReq('{{ $rid }}')">
            <div>
                <div class="req-title">
                    Request from <span style="color:#2563eb;">{{ $req->office }}</span>
                    <span class="muted">•</span>
                    <span class="muted">{{ $req->client?->name ?? 'Client' }}</span>
                    <span class="muted">•</span>
                    <span class="muted">{{ $req->created_at?->format('M d, Y') }}</span>
                </div>

                <div class="req-sub">
                    <span class="muted">Status:</span>
                    <span class="status-pill">{{ strtoupper(str_replace('_',' ', $req->status)) }}</span>
                    <span class="muted" style="margin-left:10px;">Request ID:</span>
                    <b>#{{ $req->id }}</b>
                </div>
            </div>

            <div class="req-right">
                Ref. No:
                <span style="color:#0f172a;">
                    #{{ $req->id }}
                </span>
                <div class="muted" style="font-size:12px; font-weight:600; margin-top:4px;">
                    Click to view details
                </div>
            </div>
        </div>

        <div id="{{ $rid }}" class="req-body">
            <div class="muted" style="margin-bottom:10px;">
                Approve partially by setting Approved Qty per item (0 = rejected item).
            </div>

            {{-- ✅ ONE FORM FOR ALL BUTTONS --}}
            <form method="POST" action="{{ route('admin.requests.decision', $req->id) }}">
                @csrf
                @method('PUT')

                <div style="overflow:auto; border-radius:12px; border:1px solid #e2e8f0;">
                    <table>
                        <tr>
                            <th style="min-width:200px;">Item</th>
                            <th style="min-width:140px;">Requested</th>
                            <th style="min-width:140px;">Available</th>
                            <th style="min-width:160px;">Approved Qty</th>
                        </tr>

                        @forelse($req->items as $item)
                            <tr>
                                <td style="text-align:left;">
                                    <b>{{ $item->stock?->id_no ?? '' }}</b> —
                                    {{ $item->stock?->description ?? 'N/A' }}
                                    <div class="muted" style="font-size:12px;">
                                        Unit: {{ $item->stock?->unit ?? '—' }}
                                    </div>
                                </td>

                                <td>{{ $item->requested_qty }}</td>
                                <td>{{ $item->stock?->stock ?? 0 }}</td>

                                <td style="min-width:160px;">
                                    <div style="display:flex; gap:6px; align-items:center;">
                                        <input
                                            type="number"
                                            name="approved_qty[{{ $item->id }}]"
                                            min="0"
                                            max="{{ $item->stock?->stock ?? 0 }}"
                                            value="{{ $item->approved_qty ?? 0 }}"
                                            {{ $activeTab !== 'pending' ? 'readonly' : '' }}
                                            style="flex:1; text-align:center;"
                                        >
                                        @if($activeTab === 'pending')
                                            <button
                                                type="button"
                                                class="btn-max"
                                                onclick="setMax(this, {{ $item->requested_qty }})"
                                                style="padding:8px 10px; border-radius:10px; border:1px solid #2563eb; background:#2563eb; color:#fff; cursor:pointer; font-weight:700; white-space:nowrap; flex-shrink:0;"
                                            >
                                                Max
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="muted">No request items found for this request.</td>
                            </tr>
                        @endforelse
                    </table>
                </div>

                @if($req->status !== 'ready_to_receive')
                    <div style="display:flex; gap:10px; flex-wrap:wrap; margin-top:12px;">
                        {{-- Save Decision - only show if NOT approved --}}
                        @if($req->status !== 'approved')
                            <button class="btn-ghost" type="button" onclick="confirmAction(event, null, 'Save Decision', 'Save the approval quantities for this request?', '{{ $req->id }}')">
                                Save Decision
                            </button>
                        @endif

                        {{-- Only show other buttons if NOT pending --}}
                        @if($req->status !== 'pending')
                            {{-- Reject Whole --}}
                            <button class="btn-ghost" type="button" onclick="confirmAction(event, 'rejected', 'Reject Entire Request', 'This request will be rejected. This action cannot be undone.', '{{ $req->id }}')">
                                Reject Whole Request
                            </button>

                            {{-- Ready to Receive --}}
                            <button class="btn-ghost" type="button" onclick="confirmAction(event, 'ready_to_receive', 'Generate Code', 'Proceed to generate a verification code for the client to claim these items.', '{{ $req->id }}')">
                                Ready to Receive
                            </button>
                        @endif
                    </div>
                @endif
            </form>

            {{-- ✅ ONLY SHOW RELEASE FORM IF READY TO RECEIVE --}}
            @if($req->status === 'ready_to_receive')
                <hr style="border:none; border-top:1px solid #e2e8f0; margin:16px 0;">

                <form method="POST" action="{{ route('admin.requests.release', $req->id) }}">
                    @csrf
                    @method('PUT')

                    <div style="background:#eff6ff; border:1px solid #2563eb; border-radius:10px; padding:12px; display:flex; gap:10px; align-items:flex-end;">
                        <div style="flex:1; min-width:200px;">
                            <label style="font-size:12px; font-weight:700; color:#0f172a; display:block; margin-bottom:6px;">🔐 Client Code</label>
                            <input type="text" name="verification_code" placeholder="Enter code" required style="padding:10px;">
                        </div>

                        <button class="btn" type="submit" style="padding:10px 16px;">Release</button>
                    </div>
                </form>
            @endif
        </div>
    </div>

@empty
    <div class="muted">No requests found.</div>
@endforelse
</div>

<!-- Confirmation Modal -->
<div id="confirmModal">
    <div class="modal-box">
        <h3 id="modal-title">Confirm</h3>
        <p id="modal-message">Are you sure?</p>
        <div class="modal-buttons">
            <button class="modal-btn modal-btn-cancel" onclick="closeConfirmModal()">Cancel</button>
            <button class="modal-btn modal-btn-confirm" onclick="submitConfirmAction()">Confirm</button>
        </div>
    </div>
</div>

<script>
let pendingAction = null;

function toggleReq(id){
    const el = document.getElementById(id);
    if(!el) return;
    el.classList.toggle('open');
}

function setMax(btn, maxValue){
    // Find the input field (previous sibling)
    const input = btn.previousElementSibling;
    if(input && input.tagName === 'INPUT'){
        input.value = maxValue;
    }
}

function confirmAction(e, status, title, message, requestId){
    e.preventDefault();
    const modal = document.getElementById('confirmModal');
    document.getElementById('modal-title').textContent = title;
    document.getElementById('modal-message').textContent = message;
    pendingAction = { status, form: e.target.closest('form') };
    if(modal) modal.classList.add('show');
}

function closeConfirmModal(){
    const modal = document.getElementById('confirmModal');
    if(modal) modal.classList.remove('show');
    pendingAction = null;
}

function submitConfirmAction(){
    if(pendingAction && pendingAction.form){
        // Only add status field if status is not null (Save Decision has null status)
        if(pendingAction.status !== null){
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'status';
            input.value = pendingAction.status;
            pendingAction.form.appendChild(input);
        }
        pendingAction.form.submit();
    }
    closeConfirmModal();
}

// Close modal on Escape key or background click
document.addEventListener('keydown', function(e){
    if(e.key === 'Escape') closeConfirmModal();
});
document.getElementById('confirmModal')?.addEventListener('click', function(e){
    if(e.target === this) closeConfirmModal();
});

// Live search (AJAX)
(function(){
    const searchForm = document.querySelector('form[action="{{ route('requests.index') }}"]');
    if(!searchForm) return;
    const searchInput = searchForm.querySelector('input[name="q"]');
    const requestsList = document.getElementById('requests-list');
    const url = '{{ route('requests.index') }}';
    let timer = null;

    function fetchResults(q){
        const params = new URLSearchParams();
        params.append('q', q || '');
        params.append('tab', '{{ $activeTab }}');

        const spinner = document.getElementById('search-spinner');
        const noResults = document.getElementById('no-results');
        if(spinner) spinner.style.display = 'inline-block';
        if(noResults) noResults.style.display = 'none';

        fetch(url + '?' + params.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.json())
            .then(data => {
                if(spinner) spinner.style.display = 'none';
                if(data.html !== undefined){
                    requestsList.innerHTML = data.html;
                }
                if(typeof data.count !== 'undefined'){
                    if(data.count === 0){
                        if(noResults) noResults.style.display = 'block';
                    } else {
                        if(noResults) noResults.style.display = 'none';
                    }
                }
            }).catch(()=>{ if(spinner) spinner.style.display = 'none'; });
    }

    searchInput.addEventListener('input', function(e){
        clearTimeout(timer);
        timer = setTimeout(()=> fetchResults(this.value.trim()), 350);
    });

    searchForm.addEventListener('submit', function(e){
        e.preventDefault();
        clearTimeout(timer);
        fetchResults(searchInput.value.trim());
    });
})();
</script>
@endsection
