<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DicCfunctionTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('dic_cfunction_types', function (Blueprint $table) {
            $table->string('code', 16)->primary();
            $table->string('name', 64);
            $table->string('comment', 128)->nullable();
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
        Schema::drop('dic_cfunction_types');
    }
}
