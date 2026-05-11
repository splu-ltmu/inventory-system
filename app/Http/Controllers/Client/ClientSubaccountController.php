<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ClientSubaccount;
use App\Models\ClientSubaccountAllocation;
use App\Models\ClientSubaccountDistribution;
use App\Models\ClientSubaccountMember;
use App\Models\StockRequestItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class ClientSubaccountController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'description' => 'nullable|string|max:1000',
        ]);

        $subaccountUser = \App\Models\User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
            'role' => 'subaccount',
            'office' => Auth::user()->office,
            'parent_client_id' => Auth::id(),
        ]);

        ClientSubaccount::create([
            'client_user_id' => Auth::id(),
            'user_id' => $subaccountUser->id,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()->route('client.account', ['tab' => 'subaccounts'])->with('success', 'Subaccount created successfully.');
    }

    public function show(ClientSubaccount $subaccount)
    {
        $this->authorizeSubaccount($subaccount);

        $members = $subaccount->members()
            ->with(['distributions.stockRequestItem.stock'])
            ->withCount(['distributions'])
            ->get();

        $hasUsedQtyColumn = Schema::hasColumn('client_subaccount_distributions', 'used_qty');

        $allocatedItems = ClientSubaccountAllocation::with(['stockRequestItem.stock'])
            ->where('subaccount_id', $subaccount->id)
            ->get()
            ->map(function ($allocation) use ($subaccount, $hasUsedQtyColumn) {
                $allocated = $allocation->allocated_qty;
                $subaccountDistributions = ClientSubaccountDistribution::whereHas('member', function ($query) use ($subaccount) {
                    $query->where('subaccount_id', $subaccount->id);
                })->where('stock_request_item_id', $allocation->stock_request_item_id);

                $distributed = $subaccountDistributions->sum('distributed_qty');
                $used = $hasUsedQtyColumn ? $subaccountDistributions->sum('used_qty') : 0;
                $allocation->member_qty = $distributed;
                $allocation->distributed_qty = $distributed + $used;
                $allocation->remaining_qty = max(0, $allocated - $allocation->distributed_qty);
                return $allocation;
            });

        return view('client.account.subaccounts.show', [
            'subaccount' => $subaccount,
            'members' => $members,
            'allocatedItems' => $allocatedItems,
        ]);
    }

    public function storeMember(Request $request, ClientSubaccount $subaccount)
    {
        $this->authorizeSubaccount($subaccount);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
        ]);

        $subaccount->members()->create($validated);

        return redirect()->route('client.account.subaccounts.show', $subaccount)
            ->with('success', 'Member added successfully.');
    }

    public function storeDistribution(Request $request, ClientSubaccount $subaccount)
    {
        $this->authorizeSubaccount($subaccount);

        $validated = $request->validate([
            'member_id' => 'required|exists:client_subaccount_members,id',
            'stock_request_item_id' => 'required|exists:stock_request_items,id',
            'distributed_qty' => 'required|integer|min:1',
        ]);

        $member = ClientSubaccountMember::find($validated['member_id']);
        if (!$member || $member->subaccount_id !== $subaccount->id) {
            abort(403);
        }

        $allocation = ClientSubaccountAllocation::where('subaccount_id', $subaccount->id)
            ->where('stock_request_item_id', $validated['stock_request_item_id'])
            ->first();

        if (!$allocation) {
            return redirect()->route('client.account.subaccounts.show', $subaccount)
                ->with('error', 'No allocation exists for that item on this subaccount.');
        }

        $allocated = (int)$allocation->allocated_qty;
        $distributed = ClientSubaccountDistribution::whereHas('member', function ($query) use ($subaccount) {
                $query->where('subaccount_id', $subaccount->id);
            })
            ->where('stock_request_item_id', $allocation->stock_request_item_id)
            ->sum('distributed_qty');

        $remaining = max(0, $allocated - $distributed);
        if ($validated['distributed_qty'] > $remaining) {
            return redirect()->route('client.account.subaccounts.show', $subaccount)
                ->with('error', 'Not enough allocated quantity remaining for that item.');
        }

        ClientSubaccountDistribution::create([
            'member_id' => $member->id,
            'stock_request_item_id' => $allocation->stock_request_item_id,
            'distributed_qty' => $validated['distributed_qty'],
        ]);

        return redirect()->route('client.account.subaccounts.show', $subaccount)
            ->with('success', 'Item distributed to member successfully.');
    }

    public function updateDistribution(Request $request, ClientSubaccount $subaccount, ClientSubaccountDistribution $distribution)
    {
        $this->authorizeSubaccount($subaccount);

        if ($distribution->member->subaccount_id !== $subaccount->id) {
            abort(403);
        }

        $allocation = ClientSubaccountAllocation::where('subaccount_id', $subaccount->id)
            ->where('stock_request_item_id', $distribution->stock_request_item_id)
            ->first();

        if (!$allocation) {
            return redirect()->route('client.account.subaccounts.show', $subaccount)
                ->with('error', 'No allocation exists for that item on this subaccount.');
        }

        $allocated = (int)$allocation->allocated_qty;
        $otherDistributed = ClientSubaccountDistribution::whereHas('member', function ($query) use ($subaccount) {
                $query->where('subaccount_id', $subaccount->id);
            })
            ->where('stock_request_item_id', $allocation->stock_request_item_id)
            ->where('id', '!=', $distribution->id)
            ->sum('distributed_qty');

        $groupCurrentQty = ClientSubaccountDistribution::where('member_id', $distribution->member_id)
            ->where('stock_request_item_id', $distribution->stock_request_item_id)
            ->sum('distributed_qty');

        $validated = $request->validate([
            'updated_qty' => 'required|integer|min:0|max:' . max(0, $groupCurrentQty - 1),
        ]);

        $newDistributedQty = $validated['updated_qty'];
        $qtyDifference = $newDistributedQty - $groupCurrentQty;

        $remainingToReduce = max(0, $groupCurrentQty - $newDistributedQty);
        $distributions = ClientSubaccountDistribution::where('member_id', $distribution->member_id)
            ->where('stock_request_item_id', $distribution->stock_request_item_id)
            ->orderBy('id')
            ->get();

        foreach ($distributions as $dist) {
            if ($remainingToReduce <= 0) {
                break;
            }

            $reduceBy = min($dist->distributed_qty, $remainingToReduce);
            $dist->distributed_qty = max(0, $dist->distributed_qty - $reduceBy);
            if (Schema::hasColumn('client_subaccount_distributions', 'used_qty')) {
                $dist->used_qty = ($dist->used_qty ?? 0) + $reduceBy;
            }
            $dist->save();
            $remainingToReduce -= $reduceBy;

            if ($dist->distributed_qty === 0 && (!Schema::hasColumn('client_subaccount_distributions', 'used_qty') || ($dist->used_qty ?? 0) === 0)) {
                $dist->delete();
            }
        }

        if ($qtyDifference < 0) {
            // Decreasing quantity - keep the client-distributed total fixed.
            // Treat the reduced amount as used by the subaccount member.
            $allocation->used_qty = ($allocation->used_qty ?? 0) + abs($qtyDifference);
            $allocation->save();
        }

        return redirect()->route('client.account.subaccounts.show', [
                'subaccount' => $subaccount,
                'tab' => $request->input('tab', 'members'),
            ])
            ->with('success', 'Distribution updated successfully.');
    }

    public function destroyDistribution(Request $request, ClientSubaccount $subaccount, ClientSubaccountDistribution $distribution)
    {
        $this->authorizeSubaccount($subaccount);

        if ($distribution->member->subaccount_id !== $subaccount->id) {
            abort(403);
        }

        $distribution->delete();

        return redirect()->route('client.account.subaccounts.show', $subaccount)
            ->with('success', 'Distribution removed successfully.');
    }

    protected function authorizeSubaccount(ClientSubaccount $subaccount)
    {
        if ($subaccount->client_user_id === Auth::id()) {
            return;
        }

        if (Auth::user()->role === 'subaccount' && $subaccount->user_id === Auth::id()) {
            return;
        }

        abort(403);
    }

    public function updateUsedQty(Request $request, ClientSubaccount $subaccount, ClientSubaccountAllocation $allocation)
    {
        $this->authorizeSubaccount($subaccount);

        if ($allocation->subaccount_id !== $subaccount->id) {
            abort(403);
        }

        $validated = $request->validate([
            'used_qty' => 'required|integer|min:0|max:' . $allocation->allocated_qty,
        ]);

        $allocation->update(['used_qty' => $validated['used_qty']]);

        return redirect()->route('client.account.subaccounts.show', [$subaccount, 'tab' => 'inventory'])
            ->with('success', 'Usage updated successfully.');
    }

    protected function approvedItems()
    {
        return StockRequestItem::whereHas('request', function ($query) {
            $query->where('client_id', Auth::id())
                ->whereIn('status', ['approved', 'ready_to_receive', 'released']);
        })->where('approved_qty', '>', 0);
    }
}
