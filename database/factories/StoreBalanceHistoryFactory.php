<?php

namespace Database\Factories;

use App\Models\StoreBalance;
use App\Models\StoreBalanceHistory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StoreBalanceHistory>
 */
class StoreBalanceHistoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'store_balance_id' => StoreBalance::factory(),
            'type' => 'initial',
            'reference_id' => null,
            'reference_type' => null, 
            'amount' => $this->faker->randomFloat(2, 0, 1000000),
            'remarks' => 'Pembuatan toko baru'
        ];
    }
}
