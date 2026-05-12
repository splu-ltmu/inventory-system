<?php
  $brand = 'Inventory System';
  $pageTitle = 'Summary';
?>

<?php $__env->startSection('sidebar'); ?>
    <?php echo $__env->make('partials.admin-sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <style>
        .cards-grid{ display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:12px; margin-bottom:22px; }
        .card{ 
            border:1px solid var(--line); 
            border-radius:14px; 
            background:#ffffff; 
            box-shadow:0 10px 28px rgba(15,23,42,.06); 
            overflow:hidden;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .card:hover{
            transform: translateY(-4px);
            box-shadow: 0 20px 40px rgba(15,23,42,.15);
            border-color: rgba(37,99,235,.2);
        }
        .card-head{ 
            padding:14px 16px; 
            background:linear-gradient(135deg, rgba(37,99,235,.05), rgba(99,102,241,.02));
            border-bottom:1px solid var(--line); 
            display:flex; 
            justify-content:space-between; 
            gap:12px; 
            cursor:pointer;
            transition: all 0.3s ease;
        }
        .card:hover .card-head{ 
            background:linear-gradient(135deg, rgba(37,99,235,.08), rgba(99,102,241,.04));
            border-bottom-color: rgba(37,99,235,.15);
        }
        .card-body{ 
            padding:16px;
            background:linear-gradient(135deg, #fafbfc 0%, rgba(99,102,241,.02) 100%);
            transition: all 0.3s ease;
        }
        .card-body.hidden{ display:none; }
        .card-title{ font-weight:800; font-size:16px; }
        .card-sub{ color:var(--muted); font-size:13px; margin-top:4px; }

        .pill{ 
            display:inline-block; 
            padding:4px 10px; 
            border-radius:999px; 
            font-size:12px; 
            font-weight:700; 
            border:1px solid var(--line);
            transition: all 0.3s ease;
        }
        .pill:hover{
            transform: scale(1.05);
        }
        .pill.pending{ 
            background:linear-gradient(180deg,var(--orange-soft),#fff7ed); 
            color:var(--orange); 
            border-color:rgba(249,115,22,.2);
        }
        .pill.pending:hover{
            box-shadow: 0 4px 12px rgba(249,115,22,.2);
        }
        .pill.approved, .pill.ready_to_receive{ 
            background:linear-gradient(180deg,#ecfdf5,#f0fdfa); 
            color:#065f46; 
            border-color:rgba(34,197,94,.2);
        }
        .pill.approved:hover, .pill.ready_to_receive:hover{
            box-shadow: 0 4px 12px rgba(34,197,94,.2);
        }
        .pill.released{ 
            background:linear-gradient(180deg,#eff6ff,#f0f9ff); 
            color:var(--blue); 
            border-color:rgba(37,99,235,.2);
        }
        .pill.released:hover{
            box-shadow: 0 4px 12px rgba(37,99,235,.2);
        }
        .pill.rejected{ 
            background:linear-gradient(180deg,#fee2e2,#fff1f2); 
            color:#991b1b; 
            border-color:rgba(244,63,94,.2);
        }
        .pill.rejected:hover{
            box-shadow: 0 4px 12px rgba(244,63,94,.2);
        }
        .pill.cancelled{ 
            background:linear-gradient(180deg,#f3f4f6,#f8fafc); 
            color:#475569; 
            border-color:rgba(226,232,240,.6);
        }
        .pill.cancelled:hover{
            box-shadow: 0 4px 12px rgba(71,81,105,.1);
        }

        .muted{ color:var(--muted); }
        .list{ list-style: disc inside; color:var(--muted); }
        .list li{ margin-bottom:6px; }
    </style>

    <div class="cards-grid">
        <div class="card">
            <div class="card-body" style="position:relative;">
                <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:12px; flex-wrap:wrap;">
                    <div>
                        <div class="card-title">Total transactions</div>
                        <div class="card-sub"><?php echo e($requests->count()); ?> total</div>
                    </div>

                    <form id="filterForm" method="GET" style="display:flex; gap:10px; align-items:center; flex-wrap:wrap; margin-top:4px;">
                        <input
                            id="filterQuery"
                            type="text"
                            name="q"
                            value="<?php echo e($q ?? ''); ?>"
                            placeholder="Search # or client"
                            style="padding:8px 10px; border-radius:10px; border:1px solid var(--line); background:#fff; min-width:200px;"
                        />

                        <select
                            id="filterOffice"
                            name="office"
                            style="padding:8px 10px; border-radius:10px; border:1px solid var(--line); background:#fff; min-width:180px;"
                        >
                            <option value="">All Offices</option>
                            <?php $__currentLoopData = $offices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $off): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($off); ?>" <?php echo e($off === ($office ?? '') ? 'selected' : ''); ?>><?php echo e($off); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>

                        <div style="display:flex; gap:8px; align-items:center;">
                            <label style="font-size:13px; font-weight:600; color:#374151;">Type:</label>
                            <div style="display:flex; gap:12px;">
                                <label style="display:flex; align-items:center; gap:4px; cursor:pointer; font-size:13px;">
                                    <input 
                                        type="radio" 
                                        name="type" 
                                        value="all" 
                                        <?php echo e(($type ?? 'all') === 'all' ? 'checked' : ''); ?>

                                        onchange="document.getElementById('filterForm').submit();"
                                    >
                                    <span>All</span>
                                </label>
                                <label style="display:flex; align-items:center; gap:4px; cursor:pointer; font-size:13px;">
                                    <input 
                                        type="radio" 
                                        name="type" 
                                        value="request" 
                                        <?php echo e(($type ?? 'all') === 'request' ? 'checked' : ''); ?>

                                        onchange="document.getElementById('filterForm').submit();"
                                    >
                                    <span>Request</span>
                                </label>
                                <label style="display:flex; align-items:center; gap:4px; cursor:pointer; font-size:13px;">
                                    <input 
                                        type="radio" 
                                        name="type" 
                                        value="urgent" 
                                        <?php echo e(($type ?? 'all') === 'urgent' ? 'checked' : ''); ?>

                                        onchange="document.getElementById('filterForm').submit();"
                                    >
                                    <span>Urgent</span>
                                </label>
                                <label style="display:flex; align-items:center; gap:4px; cursor:pointer; font-size:13px;">
                                    <input 
                                        type="radio" 
                                        name="type" 
                                        value="direct" 
                                        <?php echo e(($type ?? 'all') === 'direct' ? 'checked' : ''); ?>

                                        onchange="document.getElementById('filterForm').submit();"
                                    >
                                    <span>Direct</span>
                                </label>
                                <label style="display:flex; align-items:center; gap:4px; cursor:pointer; font-size:13px;">
                                    <input 
                                        type="radio" 
                                        name="type" 
                                        value="inbound" 
                                        <?php echo e(($type ?? 'all') === 'inbound' ? 'checked' : ''); ?>

                                        onchange="document.getElementById('filterForm').submit();"
                                    >
                                    <span>Inbound</span>
                                </label>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div>
        <?php $__empty_1 = true; $__currentLoopData = $requests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $req): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php
                $requestValue = $req->items->sum(function ($item) {
                    return ($item->stock?->price ?? 0) * ($item->approved_qty ?? $item->requested_qty);
                });
            ?>
            <div class="card status-<?php echo e($req->status); ?>" style="margin-bottom:14px; border-left:4px solid #10b981;">
                <div class="card-head" onclick="toggleReq('req-<?php echo e($req->id); ?>')">
                    <div>
                        <div class="card-title" style="color:#10b981;">Request #<?php echo e($req->id); ?></div>
                        <div class="card-sub">Created on <?php echo e($req->created_at?->format('F j, Y, g:i A')); ?></div>
                        <div style="margin-top:8px; padding:6px 10px; background:linear-gradient(135deg, #d1fae5, #a7f3d0); border-radius:8px; border:1px solid #6ee7b7;">
                            <div style="font-size:12px; color:#047857; font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">Client</div>
                            <div style="font-size:14px; font-weight:800; color:#065f46; margin-top:2px;"><?php echo e($req->client?->name ?? 'Unknown'); ?></div>
                            <div style="font-size:12px; color:#047857; margin-top:4px;">Office: <?php echo e($req->office ?? 'Not specified'); ?></div>
                        </div>
                                                <div style="margin-top:8px;">
                            <span style="padding:4px 8px; border-radius:6px; background:#10b981; color:#fff; font-size:11px; font-weight:700;"><?php echo e(strtoupper(str_replace('_', ' ', $req->status))); ?></span>
                        </div>
                    </div>

                    <div style="text-align:right; min-width:160px;">
                        <div style="font-size:12px; color:var(--muted); text-transform:uppercase; letter-spacing:0.5px; font-weight:600;">Type</div>
                        <div style="margin-top:4px;">
                            <span style="padding:4px 8px; border-radius:6px; background:#10b981; color:#fff; font-size:12px; font-weight:700;">REQUEST</span>
                        </div>
                        <?php if($req->verification_code): ?>
                            <div style="font-size:12px; color:var(--muted); margin-top:4px;">Code: <span style="font-weight:700; color:var(--text);"><?php echo e($req->verification_code); ?></span></div>
                        <?php endif; ?>
                        <?php if($req->received_by): ?>
                            <div style="margin-top:8px; padding:6px 10px; background:linear-gradient(135deg, #f0f9ff, #e0f2fe); border-radius:8px; border:1px solid #93c5fd;">
                                <div style="font-size:12px; color:#1e40af; font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">Received By</div>
                                <div style="font-size:14px; font-weight:800; color:#1e3a8a; margin-top:2px;"><?php echo e($req->received_by); ?></div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div id="req-<?php echo e($req->id); ?>" class="card-body hidden">
                    <div style="font-weight:700; margin-bottom:12px; color:var(--text); font-size:14px;">Request Details</div>
                    <div style="display:grid; gap:8px;">
                        <?php $__currentLoopData = $req->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $unitPrice = $item->stock?->price ?? 0;
                                $quantity = $item->approved_qty ?? $item->requested_qty;
                                $lineTotal = $unitPrice * $quantity;
                            ?>
                            <div style="padding:12px; background:linear-gradient(135deg, #d1fae5, #a7f3d0); border-radius:8px;">
                                <div style="font-weight:600; color:#065f46; margin-bottom:4px;"><?php echo e($item->stock?->description ?? 'Unknown Item'); ?></div>
                                <div style="display:flex; gap:16px; font-size:12px; color:#047857;">
                                    <span><strong>ID:</strong> <?php echo e($item->stock?->id_no ?? 'N/A'); ?></span>
                                    <span><strong>Unit:</strong> <?php echo e($item->stock?->unit ?? 'N/A'); ?></span>
                                    <span><strong>Quantity:</strong> <?php echo e($quantity); ?></span>
                                    <?php if($unitPrice): ?>
                                        <span><strong>Value:</strong> ₱<?php echo e(number_format($lineTotal, 2)); ?></span>
                                    <?php endif; ?>
                                </div>
                                <?php if($item->requested_qty !== $item->approved_qty): ?>
                                    <div style="margin-top:8px; padding:6px 8px; background:#fef3c7; border-radius:6px; border:1px solid #fbbf24;">
                                        <strong style="color:#92400e;">Quantity:</strong> Requested: <?php echo e($item->requested_qty); ?>, Approved: <?php echo e($item->approved_qty ?? 0); ?>

                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="card">
                <div class="card-body">
                    <div class="muted">No transactions found yet.</div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Urgent Outbounds Section -->
    <?php if(isset($urgentOutbounds) && $urgentOutbounds->count() > 0): ?>
        <h3 style="margin:24px 0 12px 0; color:#dc2626; font-size:18px; font-weight:800;">Urgent Outbound Requests</h3>
        <?php $__empty_1 = true; $__currentLoopData = $urgentOutbounds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $urgent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="card" style="margin-bottom:14px; border-left:4px solid #dc2626;">
                <div class="card-head" onclick="toggleReq('urgent-<?php echo e($urgent->id); ?>')">
                    <div>
                        <div class="card-title" style="color:#dc2626;">Urgent Outbound #<?php echo e($urgent->id); ?></div>
                        <div class="card-sub">Created <?php echo e($urgent->created_at?->format('M d, Y @ h:i A')); ?></div>
                        <div style="margin-top:8px; padding:6px 10px; background:linear-gradient(135deg, #fef2f2, #fee2e2); border-radius:8px; border:1px solid #fca5a5;">
                            <div style="font-size:12px; color:#991b1b; font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">Recipient</div>
                            <div style="font-size:14px; font-weight:800; color:#7f1d1d; margin-top:2px;"><?php echo e($urgent->urgent_recipient_name ?? 'Unknown'); ?></div>
                            <div style="font-size:12px; color:#991b1b; margin-top:4px;">Office: <?php echo e($urgent->urgent_recipient_office ?? 'Not specified'); ?></div>
                        </div>
                        
                        <?php if($urgent->reason): ?>
                            
                        <?php endif; ?>
                        <div style="margin-top:8px;">
                            <span style="padding:4px 8px; border-radius:6px; background:#dc2626; color:#fff; font-size:11px; font-weight:700;">URGENT</span>
                        </div>
                    </div>

                    <div style="text-align:right; min-width:160px;">
                        <div style="font-size:12px; color:var(--muted); text-transform:uppercase; letter-spacing:0.5px; font-weight:600;">Item</div>
                        <div style="margin-top:4px;">
                            <div style="font-weight:700; color:#1f2937;"><?php echo e($urgent->stock?->description ?? 'Unknown'); ?></div>
                            <div style="font-size:12px; color:#6b7280; margin-top:2px;">Qty: <?php echo e($urgent->total); ?></div>
                            <?php if($urgent->stock?->price): ?>
                                <div style="font-size:12px; color:#6b7280; margin-top:2px;">Value: ₱<?php echo e(number_format($urgent->stock->price * $urgent->total, 2)); ?></div>
                            <?php endif; ?>
                        </div>
                        <?php if($urgent->received_by): ?>
                            <div style="margin-top:8px; padding:6px 10px; background:linear-gradient(135deg, #fef2f2, #fee2e2); border-radius:8px; border:1px solid #fca5a5;">
                                <div style="font-size:12px; color:#991b1b; font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">Received By</div>
                                <div style="font-size:14px; font-weight:800; color:#7f1d1d; margin-top:2px;"><?php echo e($urgent->received_by); ?></div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div id="urgent-<?php echo e($urgent->id); ?>" class="card-body hidden">
                    <div style="font-weight:700; margin-bottom:12px; color:var(--text); font-size:14px;">Urgent Request Details</div>
                    <div style="display:grid; gap:8px;">
                        <div style="padding:12px; background:linear-gradient(135deg, #fef2f2, #fee2e2); border-radius:8px;">
                            <div style="font-weight:600; color:#7f1d1d; margin-bottom:4px;"><?php echo e($urgent->stock?->description ?? 'Unknown Item'); ?></div>
                            <div style="display:flex; gap:16px; font-size:12px; color:#991b1b;">
                                <span><strong>ID:</strong> <?php echo e($urgent->stock?->id_no ?? 'N/A'); ?></span>
                                <span><strong>Unit:</strong> <?php echo e($urgent->stock?->unit ?? 'N/A'); ?></span>
                                <span><strong>Quantity:</strong> <?php echo e($urgent->total); ?></span>
                                <?php if($urgent->stock?->price): ?>
                                    <span><strong>Value:</strong> ₱<?php echo e(number_format($urgent->stock->price * $urgent->total, 2)); ?></span>
                                <?php endif; ?>
                            </div>
                            <?php if($urgent->reason): ?>
                                <div style="margin-top:8px; padding:6px 8px; background:#fef9c3; border-radius:6px; border:1px solid #fde047;">
                                    <strong style="color:#854d0e;">Reason:</strong> <?php echo e($urgent->reason); ?>

                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <?php endif; ?>
        
        <hr style="margin:24px 0; border:none; border-top:1px solid #e2e8f0;">
    <?php endif; ?>

    <!-- Direct Requests Section -->
    <?php if(isset($directRequests) && $directRequests->count() > 0): ?>
        <h3 style="margin:24px 0 12px 0; color:#10b981; font-size:18px; font-weight:800;">Direct Requests</h3>
        <?php $__empty_1 = true; $__currentLoopData = $directRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="card" style="margin-bottom:14px; border-left:4px solid #10b981;">
                <div class="card-head" onclick="toggleReq('direct-<?php echo e($request->id); ?>')">
                    <div>
                        <div class="card-title" style="color:#10b981;">Direct Request #<?php echo e($request->id); ?></div>
                        <div class="card-sub">Created <?php echo e($request->created_at?->format('M d, Y @ h:i A')); ?></div>
                        <div style="margin-top:8px; padding:6px 10px; background:linear-gradient(135deg, #d1fae5, #a7f3d0); border-radius:8px; border:1px solid #6ee7b7;">
                            <div style="font-size:12px; color:#047857; font-weight:600; text-transform:uppercase; letter-spacing:0.5px;"><?php if($request->member): ?> Member <?php else: ?> Client <?php endif; ?></div>
                            <div style="font-size:14px; font-weight:800; color:#065f46; margin-top:2px;"><?php echo e($request->member?->name ?? $request->client?->name ?? 'Unknown'); ?></div>
                            <?php if($request->member): ?>
                                <div style="font-size:12px; color:#047857; margin-top:4px;">Client: <?php echo e($request->client?->name ?? 'Unknown'); ?></div>
                            <?php else: ?>
                                <div style="font-size:12px; color:#047857; margin-top:4px;">Office: <?php echo e($request->office ?? 'Not specified'); ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <?php if($request->reason): ?>
                            
                        <?php endif; ?>
                        <div style="margin-top:8px;">
                            <span style="padding:4px 8px; border-radius:6px; background:#10b981; color:#fff; font-size:11px; font-weight:700;">Direct request</span>
                        </div>
                    </div>

                    <div style="text-align:right; min-width:160px;">
                        <div style="font-size:12px; color:var(--muted); text-transform:uppercase; letter-spacing:0.5px; font-weight:600;">Item</div>
                        <div style="margin-top:4px;">
                            <div style="font-weight:700; color:#1f2937;"><?php echo e($request->stock?->description ?? 'Unknown'); ?></div>
                            <div style="font-size:12px; color:#6b7280; margin-top:2px;">Qty: <?php echo e($request->total); ?></div>
                            <?php if($request->stock?->price): ?>
                                <div style="font-size:12px; color:#6b7280; margin-top:2px;">Value: ₱<?php echo e(number_format($request->stock->price * $request->total, 2)); ?></div>
                            <?php endif; ?>
                        </div>
                        <?php if($request->received_by): ?>
                            <div style="margin-top:8px; padding:6px 10px; background:linear-gradient(135deg, #f0f9ff, #e0f2fe); border-radius:8px; border:1px solid #93c5fd;">
                                <div style="font-size:12px; color:#1e40af; font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">Received By</div>
                                <div style="font-size:14px; font-weight:800; color:#1e3a8a; margin-top:2px;"><?php echo e($request->received_by); ?></div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div id="direct-<?php echo e($request->id); ?>" class="card-body hidden">
                    <div style="font-weight:700; margin-bottom:12px; color:var(--text); font-size:14px;">Direct Request Details</div>
                    <div style="display:grid; gap:8px;">
                        <div style="padding:12px; background:linear-gradient(135deg, #d1fae5, #a7f3d0); border-radius:8px;">
                            <div style="font-weight:600; color:#065f46; margin-bottom:4px;"><?php echo e($request->stock?->description ?? 'Unknown Item'); ?></div>
                            <div style="display:flex; gap:16px; font-size:12px; color:#047857;">
                                <span><strong>ID:</strong> <?php echo e($request->stock?->id_no ?? 'N/A'); ?></span>
                                <span><strong>Unit:</strong> <?php echo e($request->stock?->unit ?? 'N/A'); ?></span>
                                <span><strong>Quantity:</strong> <?php echo e($request->total); ?></span>
                                <?php if($request->stock?->price): ?>
                                    <span><strong>Value:</strong> ₱<?php echo e(number_format($request->stock->price * $request->total, 2)); ?></span>
                                <?php endif; ?>
                            </div>
                            <?php if($request->reason): ?>
                                <div style="margin-top:8px; padding:6px 8px; background:#fef9c3; border-radius:6px; border:1px solid #fde047;">
                                    <strong style="color:#854d0e;">Reason:</strong> <?php echo e($request->reason); ?>

                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <?php endif; ?>
    <?php endif; ?>

    <!-- Inbound Records Section -->
    <?php if(isset($inbounds) && $inbounds->count() > 0): ?>
        <h3 style="margin:24px 0 12px 0; color:#3b82f6; font-size:18px; font-weight:800;">Inbound Records</h3>
        
        <!-- Import Batches (Grouped Records) -->
        <?php if(isset($groupedInbounds) && $groupedInbounds->count() > 0): ?>
            <?php $__empty_1 = true; $__currentLoopData = $groupedInbounds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $batchKey => $batch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $firstRecord = $batch->first();
                    $totalItems = $batch->count();
                    $totalQuantity = $batch->sum('total');
                    $batchTime = $firstRecord->created_at;
                ?>
                <div class="card" style="margin-bottom:14px; border-left:4px solid #10b981;">
                    <div class="card-head" onclick="toggleReq('import-batch-<?php echo e($batchKey); ?>')">
                        <div>
                            <div class="card-title" style="color:#10b981;">Import Batch</div>
                            <div class="card-sub">Imported <?php echo e($batchTime?->format('M d, Y @ h:i A')); ?></div>
                            <div style="margin-top:8px; padding:6px 10px; background:linear-gradient(135deg, #d1fae5, #a7f3d0); border-radius:8px; border:1px solid #6ee7b7;">
                                <div style="font-size:12px; color:#047857; font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">Import Summary</div>
                                <div style="font-size:14px; font-weight:800; color:#065f46; margin-top:2px;"><?php echo e($totalItems); ?> items • <?php echo e($totalQuantity); ?> total units</div>
                            </div>
                        </div>
                        <div style="text-align:right; min-width:160px;">
                            <div style="font-size:12px; color:var(--muted); text-transform:uppercase; letter-spacing:0.5px; font-weight:600;">Type</div>
                            <div style="margin-top:4px;">
                                <span style="padding:4px 8px; border-radius:6px; background:#10b981; color:#fff; font-size:12px; font-weight:700;">IMPORT</span>
                            </div>
                            <div style="font-size:12px; color:var(--muted); margin-top:4px;">Batch ID: <span style="font-weight:700; color:var(--text);"><?php echo e($batchKey); ?></span></div>
                        </div>
                    </div>

                    <div id="import-batch-<?php echo e($batchKey); ?>" class="card-body hidden">
                        <div style="font-weight:700; margin-bottom:12px; color:var(--text); font-size:14px;">Import Details</div>
                        <div style="display:grid; gap:8px;">
                            <?php $__currentLoopData = $batch; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inbound): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div style="padding:12px; background:linear-gradient(135deg, #d1fae5, #a7f3d0); border-radius:8px;">
                                    <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:16px;">
                                        <div style="flex:1;">
                                            <div style="font-weight:600; color:#047857; margin-bottom:4px;"><?php echo e($inbound->stock?->description ?? 'Unknown Item'); ?></div>
                                            <div style="display:flex; gap:16px; font-size:12px; color:#047857;">
                                                <span><strong>ID:</strong> <?php echo e($inbound->stock?->id_no ?? 'N/A'); ?></span>
                                                <span><strong>Unit:</strong> <?php echo e($inbound->stock?->unit ?? 'N/A'); ?></span>
                                                <span><strong>Quantity:</strong> <?php echo e($inbound->total); ?></span>
                                                <?php if($inbound->stock?->category_name): ?>
                                                    <span><strong>Category:</strong> <?php echo e($inbound->stock->category_name); ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div style="text-align:right;">
                                            <div style="font-size:14px; font-weight:700; color:#047857;">+<?php echo e($inbound->total); ?> <?php echo e($inbound->stock?->unit ?? 'pcs'); ?></div>
                                            <div style="font-size:12px; color:#64748b; margin-top:2px;">Added to stock</div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Manual Inbound Records -->
        <?php if(isset($manualInbounds) && $manualInbounds->count() > 0): ?>
            <?php $__empty_1 = true; $__currentLoopData = $manualInbounds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inbound): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="card" style="margin-bottom:14px; border-left:4px solid #3b82f6;">
                    <div class="card-head" onclick="toggleReq('inbound-<?php echo e($inbound->id); ?>')">
                        <div>
                            <div class="card-title" style="color:#3b82f6;">Inbound #<?php echo e($inbound->id); ?></div>
                            <div class="card-sub">Created <?php echo e($inbound->created_at?->format('M d, Y @ h:i A')); ?></div>
                            <div style="margin-top:8px; padding:6px 10px; background:linear-gradient(135deg, #eff6ff, #dbeafe); border-radius:8px; border:1px solid #93c5fd;">
                                <div style="font-size:12px; color:#1e40af; font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">Stock Item</div>
                                <div style="font-size:14px; font-weight:800; color:#1e3a8a; margin-top:2px;"><?php echo e($inbound->stock?->description ?? 'Unknown Item'); ?></div>
                                <?php if($inbound->stock?->category_name): ?>
                                    <div style="font-size:12px; color:#1e40af; margin-top:4px;">Category: <?php echo e($inbound->stock->category_name); ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div style="text-align:right; min-width:160px;">
                            <div style="font-size:12px; color:var(--muted); text-transform:uppercase; letter-spacing:0.5px; font-weight:600;">Type</div>
                            <div style="margin-top:4px;">
                                <span style="padding:4px 8px; border-radius:6px; background:#3b82f6; color:#fff; font-size:12px; font-weight:700;">MANUAL</span>
                            </div>
                            <?php if($inbound->stock?->id_no): ?>
                                <div style="font-size:12px; color:var(--muted); margin-top:4px;">ID: <span style="font-weight:700; color:var(--text);"><?php echo e($inbound->stock->id_no); ?></span></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div id="inbound-<?php echo e($inbound->id); ?>" class="card-body hidden">
                        <div style="font-weight:700; margin-bottom:12px; color:var(--text); font-size:14px;">Inbound Details</div>
                        <div style="padding:12px; background:linear-gradient(135deg, #eff6ff, #dbeafe); border-radius:8px;">
                            <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:16px;">
                                <div style="flex:1;">
                                    <div style="font-weight:600; color:#1e40af; margin-bottom:4px;"><?php echo e($inbound->stock?->description ?? 'Unknown Item'); ?></div>
                                    <div style="display:flex; gap:16px; font-size:12px; color:#1e40af;">
                                        <span><strong>ID:</strong> <?php echo e($inbound->stock?->id_no ?? 'N/A'); ?></span>
                                        <span><strong>Unit:</strong> <?php echo e($inbound->stock?->unit ?? 'N/A'); ?></span>
                                        <span><strong>Quantity:</strong> <?php echo e($inbound->total); ?></span>
                                        <?php if($inbound->stock?->category_name): ?>
                                            <span><strong>Category:</strong> <?php echo e($inbound->stock->category_name); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div style="text-align:right;">
                                    <div style="font-size:14px; font-weight:700; color:#1e40af;">+<?php echo e($inbound->total); ?> <?php echo e($inbound->stock?->unit ?? 'pcs'); ?></div>
                                    <div style="font-size:12px; color:#64748b; margin-top:2px;">Added to stock</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <?php endif; ?>
        <?php endif; ?>

        <?php if((isset($groupedInbounds) && $groupedInbounds->count() == 0) && (isset($manualInbounds) && $manualInbounds->count() == 0)): ?>
            <div style="text-align:center; padding:40px; color:var(--muted);">
                No inbound records found.
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <script>
        function toggleReq(id){
            const el = document.getElementById(id);
            if(!el) return;
            el.classList.toggle('hidden');
        }

        // Auto-update filters without needing a button.
        (function(){
            const input = document.getElementById('filterQuery');
            const select = document.getElementById('filterOffice');
            const form = document.getElementById('filterForm');

            if (!form) return;

            let timeout;
            const submit = () => {
                const params = new URLSearchParams();
                const q = input?.value?.trim();
                const office = select?.value?.trim();
                
                // Get selected radio button
                const typeRadio = document.querySelector('input[name="type"]:checked');
                const type = typeRadio ? typeRadio.value : 'all';

                if (q) params.set('q', q);
                if (office) params.set('office', office);
                if (type && type !== 'all') params.set('type', type);

                const url = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
                window.location.href = url;
            };

            input?.addEventListener('input', () => {
                clearTimeout(timeout);
                timeout = setTimeout(submit, 500);
            });

            select?.addEventListener('change', submit);
        })();
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/admin/summary.blade.php ENDPATH**/ ?>