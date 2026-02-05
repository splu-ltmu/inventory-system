@extends('layouts.app')

@php
  $brand = 'Inventory System';
  $pageTitle = 'Inbound';
  $pageSubtitle = 'Incoming items added to inventory.';
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
    table{ width:100%; border-collapse:collapse; min-width: 860px; background:#fff; }
    th,td{ border:1px solid var(--line); padding:10px; text-align:left; }
    th{ background: rgba(37,99,235,.06); color: var(--text); font-weight:700; }
    td{ color: var(--text); }
    .muted{ color: var(--muted); }
</style>

<div class="toolbar">
    <h2 style="margin:0;">Inbound Items</h2>
    <a class="btn-link" href="{{ route('inbound.create') }}">Add Inbound</a>
</div>

<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th style="min-width:160px;">Stock ID</th>
                <th>Description</th>
                <th style="min-width:160px;">Total Added</th>
            </tr>
        </thead>
        <tbody>
            @forelse($inbounds as $row)
                <tr>
                    <td>{{ $row->stock?->id_no ?? $row->stock_id }}</td>
                    <td class="muted">{{ $row->stock?->description ?? $row->description ?? '—' }}</td>
                    <td>{{ $row->total_added ?? $row->total ?? '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" style="color:var(--muted);">No inbound records yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
