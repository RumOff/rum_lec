<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Survey;
use Illuminate\Database\Eloquent\Factories\Factory;

class SurveyFactory extends Factory
{
    protected $model = Survey::class;

    public function definition()
    {
        return [
            'company_id' => Company::factory(),
            'form_url' => $this->faker->url,
            'form_password' => $this->faker->optional()->password,
            'title' => $this->faker->sentence,
            'starts_at' => $this->faker->dateTimeBetween('now', '+1 month'),
            'expires_at' => $this->faker->dateTimeBetween('+1 month', '+2 months'),
            'open_results' => $this->faker->boolean,
        ];
    }
}
