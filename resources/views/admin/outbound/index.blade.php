@extends('layouts.app')

@php
  $brand = 'Inventory System';
  $pageTitle = 'Outbound';
  $pageSubtitle = 'Items released from inventory (verified requests).';
@endphp

@section('sidebar')
    @include('partials.admin-sidebar')
@endsection

@section('content')
<style>
    .toolbar{ display:flex; justify-content:space-between; align-items:center; gap:12px; margin-bottom:16px; flex-wrap:wrap; }
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

    .ob-card{
        margin-bottom:12px;
        border:1px solid rgba(37,99,235,.12);
        border-radius:14px;
        overflow:hidden;
        background:linear-gradient(180deg, #fbfdff 0%, #f1f8ff 100%);
        transition:box-shadow 0.25s ease, border-color 0.2s ease, transform .12s ease;
    }
    .ob-card:hover{ 
        border-color: rgba(37,99,235,.35);
        box-shadow: 0 8px 20px rgba(37,99,235,.12);
        transform: translateY(-3px);
    }

    .ob-header{
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:12px;
        padding:16px;
        background:linear-gradient(90deg, rgba(37,99,235,.10) 0%, rgba(37,99,235,.06) 100%);
        border-bottom:1px solid rgba(37,99,235,.06);
        cursor:pointer;
        transition:background 0.18s ease;
    }
    .ob-header:hover{ 
        background:linear-gradient(90deg, rgba(37,99,235,.16) 0%, rgba(37,99,235,.10) 100%);
    }

    .ob-title{
        font-weight:900;
        font-size:16px;
        color:var(--blue);
        margin:0;
    }
    .ob-sub{
        margin-top:6px;
        color:var(--muted);
        font-size:13px;
    }

    .ob-header-right{
        text-align:right;
        display:flex;
        flex-direction:column;
        align-items:flex-end;
        gap:8px;
        white-space:nowrap;
    }

    .ob-date{
        font-weight:700;
        font-size:14px;
        color:var(--text);
    }

    .ob-count{
        font-weight:700;
        font-size:14px;
        color:var(--text);
    }

    .ob-toggle{ 
        color:var(--muted); 
        font-size:12px; 
        font-weight:600;
        display:flex;
        align-items:center;
        gap:6px;
    }

    .ob-toggle::after{
        content:"▼";
        display:inline-block;
        transition:transform 0.2s ease;
        font-size:10px;
    }

    .ob-body{
        display:none;
        padding:16px;
        background:#fff;
    }
    .ob-body.open{ display:block; }
    .ob-body.open ~ .ob-header .ob-toggle::after{
        transform:rotate(-180deg);
    }

    .pill{
        display:inline-block; 
        padding:5px 12px; 
        border-radius:999px;
        border:1px solid rgba(37,99,235,.45);
        background: rgba(37,99,235,.14);
        color: var(--blue);
        font-size:12px; 
        font-weight:800;
    }
    .pill.orange{
        border-color: rgba(249,115,22,.35);
        background: rgba(249,115,22,.12);
        color: var(--orange);
    }
    .pill.green{
        border-color: rgba(22,163,74,.35);
        background: rgba(22,163,74,.12);
        color: #16a34a;
    }

    .table-wrap{ overflow:auto; border:1px solid var(--line); border-radius:12px; }
    table{ width:100%; border-collapse:collapse; background:#fff; }
    th,td{ border:1px solid var(--line); padding:12px; text-align:left; }
    th{ background: rgba(37,99,235,.06); color: var(--text); font-weight:700; font-size:13px; }
    td{ color: var(--text); font-size:13px; }
    td b{ color:var(--text); font-weight:700; }

    .ob-summary{
        margin-top:16px;
        padding:16px;
        background:linear-gradient(135deg, rgba(37,99,235,.06) 0%, rgba(37,99,235,.03) 100%);
        border:1px solid rgba(37,99,235,.1);
        border-radius:12px;
    }

    .ob-summary-grid{
        display:grid;
        grid-template-columns:repeat(auto-fit, minmax(180px, 1fr));
        gap:16px;
    }

    .ob-summary-item{
        display:flex;
        flex-direction:column;
    }

    .ob-summary-label{
        color:var(--muted);
        font-size:12px;
        font-weight:700;
        text-transform:uppercase;
        letter-spacing:0.5px;
        margin-bottom:6px;
    }

    .ob-summary-value{
        color:var(--text);
        font-weight:700;
        font-size:16px;
    }

    .search-filter-wrap{
        margin-bottom:20px;
    }

    .search-input{
        width:100%;
        max-width:400px;
        padding:10px 14px;
        border:1px solid var(--line);
        border-radius:8px;
        font-size:14px;
        color:var(--text);
    }

    .search-input:focus{
        outline:none;
        border-color:var(--blue);
        box-shadow:0 0 0 3px rgba(37,99,235,.1);
    }

    .no-results{
        text-align:center;
        padding:60px 20px;
        color:var(--muted);
    }

    .no-results-icon{
        font-size:48px;
        margin-bottom:12px;
        opacity:0.5;
    }

    .no-results-text{
        margin:0;
        font-size:16px;
    }
</style>

<div class="toolbar">
    <h2 style="margin:0;">Outbound Items</h2>
    <a class="btn-link" href="{{ route('outbound.create') }}">Add Outbound</a>
</div>
@php
    $pendingRequests = class_exists(\App\Models\StockRequest::class) ? \App\Models\StockRequest::where('status','pending')->count() : 0;
    $pendingPR = class_exists(\App\Models\PasswordResetRequest::class) ? \App\Models\PasswordResetRequest::where('status','pending')->count() : 0;
    $totalPending = $pendingRequests + $pendingPR;
@endphp

{{-- Outbound-specific notification button removed to avoid duplicate notifications. Main notifications partial is used instead. --}}

<div class="search-filter-wrap">
    <input type="text" id="searchInput" class="search-input" placeholder="Search by client, office, or item...">
</div>

@php
    // Group outbounds by date first, then by client_id and office
    $groupedByDate = $outbounds->groupBy(function($item) {
        return $item->deducted_at?->format('Y-m-d') ?? $item->created_at?->format('Y-m-d') ?? 'No Date';
    });
@endphp

@forelse($groupedByDate as $dateKey => $dateGroup)
    <h3 style="margin-top:20px; margin-bottom:12px; color:var(--text); font-size:16px; font-weight:700;">
        {{ $dateKey !== 'No Date' ? \Carbon\Carbon::parse($dateKey)->format('F d, Y') : 'No Date' }}
    </h3>

    @php
        // Group by client and office within each date
        $grouped = $dateGroup->groupBy(function($item) {
            return $item->client_id . '-' . $item->office;
        });
    @endphp

    @forelse($grouped as $groupKey => $group)
        @php
            $firstRow = $group->first();
            $obid = 'ob-' . $firstRow->client_id . '-' . str_replace(' ', '-', strtolower($firstRow->office)) . '-' . $dateKey;
            $clientName = $firstRow->client?->name ?? 'Unknown Client';
            $clientOffice = $firstRow->office ?? '—';
            $groupDate = $firstRow->deducted_at?->format('M d, Y') ?? $firstRow->created_at?->format('M d, Y') ?? '—';
        @endphp

        <div class="ob-card" data-client="{{ strtolower($clientName) }}" data-office="{{ strtolower($clientOffice) }}">
            <div class="ob-header" onclick="toggleOb('{{ $obid }}')">
                <div style="flex:1;">
                    <div class="ob-title">{{ $clientName }}</div>
                    <div class="ob-sub">{{ $clientOffice }} • {{ $group->count() }} item{{ $group->count() !== 1 ? 's' : '' }}</div>
                </div>

                <div class="ob-header-right">
                    <div class="ob-count" style="text-align:right;">
                        <div class="ob-date" style="font-weight:700;">{{ $groupDate }}</div>
                        <div style="font-size:13px;color:var(--muted);">{{ $group->count() }} item{{ $group->count() !== 1 ? 's' : '' }}</div>
                    </div>
                    <div class="ob-toggle">Click to expand</div>
                </div>
            </div>

            <div id="{{ $obid }}" class="ob-body">
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th style="min-width:120px;">Stock ID</th>
                                <th>Description</th>
                                <th style="min-width:100px;">Unit</th>
                                <th style="min-width:100px;">Quantity</th>
                                <th style="min-width:120px;">Approval</th>
                                <th style="min-width:120px;">Status</th>
                                <th style="min-width:140px;">Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($group as $row)
                                @php
                                    $status = $row->status ?? 'released';
                                    $approval = $row->approval ?? 'pending';
                                    $stockId = $row->stock?->id_no ?? $row->stock_id;
                                    $description = $row->stock?->description ?? $row->description ?? '—';
                                @endphp
                                <tr>
                                    <td><b>{{ $stockId }}</b></td>
                                    <td>{{ $description }}</td>
                                    <td>{{ $row->stock?->unit ?? '—' }}</td>
                                    <td>{{ $row->total ?? '—' }}</td>
                                    <td>
                                        <span class="pill {{ $approval === 'approved' ? 'green' : 'orange' }}">
                                            {{ ucfirst($approval) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="pill {{ $status === 'released' ? 'green' : 'orange' }}">
                                            Released
                                        </span>
                                    </td>
                                    <td>{{ $row->deducted_at?->format('h:i A') ?? $row->created_at?->format('h:i A') ?? '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="ob-summary">
                    <div class="ob-summary-grid">
                        <div class="ob-summary-item">
                            <span class="ob-summary-label">Client Name</span>
                            <span class="ob-summary-value">{{ $clientName }}</span>
                        </div>
                        <div class="ob-summary-item">
                            <span class="ob-summary-label">Office/Department</span>
                            <span class="ob-summary-value">{{ $clientOffice }}</span>
                        </div>
                        <div class="ob-summary-item">
                            <span class="ob-summary-label">Total Items</span>
                            <span class="ob-summary-value">{{ $group->count() }}</span>
                        </div>
                        <div class="ob-summary-item">
                            <span class="ob-summary-label">Total Quantity</span>
                            <span class="ob-summary-value">{{ $group->sum('total') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <p style="color:var(--muted); margin-bottom:20px;">No records for this date.</p>
    @endforelse
@empty
    <div class="no-results">
        <div class="no-results-icon">📦</div>
        <p class="no-results-text">No outbound records found.</p>
    </div>
@endforelse

<script>
function toggleOb(id){
    const el = document.getElementById(id);
    if(!el) return;
    el.classList.toggle('open');
}

const searchInput = document.getElementById('searchInput');

function filterCards(){
    const search = searchInput.value.toLowerCase();

    document.querySelectorAll('.ob-card').forEach(card => {
        const client = card.dataset.client;
        const office = card.dataset.office;

        const matchesSearch = !search || client.includes(search) || office.includes(search);

        card.style.display = matchesSearch ? '' : 'none';
    });
}

searchInput.addEventListener('input', filterCards);
</script>
@endsection
