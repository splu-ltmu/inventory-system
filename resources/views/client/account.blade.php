@extends('layouts.app')

@php
  $brand = 'Inventory System';
  $activeTab = request('tab', 'settings');
  $pageTitle = $activeTab === 'distribution' ? 'Item Distribution' : ($activeTab === 'subaccounts' ? 'Client Subaccounts' : 'Account Settings');
  $pageSubtitle = $activeTab === 'distribution' ? 'Distribute items to different subaccounts.' : ($activeTab === 'subaccounts' ? 'List your subaccounts and inventory distributed to each.' : 'Manage your account email and password.');
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

        <a href="{{ route('client.account', ['tab' => 'distribution']) }}" class="{{ request()->routeIs('client.account') && request('tab') === 'distribution' ? 'active' : '' }}">
            Distribution <small>Distribute to subaccounts</small>
        </a>

        <a href="{{ route('client.account', ['tab' => 'subaccounts']) }}" class="{{ request()->routeIs('client.account') && request('tab') === 'subaccounts' ? 'active' : '' }}">
            Client Subaccounts <small>Subaccounts & inventory</small>
        </a>

        <a href="{{ route('client.account') }}" class="{{ request()->routeIs('client.account') && request('tab', 'settings') !== 'subaccounts' && request('tab') !== 'distribution' ? 'active' : '' }}">
            Account Settings <small>Email & Password</small>
        </a>

        <a href="{{ route('client.stocks') }}" class="{{ request()->is('client/stocks*') ? 'active' : '' }}">
            Available Stocks <small>Request items</small>
        </a>

        <a href="{{ route('client.requests') }}" class="{{ request()->is('client/requests*') ? 'active' : '' }}">
            My Requests <small>Track status</small>
        </a>

    @endif
@endsection

