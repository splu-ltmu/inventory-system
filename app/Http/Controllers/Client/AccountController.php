<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\ClientSubaccount;
use App\Models\ClientSubaccountAllocation;
use App\Models\StockRequestItem;
use App\Models\ClientSubaccountMember;
use App\Models\ClientSubaccountDistribution;

class AccountController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $subaccounts = collect();

        if ($user->role === 'client') {
            $subaccounts = ClientSubaccount::where('client_user_id', $user->id)
                ->with(['user', 'members', 'allocations.stockRequestItem.stock'])
                ->withCount('members')
                ->get();
        }

        $approvedInventory = StockRequestItem::with('stock')
            ->whereHas('request', function ($query) use ($user) {
                $clientId = $user->role === 'subaccount' && $user->parent_client_id ? $user->parent_client_id : $user->id;
                $query->where('client_id', $clientId)
                    ->whereIn('status', ['approved', 'ready_to_receive', 'released']);
            })
            ->where('approved_qty', '>', 0)
            ->withSum('allocations', 'allocated_qty')
            ->get();

        return view('client.account', compact('user', 'subaccounts', 'approvedInventory'));
    }

    public function updateEmail(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($user->id),
            ],
        ]);

        $user->email = $validated['email'];
        $user->save();

        return redirect()->route('client.account')->with('success', 'Email updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
            'new_password_confirmation' => 'required',
        ]);

        $user = auth()->user();

        // Check current password
        if (!Hash::check($validated['current_password'], $user->password)) {
            return redirect()->route('client.account')->with('error', 'Current password is incorrect.');
        }

        // Update password
        $user->password = Hash::make($validated['new_password']);
        $user->save();

        return redirect()->route('client.account')->with('success', 'Password updated successfully.');
    }

    public function distributeToSubaccounts(Request $request)
    {
        $validated = $request->validate([
            'subaccount_id' => 'required|exists:client_subaccounts,id',
            'stock_request_item_id' => 'required|exists:stock_request_items,id',
            'allocated_qty' => 'required|integer|min:1',
        ]);

        $subaccount = ClientSubaccount::findOrFail($validated['subaccount_id']);
        if ($subaccount->client_user_id !== Auth::id()) {
            abort(403);
        }

        $item = StockRequestItem::with('request')->findOrFail($validated['stock_request_item_id']);
        if ($item->request->client_id !== Auth::id()) {
            abort(403);
        }

        $allocatedQty = ClientSubaccountAllocation::where('stock_request_item_id', $item->id)
            ->sum('allocated_qty');

        $remaining = (int)$item->approved_qty - (int)$allocatedQty;
        if ($validated['allocated_qty'] > $remaining) {
            return redirect()->route('client.account')->with('error', 'Not enough approved quantity available for allocation.');
        }

        $allocation = ClientSubaccountAllocation::firstOrNew([
            'subaccount_id' => $subaccount->id,
            'stock_request_item_id' => $item->id,
        ]);
        $allocation->allocated_qty = ($allocation->allocated_qty ?? 0) + $validated['allocated_qty'];
        $allocation->save();

        return redirect()->route('client.account')->with('success', 'Item allocated to subaccount successfully.');
    }
}
