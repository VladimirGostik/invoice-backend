<?php

namespace Database\Factories;

use App\Models\MonthlyInvoice;
use Illuminate\Database\Eloquent\Factories\Factory;

class MonthlyInvoiceItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $quantity = $this->faker->numberBetween(1, 100);
        $unitPrice = $this->faker->randomFloat(2, 5, 500);
        $lineTotal = $quantity * $unitPrice;

        $descriptions = [
            'Dodávka vody',
            'Dodávka elektriny',
            'Dodávka plynu',
            'Teplo',
            'Odvoz odpadu',
            'Spotreba studenej vody',
            'Spotreba teplej vody',
            'Upratovanie',
            'Údržba',
            'Správa budovy',
            'Výťah',
            'Osvetlenie',
            'Poisťovne',
        ];

        $units = [
            'm³',
            'kWh',
            'GJ',
            'ks',
            'paušál',
            'osoba',
            'byt',
        ];

        return [
            'monthly_invoice_id' => MonthlyInvoice::factory(),
            'description' => $this->faker->randomElement($descriptions),
            'unit' => $this->faker->randomElement($units),
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'line_total' => $lineTotal,
        ];
    }
}
