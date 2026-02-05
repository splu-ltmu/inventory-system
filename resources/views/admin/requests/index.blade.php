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
    }
    .btn-ghost:hover{ background:#f8fafc; }

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
                        {{-- Save Decision --}}
                        <button class="btn" type="submit">Save Decision</button>

                        {{-- Reject Whole --}}
                        <button class="btn-ghost" type="submit" name="status" value="rejected">
                            Reject Whole Request
                        </button>

                        {{-- Mark Approved --}}
                        <button class="btn-ghost" type="submit" name="status" value="approved">
                            Mark as Approved
                        </button>

                        {{-- Ready to Receive --}}
                        <button class="btn-ghost" type="submit" name="status" value="ready_to_receive">
                            Ready to Receive (Generate Code)
                        </button>
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

<script>
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
</script>
@endsection
