<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
        Schema::table('recipes', function (Blueprint $table) {
            $table->text('kids_tips')->nullable()->after('memo');
            $table->json('steps')->nullable()->after('kids_tips');
            $table->json('nutrition')->nullable()->after('steps');
        });
    }

    public function down()
    {
        Schema::table('recipes', function (Blueprint $table) {
            $table->dropColumn(['kids_tips', 'steps', 'nutrition']);
        });
    }
};
