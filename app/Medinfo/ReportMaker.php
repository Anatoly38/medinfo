<?php
/**
 * Created by PhpStorm.
 * User: shameev
 * Date: 16.09.2016
 * Time: 11:34
 */

namespace App\Medinfo;

use App\Period;
use App\Unit;
use App\UnitListMember;
use App\Form;
use App\Table;
use App\Document;
use App\Cell;
use Mockery\Exception;
use \Session;

class ReportMaker
{
    private $period;
    private $states;
    private $dtype;
    private $units;
    private $tree_scope = []; // ограничение по дереву МО
    private $list_scope = []; // ограничение по списку
    public  $all_scope = [];
    private $population_form;
    private $population_rows;
    private $population_column;
    private $group_by;

    public function __construct(int $group_by = 2, int $period_id = 4, int $sort_order = 2, $list_id = null)
    {
        $this->period = Period::find($period_id);
        $this->states = [ 2, 4, 8, 16, 32 ]; // Документы со всеми статусами
        $this->dtype = 1; // Только первичные документв
        $this->tree_scope = Unit::getDescendants(config('medinfo.report_tree'));
        if ($list_id === null) {
            config('medinfo.report_list') === 0 ? $list_id = null : $list_id = config('medinfo.report_list');
        }
        if ($list_id !== null) {
            $this->list_scope = UnitListMember::List($list_id)->select('ou_id')->pluck('ou_id')->toArray();
        }
        //dd($this->tree_scope);
        if (count($this->list_scope) > 0) {
            $this->all_scope = array_intersect($this->tree_scope, $this->list_scope);
        } else {
            $this->all_scope = $this->tree_scope;
        }
        switch ($sort_order) {
            case 1:
                $order = 'territory_type';
                break;
            case 2:
                $order = 'unit_name';
                break;
            case 3:
                $order = 'unit_code';
                break;
        }
        switch ($group_by) {
            case 1:
                $this->units = Unit::legal()->active()->orderBy('unit_code')->get();
                $this->group_by = 1;
                break;
            case 2:
                $this->units = Unit::territory()->active()->orderBy('unit_name')->get();
                // Добавляем в коллекцию "Всего"
                $all = Unit::find(config('medinfo.report_tree'));
                $this->units->push($all);
                $this->group_by = 2;
                break;
            case 3:
                $this->units = Unit::primary()->active()->orderBy('unit_code')->get();
                $this->group_by = 3;
                break;
        }
        if (count($this->all_scope) > 0) {
            $this->units = $this->units->whereIn('id', $this->all_scope);
        }
        Session::put('report_progress', 0);
        Session::put('current_unit', '');
        Session::put('count_of_units', $this->units->count());
        Session::save();

        //dd($included);
        //dd(array_diff($this->units->toArray(), $included));
        $this->population_form = Form::OfCode('100')->first();
        $population_table = Table::OfFormTableCode($this->population_form->id, '1000')->with('rows')->with('columns')->first();
        $this->population_rows = $population_table->rows;
        $this->population_column = $population_table->columns;
        //dd($this->population_column->where('column_index', 3)->first()->id);
    }

