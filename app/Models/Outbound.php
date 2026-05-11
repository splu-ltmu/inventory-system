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
        'reason',
        'approval',
        'status',
        'deducted_at',
        'received_by',
        'urgent_recipient_id',
        'urgent_recipient_name',
        'urgent_recipient_office',
        'is_urgent_outbound',
        'member_id',
        'is_direct_request',
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

    public function urgentRecipient()
    {
        return $this->belongsTo(UrgentOutboundRecipient::class, 'urgent_recipient_id');
    }

    public function member()
    {
        return $this->belongsTo(ClientMember::class, 'member_id');
    }

    /**
     * Get the display name for the recipient (client or urgent recipient)
     */
    public function getRecipientNameAttribute()
    {
        if ($this->is_urgent_outbound) {
            return $this->urgent_recipient_name ?? 'Unknown Urgent Recipient';
        }
        return $this->client?->name ?? 'Unknown Client';
    }

    /**
     * Get the display office for the recipient
     */
    public function getRecipientOfficeAttribute()
    {
        if ($this->is_urgent_outbound) {
            return $this->urgent_recipient_office ?? 'Not specified';
        }
        return $this->office ?? 'Not specified';
    }
}
