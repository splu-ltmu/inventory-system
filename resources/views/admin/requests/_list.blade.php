@forelse($shown as $req)
    @php $rid = 'req-'.$req->id; @endphp

    <div class="req-card">
        <div class="req-header" onclick="toggleReq('{{ $rid }}')">
            <div>
                <div class="req-title">
                    Request from <span style="color:#2563eb;">{{ $req->office }}</span>
                    <span class="muted">•</span>
                    <span class="muted">{{ $req->client?->name ?? 'Client' }}</span>
                    <span class="muted">•</span>
                    <span class="muted">{{ $req->created_at?->format('M d, Y') }}</span>
                </div>

                <div class="req-sub">
                    <span class="muted">Status:</span>
                    <span class="status-pill">{{ strtoupper(str_replace('_',' ', $req->status)) }}</span>
                    <span class="muted" style="margin-left:10px;">Request ID:</span>
                    <b>#{{ $req->id }}</b>
                </div>
            </div>

            <div class="req-right">
                Ref. No:
                <span style="color:#0f172a;">#{{ $req->id }}</span>
                <div class="muted" style="font-size:12px; font-weight:600; margin-top:4px;">Click to view details</div>
            </div>
        </div>

        <div id="{{ $rid }}" class="req-body">
            <div class="muted" style="margin-bottom:10px;">Approve partially by setting Approved Qty per item (0 = rejected item).</div>

            <form method="POST" action="{{ route('admin.requests.decision', $req->id) }}">
                @csrf
                @method('PUT')

                <div style="overflow:auto; border-radius:12px; border:1px solid #e2e8f0;">
                    <table>
                        <tr>
                            <th style="min-width:200px;">Item</th>
                            <th style="min-width:140px;">Requested</th>
                            <th style="min-width:140px;">Available</th>
                            <th style="min-width:160px;">Approved Qty</th>
                        </tr>

                        @forelse($req->items as $item)
                            <tr>
                                <td style="text-align:left;">
                                    <b>{{ $item->stock?->id_no ?? '' }}</b> — {{ $item->stock?->description ?? 'N/A' }}
                                    <div class="muted" style="font-size:12px;">Unit: {{ $item->stock?->unit ?? '—' }}</div>
                                </td>

                                <td>{{ $item->requested_qty }}</td>
                                <td>{{ $item->stock?->stock ?? 0 }}</td>

                                <td style="min-width:160px;">
                                    <div style="display:flex; gap:6px; align-items:center;">
                                        <input
                                            type="number"
                                            name="approved_qty[{{ $item->id }}]"
                                            min="0"
                                            max="{{ $item->stock?->stock ?? 0 }}"
                                            value="{{ $item->approved_qty ?? 0 }}"
                                            {{ $activeTab !== 'pending' ? 'readonly' : '' }}
                                            style="flex:1; text-align:center;"
                                        >
                                        @if($activeTab === 'pending')
                                            <button type="button" class="btn-max" onclick="setMax(this, {{ $item->requested_qty }})" style="padding:8px 10px; border-radius:10px; border:1px solid #2563eb; background:#2563eb; color:#fff; cursor:pointer; font-weight:700; white-space:nowrap; flex-shrink:0;">Max</button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="muted">No request items found for this request.</td>
                            </tr>
                        @endforelse
                    </table>
                </div>

                @if($req->status !== 'ready_to_receive')
                    <div style="display:flex; gap:10px; flex-wrap:wrap; margin-top:12px;">
                        @if($req->status !== 'approved')
                            <button class="btn-ghost" type="button" onclick="confirmAction(event, null, 'Save Decision', 'Save the approval quantities for this request?', '{{ $req->id }}')">
                                Save Decision
                            </button>
                        @endif

                        @if($req->status !== 'pending')
                            <button class="btn-ghost" type="button" onclick="confirmAction(event, 'rejected', 'Reject Entire Request', 'This request will be rejected. This action cannot be undone.', '{{ $req->id }}')">
                                Reject Whole Request
                            </button>

                            <button class="btn-ghost" type="button" onclick="confirmAction(event, 'ready_to_receive', 'Generate Code', 'Proceed to generate a verification code for the client to claim these items.', '{{ $req->id }}')">
                                Ready to Receive
                            </button>
                        @endif
                    </div>
                @endif
            </form>

            @if($req->status === 'ready_to_receive')
                <hr style="border:none; border-top:1px solid #e2e8f0; margin:16px 0;">

                <form method="POST" action="{{ route('admin.requests.release', $req->id) }}">
                    @csrf
                    @method('PUT')

                    <div style="background:#eff6ff; border:1px solid #2563eb; border-radius:10px; padding:12px; display:flex; gap:10px; align-items:flex-end;">
                        <div style="flex:1; min-width:200px;">
                            <label style="font-size:12px; font-weight:700; color:#0f172a; display:block; margin-bottom:6px;">🔐 Client Code</label>
                            <input type="text" name="verification_code" placeholder="Enter code" required style="padding:10px;">
                        </div>

                        <button class="btn" type="submit" style="padding:10px 16px;">Release</button>
                    </div>
                </form>
            @endif
        </div>
    </div>

@empty
    <div class="muted">No requests found.</div>
@endforelse