    public function makeReportByLegal(array $indexes)
    {
        $report_units = [];
        $calculation_errors = [];
        $u = 0;
        if ($this->group_by === 3) {
            foreach ($this->units as $u) {
                if ($u->unit_code == 1022) {
                    dd($u);
                }
            }
        }

        foreach ($this->units as $unit) {
            //if (count($this->list_scope) > 0 && !in_array($unit->id, $this->list_scope) && ($unit->node_type == 3 || $unit->node_type == 4)) {
            //if ($unit->node_type == 3 || $unit->node_type == 4) {
                //continue;
            //}
            $report_units[$unit->id]['unit_name'] = $unit->unit_name;
            $report_units[$unit->id]['unit_code'] = $unit->unit_code;
            $report_units[$unit->id]['inn'] = $unit->inn;
            $report_units[$unit->id]['node_type'] = $unit->node_type;
            $i = 0;
            $row_sum = 0;
            foreach ($indexes['content'] as $index => $rule) {
                $report_units[$unit->id][$i] = [];
                $formula =  $rule['value'];
                $cellcount = preg_match_all('/Ф([а-я0-9.-]+)Т([\w.-]+)С([\w.-]+)Г(\d{1,})/u', $formula, $matches, PREG_SET_ORDER);
                //$cellcount = preg_match_all('/(?:Ф(?P<f>[а-я0-9.-]*))?(?:Т(?P<t>[а-я0-9.-]*))?(?:С(?P<r>[0-9.-]*))?(?:Г(?P<c>\d{1,3}))?(?:П(?P<p>[01]))?/u', $formula, $matches);
                //$cellcount = preg_match_all('/(?:Ф(?P<f>[а-я0-9.-]*))?(?:Т(?P<t>[а-я0-9.-]*))?(?:С(?P<r>[0-9.-]*))?(?:Г(?P<c>\d{1,3}))/u', $formula, $matches, PREG_SET_ORDER);
                //dd($matches);
                $v = 0;
                foreach ($matches as $c_addr) {
                    $form_code = $c_addr[1];
                    $table_code = $c_addr[2];
                    $row_code = $c_addr[3];
                    $col_index = $c_addr[4];
                    $form = Form::OfCode($form_code)->first();
                    //if ($unit->id === 115 && $c_addr[0] == 'Ф30Т1001С13Г4') {
                        //dd($col_index);
                    //}
                    if ($this->group_by === 3) {
                        $v = $this->getPlainValue($unit, $form, $table_code, $row_code, $col_index);
                    } else {
                        $v = $this->getAggregatedValue($unit, $form, $table_code, $row_code, $col_index);
                    }
                    $formula = str_replace($c_addr[0], $v, $formula);
                }
                $populationlinks = preg_match_all('/население\((\d{1,})\)/u', $formula, $populationmatches, PREG_SET_ORDER);
                foreach ($populationmatches as $populationmatch) {
                    $populationgroup = $populationmatch[1];
                    try {
                        $population = $this->getPopulation($unit, $populationgroup);
                        $formula = str_replace($populationmatch[0], $population, $formula);
                    } catch (\Exception $e) {
                        $calculation_errors[] = ['formula' => $rule['value'], 'error' => $e->getMessage(), 'unit' => $unit];
                        $formula = str_replace($populationmatch[0], 0, $formula);
                    }
                }
                $m = new EvalMath;
                $value = 0;
                try {
                    $value = $m->e($formula);
                }
                catch (\Exception $e) {
                    $calculation_errors[] = ['formula' => $formula, 'error' => $e->getMessage(), 'unit' => $unit];
                    //dd("Ошибка при вычислении формулы: " . $formula . " " . $e);
                }
                //$value = eval('return ' . $formula . ';' );
                //echo $formula . PHP_EOL;
                //echo 'Вычисленное по формуле - ' . $value . PHP_EOL;
                $row_sum += $value;
                $report_units[$unit->id][$i]['id'] = $index;
                $report_units[$unit->id][$i]['value'] = number_format($value, 2, ',',' ');
                $i++;
            }
            //echo $row_sum .PHP_EOL;
            $report_units[$unit->id]['row_sum'] = $row_sum;
            $u++;
            Session::put('report_progress', $u);
            Session::put('current_unit', $unit->unit_name);
            Session::save();
        }
        return [ $report_units, $calculation_errors ];
    }

    public function getPlainValue(Unit $unit, Form $form, $table_code, $row_code, $col_index)
    {
        $val_q = "SELECT v.value AS value FROM statdata v
                          LEFT JOIN documents d on v.doc_id = d.id
                          JOIN tables t on v.table_id = t.id
                          LEFT JOIN \"rows\" r on v.row_id = r.id
                          LEFT JOIN columns c on v.col_id = c.id
                        WHERE d.form_id = {$form->id} 
                          AND d.ou_id = {$unit->id} 
                          AND d.period_id = {$this->period->id}
                          AND t.table_code = '$table_code' AND r.row_code = '$row_code' AND c.column_code = '$col_index'";
        $val_res = \DB::selectOne($val_q);
        return $val_res ? $val_res->value :  0;
    }

