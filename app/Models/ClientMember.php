<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'name',
        'email',
    ];

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function distributions()
    {
        return $this->hasMany(ClientMemberDistribution::class, 'member_id');
    }

    public function directDeductions()
    {
        return $this->hasMany(ClientDirectDeduction::class, 'member_id');
    }
}
