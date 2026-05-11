@extends('layouts.app')

@php
  $brand = 'Inventory System';
  $pageTitle = 'Notification Preferences';
@endphp

@section('sidebar')
    @include('partials.admin-sidebar')
@endsection

@section('content')
    <h2>Notification Preferences</h2>
    
    <p style="color:var(--muted); margin-bottom:24px;">
        Customize which notifications you want to see and receive via email. Toggle notifications on/off based on your preferences.
    </p>

    <form action="{{ route('admin.notification-preferences.update') }}" method="POST">
        @csrf
        @method('PUT')
        
        <div style="display:grid; gap:16px;">
            @foreach($preferences as $type => $preference)
                <div style="border:1px solid var(--border); border-radius:8px; padding:16px; background:var(--panel1);">
                    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:12px;">
                        <h4 style="margin:0; color:var(--text);">{{ $preference['label'] }}</h4>
                        <div style="display:flex; gap:12px; align-items:center;">
                            <label style="display:flex; align-items:center; gap:6px; cursor:pointer;">
                                <input type="checkbox" 
                                       name="preferences[{{ $type }}][enabled]" 
                                       value="1"
                                       {{ $preference['enabled'] ? 'checked' : '' }}
                                       onchange="this.closest('form').submit()">
                                <span style="font-size:14px;">Show in notifications</span>
                            </label>
                            
                            <label style="display:flex; align-items:center; gap:6px; cursor:pointer;">
                                <input type="checkbox" 
                                       name="preferences[{{ $type }}][email_enabled]" 
                                       value="1"
                                       {{ $preference['email_enabled'] ? 'checked' : '' }}
                                       onchange="this.closest('form').submit()">
                                <span style="font-size:14px;">Email alerts</span>
                            </label>
                        </div>
                    </div>
                    
                    <div style="font-size:13px; color:var(--muted);">
                        @switch($type)
                            @case('pending_requests')
                                Get notified when clients submit new stock requests that need your approval.
                                @break
                            @case('password_resets')
                                Get notified when users request password resets.
                                @break
                            @case('low_stock')
                                Get notified when inventory items are running low (≤5 units).
                                @break
                            @case('out_of_stock')
                                Get notified when items are completely out of stock.
                                @break
                            @case('urgent_outbounds')
                                Get notified when urgent outbound requests are submitted and need immediate attention.
                                @break
                            @case('expiring_items')
                                Get notified when items are approaching their expiry date (within 7 days).
                                @break
                            @case('new_clients')
                                Get notified when new clients register in the system.
                                @break
                            @case('system_health')
                                Get notified about system issues like failed jobs or other health alerts.
                                @break
                        @endswitch
                    </div>
                </div>
            @endforeach
        </div>

        <div style="margin-top:24px; padding:16px; background:var(--panel2); border-radius:8px;">
            <h4 style="margin:0 0 8px 0;">Email Notification Settings</h4>
            <p style="margin:0; font-size:14px; color:var(--muted);">
                Email notifications will be sent to: <strong>{{ auth()->user()->email }}</strong>
            </p>
            <p style="margin:8px 0 0 0; font-size:13px; color:var(--muted);">
                Note: Make sure your email configuration is properly set up in the system settings to receive email notifications.
            </p>
        </div>
    </form>

    @if(session('success'))
        <div style="position:fixed; top:20px; right:20px; background:var(--green); color:white; padding:12px 16px; border-radius:8px; box-shadow:0 4px 12px rgba(0,0,0,0.15); z-index:1000;">
            {{ session('success') }}
        </div>
        
        <script>
            setTimeout(() => {
                const successMsg = document.querySelector('div[style*="position:fixed"]');
                if(successMsg) successMsg.remove();
            }, 3000);
        </script>
    @endif
@endsection
