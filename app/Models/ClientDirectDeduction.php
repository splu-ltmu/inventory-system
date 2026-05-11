<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientDirectDeduction extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'stock_request_item_id',
        'member_id',
        'deducted_qty',
        'reason',
        'received_by',
    ];

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function stockRequestItem()
    {
        return $this->belongsTo(StockRequestItem::class, 'stock_request_item_id');
    }

    public function member()
    {
        return $this->belongsTo(ClientMember::class, 'member_id');
    }

    public function stock()
    {
        return $this->hasOneThrough(Stock::class, StockRequestItem::class, 'id', 'id', 'stock_request_item_id', 'stock_id');
    }
}
