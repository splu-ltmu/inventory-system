<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StockRequest;
use App\Models\PasswordResetRequest;
use App\Models\Category;
use App\Models\Stock;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $pendingRequests = StockRequest::where('status', 'pending')->count();
        $pendingPasswordResets = PasswordResetRequest::where('status', 'pending')->count();

        // Gather category analytics
        $categories = Category::all();
        $categoryAnalytics = [];

        foreach ($categories as $category) {
            // Total stock availability for this category
            $totalAvailability = Stock::where('category_id', $category->id)
                ->sum('stock');

            // Total approved items from outbound records for this category
            $totalRequested = \DB::table('outbounds')
                ->join('stocks', 'outbounds.stock_id', '=', 'stocks.id')
                ->where('stocks.category_id', $category->id)
                ->where('outbounds.approval', 'approved')
                ->sum('outbounds.total') ?? 0;

            $categoryAnalytics[] = [
                'name' => $category->name,
                'availability' => $totalAvailability ?? 0,
                'requested' => $totalRequested ?? 0,
            ];
        }

        return view('admin.dashboard', compact('pendingRequests', 'pendingPasswordResets', 'categoryAnalytics'));
    }

    /**
     * Show a simple notifications overview for admins.
     */
    public function notifications()
    {
        $pendingRequests = StockRequest::where('status', 'pending')->get();
        $pendingPasswordResets = PasswordResetRequest::where('status', 'pending')->get();
        $lowThreshold = 49;
        $lowStock = \App\Models\Stock::where('stock','>',0)->where('stock','<=',$lowThreshold)->get();
        $outStock = \App\Models\Stock::where('stock','<=',0)->get();

        return view('admin.notifications', compact('pendingRequests','pendingPasswordResets','lowStock','outStock'));
    }

    /**
     * Return notification counts as JSON for AJAX polling.
     */
    public function counts()
    {
        $pendingRequests = \App\Models\StockRequest::where('status', 'pending')->count();
        $pendingPasswordResets = \App\Models\PasswordResetRequest::where('status', 'pending')->count();
        $lowThreshold = 49;
        $lowStock = \App\Models\Stock::where('stock','>',0)->where('stock','<=',$lowThreshold)->count();
        $outStock = \App\Models\Stock::where('stock','<=',0)->count();

        $total = $pendingRequests + $pendingPasswordResets + $lowStock + $outStock;

        return response()->json([
            'pendingRequests' => $pendingRequests,
            'pendingPasswordResets' => $pendingPasswordResets,
            'lowStock' => $lowStock,
            'outStock' => $outStock,
            'total' => $total,
        ]);
    }
}
