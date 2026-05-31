<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TransactionDetail>
 */
class TransactionDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $qty = $this->faker->numberBetween(1, 5);
        $product = Product::factory()->create();
        $subtotal = $product->price * $qty;
        return [
            'transaction_id' => Transaction::factory(),
            'product_id' => $product->id,
            'qty' => $qty,
            'subtotal' => $subtotal,
        ];
    }
}
