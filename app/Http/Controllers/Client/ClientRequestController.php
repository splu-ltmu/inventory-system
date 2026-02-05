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
        $requests = StockRequest::with(['items.stock'])
            ->where('client_id', Auth::id())
            ->latest()
            ->get();

        return view('client.requests.index', compact('requests'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'office' => ['required', 'string', 'max:255'],
            'items'  => ['required', 'array', 'min:1'], // items[stockId] = qty
            'items.*'=> ['required', 'integer', 'min:1'],
        ]);

        // Create ONE request (header)
        $stockRequest = StockRequest::create([
            'client_id' => Auth::id(),
            'office' => $data['office'],
            'status' => 'pending',
            'verification_code' => null,
        ]);

        // Create request items
        foreach ($data['items'] as $stockId => $qty) {
            $stock = Stock::find($stockId);
            if (!$stock) continue;

            // clamp qty to available stock (optional safeguard)
            $qty = max(1, (int)$qty);
            $qty = min($qty, (int)$stock->stock);

            StockRequestItem::create([
                'stock_request_id' => $stockRequest->id,
                'stock_id'         => $stockId,
                'requested_qty'    => $qty,

                // IMPORTANT: admin hasn't decided yet
                'approved_qty'     => 0,      // ✅ NOT 0
                'status'           => 'pending', // ✅ pending
            ]);
        }

        return redirect()->route('client.requests')
            ->with('success', 'Request submitted. Wait for admin approval.');
    }
}
