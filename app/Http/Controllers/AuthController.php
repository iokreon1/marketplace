<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\LoginStoreRequest;
use App\Http\Requests\RegisterStoreRequest;
use App\Http\Resources\UserResource;
use App\Interfaces\AuthRepositoryInterface;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    private AuthRepositoryInterface $authRepository;

    public function __construct(AuthRepositoryInterface $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    public function register(RegisterStoreRequest $request)
    {
        $request = $request->validated();

        try {
            $user = $this->authRepository->register($request);

            return ResponseHelper::jsonResponse(true, 'Register Berhasil', new UserResource($user), 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    public function login(LoginStoreRequest $request)
    {
        $request = $request->validated();

        try {
            $user = $this->authRepository->login($request);

            return ResponseHelper::jsonResponse(true, 'Login Berhasil', new UserResource($user), 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    public function me()
    {
        try {
            $user = $this->authRepository->me();

            return ResponseHelper::jsonResponse(true, 'Profile Berhasil diambil', new UserResource($user), 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    public function logout()
    {
        try {
            $user = $this->authRepository->logout();

            return ResponseHelper::jsonResponse(true, 'Logout Berhasil ', new UserResource($user), 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();
        $role = $user->roles->first()?->name;

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
        ];

        if ($role === 'buyer') {
            $rules['phone_number'] = 'nullable|string|max:20';
            $rules['profile_picture'] = 'nullable|image|mimes:png,jpg,jpeg|max:2048';
            $rules['address'] = 'nullable|string';
            $rules['address_id'] = 'nullable|string';
            $rules['city'] = 'nullable|string';
            $rules['postal_code'] = 'nullable|string';
        } elseif ($role === 'store') {
            $rules['store_name'] = 'required|string|max:255';
            $rules['logo'] = 'nullable|image|mimes:png,jpg,jpeg|max:2048';
            $rules['about'] = 'required|string';
            $rules['phone'] = 'required|string|max:20';
            $rules['address'] = 'required|string';
            $rules['address_id'] = 'required|string';
            $rules['city'] = 'required|string';
            $rules['postal_code'] = 'required|string';
        }

        $validated = $request->validate($rules);

        // Include files manually if not returned by validate (sometimes validation excludes unpassed keys, but validate() usually includes them if they are nullable)
        if ($request->hasFile('profile_picture')) {
            $validated['profile_picture'] = $request->file('profile_picture');
        }
        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo');
        }

        try {
            $user = $this->authRepository->updateProfile($validated);

            return ResponseHelper::jsonResponse(true, 'Profil Berhasil diperbarui', new UserResource($user), 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }
}
