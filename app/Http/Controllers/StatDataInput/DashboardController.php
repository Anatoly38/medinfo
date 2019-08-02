<?php

namespace App\Http\Controllers\StatDataInput;

use Illuminate\Foundation\Auth\RedirectsUsers;
use Illuminate\Http\Request;
use Illuminate\Auth\GenericUser;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Unit;
use App\Monitoring;
use App\Period;
use App\Document;
use App\Album;
use App\Form;
use App\Table;
use App\Column;
use App\Row;
use App\Cell;
use App\NECellsFetch;
use App\ValuechangingLog;
use App\FormSection;
use App\UnitList;
use App\Medinfo\TableEditing;

class DashboardController extends Controller
{
    private $document_permission = null;
    private $tableblocks;
    private $tableblocks_has_gotten = false;
    // Текущая версия дашборда
    public function index_v2(Document $document)
    {
        $worker = Auth::guard('datainput')->user();
        //$album = Album::Default()->first(['id']);
        $album = Album::find($document->album_id);
        if (!$album) {
            $album = Album::find(config('medinfo.default_album'));
        }
        $statelabel = Document::$state_labels[$document->state];
        $monitoring = Monitoring::find($document->monitoring_id);
        $form = Form::find($document->form_id);
        $realform = Form::getRealForm($document->form_id);
        $current_unit = Unit::find($document->ou_id);
        if (!$current_unit) {
            $current_unit = UnitList::find($document->ou_id);
        }
        if ($worker->role === 0 ) {
            $editpermission = true;
        } else {
            //$editpermission = $this->isEditPermission($worker->permission, $document->state);
            $editpermission = TableEditing::isEditPermission($worker->permission, $document->state);
        }
        $disabled_states = config('medinfo.disabled_states.' . $worker->role);
        $editpermission ? $editmode = 'Редактирование' : $editmode = 'Только чтение';
        $period = Period::find($document->period_id);
        $editedtables = Table::editedTables($document->id, $album->id);
        $laststate = $this->getLastState($worker, $document, $form, $album);
        //$noteditablecells = NECellsFetch::where('f', $form->id)->select('t', 'r', 'c')->get();
        //$noteditablecells = NECellsFetch::byOuId($current_unit->id, $realform->id);
        $noteditablecells = NECellsFetch::OfTable($laststate['currenttable']->id)->get();
        //dd($laststate);
        //dd($noteditablecells);
        $autocalculate_totals = true;
        $renderingtabledata = $this->composeDataForTable($laststate['currenttable'], $album);
        $for_form_tables = $this->composeTableList($realform, $album, $editedtables);
        $tablelist = json_encode($for_form_tables['forformtable']);
        $maxtableindex = $for_form_tables['max_index'];
        $tableproperties = $renderingtabledata['tableproperties'];
        $datafields = json_encode($renderingtabledata['datafields']);
        $calcfields = $renderingtabledata['calcfields'];
        $columns = $renderingtabledata['columns'];
        $columngroups = $renderingtabledata['columngroups'];
        $firstdatacolumn = $renderingtabledata['firstdatacolumn'];
        $rowprops = $renderingtabledata['rowprops'];
        $colprops = $renderingtabledata['colprops'];
        $formsections = $this->getFormSections($realform->id, $album->id, $document->id);
        $validationrules = json_encode(self::defaultValidationRules());

        \App\RecentDocument::create(['worker_id' => $worker->id, 'document_id' => $document->id, 'occured_at' => Carbon::now(), ]);
        return view('jqxdatainput.formdashboard_v2', compact(
            'current_unit', 'document',
            'worker',
            'album',
            'statelabel',
            'editpermission',
            'editmode',
            'monitoring',
            'form',
            'period',
            'editedtables',
            'noteditablecells',
            'tablelist',
            'maxtableindex',
            'tableproperties',
            'datafields',
            'calcfields',
            'columns',
            'columngroups',
            'firstdatacolumn',
            'laststate',
            'autocalculate_totals',
            'formsections',
            'disabled_states',
            'rowprops',
            'colprops',
            'validationrules'
        ));
    }

    public function dashboardView()
    {
        return property_exists($this, 'dashboardView') ? $this->dashboardView : 'jqxdatainput.formdashboard_v2';
    }

