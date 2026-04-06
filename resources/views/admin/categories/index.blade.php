@extends('layouts.app')

@php
  $brand = 'Inventory System';
  $pageTitle = 'Categories';
  $pageSubtitle = 'Manage stock categories.';
@endphp

@section('sidebar')
    @include('partials.admin-sidebar')
@endsection

@section('content')
<style>
    .toolbar{ display:flex; justify-content:space-between; align-items:center; gap:12px; margin-bottom:12px; }
    .btn-link{
        display:inline-block;
        padding:7px 10px;
        border-radius:8px;
        border:1px solid var(--blue);
        background: var(--blue-soft);
        color: var(--blue);
        text-decoration:none;
        font-weight:700;
        font-size:13px;
        transition: all 0.3s ease;
    }
    .btn-link:hover{ 
        background: rgba(37,99,235,.18);
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(37,99,235,.15);
    }
    .btn-link:active{
        transform: translateY(0);
    }

    .actions .btn{ padding:6px 8px; font-size:12px; }
    .actions .btn-danger{ padding:6px 8px; font-size:12px; background: #f8d7da; color: #991b1b; border-color: #991b1b; }
    .actions .btn-danger:hover{ background: #fca5a5; color: #7f1d1d; }

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
        transition: all 0.3s ease;
    }
    .btn:hover{ 
        background: rgba(37,99,235,.18);
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(37,99,235,.15);
    }
    .btn:active{
        transform: translateY(0);
    }

    .btn-danger{
        border-color: var(--orange);
        background: var(--orange-soft);
        color: var(--orange);
        transition: all 0.3s ease;
    }
    .btn-danger:hover{ 
        background: rgba(249,115,22,.22);
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(249,115,22,.15);
    }
    .btn-danger:active{
        transform: translateY(0);
    }

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
