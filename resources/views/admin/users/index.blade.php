@extends('layouts.app')

@php
  $brand = 'Inventory System';
  $pageTitle = 'Client Accounts';
  $pageSubtitle = 'Manage and create client accounts.';
@endphp

@section('sidebar')
    <a href="{{ route('admin.dashboard') }}" class="{{ request()->is('admin') ? 'active' : '' }}">
        Dashboard <small>Home</small>
    </a>

    <a href="/admin/categories" class="{{ request()->is('admin/categories*') ? 'active' : '' }}">
        Categories <small>Manage</small>
    </a>

    <a href="/admin/stocks" class="{{ request()->is('admin/stocks*') ? 'active' : '' }}">
        Stocks <small>Manage</small>
    </a>

    <a href="/admin/inbound" class="{{ request()->is('admin/inbound*') ? 'active' : '' }}">
        Inbound <small>Records</small>
    </a>

    <a href="/admin/outbound" class="{{ request()->is('admin/outbound*') ? 'active' : '' }}">
        Outbound <small>Records</small>
    </a>

    <a href="/admin/requests" class="{{ request()->is('admin/requests*') ? 'active' : '' }}">
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
    .btn-edit{
        background: rgba(37,99,235,.15);
        color: var(--blue);
        border:1px solid rgba(37,99,235,.3);
    }
    .btn-edit:hover{
        background: rgba(37,99,235,.25);
    }
    .btn-delete{
        background: rgba(220,38,38,.15);
        color: var(--danger);
        border:1px solid rgba(220,38,38,.3);
    }
    .btn-delete:hover{
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
    <h2 style="margin:0;">Client Accounts</h2>
    <a class="btn-link" href="{{ route('admin.users.create') }}">Create New Account</a>
</div>

<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th style="min-width:200px;">Name</th>
                <th style="min-width:220px;">Email</th>
                <th style="min-width:140px;">Member Since</th>
                <th style="min-width:160px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td class="muted">{{ $user->email }}</td>
                    <td>{{ $user->created_at->format('M d, Y') }}</td>
                    <td>
                        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn-action btn-edit">Edit</a>
                        <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-action btn-delete" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="color:var(--muted);">No client accounts found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
