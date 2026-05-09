<?php

namespace App\Repositories;

use Exception;
use Illuminate\Support\Facades\DB;
use App\Interfaces\UserRepositoryInterface;
use App\Models\User;

class UserRepository implements UserRepositoryInterface
{
    public function getAll( // siapkan query ambil user, bisa difilter, bisa dibatasi, dan bisa langsung dieksekusi atau tidak 
        ?string $search,
        ?int $limit,
        bool $execute,
    ) {
        $query = User::where(function ($query) use ($search) { // mulai query ke tabel user
            if ($search) { // kalau ada search jalankan
                $query->search($search); // kode ini ada di model User
                                         // contoh: $query->search('wahyu')
            }
        });

        if ($limit) {
            $query->take($limit);
        }

        if ($execute) { // kalau true
            return $query -> get(); // hasil dijalankan hasilnya collection (data nyata)
        }
                        // kalau false
        return $query; // belum dijalankan masih query builder
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
        $query = User::where('id', $id);

        return $query->first();
    }

    public function create(
        array $data
    ) {
        DB::beginTransaction();

        try {
            $user = new User;
            $user->name = $data['name'];
            $user->email = $data['email'];
            $user->password = bcrypt($data['password']);
            $user->save();

            DB::commit();

            return $user;
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
            $user = User::find($id);
            $user->name = $data['name'];
            
            if (isset($data['password'])) {
                $user->password = bcrypt($data['password']);
            }

            $user->save();

            DB::commit();

            return $user;
        } catch (\Exception $e) {
            DB::rollBack();

            throw new Exception($e->getMessage());
        }
    }

    public function delete(
        string $id
    ) {
        DB::beginTransaction();

        try {
            $user = User::find($id); // cari user berdasarkan id yag sudah dikirim di parameter
            $user->delete(); // setelah ketemu usernya, hapus usernya

            DB::commit(); // agar datanya terhapus secara dari database secara langsung

            return $user; // kembalikan data user yang sudah dihapus
        } catch (\Exception $e) {
            DB::rollback(); // kalau ada error, maka data tidak jadi dihapus, tetap seperti semula

            throw new Exception($e->getMessage()); // lempar errornya supaya bisa ditangkap di controller, nanti di controller kita buat response error 
        }
    }
}


