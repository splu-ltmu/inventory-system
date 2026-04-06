@extends('layouts.app')

@php
  $brand = 'Inventory System';
  $pageTitle = 'Inbound';
  $pageSubtitle = 'Incoming items added to inventory.';
@endphp

@section('sidebar')
    @include('partials.admin-sidebar')
@endsection

@section('content')
<!-- mobile settings toggle (displayed only on narrow screens) -->
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
    <h2 style="margin:0;">Inbound Items</h2>
    <button id="mobileToolbarToggle" class="mobile-toolbar-toggle" style="background:none; border:none; font-size:28px; cursor:pointer; padding:0; line-height:0;">
        ⚙️
    </button>
</div>

<style>
    .toolbar{ display:flex; justify-content:space-between; align-items:center; gap:12px; margin-bottom:12px; flex-wrap:wrap; }
    .toolbar-actions{ display:flex; gap:8px; align-items:center; }

    /* Button system for the toolbar */
    .btn{
        display:inline-flex;
        align-items:center;
        gap:8px;
        padding:8px 12px;
        border-radius:10px;
        border:1px solid transparent;
        font-weight:700;
        cursor:pointer;
        text-decoration:none;
        background:transparent;
        color:var(--text);
        transition: all 0.3s ease;
    }
    .btn:hover:not(:disabled){
        transform: translateY(-2px);
    }
    .btn-primary{
        background: linear-gradient(180deg, var(--blue), #2b6fd6);
        color:#fff;
        border-color: rgba(37,99,235,.9);
        box-shadow: 0 1px 0 rgba(0,0,0,0.04);
        transition: all 0.3s ease;
    }
    .btn-primary:hover{
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(37,99,235,.25);
    }
    .btn-primary:active{
        transform: translateY(0);
    }
    .btn-outline{
        background:transparent;
        color:var(--blue);
        border:1px solid rgba(37,99,235,.25);
        transition: all 0.3s ease;
    }
    .btn-outline:hover{
        background: rgba(37,99,235,.08);
        border-color: rgba(37,99,235,.5);
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(37,99,235,.1);
    }
    .btn-outline:active{
        transform: translateY(0);
    }
    .btn-success{
        background:var(--green-soft);
        color:var(--green);
        border:1px solid var(--green);
    }
    .btn-success:not(:disabled){
        background:linear-gradient(180deg, #22c55e, #16a34a);
        color:#fff;
        border:1px solid #16a34a;
        box-shadow:0 4px 12px rgba(34, 197, 94, 0.3);
        font-weight:700;
        transition: all 0.3s ease;
    }
    .btn-success:not(:disabled):hover{
        transform:translateY(-2px);
        box-shadow:0 6px 16px rgba(34, 197, 94, 0.4);
    }
    .btn-success:not(:disabled):active{
        transform:translateY(0);
    }
    .btn:disabled{ opacity:.5; cursor:not-allowed; }

    .file-input{ display:flex; gap:8px; align-items:center; }
    .file-meta{ display:flex; gap:8px; align-items:center; color:var(--muted); font-size:13px; min-width:220px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .clear-file{ background:transparent; border:0; color:var(--muted); font-weight:700; cursor:pointer; padding:0 6px; border-radius:6px; }

    /* Drag & drop + spinner */
    .dropzone{
        border:2px dashed var(--line);
        border-radius:10px;
        padding:8px 12px;
        min-width:320px;
        display:flex;
        align-items:center;
        gap:12px;
        background:transparent;
        transition: background .12s, border-color .12s, box-shadow .12s;
    }
    .dropzone.dragover{ background: rgba(37,99,235,.03); border-color: rgba(37,99,235,.45); box-shadow: 0 6px 18px rgba(2,6,23,.06); }
    .dropzone .drop-instructions{ display:flex; gap:8px; align-items:center; flex:1; }

    .spinner, .small-spinner{ border:3px solid rgba(0,0,0,0.08); border-top-color: var(--blue); border-radius:50%; width:28px; height:28px; animation:spin .8s linear infinite; }
    .small-spinner{ width:14px; height:14px; border-width:2px; display:inline-block; vertical-align:middle; margin-left:8px; }
    @keyframes spin{ to{ transform:rotate(360deg); } }

    /* keep legacy .btn-link for backward compatibility */
    .btn-link{ display:inline-block; padding:10px 12px; border-radius:10px; border:1px solid var(--blue); background: var(--blue-soft); color: var(--blue); text-decoration:none; font-weight:700; }
    .btn-link:hover{ background: rgba(37,99,235,.18); }

    .table-wrap{ overflow:auto; border:1px solid var(--line); border-radius:14px; }
    table{ width:100%; border-collapse:collapse; min-width: 860px; background:#fff; }
    th,td{ border:1px solid var(--line); padding:10px; text-align:left; }
    th{ background: rgba(37,99,235,.06); color: var(--text); font-weight:700; }
    td{ color: var(--text); }
    .muted{ color:var(--muted); }

    /* mobile-specific adjustments */
    @media (max-width: 768px) {
        #inboundDropZone{
            border:none !important;
            padding:0 !important;
            background:transparent !important;
        }

        /* stack toolbar items vertically for narrow screens */
        .toolbar{
            flex-direction:column;
            align-items:flex-start;
        }
        .toolbar-actions{
            flex-direction:column;
            gap:12px;
            width:100%;
        }
        .toolbar-actions form{
            width:100%;
            display:flex;
            flex-wrap:wrap;
            gap:8px;
            align-items:center;
        }
        .file-input{ width:100%; }
        .dropzone{ min-width:auto; width:100%; }
        .dropzone .drop-instructions{ flex:1 1 100%; }
        #inboundImportBtn{ width:100%; }
    }

    /* show/hide toolbar on mobile */
    .mobile-toolbar-toggle{ display:none; }
    @media (max-width: 768px){
        .mobile-toolbar-toggle{ display:inline-block !important; }
        .toolbar-actions{ display:none; }
        .toolbar-actions.show{ display:flex; }
    }

    /* mobile options modal styles */
    #mobileOptionsModal{ 
        display:none; 
        position:fixed; 
        inset:0; 
        background:rgba(0,0,0,.45); 
        z-index:12000; 
        align-items:center; 
        justify-content:center; 
        visibility:hidden;
        opacity:0;
        transition: opacity 0.3s ease, visibility 0.3s ease;
    }
    #mobileOptionsModal.show{ 
        display:flex !important; 
        visibility:visible !important;
        opacity:1 !important;
    }
    #mobileOptionsModal .modal-content{ 
        background:#fff; 
        border-radius:12px; 
        box-shadow:0 18px 60px rgba(2,6,23,.35); 
        max-width:90vw; 
        width:90vw; 
        padding:20px; 
        position:relative; 
        max-height:80vh;
        overflow-y:auto;
    }
    #mobileOptionsModal .close-btn{ 
        position:absolute; 
        top:12px; 
        right:12px; 
        background:#eee; 
        border:none; 
        border-radius:50%; 
        width:32px; 
        height:32px; 
        font-size:18px; 
        line-height:0; 
        cursor:pointer;
        transition: background 0.2s ease;
    }
    #mobileOptionsModal .close-btn:hover{ background:#ddd; }
    
    /* vertical layout for modal toolbar actions */
    #mobileOptionsModal .toolbar-actions{
        flex-direction:column !important;
        gap:12px !important;
        width:100% !important;
    }
    #mobileOptionsModal .toolbar-actions .btn{
        width:100%;
        justify-content:center;
    }
    #mobileOptionsModal .toolbar-actions form{
        width:100% !important;
        display:flex !important;
        flex-direction:column !important;
        gap:12px;
    }
    /* hide file input control inside mobile modal – we keep logic but not UI element */
    #mobileOptionsModal input[type="file"]{
        display:none !important;
    }
    #mobileOptionsModal .toolbar-actions .file-input{
        width:100%;
        display:flex;
        flex-direction:column;
        gap:8px;
    }
    #mobileOptionsModal .toolbar-actions .dropzone{
        min-width:auto !important;
        width:100% !important;
        border:none !important;
        padding:0 !important;
        background:transparent !important;
        min-height:auto;
        gap:0;
        display:flex;
        flex-direction:column;
    }
    #mobileOptionsModal .toolbar-actions .drop-instructions{
        flex-direction:column;
        gap:8px;
        width:100%;
        display:flex;
    }
    #mobileOptionsModal .toolbar-actions .drop-instructions .btn{
        margin:0;
        width:100%;
    }
    #mobileOptionsModal .toolbar-actions .file-meta{
        min-width:auto;
        width:100%;
        white-space:normal;
        word-break:break-word;
        padding:8px;
        background:#f5f5f5;
        border-radius:6px;
        border:1px solid #ddd;
        display:flex;
        align-items:center;
        gap:8px;
    }
    #mobileOptionsModal .toolbar-actions #inboundImportBtn{
        width:100%;
        margin-top:8px;
    }
    @media (max-width: 768px){
        /* ensure toolbar actions hidden under modal approach */
        .toolbar-actions{ display:none !important; }
    }
