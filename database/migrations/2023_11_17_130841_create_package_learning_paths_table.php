<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackageLearningPathsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('package_learning_paths', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('learning_package_id');
            $table->unsignedInteger('learning_path_id');

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();

            $table->foreign('learning_package_id')->references('id')->on('learning_packages');
            $table->foreign('learning_path_id')->references('id')->on('learning_paths');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('package_learning_paths');
    }
}
