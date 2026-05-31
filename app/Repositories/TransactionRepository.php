<?php

namespace App\Repositories;

use App\Repositories\TransactionDetailRepository;
use App\Models\Transaction;
use App\Interfaces\TransactionRepositoryInterface;
use App\Models\Product;
use Exception;
use Illuminate\Support\Facades\DB;
use App\Models\Store;
use Illuminate\Support\Facades\Http;
use Override;

class TransactionRepository implements TransactionRepositoryInterface
{
    public function getAll(
        ?string $search,
        ?int $limit,
        bool $execute
    ) {
        $query = Transaction::query()->where(function ($query) use ($search) { 
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
        $query = Transaction::where('id', $id);

        return $query->first();
    }

    public function getByCode(
        string $code
    ) {
        $query = Transaction::where('code', $code);

        return $query->first();
    }

    public function create(
        array $data
    ) {
        DB::beginTransaction();
        
        try {
            $transaction = new Transaction();
            $transaction->code = 'BLUE' . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
            $transaction->buyer_id = $data['buyer_id'];
            $transaction->store_id = $data['store_id'];
            $transaction->address_id = $data['address_id'];
            $transaction->address = $data['address'];
            $transaction->city = $data['city'];
            $transaction->postal_code = $data['postal_code'];
            $transaction->shipping = $data['shipping'];
            $transaction->shipping_type = $data['shipping_type'];
            $transaction->shipping_cost = 0;
            $transaction->tax = 0;
            $transaction->grand_total = 0;
            $transaction->save();

            $transactionDetailRepository = new TransactionDetailRepository;

            $transactionDetails = [];

            foreach ($data['products'] as $transactionDetail) {
                $transactionDetail = $transactionDetailRepository->create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $transactionDetail['product_id'],
                    'qty' => $transactionDetail['qty']
                ]);

                $transactionDetails[] = $transactionDetail;
            }

            $subtotal = array_reduce($transactionDetails, function ($carry, $item) {
                return $carry + $item->subtotal;
            }, 0);

            $weight = $this->getTotalWeight($transactionDetails);

            $calculation = $this->calculateShippingAndTax($data, $subtotal, $weight);

            $transaction->shipping_cost = $calculation['shipping_cost'];
            $transaction->tax = $calculation['tax'];
            $transaction->grand_total = $calculation['grand_total'];
            $transaction->save();

            DB::commit();

            \Midtrans\Config::$serverKey = config('midtrans.serverKey');
            \Midtrans\Config::$isProduction = config('midtrans.isProduction');
            \Midtrans\Config::$isSanitized = config('midtrans.isSanitized');
            \Midtrans\Config::$is3ds = config('midtrans.is3ds');

            $params = array(
                'transaction_details' => array(
                    'order_id' => $transaction->code,
                    'gross_amount' => $transaction->grand_total
                ),
                'customer_details' => array(
                    'first_name' => $transaction->buyer->name,
                    'email' => $transaction->buyer->email,
                )
            );

            $snapToken = \Midtrans\Snap::getSnapToken($params);

            $transaction->snap_token = $snapToken;

            return $transaction;
        } catch (\Exception $e) {
            DB::rollBack();

            throw new Exception($e->getMessage());
        }
    }

    #[Override]
    public function updateStatus(
        string $id, 
        array $data
    ) {
        DB::beginTransaction();
        
        try {
            $transaction = Transaction::find($id);
            
            if (isset($data['tracking_number'])) {
                $transaction->tracking_number = $data['tracking_number'];
            }

            if (isset($data['delivery_proof'])) {
                $transaction->delivery_proof = $data['delivery_proof']->store('assets/transaction', 'public');
            }

            $transaction->delivery_status = $data['delivery_status'];
            $transaction->save();

            DB::commit();

            return $transaction;
       } catch (\Exception $e) {
            DB::rollBack();

            throw new Exception($e->getMessage());
        }
    }

    private function getTotalWeight(array $transactionDetails)
    {
        $productIds = collect($transactionDetails)->pluck('product_id')->toArray();
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

        $totalWeight = 0;

        foreach ($transactionDetails as $item) {
            $product = $products[$item['product_id']] ?? null;

            if ($product) {
                $totalWeight += $product->weight * $item['qty'];
            }
        }

        return $totalWeight;
    }

    private function calculateShippingAndTax(array $data, float $subtotal, float $weight)
    {
        $origin = Store::find($data['store_id'])->address_id;
        $destination = $data['address_id'];

        $response = Http::withHeaders([
            'key' => 'ZxburMXvcf5d8bdd5ff4eecdYpY6ff4P'
        ])->asForm()->post('https://rajaongkir.komerce.id/api/v1/calculate/domestic-cost', [
            'origin' => $origin,
            'destination' => $destination,
            'weight' => $weight,
            'courier' => $data['shipping'],
        ]);

        $result = $response->json();

        $shippingCost = 0;

        foreach ($result['data'] as $courier) {
            if ($courier['code'] == $data['shipping'] && $courier['service'] == $data['shipping_type']) {
                $shippingCost = $courier['cost'];
            }
        }

        return [
            'shipping_cost' => $shippingCost,
            'tax' => round($subtotal * 0.11),
            'grand_total' => round($subtotal * 1.11 + $shippingCost)
        ];
    }
}