</style> 

<div class="toolbar" style="position:relative;">
    <div class="toolbar-actions">
        <a class="btn btn-outline" href="{{ route('inbound.create') }}">Add Inbound</a>
        <a class="btn btn-primary" href="{{ route('inbound.template') }}">Download XLSX Template</a>
        <form action="{{ route('inbound.import') }}" method="POST" enctype="multipart/form-data" style="display:flex; gap:8px; align-items:center; margin:0;">
            @csrf
            <div class="file-input">
                <div id="inboundDropZone" class="dropzone" role="button" tabindex="0" aria-label="Drop file here or click to select">
                    <div class="drop-instructions">
                        <button type="button" id="inboundChooseBtn" class="btn btn-outline">Choose file</button>
                        <div class="file-meta"><span id="inboundFileName" class="muted">No file selected</span><button id="inboundClearBtn" type="button" class="clear-file" title="Clear selection">×</button></div>
                    </div>
                    <div id="inboundSpinner" class="spinner" style="display:none;" aria-hidden="true"></div>
                    <input type="file" name="file" accept=".xlsx,.xls,.csv" hidden required>
                </div>
            </div>
            <button id="inboundImportBtn" type="submit" class="btn btn-success" disabled>
                <span id="inboundImportLabel">Import</span>
                <span id="inboundImportSpinner" class="small-spinner" style="display:none;" aria-hidden="true"></span>
            </button>
        </form>
    </div>
