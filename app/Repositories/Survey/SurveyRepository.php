<?php

namespace App\Repositories\Survey;

use App\Models\Company;
use App\Models\Survey;
use App\Models\SurveyAnswer;
use App\Models\SurveyTargetUser;
use App\Models\SurveyDelivery;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

interface SurveyRepository
{
    public function find(int $surveyId): Survey;

    public function findWithProgress(int $surveyId): Survey;

    public function findWith(int $surveyId): Survey;

    public function findSurveyAnswer(int $userId, int $surveyAnswerId): SurveyAnswer;
    public function findSurveyAnswerByCustomKey(string $customKey): SurveyAnswer;

    public function firstSurveyAnswerBy(int $surveyTargetId): SurveyAnswer;

    public function findTarget(int $surveyTargetUserId): SurveyTargetUser;

    public function findSurveyDelivery(int $surveyDeliveryId): SurveyDelivery;

    public function paginate(int $userId): LengthAwarePaginator;

    public function paginateTarget(int $userId, int $surveyId): LengthAwarePaginator;

    public function startsSurvey(SurveyAnswer $surveyAnswer): void;

    public function collectSurveyAnswerDetails(SurveyAnswer $surveyAnswer): Collection;

    public function draftSave(SurveyAnswer $surveyAnswer, array $answers): void;

    public function completesSurvey(SurveyAnswer $surveyAnswer, array $answers): array;
    public function completesSurveyByUser(SurveyAnswer $surveyAnswer, array $answers): void;

    public function store(Company $company, array $surveyParam, array $userCsv): void;

    public function countSurveyQuestion(int $surveyId): int;

    public function collectSurveyDeliveries(int $surveyId): Collection;

    public function paginateSurveyTargetUsers(int $surveyId, ?string $q): LengthAwarePaginator;

    public function storeSurveyDelivery(Survey $survey, array $params): void;

    public function update(Survey $survey, array $surveyParam): void;

    public function updateTarget(array $params, int $surveyTargetUserId): void;

    public function storeTarget(array $params, int $surveyId): void;

    public function updateDetails(array $surveyParam): void;

    public function hasCategory(int $surveyId): bool;

    public function collectQuestion(int $surveyId): Collection;

    public function collectRadioQuestionForResults(int $surveyId): array;

    public function collectTextQuestionForResults(int $surveyId): Collection;

    public function canEditQuestion(int $surveyId): bool;

    public function storeQuestions(array $sampleAnswer): void;

    public function updateQuestions(Survey $survey, array $params): void;

    public function collectFormatedTargetSurveyAnswerDetails(int $surveyId): SupportCollection;

    public function collectFormatedOtherSurveyAnswerDetails(int $surveyId): SupportCollection;

    public function collectFormatedSurveyAnswerDetails(SurveyTargetUser $surveyTargetUser): SupportCollection;

    public function deleteSurveyDelivery(SurveyDelivery $surveyDelivery): void;

    public function deleteSurvey(Survey $survey): void;

    public function deleteTarget(SurveyTargetUser $surveyTargetUser): void;

    public function collectAll(): Collection;

    public function collectQuestionForDownload(array $params): array;

    public function builderDownloadDataBy(array $params): Builder;
}
