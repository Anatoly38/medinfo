<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePeriodsView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        DB::statement('CREATE OR REPLACE VIEW periods_view (
                year,
                id,
                name,
                pattern_id,
                parent_id)
            AS
             SELECT 
                NULL::character AS "year",
                1000000::integer AS id,
                \'Периоды\'::character varying AS name,
                NULL::integer AS pattern_id,
                NULL::integer AS parent_id
            UNION
            SELECT 
                DISTINCT("year"),
                "year"::integer AS id,
                concat("year", \' год\') AS name,
                NULL::integer AS pattern_id,
                1000000::integer AS parent_id
                FROM periods
            UNION
            SELECT 
                "year",
                id,
                name,
                pattern_id,
                "year"::integer AS parent_id
                FROM periods
            ORDER BY 1;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        DB::statement('DROP VIEW periods_view');
    }
}
