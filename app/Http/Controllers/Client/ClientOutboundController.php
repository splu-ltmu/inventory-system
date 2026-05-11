<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Outbound;
use App\Models\Stock;
use App\Models\StockRequestItem;
use App\Models\ClientMember;
use App\Models\ClientDirectDeduction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ClientOutboundController extends Controller
{
    public function index()
    {
        $outbounds = Outbound::with(['stock', 'member'])
            ->where('client_id', Auth::id())
            ->where('is_direct_request', true)
            ->latest()
            ->get();

        return view('client.outbounds.index', compact('outbounds'));
    }

    public function create()
    {
        $user = Auth::user();
        
        // Get client's available inventory (approved items minus what's already distributed)
        $availableInventory = StockRequestItem::with(['stock'])
            ->whereHas('request', function($query) use ($user) {
                $query->where('client_id', $user->id)
                      ->whereIn('status', ['approved', 'ready_to_receive', 'released']);
            })
            ->where('approved_qty', '>', 0)
            ->get()
            ->map(function($item) {
                $distributed = $item->distributed_qty ?? 0;
                $available = max(0, $item->approved_qty - $distributed);
                $item->available_qty = $available;
                return $item;
            })
            ->filter(function($item) {
                return $item->available_qty > 0;
            });

        // Get client's members for distribution
        $members = ClientMember::where('client_id', Auth::id())->get();

        return view('client.outbounds.create', compact('availableInventory', 'members'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'stock_request_item_id' => 'required|exists:stock_request_items,id',
            'member_id' => 'nullable|exists:client_members,id',
            'total' => 'required|integer|min:1',
            'reason' => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();
        $stockRequestItem = StockRequestItem::findOrFail($request->stock_request_item_id);

        // Verify this item belongs to the authenticated client
        if ($stockRequestItem->request->client_id !== $user->id) {
            return back()->with('error', 'You can only create outbounds from your own inventory.');
        }

        // Calculate available inventory
        $distributed = $stockRequestItem->distributed_qty ?? 0;
        $availableInventory = max(0, $stockRequestItem->approved_qty - $distributed);

        // Check if enough inventory is available
        if ($request->total > $availableInventory) {
            return back()->with('error', 'Not enough inventory available. Available: ' . $availableInventory . ', Requested: ' . $request->total);
        }

        // If member_id is provided, verify member belongs to this client
        if ($request->member_id) {
            $member = ClientMember::where('id', $request->member_id)
                ->where('client_id', $user->id)
                ->firstOrFail();
        }

        DB::transaction(function () use ($request, $stockRequestItem, $user) {
            // Create the outbound record
            $outbound = Outbound::create([
                'stock_id' => $stockRequestItem->stock_id,
                'client_id' => $user->id,
                'member_id' => $request->member_id,
                'office' => $user->office,
                'total' => $request->total,
                'reason' => $request->reason,
                'approval' => 'approved',
                'status' => 'received',
                'is_direct_request' => true,
                'deducted_at' => now(),
            ]);

            // Create direct deduction record to track the inventory usage
            ClientDirectDeduction::create([
                'client_id' => $user->id,
                'stock_request_item_id' => $stockRequestItem->id,
                'member_id' => $request->member_id,
                'deducted_qty' => $request->total,
                'reason' => $request->reason ?? 'Direct outbound creation',
            ]);

            // Update the distributed_qty in stock_request_items
            $stockRequestItem->increment('distributed_qty', $request->total);
        });

        return redirect()->route('client.outbounds.index')
            ->with('success', 'Outbound created successfully and inventory deducted.');
    }

    public function show(Outbound $outbound)
    {
        // Verify this outbound belongs to the authenticated client
        if ($outbound->client_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $outbound->load(['stock', 'member']);

        return view('client.outbounds.show', compact('outbound'));
    }
}
