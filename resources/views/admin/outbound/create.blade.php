@extends('layouts.app')

@php
  $brand = 'Inventory System';
  $pageTitle = 'Outbound';
  $pageSubtitle = 'Create a new outbound item release.';
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

    .form-container{ max-width:600px; background:#fff; border:1px solid var(--line); border-radius:14px; padding:20px; }
    .form-group{ margin-bottom:20px; }
    .form-group label{ display:block; margin-bottom:8px; color: var(--text); font-weight:700; }
    .form-group select,
    .form-group input[type="text"],
    .form-group input[type="number"]{ width:100%; padding:10px; border:1px solid var(--line); border-radius:8px; font-size:14px; box-sizing:border-box; }
    .form-group select:focus,
    .form-group input[type="text"]:focus,
    .form-group input[type="number"]:focus{ outline:none; border-color: var(--blue); box-shadow: 0 0 0 3px rgba(37,99,235,.1); }

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
        transition: all 0.3s ease;
    }
    .btn-submit:hover{ 
        background: rgba(37,99,235,.9);
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(37,99,235,.2);
    }
    .btn-submit:active{
        transform: translateY(0);
    }
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
        transition: all 0.3s ease;
    }
    .btn-cancel:hover{ 
        background: var(--line);
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0,0,0,.08);
    }
    .btn-cancel:active{
        transform: translateY(0);
    }

    .error-message{ color: var(--red); margin-bottom:16px; padding:12px; background: rgba(239,68,68,.1); border:1px solid rgba(239,68,68,.3); border-radius:8px; }
    .error-message ul{ margin:0; padding-left:20px; }
    .error-message li{ margin:4px 0; }
</style>

<div class="toolbar">
    <h2 style="margin:0;">Add Outbound</h2>
    <a class="btn-link" href="{{ route('outbound.index') }}">Back to Outbound</a>
</div>

<div class="form-container">
    @if($errors->any())
        <div class="error-message">
            <ul>
                @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('outbound.store') }}" method="POST">
        @csrf
        
        <div class="form-group">
            <label for="stock_id">Select Stock:</label>
            <select name="stock_id" id="stock_id" required>
                <option value="">-- Choose a stock --</option>
                @foreach($stocks as $stock)
                    <option value="{{ $stock->id }}" data-available="{{ $stock->stock }}" {{ old('stock_id') == $stock->id ? 'selected' : '' }}>{{ $stock->description }} ({{ $stock->id_no }})</option>
                @endforeach
            </select>
            <div style="margin-top:8px;">
                <span id="stockAvailableBadge" style="display:none;padding:4px 8px;border-radius:999px;font-weight:700;color:#fff;font-size:13px;"></span>
            </div>
            @error('stock_id')<span style="color:#ef4444;font-size:12px;">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
            <label for="client_id">Client:</label>
            <select name="client_id" id="client_id" required>
                <option value="">-- Choose a client --</option>
                @forelse($clients as $client)
                    <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>{{ $client->name }}</option>
                @empty
                    <option value="">No clients available</option>
                @endforelse
            </select>
            @error('client_id')<span style="color:#ef4444;font-size:12px;">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
            <label for="office">Office/Department:</label>
            <input type="text" name="office" id="office" value="{{ old('office', '') }}" placeholder="e.g., TMU, HR Department" required>
            @error('office')<span style="color:#ef4444;font-size:12px;">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
            <label for="total">Quantity:</label>
            <input type="number" name="total" id="total" value="{{ old('total', 1) }}" min="1" required>
        </div>

        {{-- Approval and Status are set automatically for admin-created outbounds --}}

        <div class="form-actions">
            <button type="submit" class="btn-submit">Add Outbound</button>
            <a href="{{ route('outbound.index') }}" class="btn-cancel">Cancel</a>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
    const select = document.getElementById('stock_id');
    const badge = document.getElementById('stockAvailableBadge');

    function updateBadge(){
        const opt = select.options[select.selectedIndex];
        const avail = opt && opt.dataset ? parseInt(opt.dataset.available || '0', 10) : 0;
        if(!opt || !opt.value){
            badge.style.display = 'none';
            return;
        }

        let bg = '#16a34a'; // green
        if(avail >= 50){ bg = '#16a34a'; }
        else if(avail > 0 && avail <= 49){ bg = '#f97316'; }
        else { bg = '#ef4444'; }

        badge.textContent = 'Available: ' + avail;
        badge.style.background = bg;
        badge.style.display = 'inline-block';
    }

    select.addEventListener('change', updateBadge);
    updateBadge();
});
</script>

@endsection
