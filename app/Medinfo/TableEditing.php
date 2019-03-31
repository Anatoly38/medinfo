<?php
/**
 * Created by PhpStorm.
 * User: shameev
 * Date: 01.10.2016
 * Time: 16:29
 */

namespace App\Medinfo;

use App\Album;
use App\Column;
use App\RowProperty;
use App\Table;

class TableEditing
{
    public static function fetchDataForTableRenedering(Table $table, Album $album , $columntype = 'numberinput', $hiderowid = true)
    {
        if (!$table) {
            return [];
        }
        $fortable = [];
        $datafields_arr = array();
        $columns_arr = array();
        $datafields_arr[0] = array('name'  => 'id');
        $columns_arr[0] = array(
            'text'  => 'id',
            'dataField' => 'id',
            'width' => 50,
            'cellsalign' => 'left',
            'hidden' => $hiderowid,
            'pinned' => true
        );
        $calculated_fields = array();
        $column_groups_arr = array();
        //$cols = $table->columns->where('deleted', 0)->sortBy('column_index');

        $cols = Column::OfTable($table->id)->orderBy('column_index')->whereDoesntHave('excluded', function ($query) use($album) {
            $query->where('album_id', $album->id);
        })->get();
        $firstDataColumn = null;
        foreach ($cols as $col) {
            $datafields_arr[] = ['name'  => $col->id, 'type'  => 'string', ];
            $width = $col->size; // Ширина графы в пикселях при отображении в браузере
            switch ( $col->decimal_count) {
                case 1:
                    $editor = 'initDecimal1Editor';
                    break;
                case 2:
                    $editor = 'initDecimal2Editor';
                    break;
                case 3:
                    $editor = 'initDecimal3Editor';
                    break;
                default:
                    $editor = 'defaultEditor';
            }
            if ($col->content_type === Column::DATA) {
                if (!$firstDataColumn) {
                    $firstDataColumn = $col->id;
                }
                $columns_arr[] = array(
                    'text'  => $col->column_code,
                    'dataField' => $col->id,
                    'width' => $width,
                    'cellsalign' => 'right',
                    'align' => 'center',
                    'cellsrenderer' => 'cellsrenderer',
                    //'cellsformat' => 'n',
                    //'cellsformat' => 'd' . $decimal_count,
                    'columntype' => $columntype,
                    'columngroup' => $col->id,
                    'filtertype' => 'number',
                    'cellclassname' => 'cellclass',
                    'cellbeginedit' => 'cellbeginedit',
                    'initeditor' => $editor,
                    'validation' => 'validation'
                );
                $column_groups_arr[] = array(
                    'text' => $col->column_name,
                    'align' => 'center',
                    'name' => $col->id,
                    'rendered' => 'tooltiprenderer'
                );
            } elseif ($col->content_type === Column::CALCULATED) {
                $calculated_fields[] = $col->id;
                $columns_arr[] = array(
                    'text' => $col->column_code,
                    'dataField' => $col->id,
                    'width' => $width,
                    'cellsalign' => 'right',
                    'align' => 'center',
                    'cellsrenderer' => 'cellsrenderer',
                    'columntype' => $columntype,
                    'columngroup' => $col->id,
                    'pinned' => false,
                    'editable' => false,
                    'filtertype' => 'number',
                    'cellclassname' => 'calculated'
                );
                $column_groups_arr[] = array(
                    'text' => $col->column_name,
                    'align' => 'center',
                    'name' => $col->id,
                    'rendered' => 'tooltiprenderer'
                );
            } elseif ($col->content_type === Column::HEADER) {
                $columns_arr[] = array(
                    'text' => $col->column_name,
                    'dataField' => $col->id,
                    'width' => $width,
                    'cellsalign' => 'left',
                    'align' => 'center',
                    'pinned' => true,
                    'editable' => false,
                    'filtertype' => 'textbox'
                );
            }
        }
        $fortable['tablecode'] = $table->table_code;
        $fortable['tablename'] = $table->table_name;
        $fortable['index'] = $table->table_index;
        $fortable['firstdatacolumn'] = $firstDataColumn;
        $fortable['datafields'] = $datafields_arr;
        $fortable['calcfields'] = $calculated_fields;
        $fortable['columns'] = $columns_arr;
        $fortable['columngroups'] = $column_groups_arr;
        $fortable['aggregates'] = self::getAggregatedRows($table->id);
        return $fortable;
    }

