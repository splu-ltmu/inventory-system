@php
    $pendingRequestsCount = class_exists(\App\Models\StockRequest::class)
        ? \App\Models\StockRequest::where('status', 'pending')->count()
        : 0;
    $pendingPR = class_exists(\App\Models\PasswordResetRequest::class)
        ? \App\Models\PasswordResetRequest::where('status', 'pending')->count()
        : 0;
@endphp

<style>
    /* small badge used in the sidebar for counts */
    .sidebar-count{
        display:inline-block;
        min-width:28px;
        padding:3px 8px;
        border-radius:999px;
        background:#ef4444;
        color:#fff;
        font-weight:700;
        font-size:12px;
        text-align:center;
    }
</style>

<a href="{{ route('admin.dashboard') }}" class="{{ request()->is('admin') ? 'active' : '' }}">
    Dashboard <small>Home</small>
</a>

<a href="{{ route('admin.summary') }}" class="{{ request()->is('admin/summary*') ? 'active' : '' }}">
    Summary <small>Transactions</small>
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
    Requests <small>Workflow</small>
    @if($pendingRequestsCount)
        <span class="sidebar-count">{{ $pendingRequestsCount }}</span>
    @endif
</a>

<a href="/admin/password-reset" class="{{ request()->is('admin/password-reset*') ? 'active' : '' }}">
    Password Reset <small>Requests</small>
    @if($pendingPR)
        <span class="sidebar-count">{{ $pendingPR }}</span>
    @endif
</a>

<a href="{{ route('admin.users.index') }}" class="{{ request()->is('admin/users*') ? 'active' : '' }}">
    Client Accounts <small>Create/Manage</small>
</a>
