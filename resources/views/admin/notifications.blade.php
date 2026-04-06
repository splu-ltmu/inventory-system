@extends('layouts.app')

@php
  $brand = 'Inventory System';
  $pageTitle = 'Notifications';
  $pageSubtitle = 'View all pending requests, password resets, and stock alerts.';
@endphp

@section('sidebar')
    @include('partials.admin-sidebar')
@endsection

@section('content')
    <h2>Notifications</h2>

    <section style="margin-top:12px;">
        <h3>Pending Requests</h3>
        @if($pendingRequests->isEmpty())
            <div style="color:var(--muted);">No pending requests.</div>
        @else
            <table style="width:100%; margin-top:8px; border-collapse:collapse;">
                <thead>
                    <tr style="background:var(--panel2); text-align:left;"><th style="padding:8px;">Request #</th><th style="padding:8px;">Client</th><th style="padding:8px;">Items</th><th style="padding:8px;">Submitted</th><th style="padding:8px;">Action</th></tr>
                </thead>
                <tbody>
                @foreach($pendingRequests as $r)
                    <tr>
                        <td style="padding:8px;"> <a href="/admin/requests#request-{{ $r->id }}" style="color:var(--blue); font-weight:700;">#{{ $r->id }}</a></td>
                        <td style="padding:8px;">{{ $r->client->name ?? '—' }}</td>
                        <td style="padding:8px;">{{ $r->items->count() ?? '—' }}</td>
                        <td style="padding:8px;">{{ $r->created_at->format('M d, Y h:i A') }}</td>
                        <td style="padding:8px;"><a href="/admin/requests" style="color:var(--blue);">Open Requests</a></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    </section>

    <section style="margin-top:18px;">
        <h3>Password Reset Requests</h3>
        @if($pendingPasswordResets->isEmpty())
            <div style="color:var(--muted);">No password reset requests.</div>
        @else
            <ul>
                @foreach($pendingPasswordResets as $p)
                    <li><a href="/admin/password-reset" style="color:var(--blue);">{{ $p->email ?? '—' }}</a> — <span style="color:var(--muted);">{{ $p->created_at->diffForHumans() }}</span></li>
                @endforeach
            </ul>
        @endif
    </section>

    <section style="margin-top:18px;">
        <h3>Stock Alerts</h3>
        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap:12px;">
            <div>
                <strong>Low stock</strong>
                <ul style="margin-top:8px;">
                    @forelse($lowStock as $s)
                        <li><a href="/admin/stocks" style="color:var(--blue);">{{ $s->name }}</a> — <span style="color:var(--muted);">{{ $s->stock }} left</span></li>
                    @empty
                        <li style="color:var(--muted);">None</li>
                    @endforelse
                </ul>
            </div>
            <div>
                <strong>Out of stock</strong>
                <ul style="margin-top:8px;">
                    @forelse($outStock as $s)
                        <li><a href="/admin/stocks" style="color:var(--blue);">{{ $s->name }}</a></li>
                    @empty
                        <li style="color:var(--muted);">None</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </section>

@endsection
