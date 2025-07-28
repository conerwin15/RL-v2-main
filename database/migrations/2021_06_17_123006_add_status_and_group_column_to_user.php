<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusAndGroupColumnToUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {

            $table->unsignedInteger('group_id')->after('job_role_id')->nullable();
            $table->unsignedInteger('dealer_id')->after('group_id')->nullable();
            $table->unsignedInteger('created_by')->after('password')->nullable();
            $table->unsignedInteger('updated_by')->after('created_by')->nullable();
            $table->tinyInteger('status')->default(1)->aftre('remember_token');
            $table->string('image')->nullable();
            $table->string('remarks')->nullable();

            $table->foreign('group_id')->references('id')->on('groups');
            $table->foreign('dealer_id')->references('id')->on('users');
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
        Schema::table('users', function (Blueprint $table) {

            $table->dropForeign(['group_id']);
            $table->dropForeign(['dealer_id']);
            $table->dropColumn('group_id');
            $table->dropColumn('dealer_id');
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
            $table->dropColumn('created_by');
            $table->dropColumn('updated_by');
            $table->dropColumn('status');
            $table->dropColumn('remarks');
        });
    }
}
