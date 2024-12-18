<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Company;
use App\Models\Survey;
use App\Models\SurveyAnswer;
use App\Models\SurveyQuestion;
use App\Models\User;
use App\Models\SurveyTargetUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // 管理者作成
        if (! Admin::exists()) {
            $admins = Admin::factory()
                ->count(2)
                ->sequence(
                    [
                        'name' => 'super admin user',
                        'email' => env('SEED_SUPERADMIN_EMAIL', 'ito@example.net'),
                        'is_superadmin' => true,
                    ],
                    [
                        'name' => 'admin user',
                        'email' => env('SEED_ADMIN_EMAIL', 'wada@example.net'),
                        'is_superadmin' => false,
                    ]
                )
                ->create();
        }

        // 会社作成
        if (! Company::exists()) {
            $companies = Company::factory()
                ->count(5)
                ->has(
                    Survey::factory()
                        ->count(3)
                        ->sequence(
                            [
                                'title' => today()->subMonths(3)->format('Y年m月'),
                                'form_url' => "https://sample.com/",
                                'starts_at' => today()->subMonths(3),
                                'expires_at' => today()->subMonths(2),
                            ],
                            [
                                'title' => today()->format('Y年m月'),
                                'form_url' => "https://sample.com/",
                                'starts_at' => today(),
                                'expires_at' => today()->addMonth(),
                            ]
                        )
                )
                ->hasUsers(40)
                ->create();

            // 中間テーブルcompany_adminsへのデータ挿入
            foreach ($companies as $company) {
                $company->admins()->attach($admins->random()); // ランダムに1人の管理者を割り当て
            }

            // 質問とサーベイ対象ユーザーの作成
            foreach ($companies as $company) {
                foreach ($company->surveys as $survey) {
                    // 質問の作成
                    SurveyQuestion::factory()
                        ->count(10)
                        ->sequence(fn ($sequence) => [
                            'survey_id' => $survey->id,
                            'sort' => $sequence->index + 1
                        ])
                        ->create();

                    // サーベイ対象の作成
                    foreach ($company->users->chunk(5) as $chunkCount => $users) {
                        foreach ($users as $index => $user) {
                            if ($index % 5 !== 0) {
                                SurveyTargetUser::factory()
                                    ->roleRater()
                                    ->has(
                                        SurveyAnswer::factory()
                                            ->state(['survey_id' => $survey->id])
                                    )
                                    ->state([
                                        'survey_id' => $survey->id,
                                        'user_id' => $user->id,
                                        'team' => 'チーム' . ($chunkCount + 1),
                                    ])
                                    ->create();
                            } else {
                                SurveyTargetUser::factory()
                                    ->roleRated()
                                    ->has(
                                        SurveyAnswer::factory()
                                            ->state([
                                                'survey_id' => $survey->id,
                                                'survey_target_user_id' => $user->id,
                                                'custom_key' => hash('sha256', Str::random(8)),
                                            ])
                                    )
                                    ->state([
                                        'survey_id' => $survey->id,
                                        'user_id' => $user->id,
                                        'team' => 'チーム' . ($chunkCount + 1),
                                    ])
                                    ->create();
                            }
                        }
                    }
                }
            }

            // サーベイ回答詳細の作成
            foreach ($companies as $company) {
                foreach ($company->surveys as $survey) {
                    $surveyQuestions = $survey->surveyQuestions()->orderBy('sort', 'asc')->get();
                    $surveyAnswers = $survey->surveyAnswers()->get();

                    foreach ($surveyAnswers as $surveyAnswer) {
                        $details = $surveyQuestions->map(fn ($question, $index) => [
                            'sort' => $index + 1,
                            'survey_answer_id' => $surveyAnswer->id,
                            'survey_question_id' => $question->id,
                        ])->toArray();

                        $surveyAnswer->surveyAnswerDetails()->createMany($details);
                    }
                }
            }
        }
    }
}
