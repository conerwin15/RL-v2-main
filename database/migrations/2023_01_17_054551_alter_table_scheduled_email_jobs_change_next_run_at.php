<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableScheduledEmailJobsChangeNextRunAt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('scheduled_email_jobs', function (Blueprint $table) {
            $table->dateTime('next_run_at')->change();
            $table->dateTime('end_date')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('scheduled_email_jobs', function (Blueprint $table) {
            $table->dropColumn('next_run_at');
            $table->dropColumn('end_date');
        });
    }
}
