@extends('layouts.app')

@php
  $brand = 'Inventory System';
  $pageTitle = 'Admin Panel';
  $pageSubtitle = 'Manage categories, stocks, inbound/outbound, and requests.';
@endphp

@section('sidebar')
    <a href="{{ route('admin.dashboard') }}" class="{{ request()->is('admin') ? 'active' : '' }}">
        Dashboard <small>Home</small>
    </a>

    <a href="/admin/categories" class="{{ request()->is('admin/categories*') ? 'active' : '' }}">
        Categories <small>Manage</small>
    </a>

    <a href="/admin/stocks" class="{{ request()->is('admin/stocks*') ? 'active' : '' }}">
        Stocks <small>Manage</small>
    </a>

    <a href="/admin/inbound" class="{{ request()->is('admin/inbound*') ? 'active' : '' }}">
        Inbound <small>Records</small>
    </a>

    <a href="/admin/outbound" class="{{ request()->is('admin/outbound*') ? 'active' : '' }}">
        Outbound <small>Records</small>
    </a>

    <a href="/admin/requests" class="{{ request()->is('admin/requests*') ? 'active' : '' }}">
        Requests <small>Approve/Reject</small>
    </a>

    <a href="/admin/password-reset" class="{{ request()->is('admin/password-reset*') ? 'active' : '' }}">
        Password Reset <small>Requests</small>
    </a>

    <a href="{{ route('admin.users.index') }}" class="{{ request()->is('admin/users*') ? 'active' : '' }}">
        Client Accounts <small>Create/Manage</small>
    </a>
@endsection

@section('content')
    <h2 style="margin:0 0 10px;">Welcome, {{ auth()->user()->name }} 👋</h2>

    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap:12px; margin-top:14px;">
        <div style="padding:14px; border:1px solid rgba(255,255,255,.08); border-radius:14px; background:rgba(255,255,255,.02);">
            <div style="color:#9ca3af; font-size:12px;">Quick Access</div>
            <div style="font-weight:700; margin-top:6px;">Stocks</div>
            <a href="/admin/stocks" style="display:inline-block; margin-top:10px; color:#22c55e; text-decoration:none;">Open →</a>
        </div>

        <div style="padding:14px; border:1px solid rgba(255,255,255,.08); border-radius:14px; background:rgba(255,255,255,.02); position:relative;">
            <div style="color:#9ca3af; font-size:12px;">Quick Access</div>
            <div style="font-weight:700; margin-top:6px; display:flex; align-items:center; gap:8px;">
                Requests
                @if($pendingRequests > 0)
                    <span style="display:inline-block; background:#ef4444; color:white; font-size:11px; font-weight:700; padding:2px 8px; border-radius:999px; min-width:24px; text-align:center;">
                        {{ $pendingRequests }}
                    </span>
                @endif
            </div>
            <a href="/admin/requests" style="display:inline-block; margin-top:10px; color:#22c55e; text-decoration:none;">Open →</a>
        </div>

        <div style="padding:14px; border:1px solid rgba(255,255,255,.08); border-radius:14px; background:rgba(255,255,255,.02);">
            <div style="color:#9ca3af; font-size:12px;">Quick Access</div>
            <div style="font-weight:700; margin-top:6px; display:flex; align-items:center; gap:8px;">
                Password Reset
                @if($pendingPasswordResets > 0)
                    <span style="display:inline-block; background:#ef4444; color:white; font-size:11px; font-weight:700; padding:2px 8px; border-radius:999px; min-width:24px; text-align:center;">
                        {{ $pendingPasswordResets }}
                    </span>
                @endif
            </div>
            <a href="/admin/password-reset" style="display:inline-block; margin-top:10px; color:#22c55e; text-decoration:none;">Open →</a>
        </div>
    </div>

    <div style="margin-top:32px; padding:20px; border:1px solid rgba(255,255,255,.08); border-radius:14px; background:rgba(255,255,255,.02);">
        <h3 style="margin:0 0 20px; color:#fff;">Category Analytics</h3>
        <p style="color:#9ca3af; font-size:13px; margin:0 0 16px;">Total availability vs. total approved requests by category</p>
        <div style="position:relative; height:400px;">
            <canvas id="categoryChart"></canvas>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('categoryChart').getContext('2d');
            const categories = @json($categoryAnalytics);
            
            const labels = categories.map(c => c.name);
            const availability = categories.map(c => c.availability);
            const requested = categories.map(c => c.requested);

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Available',
                            data: availability,
                            backgroundColor: 'rgba(34, 197, 94, 0.7)',
                            borderColor: 'rgba(34, 197, 94, 1)',
                            borderWidth: 1,
                        },
                        {
                            label: 'Outbound/Approved Request',
                            data: requested,
                            backgroundColor: 'rgba(59, 130, 246, 0.7)',
                            borderColor: 'rgba(59, 130, 246, 1)',
                            borderWidth: 1,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            labels: {
                                color: '#9ca3af',
                                font: { size: 12 }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { color: '#9ca3af' },
                            grid: { color: 'rgba(255,255,255,.05)' }
                        },
                        x: {
                            ticks: { color: '#9ca3af' },
                            grid: { color: 'rgba(255,255,255,.05)' }
                        }
                    }
                }
            });
        });
    </script>
@endsection
