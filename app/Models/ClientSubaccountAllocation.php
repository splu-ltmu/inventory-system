<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientSubaccountAllocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'subaccount_id',
        'stock_request_item_id',
        'allocated_qty',
    ];

    public function subaccount()
    {
        return $this->belongsTo(ClientSubaccount::class, 'subaccount_id');
    }

    public function stockRequestItem()
    {
        return $this->belongsTo(StockRequestItem::class, 'stock_request_item_id');
    }
}