    protected function composeDataForTable(Table $table, Album $album)
    {
        $table_props = [ "id" => $table->id , "code" => $table->table_code, "name" => $table->table_name, "index" => $table->table_index, ];
        //$table_props = [ "id" => $table->id , "code" => $table->table_code, "name" => 'asdfasdfasdf ', "index" => $table->table_index, ];
        //dd(json_encode($table_props));
        //dd($table->table_name);
        $datafortable = TableEditing::fetchDataForTableRenedering($table, $album);
        $composedata['tableproperties'] = json_encode($table_props);
        //$composedata['datafields'] = json_encode($datafortable['datafields']);
        $composedata['datafields'] = $datafortable['datafields'];
        $composedata['calcfields'] = json_encode($datafortable['calcfields']);
        $composedata['columns'] = json_encode($datafortable['columns']);
        $composedata['columngroups'] = json_encode($datafortable['columngroups']);
        $composedata['firstdatacolumn'] = $datafortable['firstdatacolumn'];
        $composedata['noteditablecells'] = NECellsFetch::OfTable($table->id)->get();
        $composedata['rowprops'] = $datafortable['rowprops'];
        $composedata['colprops'] = $datafortable['colprops'];
        return $composedata;
    }
    // данные для таблицы-фильтра для навигации по отчетным таблицам в форме
    protected function composeTableList(Form $form, Album $album, $editedtables)
    {
        $tables = Table::OfForm($form->id)->whereDoesntHave('excluded', function ($query) use($album) {
            $query->where('album_id', $album->id);
        })->orderBy('table_index')->get();
        $max_index = $tables->last()->table_index;
        $forformtable = [];
        foreach ($tables as $table) {
            in_array($table->id, $editedtables) ? $edited = 1 : $edited = 0;
            $forformtable[] = [ "id" => $table->id, "tindex" => $table->table_index, "code" => $table->table_code, "name" => $table->table_name, "edited" => $edited ];
            //$forformtable[] = '{ "id": ' . $table->id . ', "code": "' . $table->table_code . '", "name": "' . $table->table_name . '", "edited": ' . $edited . ' }';
        }
        return compact('forformtable', 'max_index');
    }

    public function getRealForm(Form $form)
    {
        if ($form->relation) {
            return Form::find($form->relation);
        } else {
            return $form;
        }
    }

    public function fetchDataForDataGrid(Album $album, Table $table)
    {
        return $this->composeDataForTable($table, $album);
    }

    public function fetchValues(int $document, int $album, Table $table)
    {
        $rows = Row::OfTable($table->id)->whereDoesntHave('excluded', function ($query) use($album) {
            $query->where('album_id', $album);
        })->orderBy('row_index')->get();
        $cols = Column::OfTable($table->id)->whereDoesntHave('excluded', function ($query) use($album) {
            $query->where('album_id', $album);
        })->orderBy('column_index')->get();
        $data = array();
        $i=0;
        foreach ($rows as $r) {
            $row = array();
            $row['id'] = $r->id;
            foreach($cols as $col) {
                switch ($col->content_type) {
                    case Column::HEADER :
                        if ($col->column_index == 1) {
                            $row[$col->id] = $r->row_name;
                        } elseif ($col->column_index == 2) {
                            $row[$col->id] = $r->row_code;
                        }
                        break;
                    case Column::CALCULATED :
                    case Column::DATA :
                        if ($c = Cell::OfDTRC($document, $table->id, $r->id, $col->id)->first()) {
                            if (is_null($c->value)) {
                                $c->delete();
                            } else {
                                $row[$col->id] = number_format($c->value, $col->decimal_count, '.', '');
                            }
                        }
                        break;
                        default:
                            $row[$col->id] = '#ЧИСЛО!';
                }
            }
            $data[$i] = $row;
            $i++;
        }
        return $data;
    }

