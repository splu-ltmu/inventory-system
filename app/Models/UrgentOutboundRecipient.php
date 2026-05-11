<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UrgentOutboundRecipient extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'office',
        'reason',
    ];

    /**
     * Get the outbound records for this urgent recipient.
     */
    public function outbounds()
    {
        return $this->hasMany(Outbound::class, 'urgent_recipient_id');
    }

    /**
     * Scope to search by name or office
     */
    public function scopeSearch($query, $term)
    {
        return $query->where(function($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('office', 'like', "%{$term}%");
        });
    }
}
