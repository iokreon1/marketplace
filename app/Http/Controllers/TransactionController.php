<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\TransactionStoreRequest;
use App\Http\Requests\TransactionUpdateRequest;
use App\Http\Resources\PaginateResource;
use App\Http\Resources\TransactionResource;
use Illuminate\Http\Request;
use App\Interfaces\TransactionRepositoryInterface;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Spatie\Permission\Middleware\PermissionMiddleware;

class TransactionController extends Controller implements HasMiddleware
{
    private TransactionRepositoryInterface $transactionRepository;

    public function __construct(TransactionRepositoryInterface $transactionRepository)
    {
        $this->transactionRepository = $transactionRepository;
    }

    public static function middleware()
    {
        return [
            new Middleware(PermissionMiddleware::using(['transaction-list|transaction-create|transaction-edit|transaction-delete']), only: ['index', 'getAllPaginated', 'show', 'approve']),
            new Middleware(PermissionMiddleware::using(['transaction-create']), only: ['store']),
            new Middleware(PermissionMiddleware::using(['transaction-edit']), only: ['approve']),
            new Middleware(PermissionMiddleware::using(['transaction-delete']), only: ['destroy']),
        ];
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
            'row_per_page' => 'required|integer',
            'status' => 'nullable|string'
        ]);

        try {
            $transactions = $this->transactionRepository->getAllPaginated(
                $request['search'] ?? null,
                $request['row_per_page'],
                $request['status'] ?? null
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

            $user = auth()->user();
            if ($user->hasRole('admin')) {
                // Admin can update
            } elseif ($user->hasRole('store')) {
                $store = $user->store;
                if (!$store || $transaction->store_id !== $store->id) {
                    return ResponseHelper::jsonResponse(false, 'Unauthorized', null, 403);
                }
            } elseif ($user->hasRole('buyer')) {
                $buyer = $user->buyer;
                if (!$buyer || $transaction->buyer_id !== $buyer->id) {
                    return ResponseHelper::jsonResponse(false, 'Unauthorized', null, 403);
                }
                if (isset($request['delivery_status']) && $request['delivery_status'] !== 'completed') {
                    return ResponseHelper::jsonResponse(false, 'Pembeli hanya boleh menandai transaksi sebagai selesai', null, 403);
                }
            } else {
                return ResponseHelper::jsonResponse(false, 'Unauthorized', null, 403);
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

    public function midtransCallback(Request $request)
    {
        \Midtrans\Config::$serverKey = config('midtrans.serverKey');
        \Midtrans\Config::$isProduction = config('midtrans.isProduction');
        \Midtrans\Config::$isSanitized = config('midtrans.isSanitized');
        \Midtrans\Config::$is3ds = config('midtrans.is3ds');

        try {
            $notification = new \Midtrans\Notification();

            $transactionStatus = $notification->transaction_status;
            $paymentType = $notification->payment_type;
            $orderId = $notification->order_id;
            $fraudStatus = $notification->fraud_status;

            $transaction = $this->transactionRepository->getByCode($orderId);

            if (!$transaction) {
                return ResponseHelper::jsonResponse(false, 'Transaksi tidak ditemukan', null, 404);
            }

            if ($transactionStatus == 'capture') {
                if ($paymentType == 'credit_card') {
                    if ($fraudStatus == 'challenge') {
                        $transaction->payment_status = 'unpaid';
                    } else {
                        $transaction->payment_status = 'paid';
                    }
                }
            } else if ($transactionStatus == 'settlement') {
                $transaction->payment_status = 'paid';
            } else if ($transactionStatus == 'pending') {
                $transaction->payment_status = 'unpaid';
            } else if ($transactionStatus == 'deny' || $transactionStatus == 'expire' || $transactionStatus == 'cancel') {
                $transaction->payment_status = 'unpaid';
            }

            $transaction->save();

            return ResponseHelper::jsonResponse(true, 'Status transaksi berhasil diperbarui', null, 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    public function simulatePayment(string $id)
    {
        try {
            $transaction = $this->transactionRepository->getById($id);

            if (!$transaction) {
                return ResponseHelper::jsonResponse(false, 'Transaksi tidak ditemukan', null, 404);
            }

            $user = auth()->user();
            if ($user->hasRole('store')) {
                $store = $user->store;
                if (!$store || $transaction->store_id !== $store->id) {
                    return ResponseHelper::jsonResponse(false, 'Unauthorized', null, 403);
                }
            } elseif ($user->hasRole('buyer')) {
                $buyer = $user->buyer;
                if (!$buyer || $transaction->buyer_id !== $buyer->id) {
                    return ResponseHelper::jsonResponse(false, 'Unauthorized', null, 403);
                }
            }

            $transaction->payment_status = 'paid';
            $transaction->save();

            return ResponseHelper::jsonResponse(true, 'Simulasi pembayaran sukses', new TransactionResource($transaction), 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }
}
