<?php

namespace Database\Factories;

use App\Enums\CompanyTypeEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
    return [
        'company_type'         => CompanyTypeEnum::cases()[array_rand(CompanyTypeEnum::cases())]->value,
        'company_name'         => $this->faker->company(),
        'company_city'         => $this->faker->city(),
        'company_state'        => $this->faker->country(),
        'company_address'      => $this->faker->streetAddress(),
        'company_zip'          => $this->faker->postcode(),
        'company_ico'          => $this->faker->numerify('########'),
        'company_dic'          => $this->faker->numerify('##########'),
        'company_ic_dph'       => null,
        'company_bank_account' => 'SK' . $this->faker->numerify(str_repeat('#', 26)),
        'company_bank_swift'   => strtoupper($this->faker->lexify('????????')),
    ];
}

}
