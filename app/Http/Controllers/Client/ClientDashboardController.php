<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ClientSubaccount;
use App\Models\Outbound;
use App\Models\StockRequest;
use Illuminate\Support\Facades\Auth;

class ClientDashboardController extends Controller
{
    public function index()
    {
        if (Auth::user()->role === 'subaccount') {
            $subaccount = ClientSubaccount::where('user_id', Auth::id())->first();
            if ($subaccount) {
                return redirect()->route('client.account.subaccounts.show', $subaccount);
            }
        }

        return view('client.dashboard');
    }

    public function summary()
    {
        $requests = StockRequest::with(['items.stock'])
            ->where('client_id', Auth::id())
            ->latest()
            ->get();

        $counts = $requests->groupBy('status')->map->count();

        return view('client.summary', [
            'counts' => $counts,
            'requests' => $requests,
        ]);
    }

    public function inventory()
    {
        $outbounds = Outbound::with(['stock'])
            ->where('client_id', Auth::id())
            ->where('approval', 'approved')
            ->whereIn('status', ['on process', 'received'])
            ->latest()
            ->get();

        return view('client.inventory', [
            'outbounds' => $outbounds,
        ]);
    }
}
