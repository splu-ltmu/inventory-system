@extends('layouts.app')

@php
  $brand = 'Inventory System';
  $pageTitle = 'Password Reset';
  $pageSubtitle = 'Manage client password reset requests.';
@endphp

@section('sidebar')
    @include('partials.admin-sidebar')
@endsection

@section('content')
<style>
    .toolbar{ display:flex; justify-content:space-between; align-items:center; gap:12px; margin-bottom:12px; }
    .btn-link{
        display:inline-block;
        padding:10px 12px;
        border-radius:10px;
        border:1px solid var(--blue);
        background: var(--blue-soft);
        color: var(--blue);
        text-decoration:none;
        font-weight:700;
    }
    .btn-link:hover{ background: rgba(37,99,235,.18); }

    .table-wrap{ overflow:auto; border:1px solid var(--line); border-radius:14px; }
    table{ width:100%; border-collapse:collapse; min-width: 980px; background:#fff; }
    th,td{ border:1px solid var(--line); padding:10px; text-align:left; }
    th{ background: rgba(37,99,235,.06); color: var(--text); font-weight:700; }
    td{ color: var(--text); }
    .muted{ color: var(--muted); }

    .status-badge{
        display:inline-block;
        padding:4px 10px;
        border-radius:999px;
        font-size:12px;
        font-weight:700;
    }
    .status-pending{
        background: rgba(249,115,22,.10);
        color: var(--orange);
        border:1px solid rgba(249,115,22,.3);
    }
    .status-approved{
        background: rgba(22,163,74,.10);
        color: var(--success);
        border:1px solid rgba(22,163,74,.3);
    }
    .status-rejected{
        background: rgba(220,38,38,.10);
        color: var(--danger);
        border:1px solid rgba(220,38,38,.3);
    }

    .btn-action{
        display:inline-block;
        padding:6px 12px;
        border-radius:6px;
        border:none;
        font-size:12px;
        font-weight:700;
        cursor:pointer;
        text-decoration:none;
        margin-right:6px;
    }
    .btn-approve{
        background: rgba(22,163,74,.15);
        color: var(--success);
        border:1px solid rgba(22,163,74,.3);
    }
    .btn-approve:hover{
        background: rgba(22,163,74,.25);
    }
    .btn-reject{
        background: rgba(220,38,38,.15);
        color: var(--danger);
        border:1px solid rgba(220,38,38,.3);
    }
    .btn-reject:hover{
        background: rgba(220,38,38,.25);
    }

    .alert{
        padding:12px;
        border-radius:8px;
        margin-bottom:16px;
        border:1px solid;
    }
    .alert-success{
        background: rgba(22,163,74,.1);
        border-color: rgba(22,163,74,.3);
        color: var(--success);
    }
</style>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="toolbar">
    <h2 style="margin:0;">Password Reset Requests</h2>
</div>

<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th style="min-width:160px;">Client Name</th>
                <th style="min-width:200px;">Email</th>
                <th style="min-width:140px;">Requested At</th>
                <th style="min-width:100px;">Status</th>
                <th style="min-width:200px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($requests as $req)
                <tr>
                    <td>{{ $req->user->name ?? '—' }}</td>
                    <td class="muted">{{ $req->user->email ?? '—' }}</td>
                    <td>{{ $req->requested_at->format('M d, Y H:i') }}</td>
                    <td>
                        <span class="status-badge status-{{ $req->status }}">
                            {{ ucfirst($req->status) }}
                        </span>
                    </td>
                    <td>
                        @if($req->status === 'pending')
                            <form method="POST" action="{{ route('password-reset.approve', $req->id) }}" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn-action btn-approve">Approve</button>
                            </form>
                            <button type="button" onclick="showRejectModal({{ $req->id }})" class="btn-action btn-reject">Reject</button>
                        @elseif($req->status === 'sent')
                            <span style="color:var(--muted); font-size:12px;">Link sent</span>
                        @elseif($req->status === 'completed')
                            <span style="color:var(--success); font-size:12px;">✓ Completed</span>
                        @elseif($req->status === 'rejected')
                            <span style="color:var(--danger); font-size:12px;">✕ Rejected</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="color:var(--muted);">No password reset requests.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Reject Modal -->
<div id="rejectModal" style="display:none !important; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,.5); z-index:999; justify-content:center; align-items:center;">
    <div style="background:white; border-radius:12px; padding:24px; max-width:400px; width:90%;">
        <h3 style="margin-top:0;">Reject Request</h3>
        <form id="rejectForm" method="POST">
            @csrf
            <div style="margin-bottom:16px;">
                <label style="display:block; margin-bottom:8px; font-weight:700;">Notes (optional):</label>
                <textarea name="notes" style="width:100%; padding:10px; border:1px solid var(--line); border-radius:8px; font-family:Arial;" rows="4"></textarea>
            </div>
            <div style="display:flex; gap:12px;">
                <button type="submit" class="btn-action btn-reject" style="flex:1;">Reject</button>
                <button type="button" onclick="closeRejectModal()" style="flex:1; padding:6px 12px; border-radius:6px; border:1px solid var(--line); background:white; cursor:pointer;">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
function showRejectModal(requestId) {
    const form = document.getElementById('rejectForm');
    form.action = '/admin/password-reset/' + requestId + '/reject';
    document.getElementById('rejectModal').style.display = 'flex';
}

function closeRejectModal() {
    document.getElementById('rejectModal').style.display = 'none';
}
</script>
@endsection
