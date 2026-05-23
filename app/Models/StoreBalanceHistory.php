<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreBalanceHistory extends Model
{
    use UUID, HasFactory;

    protected $fillable = [
        'store_balance_id',
        'type',
        'reference_id',
        'reference_type',
        'amount',
        'remarks',
    ];

    protected $casts = [
        'amount' => 'decimal:2'
    ];

    public function scopeSearch($query, $search)
    {
        return $query->whereHas('storeBalance.store', function ($q) use ($search) {
            $q->where('name', 'like', '%' . $search . '%');
        });
    }

    public function storeBalance()
    {
        return $this->belongsTo(StoreBalance::class);
    }
}
