<?php

namespace Database\Factories;

use App\Models\Survey;
use App\Models\User;
use App\Models\SurveyTargetUser;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

class SurveyTargetUserFactory extends Factory
{
    protected $model = SurveyTargetUser::class;

    public function definition()
    {
        return [
            'survey_id' => Survey::factory(),
            'user_id' => User::factory(),
            'team' => 'チーム',
            'post' => fake()->jobTitle()
        ];
    }

    public function roleRater()
    {
        return $this->state(fn () => [
            'post' => Arr::random(['上司', 'トレーナー', 'トレーナー', 'トレーナー'])
        ]);
    }

    public function roleRated()
    {
        return $this->state(fn () => [
            'post' => Arr::random(['新卒新人', '中途新人'])
        ]);
    }
}
