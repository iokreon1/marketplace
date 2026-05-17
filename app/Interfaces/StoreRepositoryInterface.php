<?php

namespace App\Interfaces;

interface StoreRepositoryInterface
{
    public function getAll(
        ?string $search, // ? artinya parameter ini boleh null, karena pengguna belum tentu ingin melakukan search
        ?bool $isVerified, // filter berdasarkan status verified atau tidak
        ?int $limit, // $limit, $search, dan $execute itu variable, yang mengisi adalah pengguna, 
                     // contoh: getAll('wahyu', 10, true) -> $search = 'wahyu', $limit = 10, $execute = true
        bool $execute // tidak boleh null karena harus ada nilai true atau false
    );

    public function getAllPaginated(
        ?string $search,
        ?bool $isVerified,
        ?int $rowPerPage
    );

    public function getById(
        string $id // kita kirimkan parameter berupa string id, untuk mencari data toko berdasarkan id
    );

    public function create(
        array $data
    );

    public function updateVerifiedStatus(
        string $id,
        bool $isVerified
    );

    public function update(
        string $id,
        array $data
    );

    public function delete(
        string $id
    );
}