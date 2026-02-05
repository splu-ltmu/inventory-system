<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockRequestItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_request_id',
        'stock_id',
        'requested_qty',
        'approved_qty',
        // 'status', // ✅ keep ONLY if this column exists in your table
    ];

    public function request()
    {
        return $this->belongsTo(StockRequest::class, 'stock_request_id');
    }

    public function stock()
    {
        return $this->belongsTo(Stock::class, 'stock_id');
    }

    // OPTIONAL: helpers
    public function isApproved(): bool
    {
        return (int)$this->approved_qty > 0;
    }
}
