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
}