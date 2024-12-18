<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyFactory extends Factory
{
    protected $model = Company::class;

    public function definition()
    {
        $industries = ['it','finance','manufacturing'];

        $startDate = $this->faker->dateTimeBetween('-2 years', 'now');
        $endDate = $this->faker->dateTimeBetween($startDate, '+2 years');

        return [
            'name' => $this->faker->company(),
            'industry' => $this->faker->randomElement($industries),
            'contract_start_date' => $startDate,
            'contract_end_date' => $endDate,
        ];
    }
}
