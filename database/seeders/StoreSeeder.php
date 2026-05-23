<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\StoreBalance;
use Illuminate\Database\Seeder;
use App\Models\Store;
use App\Models\StoreBalanceHistory;
use App\Models\Withdrawal;

class StoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Store::factory()->count(10)->create()->each(function ($store) {
            $storeBalance = StoreBalance::factory()->create(['store_id' => $store->id]);
            StoreBalanceHistory::factory()->create([
                'store_balance_id' => $storeBalance->id,
                'amount' => $storeBalance->balance
            ]);
            Withdrawal::factory()->count(1)->create([
                'store_balance_id' => $storeBalance->id
            ]);
        });
    }
}
