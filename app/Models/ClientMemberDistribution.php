<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientMemberDistribution extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'stock_request_item_id',
        'distributed_qty',
        'used_qty',
    ];

    public function member()
    {
        return $this->belongsTo(ClientMember::class, 'member_id');
    }

    public function stockRequestItem()
    {
        return $this->belongsTo(StockRequestItem::class, 'stock_request_item_id');
    }
}
