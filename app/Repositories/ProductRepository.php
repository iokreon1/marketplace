<?php

namespace App\Repositories;

use App\Interfaces\ProductRepositoryInterface;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Str;
use Override;

class ProductRepository implements ProductRepositoryInterface
{
    public function getAll(
        ?string $search,
        ?string $productCategoryId,
        ?string $storeId,
        ?int $limit, 
        ?bool $random,
        bool $execute
    ) {
        $query = Product::query()->where(function ($query) use ($search, $productCategoryId, $storeId) { 
            if ($search) { 
                $query->search($search); 
            }

            if ($storeId) {
                $query->where('store_id', $storeId);
            }

            if ($productCategoryId) {
                $query->where('product_category_id', $productCategoryId);
            }
        })->with(['store', 'productCategory', 'productImages', 'productReviews']);

        if (auth()->check() && auth()->user()->hasRole('store')) {
            $store = auth()->user()->store;
            $query->where('store_id', $store ? $store->id : '00000000-0000-0000-0000-000000000000');
        }

        if ($limit) {
            $query->take($limit);
        }

        if ($random) {
            $query->inRandomOrder(); 
        }

        return $execute ? $query->get() : $query;
    }

    public function getAllPaginated(
        ?string $search,
        ?string $productCategoryId,
        ?string $storeId, 
        ?bool $random,
        ?int $rowPerPage
    ) {
        $query = $this->getAll(
            $search,
            $productCategoryId,
            $storeId,
            null,
            $random,
            false
        );

        return $query->paginate($rowPerPage);
    }

    public function getById(
        string $id
    ) {
        $query = Product::where('id', $id)->with(['store', 'productCategory', 'productImages', 'productReviews.transaction.buyer.user']);

        return $query->first();
    }

    public function getBySlug(
        string $slug
    ) {
        $query = Product::where('slug', $slug)->with(['store', 'productCategory', 'productImages', 'productReviews.transaction.buyer.user']);

        return $query->first();
    }

    public function create(
        array $data
    ) {
        DB::beginTransaction();

        try {
            $product = new Product;
            $product->store_id = $data['store_id'];
            $product->product_category_id = $data['product_category_id'];
            $product->name = $data['name'];
            $product->slug = Str::slug($data['name']) . '-i.' . rand(10000, 999999) . '.' . rand(10000000, 99999999); 
            $product->description = $data['description'];
            $product->condition = $data['condition'];
            $product->price = $data['price'];
            $product->weight = $data['weight'];
            $product->stock = $data['stock'];
            $product->save();

            $productImageRepository = new ProductImageRepository;

            if (isset($data['product_images'])) {
                foreach ($data['product_images'] as $productImage) {
                    $productImageRepository->create([
                        'product_id' => $product->id,
                        'image' => $productImage['image'],
                        'is_thumbnail' => $productImage['is_thumbnail']
                    ]);
                }
            }

            DB::commit();

            return $product;
        } catch (\Exception $e) {
            DB::rollBack();

            throw new Exception($e->getMessage());
        }
    }

    public function update(
        string $id,
        array $data
    ) {
        DB::beginTransaction();

        try {
            $product = Product::find($id);;
            $product->store_id = $data['store_id'];
            $product->product_category_id = $data['product_category_id'];
            $product->name = $data['name'];
            $product->slug = Str::slug($data['name']) . '-i.' . rand(10000, 999999) . '.' . rand(10000000, 99999999); 
            $product->description = $data['description'];
            $product->condition = $data['condition'];
            $product->price = $data['price'];
            $product->weight = $data['weight'];
            $product->stock = $data['stock'];
            $product->save();

            $productImageRepository = new ProductImageRepository;

            if (isset($data['deleted_product_images'])) {
                foreach ($data['deleted_product_images'] as $productImage) {
                    $productImageRepository->delete($productImage);
                }
            }

            if (isset($data['product_images'])) {
                foreach ($data['product_images'] as $productImage) {
                    if (!isset($productImage['id'])) {
                        $productImageRepository->create([
                            'product_id' => $product->id,
                            'image' => $productImage['image'],
                            'is_thumbnail' => $productImage['is_thumbnail']
                        ]);
                    }
                }
            }

            DB::commit();

            return $product;
        } catch (\Exception $e) {
            DB::rollBack();

            throw new Exception($e->getMessage());
        }
    }

    #[Override]
    public function delete(
        string $id
    ) {
        DB::beginTransaction();

        try {
            $product = Product::find($id);
            $product->delete();

            DB::commit();

            return $product;
        } catch (\Exception $e) {
            DB::rollBack();

            throw new Exception($e->getMessage());
        }
    }
}