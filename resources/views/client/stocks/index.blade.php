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
      ];
  })->values();
@endphp

@section('sidebar')
    <a href="{{ route('client.dashboard') }}" class="{{ request()->is('client') ? 'active' : '' }}">
        Dashboard <small>Home</small>
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
    }
    .btn:hover{ opacity:.92; }
    .btn-ghost{
        padding:10px 12px;
        border-radius:12px;
        border:1px solid #e2e8f0;
        background:#fff;
        color:#0f172a;
        cursor:pointer;
        font-weight:700;
    }
    .btn-ghost:hover{ background:#f8fafc; }

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
    .modal-card{
        max-width: 900px;
        margin: 40px auto;
        background:#ffffff;
        border:1px solid #e2e8f0;
        border-radius:16px;
        overflow:hidden;
        box-shadow:0 18px 48px rgba(15,23,42,.18);
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
            <th style="min-width:80px;">Unit</th>
            <th style="min-width:120px;">Stock</th>
        </tr>
        @foreach($stocks as $s)
            <tr>
                <td><b>{{ $s->id_no }}</b></td>
                <td>{{ $s->description }}</td>
                <td>{{ $s->unit }}</td>
                <td>
                    @if($s->stock >= 50)
                        <span class="pill ok">{{ $s->stock }} available</span>
                    @elseif($s->stock > 0 && $s->stock <= 49)
                        <span class="pill low">{{ $s->stock }} available</span>
                    @else
                        <span class="pill bad">Out of stock</span>
                    @endif
                </td>
            </tr>
        @endforeach
    </table>
</div>

{{-- CONFIRMATION MODAL --}}
<div id="confirmModalOverlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.5); z-index:10000; align-items:center; justify-content:center;">
    <div style="background:#fff; border-radius:14px; box-shadow:0 10px 40px rgba(0,0,0,.25); max-width:420px; width:90%; padding:24px;">
        <h3 id="confirmTitle" style="margin:0 0 8px 0; font-size:18px; color:#0f172a; font-weight:800;">Confirm</h3>
        <p id="confirmMessage" style="margin:0 0 20px 0; color:#475569; font-size:14px; line-height:1.5;">Are you sure?</p>
        <div style="display:flex; gap:10px; justify-content:flex-end;">
            <button type="button" onclick="closeConfirmModal()" style="padding:10px 16px; border-radius:10px; border:none; background:#e2e8f0; color:#0f172a; font-weight:700; cursor:pointer; font-size:14px;">Cancel</button>
            <button type="button" onclick="confirmAction()" style="padding:10px 16px; border-radius:10px; border:none; background:#2563eb; color:#fff; font-weight:700; cursor:pointer; font-size:14px;">Confirm</button>
        </div>
    </div>
</div>

{{-- MODAL --}}
<div class="modal" id="reqModal">
    <div class="modal-card">
        <div class="modal-head">
            <div>
                <div class="modal-title">Create Request (Multiple Items)</div>
                <div class="muted">Search items, add to list, set quantities, then submit.</div>
            </div>
            <button class="btn-ghost" type="button" onclick="closeReqModal()">✕</button>
        </div>

        <form method="POST" action="{{ route('client.requests.store') }}">
            @csrf

            <div class="modal-body">
                {{-- LEFT: search & add --}}
                <div>
                    <div class="field">
                        <label><b>Office</b></label>
                        <input type="text" name="office" placeholder="e.g., ICT" required>
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

function openReqModal(){
    document.getElementById('reqModal').style.display = 'block';
    renderStockList();
    renderCart();
}
function closeReqModal(){
    document.getElementById('reqModal').style.display = 'none';
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
        if (!q) return true;
        return (s.id_no || '').toLowerCase().includes(q) || (s.description || '').toLowerCase().includes(q);
    });

    filtered.forEach(s => {
        const btnText = cart[s.id] ? 'Added' : 'Add';
        const btnDisabled = (s.stock <= 0 || cart[s.id]) ? 'disabled' : '';
        const tr = document.createElement('tr');
        const stockBadge = s.stock >= 50 ? `<span class="pill ok">${s.stock} avail</span>` : (s.stock > 0 ? `<span class="pill low">${s.stock} avail</span>` : `<span class="pill bad">Out</span>`);
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

let confirmCallback = null;

function confirmSubmitRequest(){
    const keys = Object.keys(cart);
    if(keys.length === 0){
        alert('Please add at least one item.');
        return;
    }
    confirmCallback = 'submitRequestConfirmed';
    showConfirmModal('Submit Request', 'Submit this request for approval? You can view and manage it in My Requests.');
}

function submitRequestConfirmed(){
    const form = document.querySelector('#reqModal form');
    if(form) form.submit();
}

function showConfirmModal(title, message){
    document.getElementById('confirmTitle').textContent = title;
    document.getElementById('confirmMessage').textContent = message;
    document.getElementById('confirmModalOverlay').style.display = 'flex';
}

function closeConfirmModal(){
    document.getElementById('confirmModalOverlay').style.display = 'none';
    confirmCallback = null;
}

function confirmAction(){
    if(confirmCallback === 'submitRequestConfirmed'){
        submitRequestConfirmed();
    }
    closeConfirmModal();
}
let pendingSubmitForm = null;

function confirmSubmitRequest(){
    const keys = Object.keys(cart);
    if(keys.length === 0){
        alert('Please add at least one item.');
        return;
    }
    showConfirmModal('Submit Request', 'Submit this request for approval? You can view and manage it in My Requests.', 'submitRequestConfirmed');
}

function submitRequestConfirmed(){
    const form = document.querySelector('#reqModal form');
    if(form) form.submit();
    closeConfirmModal();
}

function showConfirmModal(title, message, actionCallback){
    document.getElementById('confirmTitle').textContent = title;
    document.getElementById('confirmMessage').textContent = message;
    window.confirmCallback = actionCallback;
    document.getElementById('confirmModalOverlay').style.display = 'flex';
}

function closeConfirmModal(){
    document.getElementById('confirmModalOverlay').style.display = 'none';
    window.confirmCallback = null;
}

function confirmAction(){
    if(window.confirmCallback && typeof window[window.confirmCallback] === 'function'){
        window[window.confirmCallback]();
    }
}</script>
@endsection
