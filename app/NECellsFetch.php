<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NECellsFetch extends Model
{
    //
    protected $table = 'noteditable_cells_view';

    public function scopeOfForm($query, $form)
    {
        return $query
            ->where('f', $form);
    }

    public function scopeOfTable($query, $table)
    {
        return $query
            ->where('t', $table);
    }

    public static function byOuId(int $ou_id, int $form_id)
    {
        $q = "SELECT
                f.id AS f,
                t.id AS t,
                n.row_id AS r,
                n.column_id AS c,
                'Все учреждения' AS g
            FROM
                noteditable_cells n
                JOIN rows r ON r.id = n.row_id
                JOIN columns c ON c.id = n.column_id
                JOIN tables t ON t.id = r.table_id
                JOIN forms f ON f.id = t.form_id
                WHERE n.condition_id = 0 AND f.id = :fid
            UNION
            SELECT
                f.id AS f,
                t.id AS t,
                n.row_id AS r,
                n.column_id AS c,
                con.condition_name AS g
            FROM
                noteditable_cells n
                JOIN rows r ON r.id = n.row_id
                JOIN columns c ON c.id = n.column_id
                JOIN tables t ON t.id = r.table_id
                JOIN forms f ON f.id = t.form_id
                JOIN necell_conditions con ON con.id = n.condition_id
                WHERE con.group_id IN (SELECT group_id FROM unit_group_members WHERE ou_id = :uid) AND f.id = :fid AND con.exclude = FALSE
            UNION
            SELECT
                f.id AS f,
                t.id AS t,
                n.row_id AS r,
                n.column_id AS c,
                con.condition_name AS g
            FROM
                noteditable_cells n
                JOIN rows r ON r.id = n.row_id
                JOIN columns c ON c.id = n.column_id
                JOIN tables t ON t.id = r.table_id
                JOIN forms f ON f.id = t.form_id
                JOIN necell_conditions con ON con.id = n.condition_id
                WHERE con.group_id NOT IN (SELECT group_id FROM unit_group_members WHERE ou_id = :uid) AND f.id = :fid AND con.exclude = TRUE";
        $ne_cells = \DB::select($q, ['fid' => $form_id, 'uid' => $ou_id]);
        return $ne_cells;
    }
}
