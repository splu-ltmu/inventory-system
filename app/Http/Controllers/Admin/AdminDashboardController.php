<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StockRequest;
use App\Models\PasswordResetRequest;
use App\Models\Category;
use App\Models\Stock;
use Illuminate\Http\Request;
use Carbon\Carbon;

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

        // --- Office analytics: count requests per office ---
        $officeCounts = \App\Models\StockRequest::select('office', \DB::raw('COUNT(*) as total'))
            ->groupBy('office')
            ->orderByDesc('total')
            ->get();

        // prepare for charting (labels + values)
        $officeAnalytics = $officeCounts->map(function($r){
            return [ 'office' => $r->office ?? 'Unknown', 'count' => (int) $r->total ];
        })->values();

        return view('admin.dashboard', compact('pendingRequests', 'pendingPasswordResets', 'categoryAnalytics', 'officeAnalytics'));
    }

    /**
     * Summary (transaction list): show every request with details.
     */
    public function summary(Request $request)
    {
        $q = trim((string)$request->query('q', ''));
        $office = trim((string)$request->query('office', ''));

        $requestsQuery = StockRequest::with(['client', 'items.stock']);

        if ($q !== '') {
            $clean = ltrim($q, '#');
            $requestsQuery->where(function ($qr) use ($clean) {
                if (is_numeric($clean)) {
                    $qr->where('id', (int)$clean);
                }
                $qr->orWhereHas('client', function ($qc) use ($clean) {
                    $qc->where('name', 'like', "%{$clean}%");
                });
            });
        }

        if ($office !== '') {
            $requestsQuery->where('office', $office);
        }

        $requests = $requestsQuery->latest()->get();

        $offices = StockRequest::select('office')
            ->distinct()
            ->orderBy('office')
            ->pluck('office')
            ->filter();

        return view('admin.summary', compact('requests', 'offices', 'q', 'office'));
    }

    /**
     * Return analytics data filtered by month (YYYY-MM). Used by ajax requests from the dashboard modal.
     */
    public function chartData(Request $request)
    {
        $month = $request->query('month');
        $date = Carbon::now();
        if($month) {
            try {
                $date = Carbon::parse($month . '-01');
            } catch (\Exception $e) {
                // ignore invalid value, stick with now
            }
        }

        $start = $date->copy()->startOfMonth();
        $end = $date->copy()->endOfMonth();

        // categories: availability (unchanged) and approved requests within the month
        $categories = Category::all();
        $categoryAnalytics = [];
        foreach ($categories as $category) {
            $totalAvailability = Stock::where('category_id', $category->id)
                ->sum('stock');

            $totalRequested = \DB::table('outbounds')
                ->join('stocks', 'outbounds.stock_id', '=', 'stocks.id')
                ->where('stocks.category_id', $category->id)
                ->where('outbounds.approval', 'approved')
                ->whereBetween('outbounds.created_at', [$start, $end])
                ->sum('outbounds.total') ?? 0;

            $categoryAnalytics[] = [
                'name' => $category->name,
                'availability' => $totalAvailability ?? 0,
                'requested' => $totalRequested ?? 0,
            ];
        }

        // office analytics: count requests created in the month
        $officeCounts = StockRequest::whereBetween('created_at', [$start, $end])
            ->select('office', \DB::raw('COUNT(*) as total'))
            ->groupBy('office')
            ->orderByDesc('total')
            ->get();

        $officeAnalytics = $officeCounts->map(function ($r) {
            return ['office' => $r->office ?? 'Unknown', 'count' => (int) $r->total];
        })->values();

        return response()->json([
            'categories' => $categoryAnalytics,
            'offices' => $officeAnalytics,
        ]);
    }

    
    public function notifications()
    {
        $pendingRequests = StockRequest::where('status', 'pending')->get();
        $pendingPasswordResets = PasswordResetRequest::where('status', 'pending')->get();
        $lowThreshold = 49;
        $lowStock = \App\Models\Stock::where('stock','>',0)->where('stock','<=',$lowThreshold)->get();
        $outStock = \App\Models\Stock::where('stock','<=',0)->get();

        return view('admin.notifications', compact('pendingRequests','pendingPasswordResets','lowStock','outStock'));
    }


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
