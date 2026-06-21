<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notification extends Model
{
    use UUID, HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'message',
        'type',
        'is_read',
    ];

    protected function casts(): array
    {
        return [
            'is_read' => 'boolean', // kolom is_read akan di-cast (dikonversi) menjadi tipe data boolean ketika digunakan pada model
        ];
    }

    // Relationship: A notification belongs to a User
    public function user()
    {
        return $this->belongsTo(User::class); // satu notification dimiliki oleh satu user, jadi kita menggunakan belongsTo
    }
}
