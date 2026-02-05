<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id','id_no','description','unit','total','stock','hidden'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function inbounds()
    {
        return $this->hasMany(Inbound::class);
    }

    public function outbounds()
    {
        return $this->hasMany(Outbound::class);
    }

    public function requests()
    {
        return $this->hasMany(Request::class);
    }
}
