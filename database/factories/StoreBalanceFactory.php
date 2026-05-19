<?php

namespace Database\Factories;

use App\Models\StoreBalance;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Store;

/**
 * @extends Factory<StoreBalance>
 */
class StoreBalanceFactory extends Factory
{
    protected $model = StoreBalance::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'store_id' => Store::factory(), // Menghubungkan dengan StoreFactory untuk mendapatkan store_id
            'balance' => $this->faker->randomFloat(2, 0, 10000000) // Saldo acak antara 0 dan 10 juta dengan 2 desimal
        ];
    }
}
