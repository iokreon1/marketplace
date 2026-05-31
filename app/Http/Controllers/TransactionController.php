<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\TransactionStoreRequest;
use App\Http\Requests\TransactionUpdateRequest;
use App\Http\Resources\PaginateResource;
use App\Http\Resources\TransactionResource;
use Illuminate\Http\Request;
use App\Interfaces\TransactionRepositoryInterface;

class TransactionController extends Controller
{
    private TransactionRepositoryInterface $transactionRepository;

    public function __construct(TransactionRepositoryInterface $transactionRepository)
    {
        $this->transactionRepository = $transactionRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $transactions = $this->transactionRepository->getAll( 
                $request->search, 
                $request->limit, 
                true 
            );

            return ResponseHelper::jsonResponse(true, 'Data Transaksi berhasil diambil', TransactionResource::collection($transactions), 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    public function getAllPaginated(Request $request)
    {
        $request = $request->validate([
            'search' => 'nullable|string',
            'row_per_page' => 'required|integer'
        ]);

        try {
            $transactions = $this->transactionRepository->getAllPaginated(
                $request['search'] ?? null,
                $request['row_per_page']
            );

            return ResponseHelper::jsonResponse(true, 'Data Transaksi berhasil diambil', PaginateResource::make($transactions, TransactionResource::class), 200);

        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TransactionStoreRequest $request)
    {
        $request = $request->validated();

        try {
            $transaction = $this->transactionRepository->create($request); 

            return ResponseHelper::jsonResponse(true, 'Transaksi berhasil ditambahkan', new TransactionResource($transaction), 201);
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
            $transaction = $this->transactionRepository->getById($id); 

            if (!$transaction) {
                return ResponseHelper::jsonResponse(true, 'Data transaksi tidak ditemukan', null, 404);
            }

            return ResponseHelper::jsonResponse(true, 'Data transaksi berhasil diambil', new TransactionResource($transaction), 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    public function showByCode(string $code)
    {
        try {
            $transaction = $this->transactionRepository->getByCode($code); 

            if (!$transaction) {
                return ResponseHelper::jsonResponse(true, 'Data transaksi tidak ditemukan', null, 404);
            }

            return ResponseHelper::jsonResponse(true, 'Data transaksi berhasil diambil', new TransactionResource($transaction), 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TransactionUpdateRequest $request, string $id)
    {
        $request = $request->validated();

        try {
            $transaction = $this->transactionRepository->getById($id); 

            if (!$transaction) {
                return ResponseHelper::jsonResponse(true, 'Data transaksi tidak ditemukan', null, 404);
            }

            $transaction = $this->transactionRepository->updateStatus($id, $request);

            return ResponseHelper::jsonResponse(true, 'Data transaksi berhasil diperbarui', new TransactionResource($transaction), 200);
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
            $transaction = $this->transactionRepository->getById($id); 

            if (!$transaction) {
                return ResponseHelper::jsonResponse(true, 'Data Transaksi tidak ditemukan', null, 404);
            }

            $transaction = $this->transactionRepository->delete($id);

            return ResponseHelper::jsonResponse(true, 'Data Transaksi berhasil dihapus', new TransactionResource($transaction), 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }
}
