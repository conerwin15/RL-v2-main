<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTemplatesDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('templates_data', function (Blueprint $table) {
            $table->increments('id');
            $table->string('template_name');
            $table->longtext('html_layout');
            $table->string('subject')->nullable();
            $table->integer('status')->default(1);
            $table->dateTime('deleted_at', $precision = 0)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('templates_data');
    }
}
