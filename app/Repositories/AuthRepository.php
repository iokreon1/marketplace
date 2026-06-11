<?php

namespace App\Repositories;

use App\Interfaces\AuthRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Auth;
use Override;

class AuthRepository implements AuthRepositoryInterface
{
    public function register(
        array $data
    ) {
        DB::beginTransaction();
        
        try {
            $user = new User;
            $user->name = $data['name'];
            $user->email = $data['email'];
            $user->password = bcrypt($data['password']);
            $user->save();

            $user->assignRole($data['role']);

            if ($data['role'] == 'buyer') {
                $user->buyer()->create([
                    'profile_picture' => null,
                    'phone_number' => null,
                ]);
            }

            $user->token = $user->createToken('auth_token')->plainTextToken;
            $user->load('roles', 'buyer', 'store');
            $user->permissions = $user->roles->flatMap->permissions->pluck('name');

            DB::commit();

            return $user;
        } catch (\Exception $e) {
            DB::rollBack();

            throw new Exception($e->getMessage());
        }
    }

    #[Override]
    public function login(
        array $data
    ) {
        DB::beginTransaction();
        
        try {
            if (!Auth::guard('web')->attempt($data)) {
                throw new \Exception('Unauthorized');
            }

            $user = Auth::user(); // Ambil data user yang sudah terautentikasi
            $user->token = $user->createToken('auth_token')->plainTextToken; // Buat token baru untuk user tersebut
            $user->load('roles', 'buyer', 'store');
            $user->permissions = $user->roles->flatMap->permissions->pluck('name');

            DB::commit();

            return $user;
        } catch (\Exception $e) {
            DB::rollBack();

            throw new Exception($e->getMessage());
        }
    }

    public function me() 
    {
        DB::beginTransaction();
        
        try {
            if (!Auth::check()) {
                throw new \Exception('Unauthorized');
            }

            $user = Auth::user();
            $user->load('roles', 'buyer', 'store');
            $user->permissions = $user->roles->flatMap->permissions->pluck('name');

            DB::commit();

            return $user;
        } catch (\Exception $e) {
            DB::rollBack();

            throw new Exception($e->getMessage());
        }
    }

    public function logout() 
    {
        DB::beginTransaction();
        
        try {
            if (!Auth::check()) {
                throw new \Exception('Unauthorized');
            }

            $user = Auth::user();
            $user->tokens()->delete();
            
            DB::commit();

            return $user;
        } catch (\Exception $e) {
            DB::rollBack();

            throw new Exception($e->getMessage());
        }
    }

    public function updateProfile(array $data)
    {
        DB::beginTransaction();

        try {
            $user = Auth::user();
            $user->name = $data['name'];
            $user->email = $data['email'];

            if (!empty($data['password'])) {
                $user->password = bcrypt($data['password']);
            }
            $user->save();

            if ($user->hasRole('buyer')) {
                $buyer = $user->buyer;
                if (!$buyer) {
                    $buyer = $user->buyer()->create([
                        'phone_number' => null,
                        'profile_picture' => null,
                    ]);
                }

                if (isset($data['profile_picture']) && $data['profile_picture'] instanceof \Illuminate\Http\UploadedFile) {
                    $buyer->profile_picture = $data['profile_picture']->store('assets/buyer', 'public');
                }

                $buyer->phone_number = $data['phone_number'] ?? $buyer->phone_number;
                $buyer->address_id = $data['address_id'] ?? $buyer->address_id;
                $buyer->city = $data['city'] ?? $buyer->city;
                $buyer->address = $data['address'] ?? $buyer->address;
                $buyer->postal_code = $data['postal_code'] ?? $buyer->postal_code;
                $buyer->save();
            } elseif ($user->hasRole('store')) {
                $store = $user->store;
                if ($store) {
                    $store->name = $data['store_name'];
                    
                    if (isset($data['logo']) && $data['logo'] instanceof \Illuminate\Http\UploadedFile) {
                        $store->logo = $data['logo']->store('assets/store', 'public');
                    }

                    $store->about = $data['about'];
                    $store->phone = $data['phone'];
                    $store->address_id = $data['address_id'];
                    $store->city = $data['city'];
                    $store->address = $data['address'];
                    $store->postal_code = $data['postal_code'];
                    $store->save();
                }
            }

            $user->load('roles', 'buyer', 'store');
            $user->permissions = $user->roles->flatMap->permissions->pluck('name');

            DB::commit();

            return $user;
        } catch (\Exception $e) {
            DB::rollBack();

            throw new Exception($e->getMessage());
        }
    }
}