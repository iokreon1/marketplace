<?php

namespace App\Http\Controllers;

use App\Http\Resources\StoreBalanceHistoryResource;
use App\Http\Resources\PaginateResource;
use App\Http\Requests\StoreBalanceHistoryRequest;
use App\Interfaces\StoreBalanceHistoryRepositoryInterface;
use App\Helpers\ResponseHelper;
use App\Models\StoreBalanceHistory;
use Illuminate\Http\Request;

class StoreBalanceHistoryController extends Controller
{
    private StoreBalanceHistoryRepositoryInterface $storeBalanceHistoryRepository;

    public function __construct(StoreBalanceHistoryRepositoryInterface $storeBalanceHistoryRepository)
    {
        $this->storeBalanceHistoryRepository = $storeBalanceHistoryRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $storeBalanceHistories = $this->storeBalanceHistoryRepository->getAll(
                $request->search,
                $request->limit,
                true
            );

            return ResponseHelper::jsonResponse(true, 'Data Riwayat Dompet Toko berhasil diambil', StoreBalanceHistoryResource::collection($storeBalanceHistories), 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, 'Gagal mengambil data Riwayat Dompet Toko', null, 500);
        }
    }

    public function getAllPaginated(Request $request)
    {
        $request = $request->validate([
            'search' => 'nullable|string',
            'row_per_page' => 'required|integer'
        ]);

        try {
            $storeBalanceHisories = $this->storeBalanceHistoryRepository->getAllPaginated(
                $request['search'] ?? null,
                $request['row_per_page']
            );

            return ResponseHelper::jsonResponse(true, 'Data user berhasil diambil', PaginateResource::make($storeBalanceHisories, StoreBalanceHistoryResource::class), 200);

        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }
    
    public function show(string $id)
    {
        try {
            $storeBalanceHistory = $this->storeBalanceHistoryRepository->getById($id);

            if (!$storeBalanceHistory) {
                return ResponseHelper::jsonResponse(true, 'Data Riwayat Dompet Toko Tidak Ditemukan', null, 404);
            }

            return ResponseHelper::jsonResponse(true, 'Data Riwayat Dompet Toko berhasil diambil', new StoreBalanceHistoryResource($storeBalanceHistory), 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }
}
