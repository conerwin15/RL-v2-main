<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddScoreToUserLearningProgressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_learning_progress', function (Blueprint $table) {
            $table->double('score', 8, 2)->after('cmi_data')->nullable();
            $table->double('max_score', 8, 2)->after('score')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_learning_progress', function (Blueprint $table) {
            $table->dropColumn('score');
            $table->dropColumn('max_score');
        });
    }
}
