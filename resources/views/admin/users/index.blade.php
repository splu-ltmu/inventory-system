@extends('layouts.app')

@php
  $brand = 'Inventory System';
  $pageTitle = 'Accounts';
  $pageSubtitle = 'Manage and create user accounts.';
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
        transition: all 0.3s ease;
    }
    .btn-edit:hover{
        background: rgba(37,99,235,.25);
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(37,99,235,.15);
    }
    .btn-edit:active{
        transform: translateY(0);
    }
    .btn-delete{
        background: rgba(220,38,38,.15);
        color: var(--danger);
        border:1px solid rgba(220,38,38,.3);
        transition: all 0.3s ease;
    }
    .btn-delete:hover{
        background: rgba(220,38,38,.25);
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(220,38,38,.15);
    }
    .btn-delete:active{
        transform: translateY(0);
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
                <th style="min-width:160px;">Office</th>
                <th style="min-width:120px;">Role</th>
                <th style="min-width:140px;">Member Since</th>
                <th style="min-width:160px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td class="muted">{{ $user->email }}</td>
                    <td class="muted">{{ $user->office ?? '-' }}</td>
                    <td>{{ $user->role ?? '-' }}</td>
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
                    <td colspan="6" style="color:var(--muted);">No client accounts found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