    public static function getAggregatedRows($table_id)
    {
        $props = RowProperty::whereHas('row', function ($query) use($table_id) {
            $query->where('table_id', $table_id);
        })->pluck('properties');
        if ($props) {
            $decoded = $props->map(function ($item, $key) {
                return json_decode($item);
            });
            return $decoded;
        } else {
            return null;
        }

    }

    public static function tableRender(Table $table, $columntype = 'textbox', $hiderowid = true)
    {
        if (!$table) {
            return [];
        }
        $fortable = [];
        $datafields_arr = array();
        $columns_arr = array();
        $datafields_arr[0] = array('name'  => 'id');
        $columns_arr[0] = array(
            'text'  => 'id',
            'dataField' => 'id',
            'width' => 50,
            'cellsalign' => 'left',
            'hidden' => $hiderowid,
            'pinned' => true
        );
        $column_groups_arr = array();
        $cols = Column::OfTable($table->id)->orderBy('column_index')->get();
        foreach ($cols as $col) {
            $datafields_arr[] = ['name'  => $col->id, 'type'  => 'string', ];
            $width = $col->size;
            $contentType = $col->getMedinfoContentType();
            if ($contentType == 'data') {
                $columns_arr[] = array(
                    'text'  => $col->column_code,
                    'dataField' => $col->id,
                    'width' => $width,
                    //'cellsalign' => 'right',
                    'align' => 'center',
                    'columntype' => $columntype,
                    'columngroup' => $col->id,
/*                    'filtertype' => 'number',
                    'cellclassname' => 'cellclass',
                    'cellbeginedit' => 'cellbeginedit',
                    'validation' => 'validation'*/
                );
                $column_groups_arr[] = array(
                    'text' => $col->column_name,
                    'align' => 'center',
                    'name' => $col->id,
                    //'rendered' => 'tooltiprenderer'
                );
            } else if ($contentType == 'header') {
                $columns_arr[] = array(
                    'text' => $col->column_name,
                    'dataField' => $col->id,
                    'width' => $width,
                    'cellsalign' => 'left',
                    'align' => 'center',
                    'pinned' => true,
                    'editable' => false,
                    //'filtertype' => 'textbox'
                );
            }
        }
        $fortable['tablecode'] = $table->table_code;
        $fortable['tablename'] = $table->table_name;
        $fortable['datafields'] = $datafields_arr;
        $fortable['columns'] = $columns_arr;
        $fortable['columngroups'] = $column_groups_arr;
        return $fortable;
    }

    public static function isEditPermission(int $permission, int $document_state)
    {
        switch (true) {
            case (($permission & config('medinfo.permission.permission_edit_report')) && ($document_state == 2 || $document_state == 16)) :
            case (($permission & config('medinfo.permission.permission_edit_prepared_report')) && $document_state == 4) :
            case (($permission & config('medinfo.permission.permission_edit_accepted_report')) && $document_state == 8) :
            case (($permission & config('medinfo.permission.permission_edit_approved_report')) && $document_state == 32) :
            case (($permission & config('medinfo.permission.permission_edit_aggregated_report')) && $document_state == 0) :
                return true;
            default:
                return false;
        }
    }

    public static function isTableBlocked(int $document, int $table)
    {
        $blockedSections = \App\DocumentSectionBlock::OfDocument($document)->Blocked()->with('formsection.tables')->get();
        if ($blockedSections) {
            $ids = [];
            foreach($blockedSections as $blockedSection) {
                foreach ($blockedSection->formsection->tables as $t) {
                    $ids[] = $t->table_id;
                }
            }
            if (in_array($table, $ids)) {
                return true;
            }
        }
        return false;
    }
}