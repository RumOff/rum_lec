<?php

namespace Database\Factories;

use App\Models\SurveyAnswerDetail;
use App\Models\SurveyAnswer;
use App\Models\SurveyQuestion;
use Illuminate\Database\Eloquent\Factories\Factory;

class SurveyAnswerDetailFactory extends Factory
{
    protected $model = SurveyAnswerDetail::class;

    public function definition()
    {
        $isScoreAnswer = $this->faker->boolean;

        return [
            'survey_answer_id' => SurveyAnswer::factory(),
            'survey_question_id' => SurveyQuestion::factory(),
            'sort' => $this->faker->numberBetween(1, 10),
            'score' => $isScoreAnswer ? $this->faker->randomElement(array_values(SurveyAnswerDetail::SCORES)) : null,
            'text' => !$isScoreAnswer ? $this->faker->sentence : null,
        ];
    }

    public function score()
    {
        return $this->state(function (array $attributes) {
            return [
                'score' => $this->faker->randomElement(array_values(SurveyAnswerDetail::SCORES)),
                'text' => null,
            ];
        });
    }

    public function text()
    {
        return $this->state(function (array $attributes) {
            return [
                'score' => null,
                'text' => $this->faker->sentence,
            ];
        });
    }
}
