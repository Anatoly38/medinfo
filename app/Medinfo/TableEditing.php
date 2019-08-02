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
use App\Document;
use App\RowProperty;
use App\ColumnProperty;
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
            $width = $col->size; // Ширина графы в пикселях при отображении в браузере
            switch ( $col->decimal_count) {
                case 1:
                    $editor = 'decimal1Editor';
                    break;
                case 2:
                    $editor = 'decimal2Editor';
                    break;
                case 3:
                    $editor = 'decimal3Editor';
                    break;
                default:
                    $editor = 'defaultEditor';
            }
            if ($col->content_type === Column::DATA) {
                $datafields_arr[] = ['name'  => $col->id, 'type'  => 'number', ];
                if (!$firstDataColumn) {
                    $firstDataColumn = $col->id;
                }
                $columns_arr[] = array(
                    'text'  => $col->column_code,
                    'dataField' => $col->id,
                    'width' => $width,
                    'cellsalign' => 'right',
                    'align' => 'center',
                    //'cellsrenderer' => 'cellsrenderer',
                    //'cellsformat' => 'n',
                    'cellsformat' => $col->decimal_count === 0 ? 'n' : 'd' . $col->decimal_count,
                    'columntype' => $columntype,
                    //'columntype' => 'template',
                    'columngroup' => $col->id,
                    'filtertype' => 'number',
                    'cellclassname' => 'cellclass',
                    'cellbeginedit' => 'cellbegineditByColumn',
                    'createeditor' => $editor,
                    //'initeditor' => $editor,
                    'validation' => 'validation'
                );
                $column_groups_arr[] = array(
                    'text' => $col->column_name,
                    'align' => 'center',
                    'name' => $col->id,
                    'rendered' => 'tooltiprenderer'
                );
            } elseif ($col->content_type === Column::CALCULATED) {
                $datafields_arr[] = ['name'  => $col->id, 'type'  => 'number', ];
                $calculated_fields[] = $col->id;
                $columns_arr[] = array(
                    'text' => $col->column_code,
                    'dataField' => $col->id,
                    'width' => $width,
                    'cellsalign' => 'right',
                    'align' => 'center',
                    'cellsformat' => $col->decimal_count === 0 ? 'n' : 'd' . $col->decimal_count,
                    //'cellsrenderer' => 'cellsrenderer',
                    'columntype' => $columntype,
                    'columngroup' => $col->id,
                    'pinned' => false,
                    'editable' => false,
                    //'filtertype' => 'number',
                    'cellclassname' => 'calculated',
                );
                $column_groups_arr[] = array(
                    'text' => $col->column_name,
                    'align' => 'center',
                    'name' => $col->id,
                    'rendered' => 'tooltiprenderer',
                );
            } elseif ($col->content_type === Column::HEADER) {
                $datafields_arr[] = ['name'  => $col->id, 'type'  => 'string', ];
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
        $fortable['index'] = $table->table_index;
        $fortable['firstdatacolumn'] = $firstDataColumn;
        $fortable['datafields'] = $datafields_arr;
        $fortable['calcfields'] = $calculated_fields;
        $fortable['columns'] = $columns_arr;
        $fortable['columngroups'] = $column_groups_arr;
        $fortable['rowprops'] = self::getRowProperties($table->id);
        $fortable['colprops'] = self::getColumnProperties($table->id);
        return $fortable;
    }

    public static function getRowProperties($table_id)
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

    public static function getColumnProperties($table_id)
    {
        $props = ColumnProperty::whereHas('column', function ($query) use($table_id) {
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

    public static function isEditable(Document $document, int $table, $worker)
    {
        $supervisor = ($worker->role === 3 || $worker->role === 4) ? true : false;
        if ($worker->role === 0 ) {
            $editpermission = true;
        } else {
            $permissionByState = self::isEditPermission($worker->permission, $document->state);
            $permissionBySection = !self::isTableBlocked($document->id, $table);
            // вариант 1: изменения запрещены только при соответствующем статусе документа
            //$editpermission = $permissionByState && $permissionBySection;
            // вариант 2: изменения запрещены при соответствующем статусе и во всех таблицах принятых разделов для всех пользователей
            //$editpermission = $permissionByState && $permissionBySection;
            // вариант 3: изменения запрещены при соответствующем статусе и во все таблицах принятых разделов для исполнителей за исключением сотрудников,
            // принимающих отчеты
            $editpermission = $permissionByState && ( $permissionBySection || $supervisor );
        }
        return $editpermission;
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
        $blockedSections = self::getBlockedSections($document);
        if ($blockedSections) {
            $ids = [];
            foreach($blockedSections as $blockedSection) {
                if ($blockedSection->formsection) {
                    foreach ($blockedSection->formsection->tables as $t) {
                        $ids[] = $t->table_id;
                    }
                }
            }
            if (in_array($table, $ids)) {
                return true;
            }
        }
        return false;
    }

    public static function getBlockedSections(int $document)
    {
        return \App\DocumentSectionBlock::OfDocument($document)->Blocked()->with('formsection.tables')->get();
    }

    public static function getBlockedTables(int $document)
    {
        $ids = [];
        $blockedSections = self::getBlockedSections($document);
        if ($blockedSections) {
            foreach($blockedSections as $blockedSection) {
                if ($blockedSection->formsection) {
                    foreach ($blockedSection->formsection->tables as $t) {
                        $ids[] = $t->table_id;
                    }
                }
            }
        }
        return $ids;
    }

}