    public function getAggregatedValue(Unit $unit, Form $form, $table_code, $row_code, $col_index)
    {
        // Проверка, нужно ли сводить данные по текущему юниту.
        // Если вдруг сводить не нужно, в слюбом случае возвращаем значение для упрощения обработки сводного отчета
        $wherein = '';
        if (count($this->all_scope) > 0 ) {
            //dd($this->included);
            $glued = implode(',', $this->all_scope);
            $wherein = " AND d.ou_id IN ( $glued )";
        }
        //dd($wherein);

        if ($unit->aggregate) {
            $scope = ['top_node' => (string)$unit->id ];
            $scope['forms'] = [ $form->id ];
            $scope['worker_scope'] = 0;
            $scope['periods'] = [ $this->period->id ];
            $scope['states'] = $this->states;
            $scope['dtypes'] = [ $this->dtype ];
            $doc_tree = new DocumentTree($scope);
            $doc_array = $doc_tree->get_documents();

            $documents = array();
            foreach ($doc_array as $doc) {
                $documents[] = $doc->id;
            }
            $stringified_documents = implode(',', $documents);
            if (empty($stringified_documents)) {
                $v = 0;
            } else {
                $val_q = "SELECT SUM(v.value) AS value FROM statdata v
                                LEFT JOIN documents d ON v.doc_id = d.id
                                JOIN tables t ON v.table_id = t.id
                                LEFT JOIN rows r ON v.row_id = r.id
                                LEFT JOIN columns c ON v.col_id = c.id
                              WHERE d.id IN ({$stringified_documents}) 
                                {$wherein}
                                AND t.table_code = '$table_code'
                                AND r.row_code = '$row_code' AND c.column_code = '$col_index'
                              GROUP BY v.table_id, v.row_id, v.col_id";
                //dd($val_q);

                $val_res = \DB::selectOne($val_q);
                $v = $val_res ? $val_res->value :  0;
             }

        } else {
            $val_q = "SELECT v.value AS value FROM statdata v
                          LEFT JOIN documents d on v.doc_id = d.id
                          JOIN tables t on v.table_id = t.id
                          LEFT JOIN rows r on v.row_id = r.id
                          LEFT JOIN columns c on v.col_id = c.id
                        WHERE d.form_id = {$form->id} 
                          AND d.ou_id = {$unit->id} 
                           {$wherein}
                          AND d.period_id = {$this->period->id}
                          AND t.table_code = '$table_code' AND r.row_code = '$row_code' AND c.column_code = '$col_index'";
            $val_res = \DB::selectOne($val_q);
            $v = $val_res ? $val_res->value :  0;
        }

        if (is_null($v)) {
            $v = 0;
        }
        return $v;
    }

    public function getPopulation($unit, $population_group = 1)
    {
        // Если данные группируются по-территориально, то население берем из таблицы 100 соответствующей территории
        // Если id юнита равно нулю, берем все население Иркутской области из выбранной категории
        $population = 0;
        $document_type = 4; // Тип документа для формы населения - "показатели"
        switch (true) {
            case $unit->id === 0 :
            case $unit->node_type == 1 :
            case $unit->node_type == 2 :
                //dd($unit);
                //dd($this->population_form);
                $document = Document::OfTUPF( $document_type, $unit->id, $this->period->id, $this->population_form->id)->first();
                if (!$document) {
                    $previous_annual = Period::PreviousYear($this->period->year)->first();
                    $document = Document::OfTUPF( $document_type, $unit->id, $previous_annual->id, $this->population_form->id)->first();
                    if (!$document) {
                        throw new \Exception("Форма \"(100) Население\" не найдена");
                    }
                }
                $cell = Cell::OfDRC(
                    $document->id,
                    $this->population_rows->where('row_code', $population_group)->first()->id,
                    $this->population_column->where('column_index', 3)->first()->id
                )->first();
                if (!$cell) {
                    throw new \Exception("В форме \"(100) Население\" не найдена запрошенная группа населения ($population_group)");
                }
                $population = $cell->value;
                break;
            case $unit->node_type == 3 || $unit->node_type == 4 :
                $this->getServicedPopulation();
                break;
            default :
                $population = 0;
                break;
        }
        return $population;
    }

    public function getServicedPopulation()
    {
        return 0;
    }

}