</div> 

<!-- mobile options modal -->
<div id="mobileOptionsModal">
    <div class="modal-content">
        <button id="mobileOptionsClose" class="close-btn">&times;</button>
        <div id="mobileOptionsContainer"></div>
    </div>
</div>

@if(session('success'))
    <div style="margin:12px 0; padding:12px; background:rgba(34,197,94,0.08); border:1px solid rgba(34,197,94,0.18); color:var(--green); border-radius:8px;">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div style="margin:12px 0; padding:12px; background:rgba(239,68,68,0.06); border:1px solid rgba(239,68,68,0.12); color:var(--red); border-radius:8px;">{{ session('error') }}</div>
@endif

<script>
(function(){
    const input = document.querySelector('form input[type="file"]');
    const dropZone = document.getElementById('inboundDropZone');
    const chooseBtn = document.getElementById('inboundChooseBtn');
    const clearBtn = document.getElementById('inboundClearBtn');
    const fileNameSpan = document.getElementById('inboundFileName');
    const importBtn = document.getElementById('inboundImportBtn');
    const importLabel = document.getElementById('inboundImportLabel');
    const importSpinner = document.getElementById('inboundImportSpinner');
    const pageSpinner = document.getElementById('inboundSpinner');
    const maxSize = 10 * 1024 * 1024; // 10MB

    function setFile(file){
        if (!file){
            input.value = '';
            // update all file name spans (original + modal clone)
            document.querySelectorAll('#inboundFileName').forEach(span => {
                span.textContent = 'No file selected';
                span.classList.add('muted');
            });
            // update all import buttons (original + modal clone)
            document.querySelectorAll('#inboundImportBtn').forEach(btn => {
                btn.disabled = true;
            });
            return;
        }
        // update all file name spans (original + modal clone)
        const fileText = `${file.name} (${Math.round(file.size/1024)} KB)`;
        document.querySelectorAll('#inboundFileName').forEach(span => {
            span.textContent = fileText;
            span.classList.remove('muted');
        });
        // update all import buttons (original + modal clone)
        document.querySelectorAll('#inboundImportBtn').forEach(btn => {
            btn.disabled = false;
        });
    }

    chooseBtn.addEventListener('click', (e) => { e.stopPropagation(); input.click(); });
    dropZone.addEventListener('click', (e) => { if (e.target === dropZone || dropZone.contains(e.target) && e.target !== chooseBtn && !e.target.closest('.clear-file')) input.click(); });

    input.addEventListener('change', () => {
        const f = input.files && input.files[0] ? input.files[0] : null;
        // keep modal input in sync, though form submission always uses original
        const clonedInput = document.querySelector('#mobileOptionsModal form input[type="file"]');
        if(clonedInput && input.files.length > 0){
            const dt = new DataTransfer();
            for(let file of input.files){ dt.items.add(file); }
            clonedInput.files = dt.files;
        }
        setFile(f);
    });

    clearBtn.addEventListener('click', () => setFile(null));

    // Drag & drop handlers
    ['dragenter','dragover'].forEach(ev => dropZone.addEventListener(ev, (e) => { e.preventDefault(); e.stopPropagation(); dropZone.classList.add('dragover'); }));
    ['dragleave','drop','dragend'].forEach(ev => dropZone.addEventListener(ev, (e) => { e.preventDefault(); e.stopPropagation(); dropZone.classList.remove('dragover'); }));

    dropZone.addEventListener('drop', (e) => {
        const dt = e.dataTransfer || e.originalEvent?.dataTransfer;
        if (dt && dt.files && dt.files.length) {
            input.files = dt.files;
        }
    });

    // mobile options modal logic
    const mobileToggle = document.getElementById('mobileToolbarToggle');
    const toolbarActions = document.querySelector('.toolbar-actions');
    const optionsModal = document.getElementById('mobileOptionsModal');
    const optionsContainer = document.getElementById('mobileOptionsContainer');
    const optionsClose = document.getElementById('mobileOptionsClose');

    console.log('Modal elements:', { mobileToggle, toolbarActions, optionsModal, optionsClose });

    // Open modal on gear icon click
    if(mobileToggle){
        mobileToggle.addEventListener('click', function(e){
            console.log('Gear icon clicked!');
            e.preventDefault();
            e.stopPropagation();
            if(toolbarActions && optionsContainer){
                optionsContainer.innerHTML = toolbarActions.innerHTML;
                
                // configure cloned file input: we'll keep it hidden but retain its attributes
                // this preserves any underlying HTML form behavior, while the actual logic
                // still uses the original input element for file data.
                const clonedInput = optionsContainer.querySelector('input[type="file"]');
                if(clonedInput){
                    clonedInput.style.display = 'none';
                }
                
                // re-bind file picker handlers to cloned elements in modal
                const clonedChooseBtn = optionsContainer.querySelector('#inboundChooseBtn');
                const clonedClearBtn = optionsContainer.querySelector('#inboundClearBtn');
                const clonedDropZone = optionsContainer.querySelector('#inboundDropZone');
                
                if(clonedChooseBtn){
                    clonedChooseBtn.addEventListener('click', (e) => { e.stopPropagation(); input.click(); });
                }
                if(clonedClearBtn){
                    clonedClearBtn.addEventListener('click', () => setFile(null));
                }
                if(clonedDropZone){
                    clonedDropZone.addEventListener('click', (e) => { if (e.target === clonedDropZone || clonedDropZone.contains(e.target) && e.target !== clonedChooseBtn && !e.target.closest('.clear-file')) input.click(); });
                }
                
                // re-bind form submit to cloned form
                const clonedForm = optionsContainer.querySelector('form');
                if(clonedForm){
                    clonedForm.addEventListener('submit', (e) => {
                        if (!input.files || !input.files.length) {
                            e.preventDefault();
                            alert('Please choose a file to import.');
                            return;
                        }
                        if (input.files[0].size > maxSize) {
                            e.preventDefault();
                            alert('File is too large. Max size is 10 MB.');
                            return;
                        }

                        // show spinner / prevent double-submit
                        document.querySelectorAll('#inboundImportBtn').forEach(btn => btn.disabled = true);
                        document.querySelectorAll('#inboundImportLabel').forEach(label => label.textContent = 'Importing...');
                        document.querySelectorAll('#inboundImportSpinner').forEach(spinner => spinner.style.display = 'inline-block');
                        if(pageSpinner) pageSpinner.style.display = 'inline-block';
                    });
                }
            }
            if(optionsModal){
                optionsModal.classList.add('show');
                console.log('Modal class added, modal should be visible now');
            }
        });
    }

    // Close modal on close button click
    if(optionsClose){
        optionsClose.addEventListener('click', function(){
            console.log('Close button clicked');
            if(optionsModal) optionsModal.classList.remove('show');
            // reset form state when closing
            document.querySelectorAll('#inboundImportBtn').forEach(btn => btn.disabled = true);
            document.querySelectorAll('#inboundImportLabel').forEach(label => label.textContent = 'Import');
            document.querySelectorAll('#inboundImportSpinner').forEach(spinner => spinner.style.display = 'none');
        });
    }

    // Close modal on backdrop click
    if(optionsModal){
        optionsModal.addEventListener('click', function(e){
            if(e.target === optionsModal){
                console.log('Backdrop clicked');
                optionsModal.classList.remove('show');
            }
        });
    }

    const form = input.closest('form');
    form.addEventListener('submit', (e) => {
        if (!input.files || !input.files.length) {
            e.preventDefault();
            alert('Please choose a file to import.');
            return;
        }
        if (input.files[0].size > maxSize) {
            e.preventDefault();
            alert('File is too large. Max size is 10 MB.');
            return;
        }

        // show spinner / prevent double-submit
        importBtn.disabled = true;
        importLabel.textContent = 'Importing...';
        importSpinner.style.display = 'inline-block';
        pageSpinner.style.display = 'inline-block';
    });

    // accessibility: Enter / Space opens file picker when drop zone focused
    dropZone.addEventListener('keydown', (e) => { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); input.click(); } });
})();
</script>

<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th style="min-width:160px;">Stock ID</th>
                <th>Description</th>
                <th style="min-width:100px;">Unit</th>
                <th style="min-width:100px;">Quantity</th>
                <th style="min-width:160px;">Category</th>
            </tr>
        </thead>
        <tbody>
            @forelse($inbounds as $row)
                <tr>
                    <td>{{ $row->id_no ?? ($row->stock?->id_no ?? '—') }}</td>
                    <td class="muted">{{ $row->description ?? ($row->stock?->description ?? '—') }}</td>
                    <td class="muted">{{ $row->unit ?? ($row->stock?->unit ?? 'pcs') }}</td>
                    <td>{{ $row->total ?? ($row->total_added ?? '—') }}</td>
                    <td class="muted">{{ $row->category_name ?? ($row->stock?->category?->name ?? $row->category ?? '—') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="color:var(--muted);">No inbound records yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
</div> <!-- close mobile toggle wrapper -->
@endsection
