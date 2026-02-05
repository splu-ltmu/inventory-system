@extends('layouts.app')

@php
  $brand = 'Inventory System';
  $pageTitle = 'Client Portal';
  $pageSubtitle = 'Browse stocks, submit requests, and track delivery/received status.';
@endphp

@section('sidebar')
    <a href="{{ route('client.dashboard') }}" class="{{ request()->is('client') ? 'active' : '' }}">
        Dashboard <small>Home</small>
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
@endsection

@section('content')
    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap:12px;">
        <div style="padding:14px; border:1px solid rgba(255,255,255,.08); border-radius:14px; background:rgba(255,255,255,.02);">
            <div style="color:#9ca3af; font-size:12px;">Quick Action</div>
            <div style="font-weight:700; margin-top:6px;">View Available Stocks</div>
            <p style="color:#9ca3af; font-size:12px; margin:10px 0 0;">
                Browse items and request using the modal form.
            </p>
            <a href="{{ route('client.stocks') }}" style="display:inline-block; margin-top:10px; color:#3b82f6; text-decoration:none;">
                Open → 
            </a>
        </div>

        <div style="padding:14px; border:1px solid rgba(255,255,255,.08); border-radius:14px; background:rgba(255,255,255,.02);">
            <div style="color:#9ca3af; font-size:12px;">Track</div>
            <div style="font-weight:700; margin-top:6px;">My Requests</div>
            <p style="color:#9ca3af; font-size:12px; margin:10px 0 0;">
                Check status: Pending → Processing → Approved → Ready To Receive → Received.
            </p>
            <a href="{{ route('client.requests') }}" style="display:inline-block; margin-top:10px; color:#22c55e; text-decoration:none;">
                Track → 
            </a>
        </div>

        <div style="padding:14px; border:1px solid rgba(255,255,255,.08); border-radius:14px; background:rgba(255,255,255,.02);">
            <div style="color:#9ca3af; font-size:12px;">Reminder</div>
            <div style="font-weight:700; margin-top:6px;">Confirm Received</div>
            <p style="color:#9ca3af; font-size:12px; margin:10px 0 0;">
                If your status is <b>Ready To Receive</b>, you can confirm once you receive it.
            </p>
            <a href="{{ route('client.requests', ['tab' => 'on_delivery']) }}" style="display:inline-block; margin-top:10px; color:#eab308; text-decoration:none;">
                Go to Ready To Receive → 
            </a>
        </div>
    </div>

    
@endsection
