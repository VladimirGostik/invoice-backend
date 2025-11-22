<?php

namespace Database\Factories;

use App\Enums\CompanyTypeEnum;
use App\Models\CompanyCustomization;
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
            'company_type' => CompanyTypeEnum::cases()[array_rand(CompanyTypeEnum::cases())]->value,
            'company_name' => $this->faker->company(),
            'company_city' => $this->faker->city(),
            'company_state' => 'Slovensko',
            'company_address' => $this->faker->streetAddress(),
            'company_zip' => $this->faker->numerify('#####'),
            'company_ico' => $this->faker->numerify('########'),
            'company_dic' => $this->faker->numerify('##########'),
            'company_ic_dph' => $this->faker->optional(0.7)->numerify('SK##########'),
            'company_bank_account' => $this->faker->iban('SK'),
            'company_bank_swift' => 'SUBASKBX',
        ];
    }

    /**
     * ✅ Automaticky vytvor customization pre main companies
     */
    public function configure()
    {
        return $this->afterCreating(function ($company) {
            // Ak je main company, vytvor customization
            if ($company->company_type->value === CompanyTypeEnum::MAIN->value) {
                CompanyCustomization::create([
                    'company_id' => $company->id,
                    'invoice_issuer_name' => $this->faker->name(),
                    'invoice_issuer_email' => $this->faker->companyEmail(),
                    'invoice_issuer_phone' => $this->faker->phoneNumber(),
                    'signature_base64' => $this->generateSignature(),
                ]);
            }
        });
    }

    /**
     * ✅ Generuje fake podpis ako base64 PNG obrázok
     */
    private function generateSignature(): string
    {
        // Vytvor obrázok 300x100 px
        $width = 300;
        $height = 100;
        $image = imagecreatetruecolor($width, $height);

        // Biely background
        $white = imagecolorallocate($image, 255, 255, 255);
        $black = imagecolorallocate($image, 0, 0, 0);
        $blue = imagecolorallocate($image, 0, 0, 255);

        imagefill($image, 0, 0, $white);

        // Nakresli "podpis" - simulované písmo
        $name = $this->faker->name();

        // Použij built-in font alebo text
        imagestring($image, 5, 10, 40, $name, $blue);

        // Nakresli čiaru (simulovaný podpis)
        for ($i = 0; $i < 5; $i++) {
            $x1 = rand(10, 50);
            $y1 = rand(60, 80);
            $x2 = rand(200, 280);
            $y2 = rand(60, 80);
            imageline($image, $x1, $y1, $x2, $y2, $black);
        }

        // Konvertuj na PNG buffer
        ob_start();
        imagepng($image);
        $imageData = ob_get_clean();
        imagedestroy($image);

        // Vráť base64 s data URI
        return 'data:image/png;base64,' . base64_encode($imageData);
    }

    /**
     * State pre main company
     */
    public function main(): static
    {
        return $this->state(fn (array $attributes) => [
            'company_type' => CompanyTypeEnum::MAIN->value,
        ]);
    }

    /**
     * State pre residential company
     */
    public function residential(): static
    {
        return $this->state(fn (array $attributes) => [
            'company_type' => CompanyTypeEnum::RESIDENTIAL->value,
        ]);
    }

    /**
     * S vlastnými customization údajmi
     */
    public function withCustomization(array $customizationData = []): static
    {
        return $this->afterCreating(function ($company) use ($customizationData) {
            CompanyCustomization::updateOrCreate(
                ['company_id' => $company->id],
                array_merge([
                    'invoice_issuer_name' => $this->faker->name(),
                    'invoice_issuer_email' => $this->faker->companyEmail(),
                    'invoice_issuer_phone' => $this->faker->phoneNumber(),
                    'signature_data_uri' => $this->generateSignature(),
                ], $customizationData)
            );
        });
    }

    /**
     * ✅ Bez podpisu
     */
    public function withoutSignature(): static
    {
        return $this->afterCreating(function ($company) {
            if ($company->companyCustomization) {
                $company->companyCustomization->update([
                    'signature_data_uri' => null,
                ]);
            }
        });
    }
}
