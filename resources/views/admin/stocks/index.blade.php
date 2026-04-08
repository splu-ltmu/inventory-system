@extends('layouts.app')

@php
  $brand = 'Inventory System';
  $pageTitle = 'Stocks';
  $pageSubtitle = 'Manage all available stocks.';
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

    /* Mobile responsive styles */
    @media (max-width: 768px) {
        .toolbar {
            flex-direction: column;
            align-items: stretch;
        }

        .toolbar > div:first-child {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .toolbar > div:first-child input,
        .toolbar > div:first-child select {
            width: 100%;
            min-width: unset;
        }

        .toolbar .btn-link {
            width: 100%;
            text-align: center;
        }

        table {
            min-width: unset;
            font-size: 13px;
        }

        th, td {
            padding: 8px;
        }

        /* Hide less critical columns on mobile */
        th:nth-child(2),
        td:nth-child(2) {
            display: none;
        }

        /* Stack category cell with button side-by-side on mobile */
        td.category-cell {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 6px;
        }

        td.category-cell .category-label {
            flex: 1;
            word-break: break-word;
            min-width: 0;
        }

        td.category-cell .btn-link {
            padding: 6px 8px;
            font-size: 12px;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .pill {
            font-size: 11px;
            padding: 3px 8px;
        }
    }

    @media (max-width: 480px) {
        th, td {
            padding: 6px;
            font-size: 12px;
        }

        .btn-link {
            padding: 8px 10px;
            font-size: 12px;
        }

        /* On very small screens, simplify the modal */
        #assignCategoryModal > div {
            width: 90% !important;
            max-width: 100%;
            margin: 0 auto;
        }
    }
</style>

<div class="toolbar">
    <div style="display:flex; gap:12px; align-items:center;">
        <h2 style="margin:0;">Stocks</h2>
        @php
            $filterCategories = $allCategories->pluck('name')->unique()->values();
        @endphp

        <input id="stocksSearch" type="search" placeholder="Search ID, description, category..." style="padding:8px 10px;border:1px solid var(--line);border-radius:8px;min-width:260px;">

        <select id="filterCategory" style="padding:8px 10px;border:1px solid var(--line);border-radius:8px;">
            <option value="">All categories</option>
            @foreach($filterCategories as $c)
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
                    <td class="muted category-cell" data-stock-id="{{ $s->id }}" onclick="openAssignModal({{ $s->id }}, @json($cat))" style="cursor:pointer; display:flex; align-items:center; justify-content:space-between; gap:12px;">
                        <span class="category-label">{{ $cat ?: 'Unknown' }}</span>
                        <div style="margin-left:auto;">
                            <button type="button" class="btn-link" style="padding:4px 6px;" onclick="openAssignModal({{ $s->id }}, @json($cat))">{{ $cat ? 'Change' : 'Assign' }}</button>
                        </div>
                    </td>
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

<!-- Assign Category Modal -->
<div id="assignCategoryModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:10000; align-items:center; justify-content:center;">
    <div style="background:#fff; border-radius:12px; padding:18px; width:480px; max-width:95%; box-shadow:0 18px 40px rgba(2,6,23,.2);">
        <h3 style="margin:0 0 8px 0; font-size:16px; font-weight:900;">Assign Category</h3>
        <div style="margin-bottom:8px; color:var(--muted);">Select an existing category or create a new one.</div>

        <div style="display:flex; flex-direction:column; gap:8px; margin-bottom:12px;">
            <select id="assignCategorySelect" style="padding:8px; border:1px solid var(--line); border-radius:8px;">
                <option value="">-- select category --</option>
                @foreach($allCategories as $catOpt)
                    <option value="{{ $catOpt->id }}">{{ $catOpt->name }}</option>
                @endforeach
            </select>

            <div class="field">
                <label style="margin-bottom:6px; font-weight:700;">Description</label>
                <input id="assignDescriptionInput" placeholder="Edit stock description (optional)" style="padding:8px; border:1px solid var(--line); border-radius:8px;">
            </div>


            <div id="assignFeedback" style="color:var(--muted); font-size:13px;"></div>
        </div>

        <div style="display:flex; gap:8px; justify-content:flex-end;">
            <button class="btn-ghost" type="button" onclick="closeAssignModal()" style="background:#f3f4f6; color:#374151; border:1px solid #d1d5db; padding:8px 16px; border-radius:6px; cursor:pointer;">Cancel</button>
            <button class="btn" type="button" onclick="saveAssignCategory()" style="background:#3b82f6; color:white; border:none; padding:8px 16px; border-radius:6px; cursor:pointer;">Save</button>
        </div>
    </div>
</div>

<script>
const CSRF_TOKEN = '{{ csrf_token() }}';
let _assignStockId = null;

function openAssignModal(stockId, currentCategory){
    _assignStockId = stockId;
    const modal = document.getElementById('assignCategoryModal');
    if (!modal) {
        console.error('assignCategoryModal element not found on page');
        alert('Assign modal is not loaded — please refresh the page.');
        return;
    }

    const select = document.getElementById('assignCategorySelect');
    // try to preselect the existing category by visible name (falls back to empty)
    if (select) {
        select.value = '';
        if (currentCategory) {
            const wanted = ('' + currentCategory).trim().toLowerCase();
            for (const opt of Array.from(select.options)) {
                if ((opt.text || '').trim().toLowerCase() === wanted) {
                    select.value = opt.value;
                    break;
                }
            }
        }
    }

    const descInput = document.getElementById('assignDescriptionInput');
    // populate description from the row if available
    try {
        const td = document.querySelector(`td.category-cell[data-stock-id='${stockId}']`);
        const row = td?.closest('tr');
        const descCell = row?.querySelector('td:nth-child(2)');
        const currentDesc = (descCell?.textContent || '').trim();
        if (descInput) descInput.value = currentDesc;
    } catch (err) {
        if (descInput) descInput.value = '';
    }

    const feedback = document.getElementById('assignFeedback');
    if (feedback) feedback.textContent = currentCategory ? `Current: ${currentCategory}` : 'No category assigned';

    modal.style.display = 'flex';
}

function closeAssignModal(){
    _assignStockId = null;
    document.getElementById('assignCategoryModal').style.display = 'none';
}

function clearAssignInputs(){
    const select = document.getElementById('assignCategorySelect');
    const desc = document.getElementById('assignDescriptionInput');
    if (select) select.value = '';
    if (desc) desc.value = '';
}

async function saveAssignCategory(){
    if (!_assignStockId) return;
    const select = document.getElementById('assignCategorySelect');
    const categoryId = select.value || null;

    const payload = new FormData();
    if (categoryId) payload.append('category_id', categoryId);
    const descVal = (document.getElementById('assignDescriptionInput')?.value || '').trim();
    if (descVal) payload.append('description', descVal);
    payload.append('_token', CSRF_TOKEN);

    try {
        const res = await fetch(`/admin/stocks/${_assignStockId}/assign-category`, {
            method: 'POST',
            body: payload,
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN }
        });
        const data = await res.json();
        if (res.ok && data.success) {
            const td = document.querySelector(`td.category-cell[data-stock-id='${_assignStockId}']`);
            const tr = td?.closest('tr');
            const newLabel = data.category_name || 'Unknown';
            if (td) td.innerHTML = `${newLabel} <button type="button" class="btn-link" style="padding:4px 6px; margin-left:8px;" onclick="openAssignModal(${_assignStockId}, ${JSON.stringify(data.category_name)})">Change</button>`;
            if (tr) {
                tr.dataset.cat = (data.category_name || '').toLowerCase();
                // update description cell in the same row if controller returned it
                if (data.stock_description) {
                    const descCell = tr.querySelector('td:nth-child(2)');
                    if (descCell) descCell.textContent = data.stock_description;
                    tr.dataset.desc = (data.stock_description || '').toLowerCase();
                }
            }
            closeAssignModal();
        } else {
            document.getElementById('assignFeedback').textContent = data.message || 'Unable to assign category.';
        }
    } catch (err) {
        document.getElementById('assignFeedback').textContent = 'Request failed.';
    }
}

// Fallback: delegated click handler so the Change/Assign button always opens the modal
// (fixes cases where inline handlers are blocked or dynamic DOM updates removed the onclick)
document.addEventListener('click', function(e) {
    const clicked = e.target.closest('.category-cell, .category-cell .btn-link');
    if (!clicked) return;
    const cell = clicked.classList.contains('category-cell') ? clicked : clicked.closest('.category-cell');
    if (!cell) return;
    const stockId = cell.getAttribute('data-stock-id');
    if (!stockId) return;

    // Get category visible text (exclude the button label)
    let categoryText = '';
    // prefer the first text node inside the cell if available
    for (const node of cell.childNodes) {
        if (node.nodeType === Node.TEXT_NODE && node.textContent.trim()) { categoryText = node.textContent.trim(); break; }
    }
    if (!categoryText) categoryText = (cell.textContent || '').trim();

    const btn = cell.querySelector('.btn-link');
    if (btn) {
        const btnText = btn.textContent.trim();
        if (categoryText.endsWith(btnText)) {
            categoryText = categoryText.slice(0, categoryText.length - btnText.length).trim();
        }
    }

    openAssignModal(stockId, categoryText || '');
});

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
