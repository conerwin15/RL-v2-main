<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScheduledEmailJobs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scheduled_email_jobs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('subject');
            $table->string('description');
            $table->enum('frequency', ['every', 'once']);
            $table->integer('frequency_amount')->nullable();
            $table->enum('frequency_unit', ['day', 'week', 'month'])->nullable();
            $table->enum('audience_type', ['filters', 'users'])->nullable();
            $table->date('next_run_at')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_processed')->default(0);
            $table->unsignedInteger('created_by');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('scheduled_email_jobs');
    }
}
