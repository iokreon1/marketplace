<?php

namespace App\Repositories;

use App\Interfaces\StoreBalanceRepositoryInterface;
use App\Models\StoreBalance;
use Illuminate\Support\Facades\DB;
use Exception;

class StoreBalanceRepository implements StoreBalanceRepositoryInterface
{
    public function getAll(
        ?string $search,
        ?int $limit,
        bool $execute
    ) {
        $query = StoreBalance::query()->where(function ($query) use ($search) {
            if ($search) {
                $query->search($search);
            }
        });

        if ($limit) {
            $query->take($limit);
        }

        if ($execute) {
            return $query->get();
        }

        return $query;
    }

    public function getAllPaginated(
        ?string $search,
        ?int $rowPerPage
    ) {
        $query = $this->getAll(
            $search,
            null,
            false
        );

        return $query->paginate($rowPerPage);
    }

    public function getById(
        string $id
    ) {
        $query = StoreBalance::where('id', $id);

        return $query->first();
    }

    public function credit(
        string $id,
        string $amount
    ) {
        DB::beginTransaction();

        try {
            $storeBalance = StoreBalance::find($id);
            $storeBalance->balance = bcadd($storeBalance->balance, $amount, 2); // bcadd untuk penjumlahan angka desimal dengan presisi 2 angka di belakang koma
            $storeBalance->save();

            DB::commit();

            return $storeBalance;
        } catch (\Exception $e) {
            DB::rollBack();

            throw new Exception($e->getMessage());
        }
    }

    public function debit(
        string $id,
        string $amount
    ) {
        DB::beginTransaction();

        try {
            $storeBalance = StoreBalance::find($id);

            if (bccomp($storeBalance->balance, $amount, 2) < 0) {
                throw new Exception("Saldo tidak mencukupi");
            }

            $storeBalance->balance = bcsub($storeBalance->balance, $amount, 2); // bcsub untuk pengurangan angka desimal dengan presisi 2 angka di belakang koma
            $storeBalance->save();

            DB::commit();

            return $storeBalance;
        } catch (\Exception $e) {
            DB::rollBack();

            throw new Exception($e->getMessage());
        }
    }
}