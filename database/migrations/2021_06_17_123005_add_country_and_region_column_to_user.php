<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCountryAndRegionColumnToUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('country_id')->after('job_role_id')->nullable();
            $table->string('region_id')->after('country_id')->nullable();

            $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade');
            
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

           $table->dropForeign(['country_id']);
           $table->dropColumn('country_id');
           $table->dropColumn('region_id');
        });
    }
}
