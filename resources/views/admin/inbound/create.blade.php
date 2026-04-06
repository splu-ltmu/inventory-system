@extends('layouts.app')

@php
  $brand = 'Inventory System';
  $pageTitle = 'Inbound';
  $pageSubtitle = 'Add new inbound item.';
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
    .form-group input,
    .form-group select{ width:100%; padding:10px; border:1px solid var(--line); border-radius:8px; font-size:14px; box-sizing:border-box; }
    .form-group input:focus,
    .form-group select:focus{ outline:none; border-color: var(--blue); box-shadow: 0 0 0 3px rgba(37,99,235,.1); }
    
    .autocomplete-wrapper{ position:relative; }
    .autocomplete-list{
        position:absolute;
        top:100%;
        left:0;
        right:0;
        background:#fff;
        border:1px solid var(--line);
        border-top:none;
        border-radius:0 0 8px 8px;
        list-style:none;
        margin:0;
        padding:0;
        max-height:300px;
        overflow-y:auto;
        z-index:1000;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    .autocomplete-list li{
        padding:10px;
        cursor:pointer;
        border-bottom:1px solid var(--line);
        display:flex;
        justify-content:space-between;
        align-items:center;
    }
    .autocomplete-list li:hover{ background:rgba(37,99,235,.05); }
    .autocomplete-list li:last-child{ border-bottom:none; }
    .autocomplete-item-info{ flex:1; }
    .autocomplete-item-description{ font-weight:500; color:var(--text); }
    .autocomplete-item-code{ font-size:12px; color:var(--muted); }
    .autocomplete-item-unit{ font-size:12px; color:var(--muted); margin-left:8px; }
    
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

    <form action="{{ route('inbound.store') }}" method="POST" id="inboundForm">
        @csrf
        
        <div class="form-group">
            <label for="stock_search">Search Stock (by Description):</label>
            <div class="autocomplete-wrapper">
                <input 
                    type="text" 
                    id="stock_search" 
                    placeholder="Type to search existing stocks..." 
                    autocomplete="off"
                >
                <ul id="suggestions_list" class="autocomplete-list" style="display:none;"></ul>
            </div>
            <input type="hidden" id="stock_id" name="stock_id" required>
            <small id="stock_info" style="color:var(--muted); margin-top:8px; display:block;"></small>
        </div>

        <div class="form-group">
            <label for="total">Total Quantity:</label>
            <input type="number" name="total" id="total" value="1" min="1" required step="1">
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-submit">Add Inbound</button>
            <a href="{{ route('inbound.index') }}" class="btn-cancel">Cancel</a>
        </div>
    </form>
</div>

<script>
(function(){
    const searchInput = document.getElementById('stock_search');
    const suggestionsList = document.getElementById('suggestions_list');
    const stockIdField = document.getElementById('stock_id');
    const stockInfoField = document.getElementById('stock_info');
    const form = document.getElementById('inboundForm');
    
    let currentSelected = null;
    
    // Fetch suggestions from API
    async function fetchSuggestions(query) {
        if (!query || query.length < 1) {
            suggestionsList.style.display = 'none';
            return;
        }
        
        try {
            const response = await fetch(`{{ route('inbound.suggestions') }}?q=${encodeURIComponent(query)}`);
            const data = await response.json();
            
            if (data.length === 0) {
                suggestionsList.innerHTML = '<li style="color:var(--muted);">No stocks found</li>';
                suggestionsList.style.display = 'block';
                return;
            }
            
            suggestionsList.innerHTML = data.map(stock => `
                <li data-id="${stock.id}" data-description="${stock.description}" data-code="${stock.id_no}" data-unit="${stock.unit}">
                    <div class="autocomplete-item-info">
                        <div class="autocomplete-item-description">${stock.description}</div>
                        <div class="autocomplete-item-code">ID: ${stock.id_no} • ${stock.category_name}</div>
                    </div>
                    <div class="autocomplete-item-unit">${stock.unit || 'pcs'}</div>
                </li>
            `).join('');
            
            // Add click handlers to each suggestion
            suggestionsList.querySelectorAll('li').forEach(li => {
                li.addEventListener('click', function() {
                    selectStock(this.dataset.id, this.dataset.description, this.dataset.code, this.dataset.unit);
                });
            });
            
            suggestionsList.style.display = 'block';
        } catch (error) {
            console.error('Error fetching suggestions:', error);
        }
    }
    
    // Select a stock from suggestions
    function selectStock(stockId, description, code, unit) {
        stockIdField.value = stockId;
        searchInput.value = description;
        stockInfoField.textContent = `Selected: ${description} (${code}) - Unit: ${unit || 'pcs'}`;
        suggestionsList.style.display = 'none';
        currentSelected = {id: stockId, description, code, unit};
    }
    
    // Handle clear selection
    function clearSelection() {
        stockIdField.value = '';
        stockInfoField.textContent = '';
        currentSelected = null;
    }
    
    // Input event listener
    searchInput.addEventListener('input', (e) => {
        const query = e.target.value.trim();
        if (query.length < 1) {
            clearSelection();
            suggestionsList.style.display = 'none';
        } else {
            fetchSuggestions(query);
        }
    });
    
    // Click outside to close suggestions
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.autocomplete-wrapper')) {
            suggestionsList.style.display = 'none';
        }
    });
    
    // Form validation
    form.addEventListener('submit', (e) => {
        if (!stockIdField.value) {
            e.preventDefault();
            alert('Please select a stock from the suggestions or choose a stock from the dropdown.');
            searchInput.focus();
        }
    });
    
    // Show dropdown when input is focused and has text
    searchInput.addEventListener('focus', (e) => {
        if (e.target.value.trim().length > 0) {
            fetchSuggestions(e.target.value.trim());
        }
    });
})();
</script>
@endsection
