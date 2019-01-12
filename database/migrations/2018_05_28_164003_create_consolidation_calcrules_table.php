<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConsolidationCalcrulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('consolidation_calcrules', function (Blueprint $table) {
            $table->increments('id');
            $table->string('script', 1024)->unique();
            $table->char('hash', 10)->unique();
            $table->string('comment', 128)->nullable();
            $table->text('ptree')->nullable();
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
        Schema::drop('consolidation_calcrules');
    }
}
