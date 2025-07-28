<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsOnlyBotLearningPathsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('learning_paths', function (Blueprint $table) {

            $table->boolean('is_only_bot')->default(0);
            $table->string('unique_ID')->unique()->nullable();
            $table->unsignedInteger('category_id')->nullable();
            $table->unsignedInteger('sub_category_id')->nullable();
            $table->string('suitable_for')->nullable();
            $table->integer('language')->nullable();
            $table->string('instructor')->nullable();
            $table->integer('level')->nullable();
            $table->string('tags_Keywords')->nullable();
            $table->unsignedInteger('uploaded_by')->nullable();
            $table->string('type')->nullable();
            $table->double('price', 15, 8)->default(0);
            $table->double('discount_price', 15, 8)->default(0);
            $table->string('duration')->nullable();
            $table->string('chatbot_name')->nullable();
            $table->text('chatbot_description')->nullable();
            $table->string('chatbot_image')->nullable();
            $table->string('iframe_link')->nullable();
            $table->string('bot_code')->unique()->nullable();
            $table->string('requirements')->nullable();

            $table->foreign('category_id')->references('id')->on('categories');
            $table->foreign('sub_category_id')->references('id')->on('categories');
            $table->foreign('uploaded_by')->references('id')->on('users');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('learning_paths', function (Blueprint $table) {
            $table->dropColumn('is_only_bot');
            $table->dropColumn('unique_ID');
            $table->dropColumn('category_id');
            $table->dropColumn('sub_category_id');
            $table->dropColumn('suitable_for');
            $table->dropColumn('language');
            $table->dropColumn('instructor');
            $table->dropColumn('level');
            $table->dropColumn('tags_Keywords');
            $table->dropColumn('uploaded_by');
            $table->dropColumn('type');
            $table->dropColumn('price');
            $table->dropColumn('discount_price');
            $table->dropColumn('duration');
            $table->dropColumn('chatbot_name');
            $table->dropColumn('chatbot_description');
            $table->dropColumn('chatbot_image');
            $table->dropColumn('iframe_link');
            $table->dropColumn('bot_code');
            $table->dropColumn('requirements');
        });
    }
}