    public function saveValue(Request $request, $document, $table)
    {
        $worker = Auth::guard('datainput')->user();
        $document = Document::find($document);
        if (TableEditing::isEditable($document, $table, $worker)) {
            $ou = $document->ou_id;
            $f = $document->form_id;
            $p = $document->period_id;
            $row = $request->row;
            $col = $request->column;
            $new = $request->value;
            //dd($new);
            $old = $request->oldvalue;
            //dd($old);
            $casted_new_value = (float)$new;
            $casted_old_value = (float)$old;
            //dd($casted_old_value);
            //dd($casted_new_value);
            if ($casted_new_value === $casted_old_value) {
                $data['cell_affected'] = false;
                $data['comment'] = "Изменения не сохранены по причине того что старое и новое значение равны, либо по причине того, что значение null изменено на 0 (или наоборот).";
            }
            else {
                $cell = Cell::firstOrCreate(['doc_id' => $document->id, 'table_id' => $table, 'row_id' => $row, 'col_id' => $col]);
                if (is_numeric($new)) {
                    if ($new == 0) {
                        //echo "Полученное значение 0, в БД записано null";
                        $cell->value = null;
                    }
                    else {
                        //echo "Получено значение отличное от нуля, в БД записано числовое значение";
                        $cell->value = $new;
                    }
                }
                else {
                    //echo "Получено нечисловое значение, в БД записано null";
                    $cell->value = null;
                }
                //$r = ($v) ?: 'No Value'; // $r is set to 'My Value' because $v is evaluated to TRUE
                $result = $cell->save();
                if ($result) {
                    $data['cell_affected'] = true;
                    //$cell_adr = 'O' . $ou . 'F' . $f . 'T' . $table . 'R' . $row . 'C' . $col . 'P' . $p ;
                    $log = [
                        'worker_id' => $worker->id,
                        'oldvalue' => $casted_old_value,
                        'newvalue' => $casted_new_value,
                        'd' => $document->id,
                        'o' => $ou,
                        'f' => $f,
                        't' => $table,
                        'r' => $row,
                        'c' => $col,
                        'p' => $p,
                        'occured_at' => Carbon::now()
                    ];
                    $event = ValuechangingLog::create($log);
                    $data['event_id'] = $event->id;
                }
                else {
                    $data['cell_affected'] = false;
                    $data['comment'] = "Ошибка сохранения данных на стороне сервера";
                }
            }
        }
        else {
            $data['cell_affected'] = false;
            $data['error'] = 1001;
/*            if (!$permissionByState) {
                $data['comment'] = "Отсутствуют права для изменения данных в этом документе (по статусу документа)";
            } elseif (!$permissionBySection) {
                $data['comment'] = "Отсутствуют права для изменения данных в этой таблице (раздел документа принят)";
            } else {
                $data['comment'] = "Отсутствуют права для изменения данных в этом документе";
            }*/
            $data['comment'] = "Отсутствуют права для изменения данных. Проверьте статус документа в целом или его раздела (при наличии)";
        }
        return $data;
    }
    // Сохранение значения ячейки БД без проверки прав - для пакетного сохранения
    public function storeCellValue(Document $document, $newcell, $worker)
    {
        $ou = $document->ou_id;
        $f = $document->form_id;
        $p = $document->period_id;
        $table = $newcell['table'];
        $row = $newcell['row'];
        $col = $newcell['column'];
        $new = $newcell['newvalue'];
        $old = $newcell['oldvalue'];
        $casted_new_value = (float)$new;
        $casted_old_value = (float)$old;
        if ($casted_new_value === $casted_old_value) {
            return false;
        }
        else {
            $cell = Cell::firstOrCreate(['doc_id' => $document->id, 'table_id' => $table, 'row_id' => $row, 'col_id' => $col]);
            if (is_numeric($new)) {
                if ($new == 0) {
                    $cell->value = null;
                }
                else {
                    $cell->value = $new;
                }
            }
            else {
                $cell->value = null;
            }
            $result = $cell->save();
            if ($result) {
                $log = [
                    'worker_id' => $worker->id,
                    'oldvalue' => $casted_old_value,
                    'newvalue' => $casted_new_value,
                    'd' => $document->id,
                    'o' => $ou,
                    'f' => $f,
                    't' => $table,
                    'r' => $row,
                    'c' => $col,
                    'p' => $p,
                    'occured_at' => Carbon::now()
                ];
                ValuechangingLog::create($log);
                return true;
            }
            else {
                return false;
            }
        }
    }
    // Пакетное сохранение изменений ячеек из журнала изменений на стороне браузера
    public function saveValues(Request $request, Document $document)
    {
        $ret = [];
        if (!is_array($request->unsaved)) {
            return [
                'error' => 1001,
                'message' => 'Неправильный формат отправки данных для сохранения в БД. Данные не сохранены',
            ];
        }
        $worker = Auth::guard('datainput')->user();
        $reccount = count($request->unsaved);
        foreach ($request->unsaved as $cell) {
            $cell['endstore_at'] = time();
            if ($this->checkEditPermission($worker, $document, $cell)) {
                $this->storeCellValue($document, $cell, $worker);
                $cell['stored'] = true;
                $cell['message'] = 'Сохранено успешно.';
            } else {
                $cell['stored'] = false;
                $cell['message'] = 'Недостаточно прав для записи.';
            }
            $ret[] = $cell;

        }

        return $ret;
    }

    public function checkEditPermission($worker, Document $document, $cell)
    {
        $supervisor = ($worker->role === 3 || $worker->role === 4) ? true : false;
        if ($this->document_permission === null) {
            $this->document_permission = TableEditing::isEditPermission($worker->permission, $document->state);
        }
        if (!$this->tableblocks_has_gotten) {
            $this->tableblocks = TableEditing::getBlockedTables($document->id);
            $this->tableblocks_has_gotten = true;
        }
        $permissionBySection = in_array($cell['table'], $this->tableblocks) ? false : true;
        return $this->document_permission && ( $permissionBySection || $supervisor );

    }

    public function fullValueChangeLog($document)
    {
        $document = Document::find($document);
        $form = Form::find($document->form_id);
        $current_unit = Unit::find($document->ou_id);
        $period = Period::find($document->period_id);
        $values = ValuechangingLog::where('d', $document->id)->orderBy('occured_at', 'desc')
            ->with('worker')
            ->with('table')
            ->get();
        return view('jqxdatainput.fullvaluelog', compact('values', 'document', 'form', 'current_unit', 'period'));
    }

