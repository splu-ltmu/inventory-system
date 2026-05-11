<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'member_id',
        'office',
        'reason',
        'status',
        'verification_code',
        'received_by',
    ];

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function member()
    {
        return $this->belongsTo(ClientMember::class, 'member_id');
    }

    // ✅ IMPORTANT: specify FK because your child table uses stock_request_id
    public function items()
    {
        return $this->hasMany(StockRequestItem::class, 'stock_request_id');
    }

    // OPTIONAL: quick helper (used in blade/controller if you want)
    public function hasApprovedItems(): bool
    {
        return $this->items()->where('approved_qty', '>', 0)->exists();
    }
}
