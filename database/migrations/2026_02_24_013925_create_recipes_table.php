<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();

            // FK
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('recipe_category_id')->constrained('recipe_categories');

            $table->string('title', 100)->index();
            $table->text('url')->nullable();

            // ★変更点：爆弾（Base64）を撤去し、画像のパス（URL）だけを保存する箱に変更！
            $table->string('image_path')->nullable();

            $table->smallInteger('private_flg')->default(0)->index();
            $table->smallInteger('favorite_flg')->default(0)->index();

            $table->text('memo')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recipes');
    }
};
