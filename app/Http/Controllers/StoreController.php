<?php

namespace App\Http\Controllers;

use App\Interfaces\StoreRepositoryInterface;
use App\Http\Resources\PaginateResource;
use Illuminate\Http\Request;
use App\Http\Resources\StoreResource;
use App\Helpers\ResponseHelper;

class StoreController extends Controller
{

    private StoreRepositoryInterface $storeRepository; // membuat property $storeRepository
                                                       // ini awalnya ibarat kotak kosong, nanti kita isi dengan nilai berupa objek dari class StoreRepository

    public function __construct(StoreRepositoryInterface $storeRepository)
    {
        $this->storeRepository = $storeRepository; // $this-> mengakses property $storeRepository
                                                   // kemudian property tersebut kita isi dengan nilai objek dari class StoreRepository 
                                                   // yang dikirimkan laravel melalui parameter $storeRepository di constructor ini
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $stores = $this->storeRepository->getAll( // method getAll ada di repository StoreRepository
                                                      // untuk mengaksesnya, kita pakai $this->storeRepository karena kita sudah simpan objek StoreRepository di property $storeRepository
                                                      // ingat, untuk mengakses method di class non static, kita harus lewat objeknya, dalam hal ini objek StoreRepository yang sudah kita simpan di property $storeRepository 
                $request->search,
                $request->is_verified,
                $request->limit,
                true
            );

        return ResponseHelper::jsonResponse(true, 'Data toko berhasil diambil', StoreResource::collection($stores), 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    public function getAllPaginated(Request $request) // tampilkan semua data user dengan pagination 
    {
        $request = $request->validate([
            'search' => 'nullable|string',
            'is_verified' => 'required|boolean',
            'row_per_page' => 'required|integer'
        ]);

        try {
            $stores = $this->storeRepository->getAllPaginated(
                $request['search'] ?? null,
                $request['is_verified'] ?? null,
                $request['row_per_page']
            );

            return ResponseHelper::jsonResponse(true, 'Data toko berhasil diambil', PaginateResource::make($stores, StoreResource::class), 200);

        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
