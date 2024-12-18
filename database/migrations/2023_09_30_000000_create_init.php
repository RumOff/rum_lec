<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('queue')->index();
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });

        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });

        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->boolean('is_superadmin')->default(false)->comment('superadmin判定');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('会社名');
            $table->string('industry')->comment('業種');
            $table->date('contract_start_date')->comment('契約開始日');
            $table->date('contract_end_date')->comment('契約終了日');
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('company_admins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('admin_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained();
            $table->string('name')->nullable();
            $table->string('email');
            $table->string('password')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('surveys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained();
            $table->string('title')->comment('診断名');
            $table->string('form_url')->comment('診断URL');
            $table->string('form_password')->nullable()->comment('診断URL');
            $table->date('starts_at')->comment('診断回答開始日時');
            $table->date('expires_at')->comment('診断回答期限');
            $table->boolean('open_results')->default(false)->comment('結果閲覧可能か');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('survey_deliveries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('job_id')->nullable();
            $table->morphs('targetable');
            $table->string('subject')->comment('メールのタイトル');
            $table->text('body')->comment('メールの本文');
            $table->datetime('scheduled_sending_at')->comment('配信予定時間');
            $table->datetime('started_sending_at')->nullable()->comment('配信開始時間');
            $table->datetime('completed_sending_at')->nullable()->comment('配信完了時間');
            $table->unsignedInteger('sending_count')->nullable()->comment('配信数');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('survey_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')->constrained();
            $table->unsignedTinyInteger('sort')->comment('質問の並び順（No）');
            $table->string('major_category')->nullable()->comment('大分類');
            $table->string('medium_category')->nullable()->comment('中分類');
            $table->string('minor_category')->nullable()->comment('小分類（因子）');
            $table->text('question_text')->comment('設問文');
            $table->json('answer_options')->comment('回答選択肢');
            $table->timestamps();
        });

        Schema::create('survey_target_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')->constrained();
            $table->foreignId('user_id')->constrained('users');
            $table->string('team')->nullable()->comment('チーム名');
            $table->string('post')->nullable()->comment('役職');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('survey_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')->constrained();
            $table->foreignId('survey_target_user_id')->constrained();
            $table->string('custom_key')->unique()->comment('アンケートとユーザーを識別するカスタムキー');
            $table->datetime('completes_at')->nullable()->comment('回答終了時間');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('survey_answer_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_answer_id')->constrained();
            $table->foreignId('survey_question_id')->constrained();
            $table->unsignedTinyInteger('sort')->comment('設問番号');
            $table->float('score', 4, 1)->nullable()->comment('回答:選択式');
            $table->text('text')->nullable()->comment('回答:記述式');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('survey_answer_details');
        Schema::dropIfExists('survey_answers');
        Schema::dropIfExists('survey_target_users');
        Schema::dropIfExists('users');
        Schema::dropIfExists('survey_questions');
        Schema::dropIfExists('survey_deliveries');
        Schema::dropIfExists('surveys');
        Schema::dropIfExists('company_admins');
        Schema::dropIfExists('companies');
        Schema::dropIfExists('admins');
        Schema::dropIfExists('failed_jobs');
        Schema::dropIfExists('jobs');
    }
};
