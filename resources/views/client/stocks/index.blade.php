@extends('layouts.app')

@php
  $brand = 'Inventory System';
  $pageTitle = 'Available Stocks';
  $pageSubtitle = 'Create one request with multiple items.';

  // ✅ FIX: prepare data BEFORE @json to avoid Blade parse error
  $stocksJson = $stocks->map(function ($s) {
      return [
          'id' => $s->id,
          'id_no' => $s->id_no,
          'description' => $s->description,
          'unit' => $s->unit,
          'stock' => $s->stock,
          'category' => $s->category?->name ?? 'Unknown',
      ];
  })->values();
@endphp

@section('sidebar')
    <a href="{{ route('client.dashboard') }}" class="{{ request()->is('client') ? 'active' : '' }}">
        Dashboard <small>Home</small>
    </a>

    <a href="{{ route('client.summary') }}" class="{{ request()->is('client/summary*') ? 'active' : '' }}">
        Summary <small>Transactions</small>
    </a>

    <a href="{{ route('client.stocks') }}" class="{{ request()->is('client/stocks*') ? 'active' : '' }}">
        Available Stocks <small>Request items</small>
    </a>

    <a href="{{ route('client.requests') }}" class="{{ request()->is('client/requests*') ? 'active' : '' }}">
        My Requests <small>Status + Code</small>
    </a>

    <a href="{{ route('client.account') }}" class="{{ request()->is('client/account*') ? 'active' : '' }}">
        Account Settings <small>Email & Password</small>
    </a>
@endsection

