<?php

namespace App\Repositories;

use App\Interfaces\StoreRepositoryInterface;
use Illuminate\Support\Facades\DB;
use App\Models\Store;

class StoreRepository implements StoreRepositoryInterface
{
    public function getAll( // siapkan query ambil user, bisa difilter, bisa dibatasi, dan bisa langsung dieksekusi atau tidak 
        ?string $search,
        ?bool $isVerified,
        ?int $limit,
        bool $execute,
    ) {
        $query = Store::query()->where(function ($query) use ($search, $isVerified) { // penjelasan sintaks ada di docs
            if ($search) { // kalau $search tidak null, jalankan kode di dalamnya / filternya
                $query->search($search); // filter data berdasarkan kata yang dicari, kode ini ada di model Store
                                         // contoh: $query->search('wahyu')
            }

            if ($isVerified !== null) {
                $query->where('is_verified', $isVerified);
            }
        });

        if ($limit) { // kalau $limit tidak null, jalankan kode di dalamnya / batasi jumlah data yang diambil 
            $query->take($limit); // ambil data sebanyak $limit, contoh: $query->take(10) -> ambil 10 data aja
        }

        if ($execute) { // kalau $execute bernilai true, jalankan querynya dan kembalikan hasilnya
            return $query -> get(); // jalankan querynya dan kembalikan hasilnya sebagai collection (data nyata)
        }          
        return $query; // kalau $execute bernilai false, kembalikan querynya saja tanpa menjalankannya
    }

    public function getAllPaginated(
        ?string $search, 
        ?bool $isVerified,
        ?int $rowPerPage
    ) {
        $query = $this->getAll( // $this->getAll itu memanggil method getAll yang ada di repository ini, untuk menyiapkan querynya dulu
            $search, // kirim nilai $search dari parameter ke method getAll
            $isVerified, // kirim nilai $isVerified dari parameter ke method getAll
            null, // limit tidak dipakai di sini, jadi dikirim null
            false // jangan dieksekusi dulu
        );

        return $query->paginate($rowPerPage); // jalankan querynya, tapi potong datanya per-halaman
                                              // contoh: paginate(10) -> potong data per 10 data untuk setiap halaman
    }
}