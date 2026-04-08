@extends('layouts.app')

@php
  $brand = 'Inventory System';
  $pageTitle = 'My Inventory';
  $pageSubtitle = 'Items received from the admin.';
@endphp

@section('sidebar')
    @if(auth()->user()->role === 'subaccount' && auth()->user()->subaccount)
        <a href="{{ route('client.account.subaccounts.show', auth()->user()->subaccount) }}" class="{{ request()->is('client/account/subaccounts*') ? 'active' : '' }}">
            Subaccount Details <small>My Subaccount · New</small>
        </a>

        <a href="{{ route('client.account') }}" class="{{ request()->is('client/account') ? 'active' : '' }}">
            Account Settings <small>Email & Password · New</small>
        </a>
    @else
        <a href="{{ route('client.dashboard') }}" class="{{ request()->is('client') ? 'active' : '' }}">
            Dashboard <small>Home</small>
        </a>

        <a href="{{ route('client.summary') }}" class="{{ request()->is('client/summary*') ? 'active' : '' }}">
            Summary <small>Transactions</small>
        </a>

        <a href="{{ route('client.inventory') }}" class="{{ request()->is('client/inventory*') ? 'active' : '' }}">
            My Inventory <small>Received Items</small>
        </a>

        <a href="{{ route('client.account') }}" class="{{ request()->is('client/account*') ? 'active' : '' }}">
            Client Subaccounts <small>Create and manage subaccounts</small>
        </a>

        <a href="{{ route('client.stocks') }}" class="{{ request()->is('client/stocks*') ? 'active' : '' }}">
            Available Stocks <small>Request items</small>
        </a>

        <a href="{{ route('client.requests') }}" class="{{ request()->is('client/requests*') ? 'active' : '' }}">
            My Requests <small>Track status</small>
        </a>

        <a href="{{ route('client.account') }}" class="{{ request()->is('client/account*') ? 'active' : '' }}">
            Account Settings <small>Email & Password</small>
        </a>
    @endif
@endsection

@section('content')
    <style>
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 16px;
        }
        .table th, .table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid var(--line);
        }
        .table th {
            background: rgba(255,255,255,.02);
            font-weight: 700;
        }
        .table tr:hover {
            background: rgba(255,255,255,.01);
        }
        .muted { color: var(--muted); }
    </style>

    <div style="margin-top:16px;">
        <h2 style="margin-bottom:14px;">My Inventory</h2>

        @if($outbounds->count() > 0)
            <table class="table">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Quantity</th>
                        <th>Office</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($outbounds as $outbound)
                        <tr>
                            <td>{{ $outbound->stock->description ?? 'Unknown' }}</td>
                            <td>{{ $outbound->total }}</td>
                            <td>{{ $outbound->office }}</td>
                            <td>{{ strtoupper(str_replace(' ', ' ', $outbound->status)) }}</td>
                            <td>{{ $outbound->created_at->format('M d, Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="muted">You have no received items yet.</div>
        @endif
    </div>
@endsection