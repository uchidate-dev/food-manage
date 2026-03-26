<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('recipes', function (Blueprint $table) {
            $table->id(); //bigint PK
            
            // FK
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('recipe_category_id')->constrained('recipe_categories');

            $table->string('title', 100)->index();
            $table->text('url')->nullable();
            $table->smallInteger('private_flg')->default(0)->index();
              // 0:公開 1:非公開
            $table->smallInteger('favorite_flg')->default(0)->index();
              // 0:通常 1:お気に入り
            $table->text('ingredients')->nullable();
            $table->fullText('ingredients');
              // 材料一覧のカラム内はフルテキスト検索を想定
            $table->mediumText('finished_image')->nullable(); // base64想定
            $table->text('memo')->nullable();

            $table->text('step1')->nullable();
            $table->text('step2')->nullable();
            $table->text('step3')->nullable();
            $table->text('step4')->nullable();
            $table->text('step5')->nullable();
            $table->text('step6')->nullable();
            $table->text('step7')->nullable();
            $table->text('step8')->nullable();
            $table->text('step9')->nullable();
            $table->text('step10')->nullable();
            $table->text('step11')->nullable();
            $table->text('step12')->nullable();
            $table->text('step13')->nullable();
            $table->text('step14')->nullable();
            $table->text('step15')->nullable();

            $table->mediumText('step_image1')->nullable(); // base64想定
            $table->mediumText('step_image2')->nullable(); // base64想定
            $table->mediumText('step_image3')->nullable(); // base64想定
            $table->mediumText('step_image4')->nullable(); // base64想定
            $table->mediumText('step_image5')->nullable(); // base64想定
            $table->mediumText('step_image6')->nullable(); // base64想定
            $table->mediumText('step_image7')->nullable(); // base64想定
            $table->mediumText('step_image8')->nullable(); // base64想定
            $table->mediumText('step_image9')->nullable(); // base64想定
            $table->mediumText('step_image10')->nullable(); // base64想定
            $table->mediumText('step_image11')->nullable(); // base64想定
            $table->mediumText('step_image12')->nullable(); // base64想定
            $table->mediumText('step_image13')->nullable(); // base64想定
            $table->mediumText('step_image14')->nullable(); // base64想定
            $table->mediumText('step_image15')->nullable(); // base64想定
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipes');
    }
};
