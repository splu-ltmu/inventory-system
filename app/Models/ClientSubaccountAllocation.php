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
        'used_qty',
    ];

    public function subaccount()
    {
        return $this->belongsTo(ClientSubaccount::class, 'subaccount_id');
    }

    public function stockRequestItem()
    {
        return $this->belongsTo(StockRequestItem::class, 'stock_request_item_id');
    }

    public function members()
    {
        return $this->hasManyThrough(
            ClientSubaccountMember::class,
            ClientSubaccountDistribution::class,
            'stock_request_item_id', // foreign key on distributions
            'id', // foreign key on members
            'stock_request_item_id', // local key on allocations
            'member_id' // local key on distributions
        );
    }
}
