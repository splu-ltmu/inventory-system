@extends('layouts.app')

@php
  $brand = 'Inventory System';
  $pageTitle = 'Categories';
  $pageSubtitle = 'Manage stock categories.';
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
    table{ width:100%; border-collapse:collapse; min-width: 720px; background:#fff; }
    th,td{ border:1px solid var(--line); padding:10px; text-align:left; }
    th{ background: rgba(37,99,235,.06); color: var(--text); font-weight:700; }
    td{ color: var(--text); }

    .actions{ display:flex; gap:8px; justify-content:flex-start; }
    .btn{
        padding:8px 10px; border-radius:10px;
        border:1px solid var(--blue);
        background: var(--blue-soft);
        color: var(--blue);
        cursor:pointer; font-weight:700;
        text-decoration:none;
        display:inline-block;
    }
    .btn:hover{ background: rgba(37,99,235,.18); }

    .btn-danger{
        border-color: var(--orange);
        background: var(--orange-soft);
        color: var(--orange);
    }
    .btn-danger:hover{ background: rgba(249,115,22,.22); }

    form{ display:inline; }
</style>

<div class="toolbar">
    <h2 style="margin:0;">Categories</h2>
    <a class="btn-link" href="{{ route('categories.create') }}">Add New Category</a>
</div>

<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th style="width:120px;">ID</th>
                <th>Name</th>
                <th style="width:220px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($categories as $cat)
                <tr>
                    <td>{{ $cat->id }}</td>
                    <td>{{ $cat->name }}</td>
                    <td>
                        <div class="actions">
                            <a class="btn" href="{{ route('categories.edit', $cat->id) }}">Edit</a>

                            <form action="{{ route('categories.destroy', $cat->id) }}" method="POST"
                                  onsubmit="return confirm('Delete this category?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger" type="submit">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" style="color:var(--muted);">No categories found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
