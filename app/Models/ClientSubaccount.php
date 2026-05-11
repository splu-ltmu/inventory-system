<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientSubaccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_user_id',
        'user_id',
        'name',
        'description',
    ];

    public function client()
    {
        return $this->belongsTo(User::class, 'client_user_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function members()
    {
        return $this->hasMany(ClientSubaccountMember::class, 'subaccount_id');
    }

    public function allocations()
    {
        return $this->hasMany(ClientSubaccountAllocation::class, 'subaccount_id');
    }

    public function distributions()
    {
        return $this->hasManyThrough(
            ClientSubaccountDistribution::class,
            ClientSubaccountMember::class,
            'subaccount_id',
            'member_id',
            'id',
            'id'
        );
    }
}
