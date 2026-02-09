@extends('layouts.app')

@php
  $brand = 'Inventory System';
  $pageTitle = 'Account Settings';
  $pageSubtitle = 'Manage your account email and password.';
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
<style>
    .account-container{ max-width:700px; }
    
    .settings-card{
        background: white;
        border: 1px solid var(--line);
        border-radius: 14px;
        margin-bottom: 14px;
        box-shadow: 0 1px 2px rgba(15,23,42,.06);
    }
    
    .card-header{
        padding: 14px 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        cursor: pointer;
        background: var(--blue-soft);
        border-bottom: 1px solid var(--line);
        border-radius: 14px 14px 0 0;
    }
    
    .card-header:hover{
        background: rgba(37,99,235,.12);
    }
    
    .card-header h3{
        margin: 0;
        color: var(--text);
        font-size: 18px;
        font-weight: 700;
    }
    
    .card-toggle{
        color: var(--muted);
        font-size: 12px;
        font-weight: 600;
    }
    
    .card-body{
        display: none;
        padding: 16px;
        background: white;
        border-radius: 0 0 14px 14px;
    }
    
    .card-body.open{ display: block; }

    .form-group{
        margin-bottom: 16px;
    }
    .form-group label{
        display: block;
        margin-bottom: 8px;
        color: var(--text);
        font-weight: 700;
        font-size: 14px;
    }
    .form-group input{
        width: 100%;
        padding: 10px;
        border: 1px solid var(--line);
        border-radius: 8px;
        font-size: 14px;
        background: white;
        color: var(--text);
        transition: border-color .2s;
        box-sizing: border-box;
    }
    .form-group input:focus{
        outline: none;
        border-color: var(--blue);
        box-shadow: 0 0 0 3px rgba(37,99,235,.1);
    }
    
    .form-group input:disabled{
        background: var(--line);
        color: var(--muted);
        cursor: not-allowed;
    }

    .btn-submit{
        display: inline-block;
        padding: 10px 20px;
        border-radius: 8px;
        border: none;
        background: var(--blue);
        color: white;
        font-weight: 700;
        font-size: 14px;
        cursor: pointer;
        transition: background .2s;
    }
    .btn-submit:hover{
        background: rgba(37,99,235,.9);
    }

    .alert{
        padding: 12px;
        border-radius: 8px;
        margin-bottom: 16px;
        border: 1px solid;
    }
    .alert-success{
        background: rgba(22,163,74,.1);
        border-color: rgba(22,163,74,.3);
        color: #16a34a;
    }
    .alert-error{
        background: rgba(220,38,38,.1);
        border-color: rgba(220,38,38,.3);
        color: #dc2626;
    }

    .error-list{
        margin: 0;
        padding-left: 20px;
    }
    .error-list li{
        margin: 4px 0;
    }
    
    .info-section{
        display: grid;
        gap: 12px;
    }
    
    .info-item{
        border-bottom: 1px solid var(--line);
        padding-bottom: 12px;
    }
    
    .info-item:last-child{
        border-bottom: none;
        padding-bottom: 0;
    }
    
    .info-label{
        color: var(--muted);
        font-size: 12px;
        font-weight: 700;
    }
    
    .info-value{
        margin: 4px 0 0;
        color: var(--text);
        font-weight: 700;
    }
    
    /* Confirmation modal */
    #confirmModal{ display:none; position:fixed; inset:0; background:rgba(0,0,0,.5); z-index:5000; align-items:center; justify-content:center; }
    #confirmModal.show{ display:flex; }
    .confirm-box{ background:#fff; border-radius:14px; box-shadow:0 10px 40px rgba(0,0,0,.25); max-width:420px; width:90%; padding:24px; }
    .confirm-box h3{ margin:0 0 8px 0; font-size:18px; color:#0f172a; font-weight:800; }
    .confirm-box p{ margin:0 0 20px 0; color:#475569; font-size:14px; line-height:1.5; }
    .confirm-buttons{ display:flex; gap:10px; justify-content:flex-end; }
    .confirm-btn-cancel{ padding:10px 16px; border-radius:10px; border:none; background:#e2e8f0; color:#0f172a; font-weight:700; cursor:pointer; font-size:14px; }
    .confirm-btn-confirm{ padding:10px 16px; border-radius:10px; border:none; background:#2563eb; color:#fff; font-weight:700; cursor:pointer; font-size:14px; }
</style>

<!-- Confirmation Modal -->
<div id="confirmModal">
    <div class="confirm-box">
        <h3 id="modal-title">Confirm</h3>
        <p id="modal-text">Are you sure?</p>
        <div class="confirm-buttons">
            <button type="button" class="confirm-btn-cancel" onclick="closeConfirmModal()">Cancel</button>
            <button type="button" class="confirm-btn-confirm" onclick="submitConfirmedForm()">Confirm</button>
        </div>
    </div>
</div>

<div class="account-container">
    <!-- Email Update Card -->
    <div class="settings-card">
        <div class="card-header" onclick="toggleCard('email-card')">
            <h3>Update Email Address</h3>
            <span class="card-toggle">Click to expand</span>
        </div>
        
        <div id="email-card" class="card-body">
            <p style="color: var(--muted); margin: 0 0 16px;">Change the email address associated with your account.</p>
            
            <form method="POST" action="{{ route('client.account.updateEmail') }}">
                @csrf
                
                <div class="form-group">
                    <label for="current_email">Current Email</label>
                    <input type="email" id="current_email" value="{{ auth()->user()->email }}" disabled>
                </div>

                <div class="form-group">
                    <label for="email">New Email Address</label>
                    <input type="email" id="email" name="email" placeholder="Enter new email" required value="{{ old('email') }}">
                    @error('email')
                        <span style="color: var(--danger); font-size: 12px;">{{ $message }}</span>
                    @enderror
                </div>

                <button type="button" class="btn-submit" onclick="showConfirmModal(this.form, 'Update Email Address', 'Update your email address? You may need to verify the new email.')">Update Email</button>\n            </form>
        </div>
    </div>

    <!-- Password Update Card -->
    <div class="settings-card">
        <div class="card-header" onclick="toggleCard('password-card')">
            <h3>Change Password</h3>
            <span class="card-toggle">Click to expand</span>
        </div>
        
        <div id="password-card" class="card-body">
            <p style="color: var(--muted); margin: 0 0 16px;">Update your account password to keep your account secure.</p>
            
            <form method="POST" action="{{ route('client.account.updatePassword') }}">
                @csrf
                
                <div class="form-group">
                    <label for="current_password">Current Password</label>
                    <input type="password" id="current_password" name="current_password" placeholder="Enter current password" required>
                    @error('current_password')
                        <span style="color: var(--danger); font-size: 12px;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input type="password" id="new_password" name="new_password" placeholder="Enter new password (min 6 characters)" required>
                    @error('new_password')
                        <span style="color: var(--danger); font-size: 12px;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="new_password_confirmation">Confirm New Password</label>
                    <input type="password" id="new_password_confirmation" name="new_password_confirmation" placeholder="Confirm new password" required>
                </div>

                <button type="button" class="btn-submit" onclick="showConfirmModal(this.form, 'Change Password', 'Change your password? Make sure to remember the new password.')">Change Password</button>\n            </form>
        </div>
    </div>

    <!-- Account Info Card -->
    <div class="settings-card">
        <div class="card-header" onclick="toggleCard('info-card')">
            <h3>Account Information</h3>
            <span class="card-toggle">Click to expand</span>
        </div>
        
        <div id="info-card" class="card-body">
            <div class="info-section">
                <div class="info-item">
                    <div class="info-label">Full Name</div>
                    <div class="info-value">{{ auth()->user()->name }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Account Type</div>
                    <div class="info-value">{{ ucfirst(auth()->user()->role) }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Member Since</div>
                    <div class="info-value">{{ auth()->user()->created_at->format('M d, Y') }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let pendingForm = null;

function toggleCard(id){
    const el = document.getElementById(id);
    if(!el) return;
    el.classList.toggle('open');
}

function showConfirmModal(form, title, message){
    pendingForm = form;
    document.getElementById('modal-title').textContent = title;
    document.getElementById('modal-text').textContent = message;
    document.getElementById('confirmModal').classList.add('show');
}

function closeConfirmModal(){
    document.getElementById('confirmModal').classList.remove('show');
    pendingForm = null;
}

function submitConfirmedForm(){
    if(pendingForm){
        pendingForm.submit();
    }
    closeConfirmModal();
}

// Close modal on Escape key
document.addEventListener('keydown', function(e){
    if(e.key === 'Escape') closeConfirmModal();
});
</script>

@endsection
