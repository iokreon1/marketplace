<?php

namespace App\Models;

use App\Models\Buyer;
use App\Models\Store;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\UUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, UUID;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [ // field yang bisa diisi, makanya tidak ada id
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', '%' . $search . '%') // object memanggil method $query->where()
            ->Orwhere('email', 'like', '%' . $search . '%');
    }

    // satu user bisa memiliki satu toko
    public function store()
    {
        return $this->hasOne(Store::class);
    }

    public function buyer()
    {
        return $this->hasOne(Buyer::class);
    }
}
