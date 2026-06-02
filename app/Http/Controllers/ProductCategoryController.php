<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\ProductCategoryStoreRequest;
use App\Http\Requests\ProductCategoryUpdateRequest;
use App\Http\Resources\ProductCategoryResource;
use App\Interfaces\ProductCategoryRepositoryInterface;
use App\Http\Resources\PaginateResource;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Spatie\Permission\Middleware\PermissionMiddleware;

class ProductCategoryController extends Controller implements HasMiddleware
{
    private ProductCategoryRepositoryInterface $productCategoryRepository;

    public function __construct(ProductCategoryRepositoryInterface $productCategoryRepository)
    {
        $this->productCategoryRepository = $productCategoryRepository;
    }

    public static function middleware()
    {
        return [
            new Middleware(PermissionMiddleware::using(['product-category-list|product-category-create|product-category-edit|product-category-delete']), only: ['index', 'getAllPagianted', 'show', 'showBySlug']),
            new Middleware(PermissionMiddleware::using(['product-category-create']), only: ['store']),
            new Middleware(PermissionMiddleware::using(['product-category-edit']), only: ['update']),
            new Middleware(PermissionMiddleware::using(['product-category-delete']), only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $productCategories = $this->productCategoryRepository->getAll( 
                $request->search, 
                $request->is_parent,
                $request->limit, 
                true 
            );

            return ResponseHelper::jsonResponse(true, 'Data Kategori Produk berhasil diambil', ProductCategoryResource::collection($productCategories), 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    public function getAllPaginated(Request $request)
    {
        $request = $request->validate([
            'search' => 'nullable|string',
            'is_parent' => 'nullable|boolean',
            'row_per_page' => 'required|integer'
        ]);

        try {
            $productCategories = $this->productCategoryRepository->getAllPaginated(
                $request['search'] ?? null,
                $request['is_parent'] ?? null,
                $request['row_per_page']
            );

            return ResponseHelper::jsonResponse(true, 'Data Kategori Produk berhasil diambil', PaginateResource::make($productCategories, ProductCategoryResource::class), 200);

        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductCategoryStoreRequest $request)
    {
        $request = $request->validated();

        try {
            $productCategory = $this->productCategoryRepository->create($request);

            return ResponseHelper::jsonResponse(true, 'Kategori Produk berhasil ditambahkan', new ProductCategoryResource($productCategory), 201);
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
            $productCategory = $this->productCategoryRepository->getById($id); 
            if (!$productCategory) {
                return ResponseHelper::jsonResponse(true, 'Data Kategori Produk tidak ditemukan', null, 404);
            }

            return ResponseHelper::jsonResponse(true, 'Data Kategori Produk berhasil diambil', new ProductCategoryResource($productCategory), 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    public function showBySlug(string $slug)
    {
        try {
            $productCategory = $this->productCategoryRepository->getBySlug($slug); 
            if (!$productCategory) {
                return ResponseHelper::jsonResponse(true, 'Data Kategori Produk tidak ditemukan', null, 404);
            }

            return ResponseHelper::jsonResponse(true, 'Data Kategori Produk berhasil diambil', new ProductCategoryResource($productCategory), 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductCategoryUpdateRequest $request, string $id)
    {
        $request = $request->validated();

        try {
            $productCategory = $this->productCategoryRepository->getById($id); 

            if (!$productCategory) {
                return ResponseHelper::jsonResponse(true, 'Data Kategori Produk tidak ditemukan', null, 404);
            }

            $productCategory = $this->productCategoryRepository->update($id, $request);

            return ResponseHelper::jsonResponse(true, 'Data Kategori Produk berhasil diupdate', new ProductCategoryResource($productCategory), 200);
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
            $productCategory = $this->productCategoryRepository->getById($id); 

            if (!$productCategory) {
                return ResponseHelper::jsonResponse(true, 'Data Kategori Produk tidak ditemukan', null, 404);
            }

            $productCategory = $this->productCategoryRepository->delete($id);

            return ResponseHelper::jsonResponse(true, 'Data Kategori Produk berhasil dihapus', new ProductCategoryResource($productCategory), 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }
}
