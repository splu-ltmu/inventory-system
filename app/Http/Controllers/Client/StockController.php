<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Stock;

class StockController extends Controller
{
    public function index()
    {
        $stocks = Stock::where('hidden', 0)->get();
        return view('client.stocks.index', compact('stocks'));
    }
}
