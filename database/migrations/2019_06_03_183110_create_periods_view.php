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
                    parent_id,
                    begin_date,
                    end_date
                )
            AS
             SELECT 
                NULL::character(4) AS "year",
                1000000::integer AS id,
                \'Периоды\'::character(8) AS name,
                NULL::integer AS pattern_id,
                NULL::integer AS parent_id,
                NULL::date AS begin_date,
                NULL::date AS end_date
            UNION
            SELECT 
                DISTINCT("year"),
                "year"::integer AS id,
                concat("year", \' год\') AS name,
                NULL::integer AS pattern_id,
                1000000::integer AS parent_id,
                NULL::date AS begin_date,
                NULL::date AS end_date
                FROM periods
            UNION
            SELECT 
                "year",
                id,
                name,
                pattern_id,
                "year"::integer AS parent_id,
                 begin_date,
                 end_date
                FROM periods
            ORDER BY 1,4,6;');
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
