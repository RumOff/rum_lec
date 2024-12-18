<?php

namespace Database\Factories;

use App\Models\Survey;
use App\Models\SurveyQuestion;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

class SurveyQuestionFactory extends Factory
{
    protected $model = SurveyQuestion::class;

    public function definition()
    {
        $majorCategory = [
            'フェイスシート',
            '本設問',
        ];
        $mediumCategory = [
            '属性情報',
            '成果',
            'キー設問',
            '変数設問'
        ];
        $minorCategory = [
            '職種（職能）',
            '職位',
            '年代',
            '性別',
            '就業年数',
            '組織年齢',
            '能率'
        ];

        return [
            'survey_id' => Survey::factory(),
            'sort' => rand(1, 20),
            'major_category' => Arr::random($majorCategory),
            'medium_category' => Arr::random($mediumCategory),
            'minor_category' => Arr::random($minorCategory),
            'question_text' => fake()->realText(35),
            'answer_options' => json_encode([
                '選択肢1',
                '選択肢2',
                '選択肢3',
                '選択肢4',
                '選択肢5'
            ]),
        ];
    }
}
