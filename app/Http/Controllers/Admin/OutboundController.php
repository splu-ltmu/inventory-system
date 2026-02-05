<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Outbound;
use App\Models\Stock;
use App\Models\StockRequest;
use App\Models\StockRequestItem;
use Illuminate\Support\Facades\DB;

class OutboundController extends Controller
{
    public function index()
    {
        $outbounds = Outbound::with(['stock', 'client'])->latest()->get();
        return view('admin.outbound.index', compact('outbounds'));
    }

    public function create()
    {
        $stocks = Stock::all();
        $clients = \App\Models\User::where('role', 'client')->get();
        return view('admin.outbound.create', compact('stocks', 'clients'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'stock_id'  => 'required|exists:stocks,id',
            'client_id' => 'required|exists:users,id',
            'office'    => 'required|string',
            'total'     => 'required|integer|min:1',
        ]);

        // Approval and status are set automatically when admin creates an outbound
        $data = $request->only('stock_id','client_id','office','total');
        $data['approval'] = 'approved';
        $data['status'] = 'received';

        // If status is received on create, perform deduction and set deducted_at atomically
        if (($data['status'] ?? '') === 'received') {
            try {
                DB::transaction(function() use ($data) {
                    // lock stock
                    $stock = Stock::where('id', $data['stock_id'])->lockForUpdate()->firstOrFail();

                    if ($data['total'] > $stock->stock) {
                        throw new \Exception("Not enough stock to deduct. Available: {$stock->stock}, Outbound: {$data['total']}");
                    }

                    // create outbound with deducted_at
                    $out = Outbound::create($data + ['deducted_at' => now()]);

                    // decrement stock
                    $stock->decrement('stock', $data['total']);
                });
            } catch (\Throwable $e) {
                return back()->with('error', $e->getMessage())->withInput();
            }

            return redirect()->route('outbound.index')->with('success', 'Outbound created and stock deducted.');
        }

        // default: create without deduction
        $out = Outbound::create($data + ['deducted_at' => null]);

        // If approval isn't approved and status isn't received, create a pending StockRequest
        if (($data['approval'] ?? '') !== 'approved' && ($data['status'] ?? '') !== 'received') {
            try {
                DB::transaction(function() use ($data, $out) {
                    $req = StockRequest::create([
                        'client_id' => $data['client_id'],
                        'office' => $data['office'] ?? '',
                        'status' => 'pending',
                    ]);

                    StockRequestItem::create([
                        'stock_request_id' => $req->id,
                        'stock_id' => $data['stock_id'],
                        'requested_qty' => $data['total'],
                        'approved_qty' => 0,
                    ]);
                });
            } catch (\Throwable $e) {
                // log but continue — outbound was created
                \Log::error('Failed to create stock request for outbound: '.$e->getMessage());
            }
        }

        return redirect()->route('outbound.index')->with('success', 'Outbound created.');
    }

    public function update(Request $request, Outbound $outbound)
    {
        $request->validate([
            'status' => 'required|in:on process,declined,received',
        ]);

        // ✅ If marking RECEIVED, deduct stock ONCE
        if ($request->status === 'received') {

            // already deducted before? do nothing
            if ($outbound->deducted_at) {
                $outbound->status = 'received';
                $outbound->save();

                return back()->with('success', 'Outbound updated (already deducted before).');
            }

            try {
                DB::transaction(function () use ($outbound) {

                    // lock outbound row
                    $ob = Outbound::where('id', $outbound->id)->lockForUpdate()->firstOrFail();

                    // lock stock row
                    $stock = Stock::where('id', $ob->stock_id)->lockForUpdate()->firstOrFail();

                    // safety check
                    if ($ob->total > $stock->stock) {
                        throw new \Exception("Not enough stock to deduct. Available: {$stock->stock}, Outbound: {$ob->total}");
                    }

                    // ✅ deduct stock
                    $stock->decrement('stock', $ob->total);

                    // ✅ mark deducted
                    $ob->deducted_at = now();
                    $ob->status = 'received';
                    $ob->save();
                });

            } catch (\Throwable $e) {
                return back()->with('error', $e->getMessage());
            }

            return back()->with('success', 'Outbound marked RECEIVED and stock deducted.');
        }

        // other statuses: just update (no deduction)
        $outbound->status = $request->status;
        $outbound->save();

        return back()->with('success', 'Outbound updated.');
    }
}
