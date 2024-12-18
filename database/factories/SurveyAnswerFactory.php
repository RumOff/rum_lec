<?php

namespace Database\Factories;

use App\Models\Survey;
use App\Models\SurveyAnswer;
use App\Models\SurveyTargetUser;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class SurveyAnswerFactory extends Factory
{
    protected $model = SurveyAnswer::class;

    public function definition()
    {
        return [
            'survey_id' => Survey::factory(),
            'survey_target_user_id' => SurveyTargetUser::factory(),
            'custom_key' => hash('sha256', Str::random(8)),
            'completes_at' => null,
        ];
    }
}
