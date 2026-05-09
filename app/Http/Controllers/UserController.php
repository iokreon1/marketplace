<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\PaginateResource;
use App\Http\Resources\UserResource;
use App\Helpers\ResponseHelper;
use App\Interfaces\UserRepositoryInterface;
use Illuminate\Http\Request;

class UserController extends Controller
{

    private UserRepositoryInterface $userRepository; // controller punya variable $userRepository dan harus mengikuti interface UserRepositoryInterface

    public function __construct(UserRepositoryInterface $userRepository) // Laravel kasih saya repositry user, nanti saya pakai di controller
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $users = $this->userRepository->getAll( // pakai repository itu untuk ambil data user
                $request->search, // pakai search (kalau ada)
                $request->limit, // pakai limit (kalau ada)
                true // langsung 
            );

            return ResponseHelper::jsonResponse(true, 'Data user berhasil diambil', UserResource::collection($users), 200);
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
            $users = $this->userRepository->getAllPaginated(
                $request['search'] ?? null,
                $request['row_per_page']
            );

            return ResponseHelper::jsonResponse(true, 'Data user berhasil diambil', PaginateResource::make($users, UserResource::class), 200);

        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserStoreRequest $request)
    {
        $request = $request->validated(); // melakukan validasi request dengan rules yang ada di UserStoreRequest,

        try {
            $user = $this->userRepository->create($request); // pakai repository itu untuk buat user baru dengan data request yang sudah divalidasi 

            return ResponseHelper::jsonResponse(true, 'Data user berhasil ditambahkan', new UserResource($user), 201);
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
            $user = $this->userRepository->getById($id); // pakai repository itu untuk ambil data user berdasarkan id

            if (!$user) {
                return ResponseHelper::jsonResponse(true, 'Data user tidak ditemukan', null, 404);
            }

            return ResponseHelper::jsonResponse(true, 'Data user berhasil diambil', new UserResource($user), 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserUpdateRequest $request, string $id)
    {
        $request = $request->validated(); // melakukan validasi request dengan rules yang ada di UserUpdateRequest,

        try {
            $user = $this->userRepository->getById($id); // pakai repository itu untuk ambil data user berdasarkan id

            if (!$user) {
                return ResponseHelper::jsonResponse(true, 'Data user tidak ditemukan', null, 404);
            }

            $user = $this->userRepository->update(
                $id, // id user yang mau diupdate
                $request // data request yang sudah divalidasi untuk update user, nanti di repository yang ngurus update usernya dengan data ini 
            );

            return ResponseHelper::jsonResponse(true, 'Data user berhasil diupdate', new UserResource($user), 200);
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
            $user = $this->userRepository->getById($id); // pakai repository itu untuk ambil data user berdasarkan id

            if (!$user) {
                return ResponseHelper::jsonResponse(true, 'Data user tidak ditemukan', null, 404);
            }

            $user = $this->userRepository->delete($id); // pakai repository itu untuk hapus user berdasarkan id, nanti di repository yang ngurus hapus usernya

            return ResponseHelper::jsonResponse(true, 'Data user berhasil dihapus', new UserResource($user), 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }
}
