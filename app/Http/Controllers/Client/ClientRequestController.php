<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StockRequest;
use App\Models\StockRequestItem;
use App\Models\Stock;
use Illuminate\Support\Facades\Auth;

class ClientRequestController extends Controller
{
    public function index()
    {
        $requests = StockRequest::with(['member', 'items.stock'])
            ->where('client_id', Auth::id())
            ->latest()
            ->get();

        return view('client.requests.index', compact('requests'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'items'     => ['required', 'array', 'min:1'], // items[stockId] = qty
            'items.*'    => ['required', 'integer', 'min:1'],
            'member_id'  => ['nullable', 'integer', 'exists:client_members,id'],
            'reason'     => ['nullable', 'string', 'max:1000'],
        ]);

        // Prepare and validate items first (prevent creating empty header)
        $prepared = [];
        foreach ($data['items'] as $stockId => $qty) {
            $stock = Stock::find($stockId);
            if (!$stock) continue;

            $qty = max(1, (int)$qty);
            $qty = min($qty, (int)$stock->stock);
            if ($qty <= 0) continue;

            $prepared[] = [
                'stock' => $stock,
                'stock_id' => $stockId,
                'qty' => $qty,
            ];
        }

        if (empty($prepared)) {
            return redirect()->back()->withInput()->with('error', 'No valid items to request.');
        }

        // use the logged-in user's office (fallback to any submitted value)
        $office = Auth::user()->office ?? $request->input('office', null);

        // Create request + items inside a transaction
        \DB::transaction(function () use ($prepared, $office, $data, &$stockRequest) {
            $stockRequest = StockRequest::create([
                'client_id' => Auth::id(),
                'member_id' => $data['member_id'] ?? null,
                'office' => $office,
                'reason' => $data['reason'] ?? null,
                'status' => 'pending',
                'verification_code' => null,
            ]);

            foreach ($prepared as $p) {
                StockRequestItem::create([
                    'stock_request_id' => $stockRequest->id,
                    'stock_id'         => $p['stock_id'],
                    'requested_qty'    => $p['qty'],
                    'approved_qty'     => 0,
                    // 'status' will use DB default (pending)
                ]);
            }
        });

        return redirect()->route('client.requests')
            ->with('success', 'Request submitted. Wait for admin approval.');
    }

    public function cancel($id)
    {
        $request = StockRequest::find($id);

        if (!$request) {
            return response()->json(['error' => 'Request not found.'], 404);
        }

        // Authorization: must be the client who created the request
        if ($request->client_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }

        // Can only cancel pending requests
        if ($request->status !== 'pending') {
            return response()->json(['error' => 'Only pending requests can be cancelled.'], 422);
        }

        // Update status to cancelled
        $request->update(['status' => 'cancelled']);

        return response()->json(['success' => 'Request cancelled successfully.'], 200);
    }
}
