<?php

namespace Database\Factories;

use App\Models\Company;
use App\Enums\CompanyTypeEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

class StreetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $streetNames = [
            'Hlavná',
            'Školská',
            'Bytčická',
            'Nová',
            'Malá',
            'Veľká',
            'Dlhá',
            'Krátka',
            'Partizánska',
            'Legionárska',
            'Mierová',
            'Slovinská',
            'Závadská',
            'Horná',
            'Dolná',
        ];

        return [
            'street_name' => $this->faker->randomElement($streetNames) . ' ' . $this->faker->numberBetween(1, 50),
            'company_id' => Company::factory()->create([
                'company_type' => CompanyTypeEnum::RESIDENTIAL->value,
            ]),
        ];
    }

    /**
     * Indicate that the street belongs to a specific residential company.
     */
    public function forCompany(int $companyId): static
    {
        return $this->state(fn (array $attributes) => [
            'company_id' => $companyId,
        ]);
    }
}
