<?php

namespace App\Repositories;

use App\Interfaces\StoreRepositoryInterface;
use Illuminate\Support\Facades\DB;
use App\Models\Store;
use Exception;

class StoreRepository implements StoreRepositoryInterface
{
    public function getAll( // siapkan query ambil user, bisa difilter, bisa dibatasi, dan bisa langsung dieksekusi atau tidak 
        ?string $search,
        ?bool $isVerified,
        ?int $limit,
        bool $execute,
    ) {
        $query = Store::query()->where(function ($query) use ($search, $isVerified) { // penjelasan sintaks ada di docs
            if ($search) { // kalau $search tidak null, jalankan kode di dalamnya / filternya
                $query->search($search); // filter data berdasarkan kata yang dicari, kode ini ada di model Store
                                         // contoh: $query->search('wahyu')
            }

            if ($isVerified !== null) {
                $query->where('is_verified', $isVerified);
            }
        });

        if ($limit) { // kalau $limit tidak null, jalankan kode di dalamnya / batasi jumlah data yang diambil 
            $query->take($limit); // ambil data sebanyak $limit, contoh: $query->take(10) -> ambil 10 data aja
        }

        if ($execute) { // kalau $execute bernilai true, jalankan querynya dan kembalikan hasilnya
            return $query -> get(); // jalankan querynya dan kembalikan hasilnya sebagai collection (data nyata)
        }          
        return $query; // kalau $execute bernilai false, kembalikan querynya saja tanpa menjalankannya
    }

    public function getAllPaginated(
        ?string $search, 
        ?bool $isVerified,
        ?int $rowPerPage
    ) {
        $query = $this->getAll( // $this->getAll itu memanggil method getAll milik object StoreRepository ini, untuk menyiapkan querynya dulu
            $search, // kirim nilai $search dari parameter ke method getAll
            $isVerified, // kirim nilai $isVerified dari parameter ke method getAll
            null, // limit tidak dipakai di sini, jadi dikirim null
            false // jangan dieksekusi dulu
        );

        return $query->paginate($rowPerPage); // jalankan querynya, tapi potong datanya per-halaman
                                              // contoh: paginate(10) -> potong data per 10 data untuk setiap halaman
    }

    public function getById(
        string $id
    ) {
        $query = Store::where('id', $id); // mulai query ke tabel store, dengan kondisi where id = $id, contoh: Store::where ('id', '123')

        return $query->first(); // jalankan querynya, tapi karena datanya cuma satu, kita ambil data pertamanya aja
    }

    public function create(
        array $data
    ) {
        DB::beginTransaction(); // mulai transaksi, ini seperti memulai proses yang harus selesai semua, kalau ada yang gagal, semua proses dibatalkan

        try {
            $store = new Store; // buat objek baru dari model Store, ini seperti menyiapkan keranjang kosong untuk diisi data toko baru
            $store->user_id = $data['user_id']; 
            $store->name = $data['name']; // isi properti name di objek $store dengan nilai dari $data['name'], 
            $store->logo = $data['logo']->store('assets/store', 'public'); // simpan file logo yang dikirim di $data['logo'] ke folder 'assets/store' di storage, dan simpan pathnya di properti logo;
            $store->about = $data['about'];
            $store->phone = $data['phone'];
            $store->address_id = $data['address_id'];
            $store->city = $data['city'];
            $store->address = $data['address'];
            $store->postal_code = $data['postal_code'];
            $store->save();

            $store->storeBalance()->create(['balance' => 0]); // setelah data toko disimpan, kita buat juga data store balance dengan nilai awal 0, ini untuk menyiapkan saldo toko yang nanti bisa diisi ketika ada transaksi masuk 

            DB::commit();

            return $store;
        } catch (\Exception $e) {
            DB::rollBack();

            throw new Exception($e->getMessage());
        }
    }

    public function updateVerifiedStatus(
        string $id, 
        bool $isVerified
    ) {
        DB::beginTransaction();

        try {
            $store = Store::find($id); // cari data toko berdasarkan id, contoh: Store::find('12')
            $store->is_verified = $isVerified; // update nilai is_verified di data toko dengan nilai $isVerified yang dikirim
            $store->save(); // simpan perubahan data toko ke database

            DB::commit();

            return $store;
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
            $store = Store::find($id); // cari data toko berdasarkan id, contoh: Store::find('12')
            $store->name = $data['name']; // isi properti name di objek $store dengan nilai dari $data['name'], 

            if (isset($data['logo'])) { // kalau ada data Logo yang dikirim, maka update Logonya, kalau tidak ada, biarkan Logo tetep seperti semula
                $store->logo = $data['logo']->store('assets/store', 'public'); 
            }
            $store->about = $data['about'];
            $store->phone = $data['phone'];
            $store->address_id = $data['address_id'];
            $store->city = $data['city'];
            $store->address = $data['address'];
            $store->postal_code = $data['postal_code'];
            $store->save();

            DB::commit();

            return $store;
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
            $store = Store::find($id); // cari data toko berdasarkan id yang sudah dikirim di parameter
            $store->delete();

            DB::commit();

            return $store;
        } catch (\Exception $e) {
            DB::rollBack();

            throw new Exception($e->getMessage());
        }
    }
}