    // TODO: Доработать сохранение настроек редактирования отчета (таблица, фильтры, ширина колонок и т.д.)
    protected function getLastState($worker, Document $document, Form $form, $album)
    {
        $laststate = [];
        $realform = Form::getRealForm($document->form_id);
        $current_table = Table::OfForm($realform->id)->whereDoesntHave('excluded', function ($query) use($album) {
            $query->where('album_id', $album->id);
        })->orderBy('table_index')->first();
        $laststate['currenttable'] = $current_table;
        return $laststate;
    }

    public function getFormSections($form, $album, $document)
    {
        return FormSection::OfForm($form)->whereHas('albums', function ($query) use($album) {
            $query->where('album_id', $album);
        })->with(['section_blocks' => function ($query) use($document) {
            $query->where('document_id', $document);
        }])->with('tables.table')
            ->orderBy('section_name')
            ->get();
    }

    public static function defaultValidationRules()
    {
        return [
            'disablenegatives' => ['rule' => '>= 0', 'message' => 'Отрицательные значения не допускаются'], // Отрицительные значения не допускаются
            'min' => ['rule' => null, 'message' => 'Значение не может быть меньше допустимого минимального значения'], // 0 - нет ограничений по минимальному значению
        ];
    }



// Предыдущая версия дашборда
    public function index(Document $document)
    {
        $worker = Auth::guard('datainput')->user();
        //$album = Album::Default()->first(['id']);
        $album = Album::find($document->album_id);
        if (!$album) {
            $album = Album::find(config('medinfo.default_album'));
        }
        $statelabel = Document::$state_labels[$document->state];
        $monitoring = Monitoring::find($document->monitoring_id);
        $form = Form::find($document->form_id);
        $current_unit = Unit::find($document->ou_id);
        if (!$current_unit) {
            $current_unit = UnitList::find($document->ou_id);
        }
        if ($worker->role === 0 ) {
            $editpermission = true;
        } else {
            //$editpermission = $this->isEditPermission($worker->permission, $document->state);
            $editpermission = TableEditing::isEditPermission($worker->permission, $document->state);
        }
        $disabled_states = config('medinfo.disabled_states.' . $worker->role);
        $editpermission ? $editmode = 'Редактирование' : $editmode = 'Только чтение';
        $period = Period::find($document->period_id);
        $editedtables = Table::editedTables($document->id, $album->id);
        //$noteditablecells = NECellsFetch::where('f', $form->id)->select('t', 'r', 'c')->get();
        $noteditablecells = NECellsFetch::byOuId($current_unit->id, $this->getRealForm($form)->id);
        $renderingtabledata = $this->composeDataForTablesRendering($this->getRealForm($form), $editedtables, $album);
        $laststate = $this->getLastState($worker, $document, $form, $album);
        $formsections = $this->getFormSections($this->getRealForm($form)->id, $album->id, $document->id);
        \App\RecentDocument::create(['worker_id' => $worker->id, 'document_id' => $document->id, 'occured_at' => Carbon::now(), ]);
        return view($this->dashboardView(), compact(
            'current_unit', 'document',
            'worker',
            'album',
            'statelabel',
            'editpermission',
            'editmode',
            'monitoring',
            'form',
            'period',
            'editedtables',
            'noteditablecells',
            'renderingtabledata',
            'laststate',
            'formsections',
            'disabled_states'
        ));
    }
    //Описательная информация для построения гридов динамически
    // возвращается json объект в формате для jqxgrid
    // В версии v2 не используется
    protected function composeDataForTablesRendering(Form $form, array $editedtables, Album $album)
    {
        $tables = Table::OfForm($form->id)->whereDoesntHave('excluded', function ($query) use($album) {
            $query->where('album_id', $album->id);
        })->orderBy('table_index')->get();
        $max_index = $tables->last()->table_index;
        $forformtable = [];
        $datafortables = [];
        foreach ($tables as $table) {
            in_array($table->id, $editedtables) ? $edited = 1 : $edited = 0;
            // данные для таблицы-фильтра для навигации по отчетным таблицам в форме
            $forformtable[] = "{ id: " . $table->id . ", code: '" . $table->table_code . "', name: '" . $table->table_name . "', edited: " . $edited . " }";
            $datafortables[$table->id] = TableEditing::fetchDataForTableRenedering($table, $album);
        }
        $datafortables_json = addslashes(json_encode($datafortables));
        $composedata['tablelist'] = $forformtable;
        $composedata['tablecompose'] = $datafortables_json;
        $composedata['max_index'] = $max_index;
        //$composedata['tablecompose'] = $datafortables;
        return $composedata;
    }


}
