<?php

namespace App\Interfaces;

interface UserRepositoryInterface
{
    public function getAll(
        ?string $search,
        ?int $limit,
        bool $execute
    );

    public function getAllPaginated(
        ?string $search,
        ?int $rowPerPage
    );

    public function getById(
        string $id
    );

    public function create(
        array $data // data untuk buat user baru, nanti di repository kita yang buat user baru dengan data ini
    );

    public function update(
        string $id, // id user yang mau diupdate
        array $data // data untuk update user, nanti di repository kita yang cari user berdasarkan id, terus update datanya dengan data ini
    );

    public function delete(
        string $id // id user yang mau dihapus, nanti di repository kita yang cari user berdasarkan id, terus hapus usernya
    );
}

