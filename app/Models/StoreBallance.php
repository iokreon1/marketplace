<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Model;

class StoreBallance extends Model
{
    use UUID;

    protected $fillable = [
        'store_id',
        'balance',
    ];

    // Store Ballance is owned by one store 
    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    // Store Ballance can have many Store Ballance Histories
    public function storeBallanceHistories()
    {
        return $this->hasMany(StoreBallanceHistory::class);
    }

    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class);
    }
}
