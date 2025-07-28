<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEmbeddedLinkThreads extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('threads', function (Blueprint $table) {
            $table->string('embedded_link')->after('body')->nullable();
            $table->boolean('is_private')->after('status')->default(0);
            $table->text('body')->nullable()->change();
            $table->string('image')->after('embedded_link')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('threads', function (Blueprint $table) {
            $table->dropColumn('embedded_link');
            $table->dropColumn('is_private');
        });
    }
}
