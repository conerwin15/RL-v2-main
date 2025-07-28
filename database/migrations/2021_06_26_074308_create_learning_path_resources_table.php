<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLearningPathResourcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('learning_path_resources', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('type', ['media_link', 'chatbot_link', 'course_link']);
            $table->string('title');
            $table->string('link');
            $table->unsignedInteger('learning_path_id');
            $table->integer('resource_order')->nullable();
            $table->unsignedInteger('scorm_package_id')->nullable();
            $table->unsignedInteger('created_by');
            $table->unsignedInteger('updated_by');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();

            $table->foreign('learning_path_id')->references('id')->on('learning_paths');
            $table->foreign('scorm_package_id')->references('scorm_package_id')->on('scorm_package');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('learning_path_resources');
    }
}
