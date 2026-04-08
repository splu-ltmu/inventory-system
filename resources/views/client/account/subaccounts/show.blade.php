@extends('layouts.app')

@php
  $brand = 'Inventory System';
  $pageTitle = 'Subaccount Details';
  $pageSubtitle = 'Track members and distributed inventory by subaccount.';
@endphp

@section('sidebar')
    <a href="{{ route('client.account.subaccounts.show', $subaccount) }}" class="{{ request()->is('client/account/subaccounts/*') ? 'active' : '' }}">
        Subaccount Details <small>My Subaccount</small>
    </a>

    <a href="{{ route('client.account') }}" class="{{ request()->is('client/account*') && !request()->is('client/account/subaccounts*') ? 'active' : '' }}">
        Account Settings <small>Email & Password</small>
    </a>
@endsection

@section('content')
<style>
    .account-container{ max-width: 980px; margin: 24px auto; padding: 0 16px; }
    .card { background: var(--surface); border:1px solid var(--line); border-radius:18px; margin-bottom:18px; overflow:hidden; }
    .card-header { display:flex; justify-content:space-between; align-items:center; padding:18px; background:rgba(37,99,235,.08); }
    .card-header h2 { margin:0; font-size:18px; }
    .card-body { padding:18px; }
    .grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:18px; }
    .form-group { margin-bottom:14px; }
    .form-group label { display:block; margin-bottom:8px; font-weight:700; color:var(--text); }
    .form-group input, .form-group select { width:100%; padding:10px; border:1px solid var(--line); border-radius:10px; font-size:14px; }
    .btn-primary { display:inline-flex; align-items:center; justify-content:center; padding:10px 18px; border:none; border-radius:10px; background:#2563eb; color:#fff; font-weight:700; cursor:pointer; }
    .btn-primary:hover{ background:#1d4ed8; }
    .table-wrap{ overflow-x:auto; }
    table{ width:100%; border-collapse:collapse; margin-top:16px; }
    th,td{ padding:12px; border:1px solid var(--line); text-align:left; }
    th{ background:rgba(37,99,235,.06); }
    .pill{ display:inline-flex; padding:4px 10px; border-radius:999px; background:rgba(37,99,235,.08); color:var(--blue); font-size:12px; font-weight:700; }
    .alert{ padding:14px 16px; border-radius:12px; margin-bottom:18px; }
    .alert-success{ background:rgba(22,163,74,.1); border:1px solid rgba(22,163,74,.2); color:#166534; }
    .alert-error{ background:rgba(220,38,38,.1); border:1px solid rgba(220,38,38,.2); color:#991b1b; }
</style>

<div class="account-container">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    <div class="card">
        <div class="card-header">
            <div>
                <h2>{{ $subaccount->name }}</h2>
                <div style="color: var(--muted); font-size:13px; margin-top:4px;">{{ $subaccount->description ?: 'No description added.' }}</div>
                <div style="color: var(--muted); font-size:13px; margin-top:4px;">Login Email: {{ optional($subaccount->user)->email ?? 'Not configured' }}</div>
            </div>
            <a href="{{ route('client.account') }}" class="btn-primary">Back to Account</a>
        </div>
        <div class="card-body">
            <div class="grid-2">
                <div>
                    <div class="pill">Members: {{ $subaccount->members()->count() }}</div>
                </div>
                <div style="text-align:right;">Created {{ $subaccount->created_at->format('M d, Y') }}</div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h2>Add Member</h2>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('client.account.subaccounts.members.store', $subaccount) }}">
                @csrf
                <div class="grid-2">
                    <div class="form-group">
                        <label for="member_name">Member Name</label>
                        <input type="text" id="member_name" name="name" required value="{{ old('name') }}">
                        @error('name')<span style="color:#dc2626;font-size:12px;">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label for="member_email">Member Email</label>
                        <input type="email" id="member_email" name="email" placeholder="Optional email" value="{{ old('email') }}">
                        @error('email')<span style="color:#dc2626;font-size:12px;">{{ $message }}</span>@enderror
                    </div>
                </div>
                <button type="submit" class="btn-primary">Add Member</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h2>Distribute Approved Inventory</h2>
        </div>
        <div class="card-body">
            @if($allocatedItems->isEmpty())
                <p style="color: var(--muted);">No items have been allocated to this subaccount yet.</p>
            @elseif($members->isEmpty())
                <p style="color: var(--muted);">Add a member first to allocate items.</p>
            @else
                <form method="POST" action="{{ route('client.account.subaccounts.distributions.store', $subaccount) }}">
                    @csrf
                    <div class="grid-2">
                        <div class="form-group">
                            <label for="member_id">Member</label>
                            <select id="member_id" name="member_id" required>
                                @foreach($members as $member)
                                    <option value="{{ $member->id }}">{{ $member->name }} @if($member->email) ({{ $member->email }}) @endif</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="stock_request_item_id">Allocated Item</label>
                            <select id="stock_request_item_id" name="stock_request_item_id" required>
                                @foreach($allocatedItems as $allocation)
                                    @php
                                        $item = $allocation->stockRequestItem;
                                        $remaining = $allocation->remaining_qty;
                                        $itemLabel = $item->stock->description ?? $item->stock->name ?? 'Item';
                                    @endphp
                                    <option value="{{ $item->id }}">{{ $itemLabel }} — allocated {{ $allocation->allocated_qty }}, remaining {{ $remaining }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="grid-2">
                        <div class="form-group">
                            <label for="distributed_qty">Quantity to Allocate</label>
                            <input type="number" id="distributed_qty" name="distributed_qty" min="1" required value="{{ old('distributed_qty', 1) }}">
                            @error('distributed_qty')<span style="color:#dc2626;font-size:12px;">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <button type="submit" class="btn-primary">Allocate Item</button>
                </form>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h2>Member Supply Overview</h2>
        </div>
        <div class="card-body">
            @if($members->isEmpty())
                <p style="color: var(--muted);">There are no members yet.</p>
            @else
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Member</th>
                                <th>Distributed Items</th>
                                <th>Total Allocated</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($members as $member)
                                <tr>
                                    <td>{{ $member->name }}@if($member->email) <span style="color:var(--muted); font-size:12px; display:block;">{{ $member->email }}</span>@endif</td>
                                    <td>
                                        @if($member->distributions->isEmpty())
                                            <span style="color:var(--muted);">No items assigned</span>
                                        @else
                                            <ul style="margin:0; padding-left:18px;">
                                                @foreach($member->distributions as $distribution)
                                                    <li>{{ $distribution->stockRequestItem->stock->description ?? $distribution->stockRequestItem->stock->name ?? 'Item' }}: {{ $distribution->distributed_qty }}</li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </td>
                                    <td>{{ $member->distributions->sum('distributed_qty') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
