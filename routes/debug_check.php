<?php

use App\Models\StockRequest;
use Illuminate\Support\Facades\Route;

Route::get('/debug-received-by', function() {
    $releasedRequests = StockRequest::where('status', 'released')
        ->with(['client', 'items.stock'])
        ->get();
    
    $output = [];
    foreach ($releasedRequests as $req) {
        $output[] = [
            'id' => $req->id,
            'client' => $req->client?->name,
            'status' => $req->status,
            'received_by' => $req->received_by,
            'verification_code' => $req->verification_code,
            'created_at' => $req->created_at,
        ];
    }
    
    return response()->json($output);
});
