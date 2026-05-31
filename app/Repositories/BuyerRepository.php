<?php

namespace App\Repositories;

use App\Interfaces\BuyerRepositoryInterface;
use App\Models\Buyer;
use Exception;
use Override;
use Illuminate\Support\Facades\DB;

class BuyerRepository implements BuyerRepositoryInterface
{
    public function getAll( // siapkan query ambil user, bisa difilter, bisa dibatasi, dan bisa langsung dieksekusi atau tidak 
        ?string $search,
        ?int $limit,
        bool $execute,
    ) {
        $query = Buyer::query()->where(function ($query) use ($search) { 
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
        $query = Buyer::where('id', $id);

        return $query->first();
    }

    #[Override]
    public function create(
        array $data
    ) {
        DB::beginTransaction();

        try {
            $buyer = new Buyer;
            $buyer->user_id = $data['user_id'];
            $buyer->profile_picture = $data['profile_picture']->store('assets/buyer', 'public');
            $buyer->phone_number = $data['phone_number'];
            $buyer->save();

            DB::commit();

            return $buyer;
        } catch (\Exception $e) {
            DB::rollBack();

            throw new Exception($e->getMessage());
        }
    }

    #[Override]
    public function update(
        string $id, 
        array $data
    ) {
        DB::beginTransaction();

        try {
            $buyer = Buyer::find($id);
            
            if (isset($data['profile_picture'])) {
                $buyer->profile_picture = $data['profile_picture']->store('assets/buyer', 'public');
            }

            $buyer->phone_number = $data['phone_number'];
            $buyer->save();

            DB::commit();

            return $buyer;
        } catch (\Exception $e) {
            DB::rollBack();

            throw new Exception($e->getMessage());
        }
    }

    public function delete(
        string $id
    ) {
        DB::beginTransaction();

        try {
            $buyer = Buyer::find($id);
            $buyer->delete();

            DB::commit();

            return $buyer;
        } catch (\Exception $e) {
            DB::rollBack();

            throw new Exception($e->getMessage());
        }
    }
}