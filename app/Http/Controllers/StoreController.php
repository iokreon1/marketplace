<?php

namespace App\Http\Controllers;

use App\Interfaces\StoreRepositoryInterface;
use App\Http\Resources\PaginateResource;
use Illuminate\Http\Request;
use App\Http\Resources\StoreResource;
use App\Helpers\ResponseHelper;
use App\Http\Requests\StoreStoreRequest;
use App\Http\Requests\StoreUpdateRequest;

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
    public function store(StoreStoreRequest $request)
    {
        $request = $request->validated(); // melakukan validasi request dengan rules yang ada di UserStoreRequest,

        try {
            $store = $this->storeRepository->create($request); // pakai repository itu untuk buat toko baru dengan data request yang sudah divalidasi 

            return ResponseHelper::jsonResponse(true, 'Data toko berhasil ditambahkan', new StoreResource($store), 201);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $store = $this->storeRepository->getById($id); // pakai repository itu untuk ambil data store berdasarkan id

            if (!$store) {
                return ResponseHelper::jsonResponse(true, 'Data toko tidak ditemukan', null, 404);
            }

            return ResponseHelper::jsonResponse(true, 'Data toko berhasil diambil', new StoreResource($store), 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    public function updateVerifiedStatus(string $id)
    {
        try {
            $store = $this->storeRepository->getById($id); // pakai repository itu untuk ambil data store berdasarkan id

            if (!$store) {
                return ResponseHelper::jsonResponse(true, 'Data toko tidak ditemukan', null, 404);
            }

            $store = $this->storeRepository->updateVerifiedStatus(
                $id,
                true
            );

            return ResponseHelper::jsonResponse(true, 'Data toko berhasil diverifikasi', new StoreResource($store), 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreUpdateRequest $request, string $id)
    {
        $request = $request->validated(); 

        try {
            $store = $this->storeRepository->getById($id); // pakai repository itu untuk ambil data store berdasarkan id

            if (!$store) {
                return ResponseHelper::jsonResponse(true, 'Data toko tidak ditemukan', null, 404);
            }

            $store = $this->storeRepository->update(
                $id, // id toko yang mau diupdate
                $request // data request yang sudah divalidasi untuk update toko, nanti di repository yang ngurus update tokonya dengan data ini
            );

            return ResponseHelper::jsonResponse(true, 'Data toko berhasil diperbarui', new StoreResource($store), 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $store = $this->storeRepository->getById($id); // pakai repository itu untuk ambil data store berdasarkan id

            if (!$store) {
                return ResponseHelper::jsonResponse(true, 'Data toko tidak ditemukan', null, 404);
            }

            $store = $this->storeRepository->delete(
                $id, // id toko yang mau dihapus
            );

            return ResponseHelper::jsonResponse(true, 'Data toko berhasil dihapus', new StoreResource($store), 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }
}
