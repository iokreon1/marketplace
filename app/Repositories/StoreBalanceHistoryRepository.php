<?php

namespace App\Repositories;

use App\Interfaces\StoreBalanceHistoryRepositoryInterface;
use App\Models\StoreBalanceHistory;
use Illuminate\Support\Facades\DB;
use Exception;
use Override;

class StoreBalanceHistoryRepository implements StoreBalanceHistoryRepositoryInterface
{
    #[Override]
    public function getAll(
        ?string $search, 
        ?int $limit, 
        bool $execute
    ) {
        $query = StoreBalanceHistory::query()->where(function ($query) use ($search) {
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

    #[Override]
    public function getAllPaginated(
        ?string $search, 
        ?int $rowPerPage
    ) {
        $query = $this->getAll(
            $search,
            null,
            false
        );

        return $query->paginate($rowPerPage); // Ambil query builder $query, lalu jalankah method bawaan laravel paginate() 
    }

    #[Override]
    public function getById(
        string $id
    ) {
        $query = StoreBalanceHistory::where('id', $id);

        return $query->first();
    }

    #[Override]
    public function create(
        array $data
    ) {
        DB::beginTransaction();

        try {
            $storeBalanceHistory = new StoreBalanceHistory;
            $storeBalanceHistory->store_balance_id = $data['store_balance_id'];
            $storeBalanceHistory->type = $data['type'];
            $storeBalanceHistory->reference_id = $data['reference_id'];
            $storeBalanceHistory->reference_type = $data['reference_type'];
            $storeBalanceHistory->amount = $data['amount'];
            $storeBalanceHistory->remarks = $data['remarks'];
            $storeBalanceHistory->save();

            DB::commit();

            return $storeBalanceHistory;
        } catch (\Exception $e) {
            DB::rollback();

            throw new Exception($e->getMessage());
        }
    }

    public function update(
        string $id, 
        array $data
    ) {
        DB::beginTransaction();

        try {
            $storeBalanceHistory = StoreBalanceHistory::find($id);
            $storeBalanceHistory->type = $data['type'];
            $storeBalanceHistory->reference_id = $data['reference_id'];
            $storeBalanceHistory->reference_type = $data['reference_type'];
            $storeBalanceHistory->amount = $data['amount'];
            $storeBalanceHistory->remarks = $data['remarks'];

            $storeBalanceHistory->save();

            DB::commit();

            return $storeBalanceHistory;
        } catch (\Exception $e) {
            DB::rollBack();

            throw new Exception($e->getMessage());
        }
    }
}
