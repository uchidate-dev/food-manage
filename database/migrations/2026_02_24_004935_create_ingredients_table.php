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
        Schema::create('ingredients', function (Blueprint $table) {
            $table->id();  //bigint PK
            
            // FK
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); 
              //cascadeOnDelete()：親（users_table）のレコードが消えたら子（ingredients_table）のレコードも消える
            $table->foreignId('ingredient_cat_id')->constrained('ingredient_categories')->cascadeOnDelete();

            $table->string('name', 100)->index();
            $table->smallInteger('quantity')->nullable();
            $table->date('expiration_date')->index()->nullable();
            $table->smallInteger('storage_type')->index()->default(0);
              //0:冷蔵庫 1:冷凍庫
            $table->smallInteger('delete_flg')->index()->default(0);
              //0:表示 1:非表示（論理削除）
            $table->string('memo', 255)->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ingredients');
    }
};