@section('content')
<style>
    .btn{
        padding:10px 12px;
        border-radius:12px;
        border:1px solid #2563eb;
        background:#2563eb;
        color:#fff;
        cursor:pointer;
        font-weight:700;
        transition: all 0.3s ease;
    }
    .btn:hover{ 
        opacity:.92;
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(37,99,235,.2);
    }
    .btn:active{
        transform: translateY(0);
    }
    .btn-ghost{
        padding:10px 12px;
        border-radius:12px;
        border:1px solid #e2e8f0;
        background:#fff;
        color:#0f172a;
        cursor:pointer;
        font-weight:700;
        transition: all 0.3s ease;
    }
    .btn-ghost:hover{ 
        background:#f8fafc;
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0,0,0,.08);
    }
    .btn-ghost:active{
        transform: translateY(0);
    }

    .btn-cancel{
        padding:10px 12px;
        border-radius:12px;
        border:1px solid #dc2626; /* red border */
        background:#fff;
        color:#dc2626; /* red text */
        cursor:pointer;
        font-weight:700;
        transition: all 0.3s ease;
    }
    .btn-cancel:hover{ 
        background:#b91c1c; 
        color:#fff;
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(220,38,38,.2);
    }
    .btn-cancel:active{
        transform: translateY(0);
    }

    table{ width:100%; border-collapse: collapse; }
    th, td{ border:1px solid #e2e8f0; padding:10px; text-align:left; }
    th{ background:#f8fafc; }

    .pill{
        display:inline-block;
        padding:4px 10px;
        border-radius:999px;
        font-size:12px;
        border:1px solid #e2e8f0;
        background:#f8fafc;
        color:#475569;
        font-weight:700;
    }
    .pill.ok{ border-color:#bbf7d0; background:#ecfdf5; color:#065f46; }
    .pill.bad{ border-color:#fecaca; background:#fef2f2; color:#991b1b; }
    .pill.low{ border-color: rgba(249,115,22,.35); background: rgba(249,115,22,.10); color: var(--orange); }

    /* Modal */
    .modal{
        display:none;
        position:fixed;
        inset:0;
        background: rgba(15,23,42,.45);
        z-index:9999;
        padding:18px;
    }
    .modal.active{
        display:flex;
        align-items:center;
        justify-content:center;
        overflow-y:auto;
    }
    .modal-card{
        max-width: 900px;
        width:100%;
        max-height: calc(100vh - 36px);
        background:#ffffff;
        border:1px solid #e2e8f0;
        border-radius:16px;
        overflow:auto;
        box-shadow:0 18px 48px rgba(15,23,42,.18);
        flex-shrink:0;
    }
    .modal-head{
        display:flex;
        justify-content:space-between;
        align-items:center;
        gap:10px;
        padding:14px 16px;
        background:#eff6ff; /* blue soft */
        border-bottom:1px solid #e2e8f0;
    }
    .modal-title{
        font-size:16px;
        font-weight:900;
        color:#0f172a;
    }
    .modal-body{
        padding:16px;
        display:grid;
        grid-template-columns: 1.2fr .8fr;
        gap:14px;
    }
    
    /* show cart toggle button on mobile */
    @media (max-width: 640px){
        .cart-toggle{ display:flex !important; }
    }

    /* Mobile: hide cart by default, show search items */
    @media (max-width: 640px){
        .modal-body{
            grid-template-columns: 1fr;
        }
        .modal-body > .cart{
            display:none;
        }
        /* When toggled, hide search and show cart only */
        .modal-body.show-cart > div:first-child{
            display:none !important;
        }
        .modal-body.show-cart > .cart{
            display:block !important;
        }
    }
    .field{
        display:flex;
        flex-direction:column;
        gap:6px;
        margin-bottom:10px;
    }
    input, select{
        padding:10px 12px;
        border-radius:12px;
        border:1px solid #e2e8f0;
        outline:none;
    }
    input:focus{ border-color:#93c5fd; }
    .muted{ color:#64748b; font-size:12px; }

    .cart{
        border:1px solid #e2e8f0;
        border-radius:14px;
        overflow:hidden;
        background:#fff;
    }
    .cart-head{
        padding:10px 12px;
        background:#fff7ed; /* orange soft */
        border-bottom:1px solid #e2e8f0;
        font-weight:900;
        color:#0f172a;
    }
    .cart-body{ padding:12px; }
    .cart-row{
        display:flex;
        gap:10px;
        align-items:center;
        justify-content:space-between;
        padding:10px 0;
        border-bottom:1px solid #f1f5f9;
    }
    .cart-row:last-child{ border-bottom:none; }
    .qty{
        width:90px;
    }
    
    .modal-btn-cancel, .modal-btn-confirm {
        padding:10px 16px; 
        border-radius:10px; 
        border:none; 
        font-weight:700; 
        cursor:pointer; 
        font-size:14px;
        transition: all 0.3s ease;
    }
    .modal-btn-cancel {
        background:#e2e8f0; 
        color:#0f172a;
    }
    .modal-btn-cancel:hover {
        background:#cbd5e1;
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0,0,0,.1);
    }
    .modal-btn-cancel:active {
        transform: translateY(0);
    }
    .modal-btn-confirm {
        background:#2563eb; 
        color:#fff;
    }
    .modal-btn-confirm:hover {
        background:rgba(37,99,235,.9);
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(37,99,235,.2);
    }
    .modal-btn-confirm:active {
        transform: translateY(0);
    }
</style>

<div style="display:flex; justify-content:space-between; align-items:center; gap:12px; margin-bottom:12px;">
    <div>
        <h2 style="margin:0;">Available Stocks</h2>
        <div class="muted">Create one request with multiple items.</div>
    </div>
    <button class="btn" onclick="openReqModal()">+ Create Request</button>
</div>

<div style="overflow:auto; border-radius:14px; border:1px solid #e2e8f0;">
    <table>
        <tr>
            <th style="min-width:120px;">ID No</th>
            <th style="min-width:220px;">Description</th>
            <th style="min-width:180px;">Category</th>
            <th style="min-width:80px;">Unit</th>
            <th style="min-width:120px;">Stock</th>
        </tr>
        @foreach($stocks as $s)
            @if($s->stock > 0)
            <tr>
                <td><b>{{ $s->id_no }}</b></td>
                <td>{{ $s->description }}</td>                <td>{{ $s->category?->name ?? 'Unknown' }}</td>                <td>{{ $s->unit }}</td>
                <td>
                    @if($s->stock >= 50)
                        <span class="pill ok">Available</span>
                    @else
                        <span class="pill low">Available</span>
                    @endif
                </td>
            </tr>
            @endif
        @endforeach
    </table>
</div>

{{-- CONFIRMATION MODAL --}}
<div id="confirmModalOverlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.5); z-index:10000; align-items:center; justify-content:center;">
    <div style="background:#fff; border-radius:14px; box-shadow:0 10px 40px rgba(0,0,0,.25); max-width:420px; width:90%; padding:24px;">
        <h3 id="confirmTitle" style="margin:0 0 8px 0; font-size:18px; color:#0f172a; font-weight:800;">Confirm</h3>
        <p id="confirmMessage" style="margin:0 0 20px 0; color:#475569; font-size:14px; line-height:1.5;">Are you sure?</p>
        <div style="display:flex; gap:10px; justify-content:flex-end;">
            <button type="button" class="modal-btn-cancel" onclick="closeConfirmModal()">Cancel</button>
            <button type="button" class="modal-btn-confirm" onclick="confirmAction()">Confirm</button>
        </div>
    </div>
</div>

{{-- MODAL --}}
<div class="modal" id="reqModal">
    <div class="modal-card">
        <div class="modal-head">
            <div style="flex:1;">
                <div class="modal-title">Create Request (Multiple Items)</div>
                <div class="muted">Search items, add to list, set quantities, then submit. <button class="btn-ghost" type="button" onclick="closeReqModal()" style="position:absolute; top:14px; right:16px; background:none; border:none; color:#0f172a; font-size:18px; padding:0; width:24px; height:24px; display:flex; align-items:center; justify-content:center;">✕</button></div>
                 
            </div>
           
        </div>

        <form method="POST" action="{{ route('client.requests.store') }}">
            @csrf

            <!-- cart toggle button for mobile -->
            <button type="button" id="cartToggleBtn" class="cart-toggle btn-ghost" onclick="toggleCartView()" title="Toggle View" style="display:none; width:100%; padding:12px; margin:0 0 12px 0; justify-content:center; gap:8px;">
                See Cart
                <!-- cart icon SVG -->
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="vertical-align:middle;">
                    <path d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.1 5H19M7 13v8a2 2 0 002 2h10a2 2 0 002-2v-3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <circle cx="9" cy="21" r="1" stroke="currentColor" stroke-width="2"/>
                    <circle cx="20" cy="21" r="1" stroke="currentColor" stroke-width="2"/>
                </svg>
            </button>

            <div class="modal-body">
                {{-- LEFT: search & add --}}
                <div>
                    <div class="field">
                        <label><b>Office</b></label>
                        <div class="muted">{{ Auth::user()->office ?? '-' }}</div>
                        <input type="hidden" name="office" value="{{ Auth::user()->office ?? '' }}">
                    </div>

                    <div class="field">
                        <label><b>Search Item</b></label>
                        <input type="text" id="searchBox" placeholder="Type to search..." oninput="renderStockList()">
                        <div class="muted">Tip: search by ID No or Description.</div>
                    </div>

                    <div style="border:1px solid #e2e8f0; border-radius:14px; overflow:hidden;">
                        <table style="margin:0;">
                            <thead>
                                <tr>
                                    <th>ID No</th>
                                    <th>Description</th>
                                    <th>Stock</th>
                                    <th style="width:140px;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="stockList"></tbody>
                        </table>
                    </div>
                </div>

                {{-- RIGHT: cart --}}
                <div class="cart">
                    <div class="cart-head">Selected Items</div>
                    <div class="cart-body">
                        <div id="cartEmpty" class="muted">No items selected yet.</div>
                        <div id="cartRows"></div>

                        <div style="display:flex; gap:10px; margin-top:12px;">
                            <button type="button" class="btn" style="flex:1;" onclick="confirmSubmitRequest()">Submit Request</button>
                            <button type="button" class="btn-ghost" onclick="clearCart()">Clear</button>
                        </div>

                        <div class="muted" style="margin-top:10px;">
                            Admin may approve partially depending on availability.
                        </div>
                    </div>
                </div>
            </div>

            {{-- hidden inputs will be injected here --}}
            <div id="hiddenInputs"></div>
        </form>
    </div>
</div>

<script>

const STOCKS = @json($stocksJson);

let cart = {}; // { stockId: qty }

function toggleCartView(){
    const body = document.querySelector('#reqModal .modal-body');
    body.classList.toggle('show-cart');
    const btn = document.getElementById('cartToggleBtn');
    if (body.classList.contains('show-cart')) {
        btn.innerHTML = `Stocks <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="vertical-align:middle;">
                    <path d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.1 5H19M7 13v8a2 2 0 002 2h10a2 2 0 002-2v-3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <circle cx="9" cy="21" r="1" stroke="currentColor" stroke-width="2"/>
                    <circle cx="20" cy="21" r="1" stroke="currentColor" stroke-width="2"/>
                </svg>`;
    } else {
        btn.innerHTML = `See Cart <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="vertical-align:middle;">
                    <path d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.1 5H19M7 13v8a2 2 0 002 2h10a2 2 0 002-2v-3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <circle cx="9" cy="21" r="1" stroke="currentColor" stroke-width="2"/>
                    <circle cx="20" cy="21" r="1" stroke="currentColor" stroke-width="2"/>
                </svg>`;
    }
}

function openReqModal(){
    const modal = document.getElementById('reqModal');
    modal.classList.add('active');
    renderStockList();
    renderCart();
}
function closeReqModal(){
    const modal = document.getElementById('reqModal');
    modal.classList.remove('active');
}
document.addEventListener('click', (e) => {
    const modal = document.getElementById('reqModal');
    if (e.target === modal) closeReqModal();
});
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeReqModal();
});

function renderStockList(){
    const q = (document.getElementById('searchBox').value || '').toLowerCase().trim();
    const tbody = document.getElementById('stockList');
    tbody.innerHTML = '';

    const filtered = STOCKS.filter(s => {
        // exclude out-of-stock items from the modal list
        if (s.stock <= 0) return false;
        if (!q) return true;
        return (s.id_no || '').toLowerCase().includes(q) || (s.description || '').toLowerCase().includes(q);
    });

    filtered.forEach(s => {
        const btnText = cart[s.id] ? 'Added' : 'Add';
        const btnDisabled = cart[s.id] ? 'disabled' : '';
        const tr = document.createElement('tr');
        // show availability level only (no numeric count)
        const stockBadge = s.stock >= 50 ? `<span class="pill ok">Available</span>` : `<span class="pill low">Available</span>`;
        tr.innerHTML = `
            <td><b>${escapeHtml(s.id_no)}</b></td>
            <td>${escapeHtml(s.description)}</td>
            <td>${stockBadge}</td>
            <td>
                <button type="button" class="btn-ghost" ${btnDisabled} onclick="addToCart(${s.id})">${btnText}</button>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

function addToCart(stockId){
    const s = STOCKS.find(x => x.id === stockId);
    if (!s || s.stock <= 0) return;
    cart[stockId] = 1;
    renderStockList();
    renderCart();
}

function removeFromCart(stockId){
    delete cart[stockId];
    renderStockList();
    renderCart();
}

function updateQty(stockId, max){
    let v = parseInt(document.getElementById('qty_'+stockId).value || '1', 10);
    if (isNaN(v) || v < 1) v = 1;
    if (v > max) v = max;
    cart[stockId] = v;
    document.getElementById('qty_'+stockId).value = v;
    renderHiddenInputs();
}

function clearCart(){
    cart = {};
    renderStockList();
    renderCart();
}

function renderCart(){
    const rows = document.getElementById('cartRows');
    const empty = document.getElementById('cartEmpty');
    rows.innerHTML = '';

    const keys = Object.keys(cart);
    if (keys.length === 0){
        empty.style.display = 'block';
        renderHiddenInputs();
        return;
    }
    empty.style.display = 'none';

    keys.forEach(k => {
        const stockId = parseInt(k, 10);
        const s = STOCKS.find(x => x.id === stockId);
        if (!s) return;

        const div = document.createElement('div');
        div.className = 'cart-row';
        div.innerHTML = `
            <div style="min-width:0;">
                <div style="font-weight:900; color:#0f172a;">${escapeHtml(s.id_no)} — ${escapeHtml(s.description)}</div>
                <div class="muted">Unit: ${escapeHtml(s.unit)} • Max: ${s.stock}</div>
            </div>

            <div style="display:flex; gap:10px; align-items:center;">
                <input class="qty" id="qty_${stockId}" type="number" min="1" max="${s.stock}"
                       value="${cart[stockId]}" onchange="updateQty(${stockId}, ${s.stock})">
                <button type="button" class="btn-ghost" onclick="removeFromCart(${stockId})">Remove</button>
            </div>
        `;
        rows.appendChild(div);
    });

    renderHiddenInputs();
}

function renderHiddenInputs(){
    const holder = document.getElementById('hiddenInputs');
    holder.innerHTML = '';

    Object.keys(cart).forEach(k => {
        const stockId = parseInt(k, 10);
        const qty = cart[stockId];

        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = `items[${stockId}]`;
        input.value = qty;

        holder.appendChild(input);
    });
}

function escapeHtml(str){
    return String(str ?? '').replace(/[&<>"']/g, m => ({
        '&':'&amp;', '<':'&lt;', '>':'&gt;', '"':'&quot;', "'":'&#039;'
    }[m]));
}

// --- single, reliable confirm/submit implementation ---
let __confirmCb = null;

function confirmSubmitRequest(){
    const keys = Object.keys(cart);
    if (keys.length === 0) {
        alert('Please add at least one item.');
        return;
    }

    // ensure hidden inputs exist before showing confirmation
    renderHiddenInputs();

    // show modal and set callback name
    showConfirmModal('Submit Request', 'Submit this request for approval? You can view and manage it in My Requests.', 'submitRequestConfirmed');
}

function submitRequestConfirmed(){
    // ensure hidden inputs are up-to-date then submit
    renderHiddenInputs();
    showLoading('Submitting request...');
    const form = document.querySelector('#reqModal form');
    if (form) {
        // disable submit button to avoid double-submits
        const btn = form.querySelector('button[type="button"].btn');
        if (btn) btn.disabled = true;
        form.submit();
    }
}

function showConfirmModal(title, message, callbackName){
    document.getElementById('confirmTitle').textContent = title;
    document.getElementById('confirmMessage').textContent = message;
    window.__confirmCb = callbackName || null;
    document.getElementById('confirmModalOverlay').style.display = 'flex';
}

function closeConfirmModal(){
    document.getElementById('confirmModalOverlay').style.display = 'none';
    window.__confirmCb = null;
}

function confirmAction(){
    if (window.__confirmCb && typeof window[window.__confirmCb] === 'function') {
        window[window.__confirmCb]();
    }
    closeConfirmModal();
}
</script>
@endsection
