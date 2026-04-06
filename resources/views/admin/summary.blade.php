@extends('layouts.app')

@php
  $brand = 'Inventory System';
  $pageTitle = 'Summary';
  $pageSubtitle = 'Transactions (requests) across all clients.';
@endphp

@section('sidebar')
    @include('partials.admin-sidebar')
@endsection

@section('content')
    <style>
        .cards-grid{ display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:12px; margin-bottom:22px; }
        .card{ 
            border:1px solid var(--line); 
            border-radius:14px; 
            background:#ffffff; 
            box-shadow:0 10px 28px rgba(15,23,42,.06); 
            overflow:hidden;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .card:hover{
            transform: translateY(-4px);
            box-shadow: 0 20px 40px rgba(15,23,42,.15);
            border-color: rgba(37,99,235,.2);
        }
        .card-head{ 
            padding:14px 16px; 
            background:linear-gradient(135deg, rgba(37,99,235,.05), rgba(99,102,241,.02));
            border-bottom:1px solid var(--line); 
            display:flex; 
            justify-content:space-between; 
            gap:12px; 
            cursor:pointer;
            transition: all 0.3s ease;
        }
        .card:hover .card-head{ 
            background:linear-gradient(135deg, rgba(37,99,235,.08), rgba(99,102,241,.04));
            border-bottom-color: rgba(37,99,235,.15);
        }
        .card-body{ 
            padding:16px;
            background:linear-gradient(135deg, #fafbfc 0%, rgba(99,102,241,.02) 100%);
            transition: all 0.3s ease;
        }
        .card-body.hidden{ display:none; }
        .card-title{ font-weight:800; font-size:16px; }
        .card-sub{ color:var(--muted); font-size:13px; margin-top:4px; }

        .pill{ 
            display:inline-block; 
            padding:4px 10px; 
            border-radius:999px; 
            font-size:12px; 
            font-weight:700; 
            border:1px solid var(--line);
            transition: all 0.3s ease;
        }
        .pill:hover{
            transform: scale(1.05);
        }
        .pill.pending{ 
            background:linear-gradient(180deg,var(--orange-soft),#fff7ed); 
            color:var(--orange); 
            border-color:rgba(249,115,22,.2);
        }
        .pill.pending:hover{
            box-shadow: 0 4px 12px rgba(249,115,22,.2);
        }
        .pill.approved, .pill.ready_to_receive{ 
            background:linear-gradient(180deg,#ecfdf5,#f0fdfa); 
            color:#065f46; 
            border-color:rgba(34,197,94,.2);
        }
        .pill.approved:hover, .pill.ready_to_receive:hover{
            box-shadow: 0 4px 12px rgba(34,197,94,.2);
        }
        .pill.released{ 
            background:linear-gradient(180deg,#eff6ff,#f0f9ff); 
            color:var(--blue); 
            border-color:rgba(37,99,235,.2);
        }
        .pill.released:hover{
            box-shadow: 0 4px 12px rgba(37,99,235,.2);
        }
        .pill.rejected{ 
            background:linear-gradient(180deg,#fee2e2,#fff1f2); 
            color:#991b1b; 
            border-color:rgba(244,63,94,.2);
        }
        .pill.rejected:hover{
            box-shadow: 0 4px 12px rgba(244,63,94,.2);
        }
        .pill.cancelled{ 
            background:linear-gradient(180deg,#f3f4f6,#f8fafc); 
            color:#475569; 
            border-color:rgba(226,232,240,.6);
        }
        .pill.cancelled:hover{
            box-shadow: 0 4px 12px rgba(71,81,105,.1);
        }

        .muted{ color:var(--muted); }
        .list{ list-style: disc inside; color:var(--muted); }
        .list li{ margin-bottom:6px; }
    </style>

    <div class="cards-grid">
        <div class="card">
            <div class="card-body" style="position:relative;">
                <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:12px; flex-wrap:wrap;">
                    <div>
                        <div class="card-title">Total transactions</div>
                        <div class="card-sub">{{ $requests->count() }} total</div>
                    </div>

                    <form id="filterForm" method="GET" style="display:flex; gap:10px; align-items:center; flex-wrap:wrap; margin-top:4px;">
                        <input
                            id="filterQuery"
                            type="text"
                            name="q"
                            value="{{ $q ?? '' }}"
                            placeholder="Search # or client"
                            style="padding:8px 10px; border-radius:10px; border:1px solid var(--line); background:#fff; min-width:200px;"
                        />

                        <select
                            id="filterOffice"
                            name="office"
                            style="padding:8px 10px; border-radius:10px; border:1px solid var(--line); background:#fff; min-width:180px;"
                        >
                            <option value="">All Offices</option>
                            @foreach($offices as $off)
                                <option value="{{ $off }}" {{ $off === ($office ?? '') ? 'selected' : '' }}>{{ $off }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div>
        @forelse($requests as $req)
            <div class="card status-{{ $req->status }}" style="margin-bottom:14px;">
                <div class="card-head" onclick="toggleReq('req-{{ $req->id }}')">
                    <div>
                        <div class="card-title">Request #{{ $req->id }} • {{ $req->client?->name ?? 'Unknown' }}</div>
                        <div class="card-sub">Office: {{ $req->office ?? 'Unknown' }} • Created {{ $req->created_at?->format('M d, Y @ h:i A') }}</div>
                    </div>

                    <div style="text-align:right; min-width:160px;">
                        <div style="font-size:12px; color:var(--muted);">Status</div>
                        <div style="margin-top:4px;">
                            <span class="pill {{ $req->status }}">{{ strtoupper(str_replace('_', ' ', $req->status)) }}</span>
                        </div>
                        @if($req->verification_code)
                            <div style="font-size:12px; color:var(--muted); margin-top:4px;">Code: <span style="font-weight:700; color:var(--text);">{{ $req->verification_code }}</span></div>
                        @endif
                    </div>
                </div>

                <div id="req-{{ $req->id }}" class="card-body hidden">
                    <div style="font-weight:700; margin-bottom:8px;">Items</div>
                    <ul class="list">
                        @foreach($req->items as $item)
                            <li>
                                {{ $item->stock?->description ?? 'Unknown item' }}
                                (requested: {{ $item->requested_qty }}, approved: {{ $item->approved_qty }})
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @empty
            <div class="card">
                <div class="card-body">
                    <div class="muted">No transactions found yet.</div>
                </div>
            </div>
        @endforelse
    </div>

    <script>
        function toggleReq(id){
            const el = document.getElementById(id);
            if(!el) return;
            el.classList.toggle('hidden');
        }

        // Auto-update filters without needing a button.
        (function(){
            const input = document.getElementById('filterQuery');
            const select = document.getElementById('filterOffice');
            const form = document.getElementById('filterForm');

            if (!form) return;

            let timeout;
            const submit = () => {
                const params = new URLSearchParams();
                const q = input?.value?.trim();
                const office = select?.value?.trim();

                if (q) params.set('q', q);
                if (office) params.set('office', office);

                const url = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
                window.location.href = url;
            };

            input?.addEventListener('input', () => {
                clearTimeout(timeout);
                timeout = setTimeout(submit, 500);
            });

            select?.addEventListener('change', submit);
        })();
    </script>
@endsection
