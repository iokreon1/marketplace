<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\WithdrawalApproveRequest;
use App\Http\Requests\WithdrawalStoreRequest;
use App\Http\Resources\PaginateResource;
use App\Http\Resources\WithdrawalResource;
use App\Interfaces\WithdrawalRepositoryInterface;
use App\Models\Notification;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Spatie\Permission\Middleware\PermissionMiddleware;

class WithdrawalController extends Controller implements HasMiddleware
{
    private WithdrawalRepositoryInterface $withdrawalRepository;

    public function __construct(WithdrawalRepositoryInterface $withdrawalRepository)
    {
        $this->withdrawalRepository = $withdrawalRepository;
    }

    public static function middleware()
    {
        return [
            new Middleware(PermissionMiddleware::using(['withdrawal-list|withdrawal-create|withdrawal-edit|withdrawal-delete']), only: ['index', 'getAllPaginated', 'show']),
            new Middleware(PermissionMiddleware::using(['withdrawal-create']), only: ['store']),
            new Middleware(PermissionMiddleware::using(['withdrawal-edit']), only: ['update']),
            new Middleware(PermissionMiddleware::using(['withdrawal-delete']), only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $withdrawals = $this->withdrawalRepository->getAll(
                $request->search,
                $request->limit,
                true
            );

            return ResponseHelper::jsonResponse(true, 'Data withdrawal berhasil diambil', WithdrawalResource::collection($withdrawals), 200);
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
            $withdrawals = $this->withdrawalRepository->getAllPaginated(
                $request['search'] ?? null,
                $request['row_per_page']
            );

            return ResponseHelper::jsonResponse(true, 'Data withdrawal berhasil diambil', PaginateResource::make($withdrawals, WithdrawalResource::class), 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(WithdrawalStoreRequest $request)
    {
        $request = $request->validated();

        try {
            $withdrawal = $this->withdrawalRepository->create($request);

            $storeUser = $withdrawal->storeBalance && $withdrawal->storeBalance->store 
                ? $withdrawal->storeBalance->store->user_id 
                : null;
            if ($storeUser) {
                Notification::create([
                    'user_id' => $storeUser,
                    'title' => 'Pengajuan Penarikan Dana',
                    'message' => 'Pengajuan penarikan dana sebesar Rp ' . number_format($withdrawal->amount, 0, ',', '.') . ' telah dikirim dan sedang menunggu persetujuan admin.',
                    'type' => 'withdrawal',
                    'is_read' => false
                ]);
            }

            return ResponseHelper::jsonResponse(true, 'Withdrawal berhasil ditambahkan', new WithdrawalResource($withdrawal), 201);
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
            $withdrawal = $this->withdrawalRepository->getById($id);

            if (!$withdrawal) {
                return ResponseHelper::jsonResponse(true, 'Data withdrawal tidak ditemukan', null, 404);
            }

            return ResponseHelper::jsonResponse(true, 'Data withdrawal berhasil diambil', new WithdrawalResource($withdrawal), 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    public function approve(WithdrawalApproveRequest $request, string $id)
    {
        $request = $request->validated();

        try {
            $withdrawal = $this->withdrawalRepository->getById($id);

            if (!$withdrawal) {
                return ResponseHelper::jsonResponse(true, 'Data withdrawal tidak ditemukan', null, 404);
            }

            $withdrawal = $this->withdrawalRepository->approve(
                $id,
                $request['proof']
            );

            $storeUser = $withdrawal->storeBalance && $withdrawal->storeBalance->store 
                ? $withdrawal->storeBalance->store->user_id 
                : null;
            if ($storeUser) {
                Notification::create([
                    'user_id' => $storeUser,
                    'title' => 'Penarikan Dana Disetujui',
                    'message' => 'Pengajuan penarikan dana sebesar Rp ' . number_format($withdrawal->amount, 0, ',', '.') . ' telah disetujui oleh admin. Bukti transfer telah dilampirkan.',
                    'type' => 'withdrawal',
                    'is_read' => false
                ]);
            }

            return ResponseHelper::jsonResponse(true, 'Data withdrawal Berhasil disetujui', new WithdrawalResource($withdrawal), 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }
}
