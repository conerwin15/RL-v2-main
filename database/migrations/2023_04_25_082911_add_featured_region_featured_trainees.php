<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFeaturedRegionFeaturedTrainees extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('featured_trainees', function (Blueprint $table) {
            $table->unsignedInteger('region_id')->after('year')->nullable();
            $table->enum('type', ['global', 'regional'])->after('region_id')->default('global');
            $table->unsignedInteger('created_by')->after('type')->nullable();

            $table->foreign('region_id')->references('id')->on('regions');
            $table->foreign('created_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('featured_trainees', function (Blueprint $table) {
            $table->dropColumn('region_id');
            $table->dropColumn('type');
            $table->dropColumn('created_by');
        });
    }
}
