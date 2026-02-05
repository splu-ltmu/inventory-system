<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Inbound;
use App\Models\Stock;

class InboundController extends Controller
{
    public function index()
    {
        $inbounds = Inbound::with('stock')->get();
        return view('admin.inbound.index', compact('inbounds'));
    }

    public function create()
    {
        $stocks = Stock::all();
        return view('admin.inbound.create', compact('stocks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'stock_id' => 'required|exists:stocks,id',
            'total' => 'required|integer|min:1'
        ]);

        $inbound = Inbound::create($request->only('stock_id', 'total'));

        // update stock
        $stock = Stock::find($request->stock_id);
        $stock->total += $request->total;
        $stock->stock += $request->total;
        $stock->save();

        return redirect()->route('inbound.index')->with('success', 'Inbound added and stock updated.');
    }
}
