<?php

namespace Database\Factories;

use App\Models\Survey;
use App\Models\SurveyDelivery;
use Illuminate\Database\Eloquent\Factories\Factory;

class SurveyDeliveryFactory extends Factory
{
    protected $model = SurveyDelivery::class;

    public function definition()
    {
        $survey = Survey::factory()->create();
        return [
            'subject' => 'サーベイ受診のお願い',
            'body' => $this->faker->realText(100),
            'scheduled_sending_at' => now(),
            'started_sending_at' => now(),
            'completed_sending_at' => now(),
            'sending_count' => 10,
            'targetable_id' => $survey->id,  // targetable_idにIDをセット
            'targetable_type' => Survey::class,  // targetable_typeにモデルのクラスをセット
        ];
    }
}
