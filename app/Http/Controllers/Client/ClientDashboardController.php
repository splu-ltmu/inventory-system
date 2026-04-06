<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\StockRequest;
use Illuminate\Support\Facades\Auth;

class ClientDashboardController extends Controller
{
    public function index()
    {
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
}
