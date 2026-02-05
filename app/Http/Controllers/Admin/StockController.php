<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Stock;
use App\Models\Category;

class StockController extends Controller
{
    // Show all stocks
    public function index()
    {
        $stocks = Stock::with('category')->get();
        return view('admin.stocks.index', compact('stocks'));
    }

    // Show form to create a stock
    public function create()
    {
        $categories = Category::all();
        return view('admin.stocks.create', compact('categories'));
    }

    // Store new stock
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'id_no' => 'required|string|unique:stocks,id_no',
            'description' => 'required|string',
            'unit' => 'required|string',
            'stock' => 'required|integer|min:0',
            'hidden' => 'boolean'
        ]);

        Stock::create($request->all());

        return redirect()->route('stocks.index')
            ->with('success', 'Stock added.');
    }

    // Generate next stock ID based on category
    public function generateId($categoryId)
    {
        $category = Category::find($categoryId);
        
        if (!$category || !$category->code) {
            return response()->json(['error' => 'Category code not set'], 400);
        }

        // Get the last stock with this category code
        $lastStock = Stock::where('id_no', 'like', $category->code . '-%')
            ->orderBy('id_no', 'desc')
            ->first();

        if ($lastStock) {
            // Extract number from last ID (e.g., "CS-001" -> "001")
            $lastNumber = (int) explode('-', $lastStock->id_no)[1];
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        $newId = $category->code . '-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        return response()->json(['id_no' => $newId]);
    }
}
