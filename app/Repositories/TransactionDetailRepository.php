<?php

namespace App\Repositories;

use App\Interfaces\TransactionDetailRepositoryInterface;
use App\Models\Product;
use App\Models\Store;
use App\Models\TransactionDetail;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http as FacadesHttp;
use League\Uri\Http;
use Override;

class TransactionDetailRepository implements TransactionDetailRepositoryInterface
{
    #[Override]
    public function create(
        array $data
    ) {
        DB::beginTransaction();
        
        try {
            $transactionDetail = new TransactionDetail;
            $transactionDetail->transaction_id = $data['transaction_id'];
            $transactionDetail->product_id = $data['product_id'];
            $transactionDetail->qty = $data['qty'];

            $product = Product::find($data['product_id']);
            $transactionDetail->subtotal = $product->price * $data['qty'];

            $transactionDetail->save();

            DB::commit();

            return $transactionDetail;
        } catch (\Exception $e) {
            DB::rollBack();

            throw new Exception($e->getMessage());
        }
    }
}