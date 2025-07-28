<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddJobRoleIdToLearningPathsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('learning_paths', function (Blueprint $table) {
            
            $table->dropForeign('learning_paths_group_id_foreign');
            $table->unsignedInteger('group_id')->nullable()->unsigned()->change();
             //Remove the following line if disable foreign key
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');
            $table->text('job_role_id')->nullable()->after('group_id');
            

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
            //
        });
    }
}
