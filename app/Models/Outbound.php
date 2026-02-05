<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Outbound extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_id',
        'total',
        'client_id',
        'office',
        'description',
        'approval',
        'status',
        'deducted_at',
    ];

    protected $casts = [
        'deducted_at' => 'datetime',
    ];

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }
}
