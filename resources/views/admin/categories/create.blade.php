@extends('layouts.app')

@php
  $brand = 'Inventory System';
  $pageTitle = 'Categories';
  $pageSubtitle = 'Add new category.';
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
    <h2 style="margin:0;">Add New Category</h2>
    <a class="btn-link" href="{{ route('categories.index') }}">Back to Categories</a>
</div>

<div class="form-container">
    @if(session('success'))
        <div class="error-message" style="background: rgba(34,197,94,.1); border-color: rgba(34,197,94,.3); color: #166534;">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="error-message">
            <ul>
                @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('categories.store') }}" method="POST">
        @csrf
        
        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" placeholder="e.g., Computer Supplies" required>
        </div>

        <div class="form-group">
            <label for="code">Code:</label>
            <input type="text" name="code" id="code" value="{{ old('code') }}" placeholder="e.g., CS" maxlength="2" style="text-transform: uppercase;" required>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-submit">Add Category</button>
            <a href="{{ route('categories.index') }}" class="btn-cancel">Cancel</a>
        </div>
    </form>
</div>

<script>
document.getElementById('code').addEventListener('input', function(e) {
    this.value = this.value.toUpperCase();
});
</script>
@endsection
