<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Form;
use App\Table;
use App\ControlledRow;
use App\ControllingRow;
use App\ControlledColumn;
use App\Medinfo\MIControlTranslater;
use App\Http\Controllers\Admin\CFunctionAdminController;
use App\CFunction;

class MedinfoControlsAdminController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $forms = Form::orderBy('form_index')->get(['id', 'form_code']);
        return view('jqxadmin.micontrols', compact('forms'));
    }

    public function fetchControlledRows(int $table, int $scope)
    {
        return ControlledRow::ofTable($table)->ofControlScope($scope)->with('table')->with('row')->get();
    }

    public function fetchControllingRows(int $table, int $relation)
    {
        return ControllingRow::ofTable($table)->ofRelation($relation)->with('table')->with('row')->get();
    }

    public function fetchColumns(int $firstcol, int $countcol)
    {
        return ControlledColumn::where('rec_id','>=' ,$firstcol)->where('rec_id', '<', $firstcol + $countcol)->get();
    }

    public function MIRulesTranslate(int $form)
    {

        $tables = Table::OfForm($form)->get();

        foreach ($tables as $table) {
            $rules = new MIControlTranslater($table->id);
            $all_rules = $rules->translateAll();
            //dd($all_rules);
            echo '<pre>';
            echo "// {$table->table_code}  \n";
            foreach ($all_rules['intable'] as $rule) {
                echo "['text' => '$rule', 'comment' => 'внутритабличный контроль строк', 'level' => 1 ,'table_id' => $table->id  ],\n";
            }
            foreach ($all_rules['inform'] as $rule) {
                echo "['text' => '$rule', 'comment' => 'внутриформенный контроль строк', 'level' => 1 , 'table_id' => $table->id  ],\n";
            }
            foreach ($all_rules['inreport'] as $rule) {
                echo "['text' => '$rule', 'comment' => 'межформенный контроль строк', 'level' => 1 , 'table_id' => $table->id  ],\n";
            }
            foreach ($all_rules['columns'] as $rule) {
                echo "['text' => '$rule', 'comment' => 'контроль граф', 'level' => 1 , 'table_id' => $table->id  ],\n";
            }
            foreach ($all_rules['inrow'] as $rule) {
                echo "['text' => '$rule', 'comment' => 'контроль внутри строки', 'level' => 1 , 'table_id' => $table->id  ],\n";
            }
            echo '</pre>';

        }


    }

    public function BatchRuleSave()
    {

        $rules = [
            // 2110
            ['text' => 'сравнение(ФТС1Г6, +ФТ2120С1Г3, >=, группы(*), графы())', 'comment' => 'внутриформенный контроль строк', 'level' => 1 , 'table_id' => 114  ],
            ['text' => 'сравнение(ФТСГ4, +ФТСГ3, <=, группы(*), строки(*))', 'comment' => 'контроль граф', 'level' => 1 , 'table_id' => 114  ],
            ['text' => 'сравнение(ФТСГ6, +ФТСГ7+ФТСГ8+ФТСГ9, >=, группы(*), строки(*))', 'comment' => 'контроль граф', 'level' => 1 , 'table_id' => 114  ],
// 2120
            ['text' => 'сравнение(ФТС1Г, +ФТС2Г, >, группы(*), графы(3))', 'comment' => 'внутритабличный контроль строк', 'level' => 1 ,'table_id' => 115  ],
            ['text' => 'сравнение(ФТС5Г, +ФТС6Г, >=, группы(*), графы(3))', 'comment' => 'внутритабличный контроль строк', 'level' => 1 ,'table_id' => 115  ],
            ['text' => 'сравнение(ФТС7Г, +ФТС8Г, >=, группы(*), графы(3))', 'comment' => 'внутритабличный контроль строк', 'level' => 1 ,'table_id' => 115  ],
            ['text' => 'сравнение(ФТС9Г, +ФТС10Г, >=, группы(*), графы(3))', 'comment' => 'внутритабличный контроль строк', 'level' => 1 ,'table_id' => 115  ],
            ['text' => 'сравнение(ФТС11Г, +ФТС15Г, >=, группы(*), графы(3))', 'comment' => 'внутритабличный контроль строк', 'level' => 1 ,'table_id' => 115  ],
            ['text' => 'сравнение(ФТС15Г, +ФТС17Г, >=, группы(*), графы(3))', 'comment' => 'внутритабличный контроль строк', 'level' => 1 ,'table_id' => 115  ],
            ['text' => 'сравнение(ФТС16Г, +ФТС11Г, <=, группы(*), графы(3))', 'comment' => 'внутритабличный контроль строк', 'level' => 1 ,'table_id' => 115  ],
            ['text' => 'сравнение(ФТС17Г, +ФТС16Г, <=, группы(*), графы(3))', 'comment' => 'внутритабличный контроль строк', 'level' => 1 ,'table_id' => 115  ],
            ['text' => 'сравнение(ФТС18Г, +ФТС19Г, >=, группы(*), графы(3))', 'comment' => 'внутритабличный контроль строк', 'level' => 1 ,'table_id' => 115  ],
            ['text' => 'сравнение(ФТС19Г, +ФТС21Г, >=, группы(*), графы(3))', 'comment' => 'внутритабличный контроль строк', 'level' => 1 ,'table_id' => 115  ],
            ['text' => 'сравнение(ФТС20Г, +ФТС18Г, <=, группы(*), графы(3))', 'comment' => 'внутритабличный контроль строк', 'level' => 1 ,'table_id' => 115  ],
            ['text' => 'сравнение(ФТС21Г, +ФТС20Г, <=, группы(*), графы(3))', 'comment' => 'внутритабличный контроль строк', 'level' => 1 ,'table_id' => 115  ],
            ['text' => 'сравнение(ФТС22Г, +ФТС18Г, =, группы(*), графы(3))', 'comment' => 'внутритабличный контроль строк', 'level' => 1 ,'table_id' => 115  ],
            ['text' => 'сравнение(ФТС23Г, +ФТС22Г, <=, группы(*), графы(3))', 'comment' => 'внутритабличный контроль строк', 'level' => 1 ,'table_id' => 115  ],
            ['text' => 'сравнение(ФТС2Г3, +ФТ2110С1Г6, <=, группы(*), графы())', 'comment' => 'внутриформенный контроль строк', 'level' => 1 , 'table_id' => 115  ],
            ['text' => 'сравнение(ФТС15Г3, +Ф30Т5116С1Г3, =, группы(*), графы())', 'comment' => 'межформенный контроль строк', 'level' => 1 , 'table_id' => 115  ],
// 2130
            ['text' => 'сравнение(ФТС1Г, +ФТС2Г+ФТС3Г+ФТС4Г+ФТС5Г+ФТС6Г+ФТС7Г+ФТС8Г+ФТС9Г+ФТС10Г+ФТС11Г+ФТС12Г+ФТС13Г+ФТС14Г+ФТС15Г, >=, группы(*), графы(4))', 'comment' => 'внутритабличный контроль строк', 'level' => 1 ,'table_id' => 116  ],
            ['text' => 'сравнение(ФТС1Г4, +ФТ2150С1Г3, >=, группы(*), графы())', 'comment' => 'внутриформенный контроль строк', 'level' => 1 , 'table_id' => 116  ],
// 2150
// 2210
            ['text' => 'сравнение(ФТС5Г, +ФТС1Г, <, группы(*), графы(3))', 'comment' => 'внутритабличный контроль строк', 'level' => 1 ,'table_id' => 117  ],
            ['text' => 'сравнение(ФТС6Г, +ФТС7Г+ФТС8Г+ФТС9Г, =, группы(*), графы(3))', 'comment' => 'внутритабличный контроль строк', 'level' => 1 ,'table_id' => 117  ],
            ['text' => 'сравнение(ФТС10Г, +ФТС1Г, <, группы(*), графы(3))', 'comment' => 'внутритабличный контроль строк', 'level' => 1 ,'table_id' => 117  ],
            ['text' => 'сравнение(ФТС11Г, +ФТС4Г, <=, группы(*), графы(3))', 'comment' => 'внутритабличный контроль строк', 'level' => 1 ,'table_id' => 117  ],
            ['text' => 'сравнение(ФТС12Г, +ФТС1Г, <, группы(*), графы(3))', 'comment' => 'внутритабличный контроль строк', 'level' => 1 ,'table_id' => 117  ],
            ['text' => 'сравнение(ФТС14Г, +ФТС1Г, <=, группы(*), графы(3))', 'comment' => 'внутритабличный контроль строк', 'level' => 1 ,'table_id' => 117  ],
            ['text' => 'сравнение(ФТС15Г, +ФТС14Г, <=, группы(*), графы(3))', 'comment' => 'внутритабличный контроль строк', 'level' => 1 ,'table_id' => 117  ],
            ['text' => 'сравнение(ФТС4Г3, +ФТ2248С1Г3, <=, группы(*), графы())', 'comment' => 'внутриформенный контроль строк', 'level' => 1 , 'table_id' => 117  ],
// 2211
            ['text' => 'сравнение(ФТС1Г, +ФТС2Г+ФТС3Г+ФТС4Г+ФТС5Г+ФТС6Г+ФТС7Г+ФТС8Г+ФТС9Г+ФТС10Г+ФТС12Г+ФТС13Г+ФТС17Г+ФТС18Г+ФТС19Г+ФТС20Г+ФТС22Г+ФТС23Г+ФТС24Г+ФТС25Г+ФТС26Г+ФТС27Г+ФТС28Г, >=, группы(*), графы(4))', 'comment' => 'внутритабличный контроль строк', 'level' => 1 ,'table_id' => 118  ],
            ['text' => 'сравнение(ФТС13Г, +ФТС14Г+ФТС15Г+ФТС16Г, >=, группы(*), графы(4))', 'comment' => 'внутритабличный контроль строк', 'level' => 1 ,'table_id' => 118  ],
            ['text' => 'сравнение(ФТС20Г, +ФТС21Г, >=, группы(*), графы(4))', 'comment' => 'внутритабличный контроль строк', 'level' => 1 ,'table_id' => 118  ],
            ['text' => 'сравнение(ФТС1Г4, +ФТ2215С1Г3, >=, группы(*), графы())', 'comment' => 'внутриформенный контроль строк', 'level' => 1 , 'table_id' => 118  ],
            ['text' => 'сравнение(ФТС2Г4, +ФТ2210С1Г3+ФТ2210С2Г3-ФТ2210С5Г3, <, группы(*), графы())', 'comment' => 'внутриформенный контроль строк', 'level' => 1 , 'table_id' => 118  ],
            ['text' => 'сравнение(ФТС6Г4, +ФТ2210С1Г3+ФТ2210С2Г3-ФТ2210С5Г3, <, группы(*), графы())', 'comment' => 'внутриформенный контроль строк', 'level' => 1 , 'table_id' => 118  ],
            ['text' => 'сравнение(ФТС9Г4, +ФТ2210С1Г3+ФТ2210С2Г3-ФТ2210С5Г3, <, группы(*), графы())', 'comment' => 'внутриформенный контроль строк', 'level' => 1 , 'table_id' => 118  ],
            ['text' => 'сравнение(ФТС10Г4, +ФТ2210С1Г3+ФТ2210С2Г3-ФТ2210С5Г3, <, группы(*), графы())', 'comment' => 'внутриформенный контроль строк', 'level' => 1 , 'table_id' => 118  ],
            ['text' => 'сравнение(ФТС12Г4, +ФТ2210С1Г3+ФТ2210С2Г3-ФТ2210С5Г3, <, группы(*), графы())', 'comment' => 'внутриформенный контроль строк', 'level' => 1 , 'table_id' => 118  ],
            ['text' => 'сравнение(ФТС13Г4, +ФТ2210С1Г3+ФТ2210С2Г3-ФТ2210С5Г3, <, группы(*), графы())', 'comment' => 'внутриформенный контроль строк', 'level' => 1 , 'table_id' => 118  ],
            ['text' => 'сравнение(ФТС18Г4, +ФТ2210С1Г3+ФТ2210С2Г3-ФТ2210С5Г3, <, группы(*), графы())', 'comment' => 'внутриформенный контроль строк', 'level' => 1 , 'table_id' => 118  ],
            ['text' => 'сравнение(ФТС19Г4, +ФТ2210С1Г3+ФТ2210С2Г3-ФТ2210С5Г3, <, группы(*), графы())', 'comment' => 'внутриформенный контроль строк', 'level' => 1 , 'table_id' => 118  ],
            ['text' => 'сравнение(ФТС20Г4, +ФТ2210С1Г3+ФТ2210С2Г3-ФТ2210С5Г3, <, группы(*), графы())', 'comment' => 'внутриформенный контроль строк', 'level' => 1 , 'table_id' => 118  ],
            ['text' => 'сравнение(ФТС22Г4, +ФТ2210С1Г3+ФТ2210С2Г3-ФТ2210С5Г3, <, группы(*), графы())', 'comment' => 'внутриформенный контроль строк', 'level' => 1 , 'table_id' => 118  ],
            ['text' => 'сравнение(ФТС23Г4, +ФТ2210С1Г3+ФТ2210С2Г3-ФТ2210С5Г3, <, группы(*), графы())', 'comment' => 'внутриформенный контроль строк', 'level' => 1 , 'table_id' => 118  ],
            ['text' => 'сравнение(ФТС24Г4, +ФТ2210С1Г3+ФТ2210С2Г3-ФТ2210С5Г3, <, группы(*), графы())', 'comment' => 'внутриформенный контроль строк', 'level' => 1 , 'table_id' => 118  ],
            ['text' => 'сравнение(ФТС25Г4, +ФТ2210С1Г3+ФТ2210С2Г3-ФТ2210С5Г3, <, группы(*), графы())', 'comment' => 'внутриформенный контроль строк', 'level' => 1 , 'table_id' => 118  ],
            ['text' => 'сравнение(ФТС26Г4, +ФТ2210С1Г3+ФТ2210С2Г3-ФТ2210С5Г3, <, группы(*), графы())', 'comment' => 'внутриформенный контроль строк', 'level' => 1 , 'table_id' => 118  ],
            ['text' => 'сравнение(ФТС27Г4, +ФТ2210С1Г3+ФТ2210С2Г3-ФТ2210С5Г3, <, группы(*), графы())', 'comment' => 'внутриформенный контроль строк', 'level' => 1 , 'table_id' => 118  ],
// 2215
// 2245
            ['text' => 'сравнение(ФТС01Г, +ФТС02Г, >=, группы(*), графы(3,4,5,6,7,8,9,10,11,12,13,14))', 'comment' => 'внутритабличный контроль строк', 'level' => 1 ,'table_id' => 119  ],
            ['text' => 'сравнение(ФТС02Г, +ФТС03Г, >=, группы(*), графы(3,4,5,6,7,8,9,10,11,12,13,14))', 'comment' => 'внутритабличный контроль строк', 'level' => 1 ,'table_id' => 119  ],
            ['text' => 'сравнение(ФТС03Г, +ФТС04Г, >=, группы(*), графы(3,4,5,6,7,8,9,10,11,12,13,14))', 'comment' => 'внутритабличный контроль строк', 'level' => 1 ,'table_id' => 119  ],
            ['text' => 'сравнение(ФТС05Г, +ФТС06Г, >=, группы(*), графы(3,4,5,6,7,8,9,10,11,12,13,14))', 'comment' => 'внутритабличный контроль строк', 'level' => 1 ,'table_id' => 119  ],
            ['text' => 'сравнение(ФТС01Г3, +ФТ2260С1Г4, >=, группы(*), графы())', 'comment' => 'внутриформенный контроль строк', 'level' => 1 , 'table_id' => 119  ],
            ['text' => 'сравнение(ФТС01Г13, +ФТ2260С1Г5, =, группы(*), графы())', 'comment' => 'внутриформенный контроль строк', 'level' => 1 , 'table_id' => 119  ],
            ['text' => 'сравнение(ФТС03Г3, +ФТ2250С1Г6+ФТ2260С1Г8, =, группы(*), графы())', 'comment' => 'внутриформенный контроль строк', 'level' => 1 , 'table_id' => 119  ],
            ['text' => 'сравнение(ФТСГ3, +ФТСГ4+ФТСГ5+ФТСГ6+ФТСГ7+ФТСГ8+ФТСГ9+ФТСГ10+ФТСГ11+ФТСГ12, =, группы(*), строки(*))', 'comment' => 'контроль граф', 'level' => 1 , 'table_id' => 119  ],
            ['text' => 'сравнение(ФТСГ13, +ФТСГ4+ФТСГ5+ФТСГ6+ФТСГ7+ФТСГ8, >=, группы(*), строки(*))', 'comment' => 'контроль граф', 'level' => 1 , 'table_id' => 119  ],
            ['text' => 'сравнение(ФТСГ14, +ФТСГ13, <=, группы(*), строки(*))', 'comment' => 'контроль граф', 'level' => 1 , 'table_id' => 119  ],
// 2246
            ['text' => 'сравнение(ФТС1Г3, +ФТ2245С01Г3, <=, группы(*), графы())', 'comment' => 'внутриформенный контроль строк', 'level' => 1 , 'table_id' => 120  ],
            ['text' => 'сравнение(ФТС2Г3, +ФТ2245С01Г3, <=, группы(*), графы())', 'comment' => 'внутриформенный контроль строк', 'level' => 1 , 'table_id' => 120  ],
            ['text' => 'сравнение(ФТС3Г3, +ФТ2245С01Г3, <=, группы(*), графы())', 'comment' => 'внутриформенный контроль строк', 'level' => 1 , 'table_id' => 120  ],
            ['text' => 'сравнение(ФТС4Г3, +ФТ2245С01Г3, <=, группы(*), графы())', 'comment' => 'внутриформенный контроль строк', 'level' => 1 , 'table_id' => 120  ],
// 2247
            ['text' => 'сравнение(ФТС1Г3, +Ф14Т2100С2Г3, =, группы(*), графы())', 'comment' => 'межформенный контроль строк', 'level' => 1 , 'table_id' => 121  ],
// 2248
            ['text' => 'сравнение(ФТС1Г, +ФТС2Г, >=, группы(*), графы(3))', 'comment' => 'внутритабличный контроль строк', 'level' => 1 ,'table_id' => 260  ],
            ['text' => 'сравнение(ФТС2Г, +ФТС3Г, >=, группы(*), графы(3))', 'comment' => 'внутритабличный контроль строк', 'level' => 1 ,'table_id' => 260  ],
            ['text' => 'сравнение(ФТС3Г, +ФТС4Г+ФТС5Г, >=, группы(*), графы(3))', 'comment' => 'внутритабличный контроль строк', 'level' => 1 ,'table_id' => 260  ],
            ['text' => 'сравнение(ФТС6Г, +ФТС1Г, <=, группы(*), графы(3))', 'comment' => 'внутритабличный контроль строк', 'level' => 1 ,'table_id' => 260  ],
            ['text' => 'сравнение(ФТС1Г3, +ФТ2245С01Г3+ФТ2245С01Г4+ФТ2245С01Г5+ФТ2245С05Г3+ФТ2245С05Г4+ФТ2245С05Г5, <, группы(*), графы())', 'comment' => 'внутриформенный контроль строк', 'level' => 1 , 'table_id' => 260  ],
// 2249
            ['text' => 'сравнение(ФТС1Г3, +ФТ2210С1Г3+ФТ2210С2Г3, <, группы(*), графы())', 'comment' => 'внутриформенный контроль строк', 'level' => 1 , 'table_id' => 541  ],
// 2250
            ['text' => 'сравнение(ФТС1Г, +ФТС2Г+ФТС3Г+ФТС4Г, =, группы(*), графы(4,5,6))', 'comment' => 'внутритабличный контроль строк', 'level' => 1 ,'table_id' => 122  ],
            ['text' => 'сравнение(ФТС2Г, +ФТС2.1Г+ФТС2.2Г+ФТС2.3Г+ФТС2.4Г+ФТС2.5Г+ФТС2.6Г+ФТС2.7Г+ФТС2.8Г+ФТС2.9Г, >=, группы(*), графы(4,5,6))', 'comment' => 'внутритабличный контроль строк', 'level' => 1 ,'table_id' => 122  ],
            ['text' => 'сравнение(ФТС2.1Г, +ФТС2.1.1Г, >=, группы(*), графы(4,5,6))', 'comment' => 'внутритабличный контроль строк', 'level' => 1 ,'table_id' => 122  ],
            ['text' => 'сравнение(ФТС2.8Г, +ФТС2.8.1Г, >=, группы(*), графы(4,5,6))', 'comment' => 'внутритабличный контроль строк', 'level' => 1 ,'table_id' => 122  ],
            ['text' => 'сравнение(ФТС5Г, +ФТС2Г+ФТС3Г+ФТС4Г, =, группы(*), графы(4,5,6))', 'comment' => 'внутритабличный контроль строк', 'level' => 1 ,'table_id' => 122  ],
            ['text' => 'сравнение(ФТС1Г4, +ФТ2245С01Г4+ФТ2245С01Г5, =, группы(*), графы())', 'comment' => 'внутриформенный контроль строк', 'level' => 1 , 'table_id' => 122  ],
            ['text' => 'сравнение(ФТС1Г5, +ФТ2245С02Г4+ФТ2245С02Г5, =, группы(*), графы())', 'comment' => 'внутриформенный контроль строк', 'level' => 1 , 'table_id' => 122  ],
            ['text' => 'сравнение(ФТС1Г6, +ФТ2245С03Г4+ФТ2245С03Г5, =, группы(*), графы())', 'comment' => 'внутриформенный контроль строк', 'level' => 1 , 'table_id' => 122  ],
            ['text' => 'сравнение(ФТСГ4, +ФТСГ5, >=, группы(*), строки(*))', 'comment' => 'контроль граф', 'level' => 1 , 'table_id' => 122  ],
            ['text' => 'сравнение(ФТСГ5, +ФТСГ6, >=, группы(*), строки(*))', 'comment' => 'контроль граф', 'level' => 1 , 'table_id' => 122  ],
// 2260
            ['text' => 'сравнение(ФТС1Г, +ФТС2Г+ФТС3Г+ФТС4Г+ФТС5Г+ФТС6Г, =, группы(*), графы(4,5,6,7,8,9))', 'comment' => 'внутритабличный контроль строк', 'level' => 1 ,'table_id' => 123  ],
            ['text' => 'сравнение(ФТС4Г, +ФТС4.1Г+ФТС4.2Г+ФТС4.3Г+ФТС4.4Г+ФТС4.5Г+ФТС4.6Г+ФТС4.7Г+ФТС4.8Г+ФТС4.9Г+ФТС4.10Г, >=, группы(*), графы(4,5,6,7,8,9))', 'comment' => 'внутритабличный контроль строк', 'level' => 1 ,'table_id' => 123  ],
            ['text' => 'сравнение(ФТС4.2Г, +ФТС4.2.1Г, >=, группы(*), графы(4,5,6,7,8,9))', 'comment' => 'внутритабличный контроль строк', 'level' => 1 ,'table_id' => 123  ],
            ['text' => 'сравнение(ФТС4.4Г, +ФТС4.4.1Г+ФТС4.4.2Г+ФТС4.4.3Г+ФТС4.4.4Г, >=, группы(*), графы(4,5,6,7,8,9))', 'comment' => 'внутритабличный контроль строк', 'level' => 1 ,'table_id' => 123  ],
            ['text' => 'сравнение(ФТС4.5Г, +ФТС4.5.1Г, >=, группы(*), графы(4,5,6,7,8,9))', 'comment' => 'внутритабличный контроль строк', 'level' => 1 ,'table_id' => 123  ],
            ['text' => 'сравнение(ФТС7Г, +ФТС2Г+ФТС3Г+ФТС4Г+ФТС5Г+ФТС6Г, =, группы(*), графы(4,5,6,7,8,9))', 'comment' => 'внутритабличный контроль строк', 'level' => 1 ,'table_id' => 123  ],
            ['text' => 'сравнение(ФТС1Г4, +ФТ2245С01Г3, <=, группы(*), графы())', 'comment' => 'внутриформенный контроль строк', 'level' => 1 , 'table_id' => 123  ],
            ['text' => 'сравнение(ФТС1Г6, +ФТ2245С02Г6+ФТ2245С02Г7+ФТ2245С02Г8+ФТ2245С02Г9+ФТ2245С02Г10+ФТ2245С02Г11+ФТ2245С02Г12, =, группы(*), графы())', 'comment' => 'внутриформенный контроль строк', 'level' => 1 , 'table_id' => 123  ],
            ['text' => 'сравнение(ФТС1Г7, +ФТ2245С02Г6+ФТ2245С02Г7+ФТ2245С02Г8, =, группы(*), графы())', 'comment' => 'внутриформенный контроль строк', 'level' => 1 , 'table_id' => 123  ],
            ['text' => 'сравнение(ФТС1Г8, +ФТ2245С03Г6+ФТ2245С03Г7+ФТ2245С03Г8+ФТ2245С03Г9+ФТ2245С03Г10+ФТ2245С03Г11+ФТ2245С03Г12, =, группы(*), графы())', 'comment' => 'внутриформенный контроль строк', 'level' => 1 , 'table_id' => 123  ],
            ['text' => 'сравнение(ФТС1Г9, +ФТ2245С05Г6+ФТ2245С05Г7+ФТ2245С05Г8+ФТ2245С05Г9+ФТ2245С05Г10+ФТ2245С05Г11+ФТ2245С05Г12, =, группы(*), графы())', 'comment' => 'внутриформенный контроль строк', 'level' => 1 , 'table_id' => 123  ],
            ['text' => 'сравнение(ФТСГ4, +ФТСГ5, >=, группы(*), строки(*))', 'comment' => 'контроль граф', 'level' => 1 , 'table_id' => 123  ],
            ['text' => 'сравнение(ФТСГ5, +ФТСГ7, >=, группы(*), строки(*))', 'comment' => 'контроль граф', 'level' => 1 , 'table_id' => 123  ],
            ['text' => 'сравнение(ФТСГ6, +ФТСГ4, <=, группы(*), строки(*))', 'comment' => 'контроль граф', 'level' => 1 , 'table_id' => 123  ],
            ['text' => 'сравнение(ФТСГ7, +ФТСГ6, <=, группы(*), строки(*))', 'comment' => 'контроль граф', 'level' => 1 , 'table_id' => 123  ],
            ['text' => 'сравнение(ФТСГ8, +ФТСГ6, <=, группы(*), строки(*))', 'comment' => 'контроль граф', 'level' => 1 , 'table_id' => 123  ],
        ];
        $table = Table::find($rules[0]['table_id']);
        $dcheck = new CFunctionAdminController();
        echo '<pre>';
        foreach ($rules as $rule) {
            $cache = $dcheck->compile($rule['text'], $table);
            if (!$cache) {
                echo "<span style='color: red'>Правило: " . $rule['text'] . " не сохранено" . $dcheck->compile_error . "</span>\n";
            } else {
                $rule['function'] = $dcheck->functionIndex;
                $this->saveScript($rule, $cache);
                echo "<span style='color: green'>Правило: " . $rule['text'] . " сохранено</span>\n";
            }
        }
        echo '</pre>';
    }

    public function saveScript($rule, $cache)
    {
        $newfunction = new CFunction();
        $newfunction->table_id = $rule['table_id'];
        $newfunction->level = $rule['level'];
        $newfunction->function = $rule['function'];
        $newfunction->script = $rule['text'];
        $newfunction->comment = $rule['comment'];
        $newfunction->blocked = 0;
        $newfunction->compiled_cashe = $cache;
        return $newfunction->save();
    }

}
