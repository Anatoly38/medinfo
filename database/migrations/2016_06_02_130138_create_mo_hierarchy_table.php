<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMoHierarchyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mo_hierarchy', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('parent_id')->index()->nullable();
            $table->string('unit_code', 32)->unique();
            $table->char('inn', 10)->nullable()->unique();
            $table->smallInteger('node_type')->default(3)->index();
            $table->smallInteger('report')->default(0);
            $table->smallInteger('aggregate')->default(0);
            $table->string('unit_name', 256)->index();
            $table->smallInteger('blocked')->default(0);
            $table->integer('medinfo_id')->nullable()->unique();
            $table->softDeletes();
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
        Schema::drop('mo_hierarchy');
    }
}
