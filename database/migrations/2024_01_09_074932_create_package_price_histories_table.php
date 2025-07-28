<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackagePriceHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('package_price_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('package_id');
            $table->enum('price_type', ['price', 'discounted_price']);
            $table->decimal('updated_price');
            $table->timestamps();

            // Adding foreign keys
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('package_id')->references('id')->on('learning_packages');
        });
       
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('package_price_histories');
    }
}
