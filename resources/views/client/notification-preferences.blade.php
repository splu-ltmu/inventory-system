@extends('layouts.app')

@php
  $brand = 'Inventory System';
  $pageTitle = 'Notification Preferences';
@endphp

@section('sidebar')
    @include('client.sidebar')
@endsection

@section('content')
    <h2>Notification Preferences</h2>
    
    <p style="color:var(--muted); margin-bottom:24px;">
        Customize which notifications you want to see. Toggle notifications on/off based on your preferences.
    </p>

    <form action="{{ route('client.notification-preferences.update') }}" method="POST">
        @csrf
        @method('PUT')
        
        <div style="display:grid; gap:16px;">
            @php
                $clientTypes = [
                    'request_updates' => 'Request Status Updates',
                    'inventory_alerts' => 'Inventory Alerts', 
                    'member_activity' => 'Member Activity',
                ];
                
                // Get current preferences (simplified approach)
                $currentPreferences = [
                    'request_updates' => true,
                    'inventory_alerts' => true,
                    'member_activity' => true,
                ];
            @endphp
            
            @foreach($clientTypes as $type => $label)
                @if(isset($currentPreferences[$type]))
                    <div style="border:1px solid var(--border); border-radius:8px; padding:16px; background:var(--panel1);">
                        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:12px;">
                            <h4 style="margin:0; color:var(--text);">{{ $label }}</h4>
                            <label style="display:flex; align-items:center; gap:6px; cursor:pointer;">
                                <input type="checkbox" 
                                       name="preferences[{{ $type }}][enabled]" 
                                       value="1"
                                       {{ $currentPreferences[$type] ? 'checked' : '' }}
                                       onchange="this.closest('form').submit()">
                                <span style="font-size:14px;">Enable notifications</span>
                            </label>
                        </div>
                        
                        <div style="font-size:13px; color:var(--muted);">
                            @switch($type)
                                @case('request_updates')
                                    Get notified when your requests are approved, rejected, or need attention.
                                    @break
                                @case('inventory_alerts')
                                    Get notified when your inventory items are running low.
                                    @break
                                @case('member_activity')
                                    Get notified when new members are added to your team.
                                    @break
                            @endswitch
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        <div style="margin-top:24px; padding:16px; background:var(--panel2); border-radius:8px;">
            <h4 style="margin:0 0 8px 0;">About Client Notifications</h4>
            <p style="margin:0; font-size:14px; color:var(--muted);">
                Client notifications help you stay informed about your inventory requests, stock levels, and team activities. 
                You'll receive real-time updates in the notification bell icon and can view detailed history on this page.
            </p>
            <p style="margin:8px 0 0 0; font-size:13px; color:var(--muted);">
                <strong>Note:</strong> As a client, you'll receive notifications for all your requests, 
                inventory changes, and member management activities.
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
