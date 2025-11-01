<?php

namespace Database\Factories;

use App\Enums\InvoiceStatusEnum;
use App\Enums\InvoiceTypeEnum;
use App\Models\Company;
use App\Models\Street;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subtotal = $this->faker->randomFloat(2, 50, 5000);
        $tax = $subtotal * 0.20; // 20% DPH
        $total = $subtotal + $tax;

        return [
            'company_id' => Company::factory(),
            'residential_company_id' => null,
            'street_id' => null,
            'invoice_number' => null,
            'variable_symbol' => null,
            'invoice_name' => $this->faker->words(3, true),
            'type' => InvoiceTypeEnum::MONTHLY->value,
            'status' => InvoiceStatusEnum::DRAFT->value,
            'billing_year' => now()->year,
            'billing_month' => now()->month,
            'issued_at' => null,
            'due_at' => null,
            'delivered_at' => null,
            'payment_date' => null,

            // Company snapshot
            'company_name' => $this->faker->company(),
            'company_city' => $this->faker->city(),
            'company_state' => 'Slovensko',
            'company_address' => $this->faker->streetAddress(),
            'company_zip' => $this->faker->numerify('#####'),
            'company_ico' => $this->faker->numerify('########'),
            'company_dic' => $this->faker->numerify('##########'),
            'company_ic_dph' => $this->faker->optional(0.7)->numerify('SK##########'),

            // Customization
            'invoice_text' => $this->faker->optional()->sentence(),
            'is_complex_billing' => false,
            'additional_info_1' => null,
            'additional_info_2' => null,
            'invoice_above_table_text' => $this->faker->optional()->text(200),

            // Totals
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $total,

            'info_dph' => 'Ceny sú uvedené s 20% DPH',
            'invoice_issuer_name' => $this->faker->name(),
            'invoice_issuer_email' => $this->faker->companyEmail(),
            'invoice_issuer_phone' => $this->faker->phoneNumber(),
            'signatures' => null,
            'qr_code' => null,
        ];
    }

    /**
     * Monthly invoice state
     */
    public function monthly(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => InvoiceTypeEnum::MONTHLY->value,
            'invoice_number' => null,
            'issued_at' => null,
            'due_at' => null,
        ]);
    }

    /**
     * With residential company
     */
    public function withResidential(): static
    {
        return $this->state(fn (array $attributes) => [
            'residential_company_id' => Company::factory(),
        ]);
    }

    /**
     * With street
     */
    public function withStreet(): static
    {
        return $this->state(fn (array $attributes) => [
            'street_id' => Street::factory(),
        ]);
    }

    /**
     * Complex billing
     */
    public function complexBilling(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_complex_billing' => true,
            'additional_info_1' => $this->faker->sentence(),
            'additional_info_2' => $this->faker->sentence(),
        ]);
    }
}