@section('content')
<style>
    .account-container{
        max-width: 840px;
        margin: 24px auto;
        padding: 0 16px;
    }
    
    .settings-card{
        background: var(--surface);
        border: 1px solid var(--line);
        border-radius: 18px;
        margin-bottom: 18px;
        box-shadow: 0 10px 25px rgba(15,23,42,.08);
        overflow: hidden;
        transition: transform .2s, box-shadow .2s;
    }
    .settings-card:hover{
        transform: translateY(-2px);
        box-shadow: 0 14px 28px rgba(15,23,42,.12);
    }
    
    .card-header{
        padding: 16px 18px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        cursor: pointer;
        background: rgba(37,99,235,.08);
        border-bottom: 1px solid var(--line);
    }
    
    .card-header:hover{
        background: rgba(37,99,235,.16);
    }
    
    .card-header h3{
        margin: 0;
        color: var(--text);
        font-size: 18px;
        font-weight: 700;
    }
    
    .card-toggle{
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: var(--muted);
        font-size: 12px;
        font-weight: 600;
        letter-spacing: .2px;
    }
    .card-toggle::after{
        content: '▾';
        display: inline-block;
        transition: transform .2s ease;
    }
    .card-header.open .card-toggle::after{
        transform: rotate(-180deg);
    }
    
    .card-body{
        display: none;
        padding: 18px;
        background: rgba(255,255,255,.8);
        border-radius: 0 0 18px 18px;
        animation: fadeIn 180ms ease-out;
    }
    
    .card-body.open{ display: block; }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-4px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    

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
        transition: all 0.3s ease;
    }
    .btn-submit:hover{
        background: rgba(37,99,235,.9);
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(37,99,235,.2);
    }
    .btn-submit:active{
        transform: translateY(0);
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
    .confirm-btn-cancel{ padding:10px 16px; border-radius:10px; border:none; background:#e2e8f0; color:#0f172a; font-weight:700; cursor:pointer; font-size:14px; transition: all 0.3s ease; }
    .confirm-btn-cancel:hover{ background:#cbd5e1; transform: translateY(-2px); box-shadow: 0 6px 12px rgba(0,0,0,.1); }
    .confirm-btn-cancel:active{ transform: translateY(0); }
    .confirm-btn-confirm{ padding:10px 16px; border-radius:10px; border:none; background:#2563eb; color:#fff; font-weight:700; cursor:pointer; font-size:14px; transition: all 0.3s ease; }
    .confirm-btn-confirm:hover{ background:rgba(37,99,235,.9); transform: translateY(-2px); box-shadow: 0 6px 12px rgba(37,99,235,.2); }
    .confirm-btn-confirm:active{ transform: translateY(0); }
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
    @if($activeTab !== 'subaccounts')
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

                <button type="button" class="btn-submit" onclick="showConfirmModal(this.form, 'Update Email Address', 'Update your email address? You may need to verify the new email.')">Update Email</button>           </form>
        </div>
    </div>

    <!-- Password Update Card -->
    @if(auth()->user()->role === 'client')
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

                <button type="button" class="btn-submit" onclick="showConfirmModal(this.form, 'Change Password', 'Change your password? Make sure to remember the new password.')">Change Password</button>           </form>
        </div>
    </div>
    @endif

    <!-- Distribution Tab -->
    @if($activeTab === 'distribution' && auth()->user()->role === 'client')
    <div class="settings-card">
        <div class="card-header" onclick="toggleCard('distribution-card')">
            <h3>My Inventory</h3>
            <span class="card-toggle">Click to expand</span>
        </div>

        <div id="distribution-card" class="card-body open">
            <p style="color: var(--muted); margin: 0 0 16px;">Your approved inventory shows how much you have received, allocated to subaccounts, and how much is available in "My Inventory".</p>

            @if($approvedInventory->isEmpty())
                <p style="margin-top:20px; color: var(--muted);">No approved items are available yet.</p>
            @else
                <div style="margin-top:20px; overflow-x:auto;">
                    <table style="width:100%; border-collapse:collapse;">
                        <thead>
                            <tr>
                                <th style="padding:10px; border:1px solid var(--line); background:rgba(37,99,235,.06); text-align:left;">Item</th>
                                <th style="padding:10px; border:1px solid var(--line); background:rgba(37,99,235,.06); text-align:left;">Received</th>
                                <th style="padding:10px; border:1px solid var(--line); background:rgba(37,99,235,.06); text-align:left;">Distributed</th>
                                <th style="padding:10px; border:1px solid var(--line); background:rgba(37,99,235,.06); text-align:left;">My Inventory</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($approvedInventory as $item)
                                @php
                                    $distributed = $item->distributed_qty ?? 0;
                                    $myInventory = max(0, $item->approved_qty - $distributed);
                                @endphp
                                <tr>
                                    <td style="padding:10px; border:1px solid var(--line);">{{ $item->stock->description ?? $item->stock->name ?? 'Item' }}</td>
                                    <td style="padding:10px; border:1px solid var(--line);">{{ $item->approved_qty }}</td>
                                    <td style="padding:10px; border:1px solid var(--line);">{{ $distributed }}</td>
                                    <td style="padding:10px; border:1px solid var(--line); font-weight:700; color:#2563eb;">{{ $myInventory }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <div class="settings-card">
        <div class="card-header" onclick="toggleCard('allocate-card')">
            <h3>Allocate Items to Subaccounts</h3>
            <span class="card-toggle">Click to expand</span>
        </div>

        <div id="allocate-card" class="card-body">
            <p style="color: var(--muted); margin: 0 0 16px;">Allocate items from "My Inventory" to a subaccount. The subaccount can then distribute them to members.</p>

            @if($approvedInventory->isEmpty())
                <p style="color: var(--muted);">No items are available for allocation.</p>
            @elseif($subaccounts->isEmpty())
                <p style="color: var(--muted);">Create a subaccount first before allocating items.</p>
            @else
                <form method="POST" action="{{ route('client.account.distributeToSubaccounts') }}">
                    @csrf
                    <div class="form-group">
                        <label for="subaccount_id_dist">Subaccount</label>
                        <select id="subaccount_id_dist" name="subaccount_id" required>
                            <option value="">-- select subaccount --</option>
                            @foreach($subaccounts as $subaccount)
                                <option value="{{ $subaccount->id }}">{{ $subaccount->name }}</option>
                            @endforeach
                        </select>
                        @error('subaccount_id')<span style="color: var(--danger); font-size: 12px;">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label for="stock_request_item_id_dist">Approved Item</label>
                        <select id="stock_request_item_id_dist" name="stock_request_item_id" required>
                            @foreach($approvedInventory as $item)
                                @php
                                    $distributed = $item->distributed_qty ?? 0;
                                    $myInventory = max(0, $item->approved_qty - $distributed);
                                @endphp
                                <option value="{{ $item->id }}">{{ $item->stock->description ?? $item->stock->name ?? 'Item' }} — my inventory: {{ $myInventory }}</option>
                            @endforeach
                        </select>
                        @error('stock_request_item_id')<span style="color: var(--danger); font-size: 12px;">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label for="allocated_qty_dist">Quantity to Allocate</label>
                        <input type="number" id="allocated_qty_dist" name="allocated_qty" min="1" required value="{{ old('allocated_qty', 1) }}">
                        @error('allocated_qty')<span style="color: var(--danger); font-size: 12px;">{{ $message }}</span>@enderror
                    </div>
                    <button type="submit" class="btn-submit">Allocate Item</button>
                </form>
            @endif
        </div>
    </div>
    @endif

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
    @endif

    @if($activeTab === 'subaccounts' && auth()->user()->role === 'client')
    <div class="settings-card">
        <div class="card-header" onclick="toggleCard('subaccounts-card')">
            <h3>Client Subaccounts</h3>
            <span class="card-toggle">Click to expand</span>
        </div>

        <div id="subaccounts-card" class="card-body open">
            <p style="color: var(--muted); margin: 0 0 16px;">Create subaccounts for your client account to track distributed supplies across teams.</p>

            <form method="POST" action="{{ route('client.account.subaccounts.store') }}">
                @csrf
                <div class="form-group">
                    <label for="subaccount_name">Subaccount Name</label>
                    <input type="text" id="subaccount_name" name="name" placeholder="e.g. Logistics Team" required value="{{ old('name') }}">
                    @error('name')
                        <span style="color: var(--danger); font-size: 12px;">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="subaccount_email">Subaccount Email</label>
                    <input type="email" id="subaccount_email" name="email" placeholder="Enter subaccount email" required value="{{ old('email') }}">
                    @error('email')
                        <span style="color: var(--danger); font-size: 12px;">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="subaccount_password">Password</label>
                    <input type="password" id="subaccount_password" name="password" placeholder="Enter a password" required>
                    @error('password')
                        <span style="color: var(--danger); font-size: 12px;">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="subaccount_password_confirmation">Confirm Password</label>
                    <input type="password" id="subaccount_password_confirmation" name="password_confirmation" placeholder="Confirm the password" required>
                </div>
                <div class="form-group">
                    <label for="subaccount_description">Description</label>
                    <input type="text" id="subaccount_description" name="description" placeholder="Optional description" value="{{ old('description') }}">
                </div>
                <button type="submit" class="btn-submit">Create Subaccount</button>
            </form>

            @if($subaccounts->isEmpty())
                <p style="margin-top:20px; color: var(--muted);">No subaccounts yet. Create one to begin assigning members.</p>
            @else
                <div style="margin-top:20px; overflow-x:auto;">
                    <table style="width:100%; border-collapse:collapse;">
                        <thead>
                            <tr>
                                <th style="padding:10px; border:1px solid var(--line); background:rgba(37,99,235,.06); text-align:left;">Name</th>
                                <th style="padding:10px; border:1px solid var(--line); background:rgba(37,99,235,.06); text-align:left;">Members</th>
                                <th style="padding:10px; border:1px solid var(--line); background:rgba(37,99,235,.06); text-align:left;">Allocated Items</th>
                                <th style="padding:10px; border:1px solid var(--line); background:rgba(37,99,235,.06); text-align:left;">Total Allocated Qty</th>
                                <th style="padding:10px; border:1px solid var(--line); background:rgba(37,99,235,.06); text-align:left;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($subaccounts as $subaccount)
                                @php
                                    $allocationCount = $subaccount->allocations->count();
                                    $allocatedQty = $subaccount->allocations->sum('allocated_qty');
                                @endphp
                                <tr>
                                    <td style="padding:10px; border:1px solid var(--line);">
                                        {{ $subaccount->name }}
                                        <div style="color: var(--muted); font-size:13px; margin-top:4px;">{{ $subaccount->user->email ?? 'No login email' }}</div>
                                    </td>
                                    <td style="padding:10px; border:1px solid var(--line);">{{ $subaccount->members_count }}</td>
                                    <td style="padding:10px; border:1px solid var(--line);">{{ $allocationCount }}</td>
                                    <td style="padding:10px; border:1px solid var(--line);">{{ $allocatedQty }}</td>
                                    <td style="padding:10px; border:1px solid var(--line);">
                                        <a href="{{ route('client.account.subaccounts.show', $subaccount) }}" class="btn-submit" style="display:inline-block; padding:8px 14px; background: #2563eb;">View</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
    @endif
    @endif
</div>

@php
    $subaccountMembers = $subaccounts->mapWithKeys(function($subaccount) {
        return [
            $subaccount->id => $subaccount->members->map(function($member) {
                return [
                    'id' => $member->id,
                    'name' => $member->name,
                    'email' => $member->email,
                ];
            })->toArray(),
        ];
    })->toArray();
@endphp

<script>
let pendingForm = null;

function toggleCard(id){
    const body = document.getElementById(id);
    if(!body) return;

    const header = body.previousElementSibling;
    const isOpen = body.style.display !== 'block';

    body.style.display = isOpen ? 'block' : 'none';
    body.classList.toggle('open', isOpen);

    if(header && header.classList) {
        header.classList.toggle('open', isOpen);
    }
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

function updateMembers() {
    const subaccountSelect = document.getElementById('subaccount_id');
    const memberSelect = document.getElementById('member_id');
    if (!subaccountSelect || !memberSelect) return;

    const selectedSubaccountId = subaccountSelect.value;
    memberSelect.innerHTML = '<option value="">-- select member --</option>';
    if (!selectedSubaccountId) return;

    const subaccountMembers = @json($subaccountMembers);

    const members = subaccountMembers[selectedSubaccountId] || [];
    members.forEach(member => {
        const option = document.createElement('option');
        option.value = member.id;
        option.textContent = member.email ? `${member.name} (${member.email})` : member.name;
        memberSelect.appendChild(option);
    });
}
</script>

@endsection
