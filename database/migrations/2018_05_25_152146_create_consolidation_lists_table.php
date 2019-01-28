<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConsolidationListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('consolidation_lists', function (Blueprint $table) {
            $table->increments('id');
            $table->string('script', 1024)->unique();
            $table->char('scripthash', 10)->unique();
            $table->char('prophash', 10)->index();
            $table->string('comment', 128)->nullable();
            $table->jsonb('properties')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::drop('consolidation_lists');
    }
}
