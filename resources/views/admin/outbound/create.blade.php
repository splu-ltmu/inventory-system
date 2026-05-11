@extends('layouts.app')

@php
  $brand = 'Inventory System';
  $pageTitle = 'Outbound';
@endphp

@section('sidebar')
    @include('partials.admin-sidebar')
@endsection

@section('content')
<style>
    .toolbar{ display:flex; justify-content:space-between; align-items:center; gap:12px; margin-bottom:12px; }
    .btn-link{
        display:inline-block;
        padding:10px 12px;
        border-radius:10px;
        border:1px solid var(--blue);
        background: var(--blue-soft);
        color: var(--blue);
        text-decoration:none;
        font-weight:700;
    }
    .btn-link:hover{ background: rgba(37,99,235,.18); }

    .form-container{ max-width:600px; background:#fff; border:1px solid var(--line); border-radius:14px; padding:20px; }
    .form-group{ margin-bottom:20px; }
    .form-group label{ display:block; margin-bottom:8px; color: var(--text); font-weight:700; }
    .form-group select,
    .form-group input[type="text"],
    .form-group input[type="number"]{ width:100%; padding:10px; border:1px solid var(--line); border-radius:8px; font-size:14px; box-sizing:border-box; }
    .form-group select:focus,
    .form-group input[type="text"]:focus,
    .form-group input[type="number"]:focus{ outline:none; border-color: var(--blue); box-shadow: 0 0 0 3px rgba(37,99,235,.1); }

    .form-actions{ display:flex; gap:12px; margin-top:24px; }
    .btn-submit{
        display:inline-block;
        padding:10px 20px;
        border-radius:10px;
        border:none;
        background: var(--blue);
        color: white;
        text-decoration:none;
        font-weight:700;
        cursor:pointer;
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
    .btn-cancel{
        display:inline-block;
        padding:10px 20px;
        border-radius:10px;
        border:1px solid var(--line);
        background: transparent;
        color: var(--text);
        text-decoration:none;
        font-weight:700;
        cursor:pointer;
        transition: all 0.3s ease;
    }
    .btn-cancel:hover{ 
        background: var(--line);
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0,0,0,.08);
    }
    .btn-cancel:active{
        transform: translateY(0);
    }

    .error-message{ color: var(--red); margin-bottom:16px; padding:12px; background: rgba(239,68,68,.1); border:1px solid rgba(239,68,68,.3); border-radius:8px; }
    .error-message ul{ margin:0; padding-left:20px; }
    .error-message li{ margin:4px 0; }
</style>

<div class="toolbar">
    <h2 style="margin:0;">Add Outbound</h2>
    <a class="btn-link" href="{{ route('outbound.index') }}">Back to Outbound</a>
</div>

<div class="form-container">
    @if($errors->any())
        <div class="error-message">
            <ul>
                @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('outbound.store') }}" method="POST">
        @csrf
        
        <div class="form-group">
            <label for="stock_id">Select Stock:</label>
            <select name="stock_id" id="stock_id" required>
                <option value="">-- Choose a stock --</option>
                @foreach($stocks as $stock)
                    <option value="{{ $stock->id }}" data-available="{{ $stock->stock }}" {{ old('stock_id') == $stock->id ? 'selected' : '' }}>{{ $stock->description }} ({{ $stock->id_no }})</option>
                @endforeach
            </select>
            <div style="margin-top:8px;">
                <span id="stockAvailableBadge" style="display:none;padding:4px 8px;border-radius:999px;font-weight:700;color:#fff;font-size:13px;"></span>
            </div>
            @error('stock_id')<span style="color:#ef4444;font-size:12px;">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
            <label for="recipient_search">Recipient (Client or Urgent):</label>
            <div style="position:relative;">
                <input 
                    type="text" 
                    id="recipient_search" 
                    name="recipient_search" 
                    value="{{ old('recipient_search', '') }}" 
                    placeholder="Type client name, member name, or office for urgent outbound..."
                    required
                    style="width:100%; padding:10px; border:1px solid var(--line); border-radius:8px; font-size:14px; box-sizing:border-box;"
                >
                
                <!-- Hidden fields to store the selected recipient data -->
                <input type="hidden" name="client_id" id="client_id" value="">
                <input type="hidden" name="member_id" id="member_id" value="">
                <input type="hidden" name="urgent_recipient_id" id="urgent_recipient_id" value="">
                <input type="hidden" name="urgent_recipient_name" id="urgent_recipient_name" value="">
                <input type="hidden" name="urgent_recipient_office" id="urgent_recipient_office" value="">
                <input type="hidden" name="is_urgent_outbound" id="is_urgent_outbound" value="false">
                
                <!-- Dropdown for search results -->
                <div id="recipient_dropdown" style="position:absolute; top:100%; left:0; right:0; background:#fff; border:1px solid var(--line); border-radius:8px; box-shadow:0 4px 12px rgba(0,0,0,0.15); max-height:200px; overflow-y:auto; z-index:1000; display:none;">
                    <div id="recipient_results"></div>
                </div>
            </div>
            <div style="margin-top:8px; font-size:12px; color:var(--muted);">
                <span id="recipient_type_hint"></span>
            </div>
            @error('recipient_search')<span style="color:#ef4444;font-size:12px;">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
            <label for="office">Office/Department:</label>
            <input type="text" name="office" id="office" value="{{ old('office', '') }}" placeholder="e.g., TMU, HR Department" required>
            @error('office')<span style="color:#ef4444;font-size:12px;">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
            <label for="total">Quantity:</label>
            <input type="number" name="total" id="total" value="{{ old('total', 1) }}" min="1" required>
        </div>

        {{-- Approval and Status are set automatically for admin-created outbounds --}}

        <div class="form-actions">
            <button type="submit" class="btn-submit">Add Outbound</button>
            <a href="{{ route('outbound.index') }}" class="btn-cancel">Cancel</a>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
    const select = document.getElementById('stock_id');
    const badge = document.getElementById('stockAvailableBadge');
    const recipientSearch = document.getElementById('recipient_search');
    const recipientDropdown = document.getElementById('recipient_dropdown');
    const recipientResults = document.getElementById('recipient_results');
    const recipientTypeHint = document.getElementById('recipient_type_hint');
    const officeInput = document.getElementById('office');

    // Stock availability badge functionality
    function updateBadge(){
        const opt = select.options[select.selectedIndex];
        const avail = opt && opt.dataset ? parseInt(opt.dataset.available || '0', 10) : 0;
        if(!opt || !opt.value){
            badge.style.display = 'none';
            return;
        }

        let bg = '#16a34a'; // green
        if(avail >= 50){ bg = '#16a34a'; }
        else if(avail > 0 && avail <= 49){ bg = '#f97316'; }
        else { bg = '#ef4444'; }

        badge.textContent = 'Available: ' + avail;
        badge.style.background = bg;
        badge.style.display = 'inline-block';
    }

    select.addEventListener('change', updateBadge);
    updateBadge();

    // Recipient search functionality
    let searchTimeout;
    let selectedRecipient = null;

    recipientSearch.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const searchTerm = this.value.trim();
        
        if (searchTerm.length < 2) {
            recipientDropdown.style.display = 'none';
            return;
        }

        searchTimeout = setTimeout(() => {
            searchRecipients(searchTerm);
        }, 300);
    });

    recipientSearch.addEventListener('focus', function() {
        if (this.value.trim().length >= 2) {
            searchRecipients(this.value.trim());
        }
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!recipientSearch.contains(e.target) && !recipientDropdown.contains(e.target)) {
            recipientDropdown.style.display = 'none';
        }
    });

    function searchRecipients(term) {
        fetch(`/admin/outbound/search-recipients?term=${encodeURIComponent(term)}`)
            .then(response => response.json())
            .then(data => {
                displayRecipientResults(data, term);
            })
            .catch(error => {
                console.error('Error searching recipients:', error);
                recipientDropdown.style.display = 'none';
            });
    }

    function displayRecipientResults(results, searchTerm) {
        recipientResults.innerHTML = '';
        
        if (results.length === 0) {
            // Show the typed name as a non-member option
            recipientResults.innerHTML = `
                <div class="recipient-option" data-name="${searchTerm}" data-type="non-member" style="padding:12px; cursor:pointer; border-bottom:1px solid #f3f4f6; transition:background 0.2s;">
                    <div style="font-weight:600; color:#dc2626;">${searchTerm}</div>
                    <div style="font-size:12px; color:#6b7280; margin-top:2px;">
                        Non-member - Will be saved as urgent recipient
                    </div>
                </div>
            `;
            
            // Add click handler for non-member option
            const nonMemberOption = recipientResults.querySelector('.recipient-option');
            if (nonMemberOption) {
                nonMemberOption.addEventListener('click', () => {
                    selectNonMemberRecipient(searchTerm);
                });
                nonMemberOption.addEventListener('mouseenter', () => nonMemberOption.style.background = '#fef2f2');
                nonMemberOption.addEventListener('mouseleave', () => nonMemberOption.style.background = '');
            }
        } else {
            results.forEach(result => {
                const item = document.createElement('div');
                item.style.cssText = 'padding:12px; cursor:pointer; border-bottom:1px solid #f3f4f6; transition:background 0.2s';
                item.innerHTML = `
                    <div style="font-weight:600; color:#1f2937;">${result.name}</div>
                    <div style="font-size:12px; color:#6b7280; margin-top:2px;">
                        ${result.type === 'client' ? 'Client' : result.type === 'member' ? 'Client Member' : 'Urgent Recipient'}
                        ${result.office ? ` • ${result.office}` : ''}
                    </div>
                `;
                
                item.addEventListener('click', () => selectRecipient(result));
                item.addEventListener('mouseenter', () => item.style.background = '#f9fafb');
                item.addEventListener('mouseleave', () => item.style.background = '');
                
                recipientResults.appendChild(item);
            });
        }
        
        recipientDropdown.style.display = 'block';
    }

    function selectNonMemberRecipient(name) {
        selectedRecipient = { type: 'non-member', name: name };
        recipientSearch.value = name;
        
        // Clear all hidden fields first
        document.getElementById('client_id').value = '';
        document.getElementById('member_id').value = '';
        document.getElementById('urgent_recipient_id').value = '';
        document.getElementById('urgent_recipient_name').value = name;
        document.getElementById('urgent_recipient_office').value = '';
        document.getElementById('is_urgent_outbound').value = 'true';
        
        // Enable office field for non-members
        officeInput.value = '';
        officeInput.disabled = false;
        recipientTypeHint.textContent = 'Selected: Non-member - Will create urgent recipient';
        recipientTypeHint.style.color = '#dc2626';
        
        recipientDropdown.style.display = 'none';
    }

    function selectRecipient(result) {
        selectedRecipient = result;
        recipientSearch.value = result.name;
        
        // Clear all hidden fields first
        document.getElementById('client_id').value = '';
        document.getElementById('member_id').value = '';
        document.getElementById('urgent_recipient_id').value = '';
        document.getElementById('urgent_recipient_name').value = '';
        document.getElementById('urgent_recipient_office').value = '';
        document.getElementById('is_urgent_outbound').value = 'false';
        
        if (result.type === 'client') {
            document.getElementById('client_id').value = result.id;
            document.getElementById('is_urgent_outbound').value = 'false';
            officeInput.value = result.office || '';
            officeInput.disabled = false;
            recipientTypeHint.textContent = 'Selected: Client - Office will be used for outbound record';
            recipientTypeHint.style.color = '#059669';
        } else if (result.type === 'member') {
            document.getElementById('client_id').value = result.client_id;
            document.getElementById('member_id').value = result.id;
            document.getElementById('is_urgent_outbound').value = 'false';
            
            // Auto-populate office field and handle non-office members
            if (result.has_office) {
                officeInput.value = result.office;
                officeInput.disabled = false;
                recipientTypeHint.textContent = `Selected: Client Member (${result.client_name}) - Office: ${result.office}`;
                recipientTypeHint.style.color = '#059669';
            } else {
                officeInput.value = 'non office member';
                officeInput.disabled = true; // Disable for non-office members
                recipientTypeHint.textContent = `Selected: Client Member (${result.client_name}) - Non-office member`;
                recipientTypeHint.style.color = '#f59e0b';
            }
        } else {
            document.getElementById('urgent_recipient_id').value = result.id;
            document.getElementById('urgent_recipient_name').value = result.name;
            document.getElementById('urgent_recipient_office').value = result.office || '';
            document.getElementById('is_urgent_outbound').value = 'true';
            officeInput.value = result.office || '';
            officeInput.disabled = false;
            recipientTypeHint.textContent = 'Selected: Urgent Recipient - Will appear in Summary tab';
            recipientTypeHint.style.color = '#dc2626';
        }
        
        recipientDropdown.style.display = 'none';
    }

    // Handle Enter key for creating new urgent recipient
    recipientSearch.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const searchTerm = this.value.trim();
            
            if (searchTerm && selectedRecipient && selectedRecipient.name === searchTerm) {
                // Already selected, submit form
                this.closest('form').submit();
            } else if (searchTerm) {
                // Create new urgent recipient
                createUrgentRecipient(searchTerm);
            }
        }
    });

    function createUrgentRecipient(name) {
        // Extract office from the input if it contains a comma or "office:" pattern
        let recipientName = name;
        let recipientOffice = '';
        
        if (name.includes(',')) {
            const parts = name.split(',');
            recipientName = parts[0].trim();
            recipientOffice = parts[1].trim();
        } else if (name.toLowerCase().includes('office:')) {
            const parts = name.split(/office:/i);
            recipientName = parts[0].trim();
            recipientOffice = parts[1].trim();
        }
        
        const urgentData = {
            type: 'urgent_new',
            name: recipientName,
            office: recipientOffice
        };
        
        selectRecipient(urgentData);
    }
});
</script>

@endsection
