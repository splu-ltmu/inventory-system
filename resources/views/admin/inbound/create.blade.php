@extends('layouts.app')

@php
  $brand = 'Inventory System';
  $pageTitle = 'Inbound';
  $pageSubtitle = 'Add new inbound item.';
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

    .form-container{ max-width:600px; background:#fff; border:1px solid var(--line); border-radius:14px; padding:20px; }
    .form-group{ margin-bottom:20px; }
    .form-group label{ display:block; margin-bottom:8px; color: var(--text); font-weight:700; }
    .form-group select,
    .form-group input{ width:100%; padding:10px; border:1px solid var(--line); border-radius:8px; font-size:14px; }
    .form-group select:focus,
    .form-group input:focus{ outline:none; border-color: var(--blue); box-shadow: 0 0 0 3px rgba(37,99,235,.1); }
    
    .form-actions{ display:flex; gap:12px; margin-top:24px; }
    .btn-submit{
        display:inline-block;
        padding:10px 20px;
        border-radius:10px;
        border:none;
        background: var(--blue);
        color: white;
        text-decoration:none;
        font-weight:700;
        cursor:pointer;
    }
    .btn-submit:hover{ background: rgba(37,99,235,.9); }
    .btn-cancel{
        display:inline-block;
        padding:10px 20px;
        border-radius:10px;
        border:1px solid var(--line);
        background: transparent;
        color: var(--text);
        text-decoration:none;
        font-weight:700;
        cursor:pointer;
    }
    .btn-cancel:hover{ background: var(--line); }

    .error-message{ color: var(--red); margin-bottom:16px; padding:12px; background: rgba(239,68,68,.1); border:1px solid rgba(239,68,68,.3); border-radius:8px; }
    .error-message ul{ margin:0; padding-left:20px; }
    .error-message li{ margin:4px 0; }
</style>

<div class="toolbar">
    <h2 style="margin:0;">Add Inbound Item</h2>
    <a class="btn-link" href="{{ route('inbound.index') }}">Back to Inbound</a>
</div>

<div class="form-container">
    @if($errors->any())
        <div class="error-message">
            <ul>
                @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('inbound.store') }}" method="POST">
        @csrf
        
        <div class="form-group">
            <label for="stock_id">Select Stock:</label>
            <select name="stock_id" id="stock_id" required>
                <option value="">-- Choose a stock --</option>
                @foreach($stocks as $stock)
                    <option value="{{ $stock->id }}">{{ $stock->description }} ({{ $stock->id_no }})</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="total">Total Quantity:</label>
            <input type="number" name="total" id="total" value="1" min="1" required>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-submit">Add Inbound</button>
            <a href="{{ route('inbound.index') }}" class="btn-cancel">Cancel</a>
        </div>
    </form>
</div>
@endsection
