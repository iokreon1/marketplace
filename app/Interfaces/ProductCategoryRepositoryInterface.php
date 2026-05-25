<?php

namespace App\Interfaces;

interface ProductCategoryRepositoryInterface
{
    public function getAll(
        ?string $search,
        ?bool $isParent = null,
        ?int $limit,
        bool $execute
    );

    public function getAllPaginated(
        ?string $search,
        ?bool $isParent = null,
        ?int $rowPerPage
    );

    public function getById(
        string $id
    );

    public function getBySlug(
        string $slug
    );

    public function create(
        array $data
    );

    public function update(
        string $id,
        array $data
    );

    public function delete(
        string $id
    );
}