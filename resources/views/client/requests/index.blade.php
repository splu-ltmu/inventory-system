@extends('layouts.app')

@php
  $brand = 'Inventory System';
  $pageTitle = 'My Requests';
  $pageSubtitle = 'See approved items, rejected items, and verification code when ready.';
@endphp

@section('sidebar')
    <a href="{{ route('client.dashboard') }}" class="{{ request()->is('client') ? 'active' : '' }}">
        Dashboard <small>Home</small>
    </a>

    <a href="{{ route('client.stocks') }}" class="{{ request()->is('client/stocks*') ? 'active' : '' }}">
        Available Stocks <small>Request items</small>
    </a>

    <a href="{{ route('client.requests') }}" class="{{ request()->is('client/requests*') ? 'active' : '' }}">
        My Requests <small>Status + Code</small>
    </a>

    <a href="{{ route('client.account') }}" class="{{ request()->is('client/account*') ? 'active' : '' }}">
        Account Settings <small>Email & Password</small>
    </a>
@endsection

@section('content')
<style>
    table{ width:100%; border-collapse: collapse; }
    th, td{ border:1px solid #e2e8f0; padding:10px; text-align:left; }
    th{ background:#f8fafc; }

    .muted{ color:#64748b; font-size:12px; }
    .pill{
        display:inline-block;
        padding:4px 10px;
        border-radius:999px;
        font-size:12px;
        border:1px solid #e2e8f0;
        background:#f8fafc;
        color:#475569;
        font-weight:800;
    }
    .pill.pending{ border-color:#bfdbfe; background:#eff6ff; color:#1d4ed8; }
    .pill.approved{ border-color:#bbf7d0; background:#ecfdf5; color:#065f46; }
    .pill.rejected{ border-color:#fecaca; background:#fef2f2; color:#991b1b; }

    .card{
        border:1px solid #e2e8f0;
        border-radius:14px;
        overflow:hidden;
        background:#fff;
        box-shadow:0 1px 2px rgba(15,23,42,.06);
        margin-bottom:14px;
    }
    .card-head{
        display:flex;
        justify-content:space-between;
        gap:10px;
        padding:14px 16px;
        background:var(--blue-soft);
        border-bottom:1px solid #e2e8f0;
        cursor:pointer;
        align-items:flex-start;
    }
    .card-head:hover{ background:rgba(37,99,235,.12); }
    .title{ font-size:18px; font-weight:900; color:#0f172a; }
    
    .card-body{
        display:none;
        padding:14px 16px 16px;
        background:#fff;
    }
    .card-body.open{ display:block; }
    
    .card-toggle{ color:var(--muted); font-size:12px; font-weight:600; }
</style>

{{-- Flash message shown in layout; avoid duplicate here --}}

@forelse($requests as $req)
    @php
        $status = $req->status; // pending / approved / ready_to_receive / rejected / released
        $code = $req->verification_code;
        $rid = 'req-'.$req->id;
    @endphp

    <div class="card">
        <div class="card-head" onclick="toggleReq('{{ $rid }}')">
            <div style="flex:1;">
                <div class="title">
                    Request from <span style="color:#2563eb;">{{ $req->office }}</span>
                    <span class="muted">•</span>
                    <span class="muted">{{ $req->created_at?->format('M d, Y') }}</span>
                </div>

                <div style="margin-top:6px;">
                    <span class="muted">Status:</span>
                    @if($status === 'pending')
                        <span class="pill pending">PENDING</span>
                    @elseif($status === 'approved')
                        <span class="pill approved">APPROVED</span>
                    @elseif($status === 'ready_to_receive')
                        <span class="pill approved">READY TO RECEIVE</span>
                    @elseif($status === 'rejected')
                        <span class="pill rejected">REJECTED</span>
                    @else
                        <span class="pill">{{ strtoupper(str_replace('_',' ', $status)) }}</span>
                    @endif
                </div>
            </div>

            <div style="text-align:right; white-space:nowrap;">
                <div style="font-weight:900; font-size:12px; margin-bottom:4px;">
                    Ref. No: <span style="color:#0f172a;">#{{ $req->id }}</span>
                </div>
                @if($code)
                    <div style="font-weight:900; font-size:18px;">
                        Code: <span style="color:#0f172a;">{{ $code }}</span>
                    </div>
                    <div class="muted">Show to admin</div>
                @else
                    <div class="muted">Waiting code</div>
                @endif
                <div class="card-toggle" style="margin-top:4px;">Click to expand</div>
            </div>
        </div>

        <div id="{{ $rid }}" class="card-body">
            <div style="overflow:auto; border-radius:14px; border:1px solid #e2e8f0;">
                <table>
                    <tr>
                        <th style="min-width:260px;">Item</th>
                        <th style="min-width:120px;">Requested</th>
                        <th style="min-width:120px;">Approved</th>
                        <th style="min-width:140px;">Result</th>
                    </tr>

                    @forelse($req->items as $item)
                        @php
                            $requested = (int) $item->requested_qty;
                            $approved = $item->approved_qty; // can be null
                        @endphp

                        <tr>
                            <td>
                                <b>{{ $item->stock?->id_no ?? '' }}</b> — {{ $item->stock?->description ?? 'N/A' }}
                                <div class="muted">Unit: {{ $item->stock?->unit ?? '—' }}</div>
                            </td>

                            <td>{{ $requested }}</td>

                            {{-- Approved column --}}
                            <td>
                                @if($status === 'pending')
                                    —
                                @else
                                    {{ (int)($approved ?? 0) }}
                                @endif
                            </td>

                            {{-- Result column --}}
                            <td>
                                @if($status === 'pending')
                                    <span class="pill pending">PENDING</span>
                                @else
                                    @if((int)($approved ?? 0) > 0)
                                        <span class="pill approved">APPROVED</span>
                                    @else
                                        <span class="pill rejected">REJECTED</span>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="muted">No request items found.</td>
                        </tr>
                    @endforelse
                </table>
            </div>
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
</script>
@endsection
