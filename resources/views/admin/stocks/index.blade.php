@extends('layouts.app')

@php
  $brand = 'Inventory System';
  $pageTitle = 'Stocks';
  $pageSubtitle = 'Manage all available stocks.';
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

    .pill{
        display:inline-block; padding:4px 10px; border-radius:999px;
        border:1px solid var(--line);
        background: rgba(37,99,235,.06);
        color: var(--blue);
        font-size:12px; font-weight:700;
    }
    .pill.orange{
        background: rgba(249,115,22,.10);
        color: var(--orange);
        border-color: rgba(249,115,22,.35);
    }
    .pill.green{
        background: rgba(16,163,82,.08);
        color: var(--success);
        border-color: rgba(16,163,82,.28);
    }
    .pill.red{
        background: rgba(239,68,68,.08);
        color: var(--danger);
        border-color: rgba(239,68,68,.28);
    }
</style>

<div class="toolbar">
    <div style="display:flex; gap:12px; align-items:center;">
        <h2 style="margin:0;">Stocks</h2>
        @php
            $categories = $stocks->map(function($s){ return $s->category?->name ?? $s->category_name; })->filter()->unique()->values();
        @endphp

        <input id="stocksSearch" type="search" placeholder="Search ID, description, category..." style="padding:8px 10px;border:1px solid var(--line);border-radius:8px;min-width:260px;">

        <select id="filterCategory" style="padding:8px 10px;border:1px solid var(--line);border-radius:8px;">
            <option value="">All categories</option>
            @foreach($categories as $c)
                <option value="{{ strtolower($c) }}">{{ $c }}</option>
            @endforeach
        </select>

        <select id="filterAvailability" style="padding:8px 10px;border:1px solid var(--line);border-radius:8px;">
            <option value="">All</option>
            <option value="in">In stock (green)</option>
            <option value="low">Low stock (orange / red)</option>
            <option value="out">Out of stock (red)</option>
        </select>
    </div>

    <a class="btn-link" href="{{ route('stocks.create') }}">Add New Stock</a>
</div>

<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th style="min-width:110px;">ID No</th>
                <th style="min-width:220px;">Description</th>
                <th style="min-width:200px;">Category</th>
                <th style="min-width:80px;">Unit</th>
                <th style="min-width:90px;">Stock</th>
            </tr>
        </thead>
        <tbody>
            @forelse($stocks as $s)
                @php
                    $cat = $s->category?->name ?? $s->category_name ?? '';
                    $desc = $s->description ?? $s->name ?? '';
                    $stockVal = $s->stock ?? 0;
                @endphp
                <tr data-id="{{ strtolower($s->id_no ?? $s->id) }}" data-desc="{{ strtolower($desc) }}" data-cat="{{ strtolower($cat) }}" data-stock="{{ $stockVal }}">
                    <td>{{ $s->id_no ?? $s->id }}</td>
                    <td>{{ $desc ?: '—' }}</td>
                    <td class="muted">{{ $cat ?: '—' }}</td>
                    <td>{{ $s->unit ?? '—' }}</td>
                    <td>
                        @if($stockVal >= 50)
                            <span class="pill green">{{ $stockVal }} available</span>
                        @elseif($stockVal > 0 && $stockVal <= 49)
                            <span class="pill orange">{{ $stockVal }} available</span>
                        @else
                            <span class="pill red">Out of stock</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="color:var(--muted);">No stocks found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
<script>
const stocksSearch = document.getElementById('stocksSearch');
const filterCategory = document.getElementById('filterCategory');
const filterAvailability = document.getElementById('filterAvailability');

function filterStocks(){
    const q = stocksSearch.value.trim().toLowerCase();
    const cat = filterCategory.value;
    const avail = filterAvailability.value; // "in" or "out" or ""

    document.querySelectorAll('.table-wrap tbody tr[data-id]').forEach(row => {
        const id = row.dataset.id || '';
        const desc = row.dataset.desc || '';
        const category = row.dataset.cat || '';
        const stock = parseInt(row.dataset.stock || '0', 10);

        let visible = true;

        if(q){
            visible = id.includes(q) || desc.includes(q) || category.includes(q);
        }

        if(visible && cat){
            visible = category === cat;
        }

        if(visible && avail){
            // Availability ranges match the colored badges:
            // green: >=50, low: 1-49, out: 0
            if(avail === 'in') visible = stock >= 50; // green (ample)
            if(avail === 'low') visible = stock > 0 && stock <= 49; // low stock (orange)
            if(avail === 'out') visible = stock <= 0; // out of stock
        }

        row.style.display = visible ? '' : 'none';
    });
}

stocksSearch && stocksSearch.addEventListener('input', filterStocks);
filterCategory && filterCategory.addEventListener('change', filterStocks);
filterAvailability && filterAvailability.addEventListener('change', filterStocks);
</script>
@endsection
