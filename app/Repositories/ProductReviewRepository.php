<?php

namespace App\Repositories;

use Exception;
use App\Interfaces\ProductReviewRepositoryInterface;
use App\Models\ProductReview;
use Illuminate\Support\Facades\DB;

class ProductReviewRepository implements ProductReviewRepositoryInterface
{
    public function create(
        array $data
    ) {
        DB::beginTransaction();

        try {
            $productReview = new ProductReview;
            $productReview->transaction_id = $data['transaction_id']; 
            $productReview->product_id = $data['product_id'];
            $productReview->rating = $data['rating'];
            $productReview->review = $data['review'];

            if (isset($data['photo']) && $data['photo'] instanceof \Illuminate\Http\UploadedFile) {
                $productReview->photo = $data['photo']->store('assets/review', 'public');
            }

            $productReview->save();

            DB::commit();

            return $productReview;
        } catch (\Exception $e) {
            DB::rollBack();

            throw new Exception($e->getMessage());
        }
    }
}