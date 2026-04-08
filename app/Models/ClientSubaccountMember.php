<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientSubaccountMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'subaccount_id',
        'name',
        'email',
    ];

    public function subaccount()
    {
        return $this->belongsTo(ClientSubaccount::class, 'subaccount_id');
    }

    public function distributions()
    {
        return $this->hasMany(ClientSubaccountDistribution::class, 'member_id');
    }
}
