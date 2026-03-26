<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recipe_ingredients', function (Blueprint $table) {
            $table->id();

            // どのレシピに
            $table->foreignId('recipe_id')->constrained()->cascadeOnDelete();
            // どの食材(マスター)が必要
            $table->foreignId('ingredient_id')->constrained()->cascadeOnDelete();

            // 分量(例：「100g」「1/2個」などを文字で入れる)
            $table->string('quantity', 50)->nullable();
            $table->timestamps();

            // 同じレシピに同じ食材が重複登録されないようにする実務的ガード！
            $table->unique(['recipe_id', 'ingredient_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recipe_ingredients');
    }
};
