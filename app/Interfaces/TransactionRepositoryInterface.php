<?php

namespace App\Interfaces;

interface TransactionRepositoryInterface
{
    public function getAll(
        ?string $search,
        ?int $limit,
        bool $execute,
        ?string $status = null
    );

    public function getAllPaginated(
        ?string $search,
        ?int $rowPerPage,
        ?string $status = null
    );

    public function getById(
        string $id
    );

    public function getByCode(
        string $code
    );

    public function create(
        array $data
    );

    public function updateStatus(
        string $id,
        array $data
    );

    public function delete(
        string $id
    );
}