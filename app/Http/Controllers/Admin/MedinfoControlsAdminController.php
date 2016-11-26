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
// 2100
            ['text' => 'сравнение(С02, С03+С04+С05, =, группы(*), графы(*))', 'comment' => 'внутритабличный контроль строк', 'level' => 1 ,'table_id' => 179  ],
            ['text' => 'сравнение(С11, С01+С02+сумма(С06:С10), =, группы(*), графы(*))', 'comment' => 'внутритабличный контроль строк', 'level' => 1 ,'table_id' => 179  ],
            ['text' => 'сравнение(С06Г8, Т2130С4Г3+Т2130С5Г3+Т2130С6Г3, >=, группы(*), графы())', 'comment' => 'внутриформенный контроль строк', 'level' => 1 , 'table_id' => 179  ],
            ['text' => 'сравнение(С07Г8, Т2130С7Г3+Т2130С8Г3+Т2130С9Г3, >=, группы(*), графы())', 'comment' => 'внутриформенный контроль строк', 'level' => 1 , 'table_id' => 179  ],
            ['text' => 'сравнение(С01Г5, Ф11Т2000С02Г4, =, группы(*), графы())', 'comment' => 'межформенный контроль строк', 'level' => 1 , 'table_id' => 179  ],
            ['text' => 'сравнение(С02Г5, Ф11Т2000С04Г4, =, группы(*), графы())', 'comment' => 'межформенный контроль строк', 'level' => 1 , 'table_id' => 179  ],
            ['text' => 'сравнение(С03Г5, Ф11Т2000С05Г4, =, группы(*), графы())', 'comment' => 'межформенный контроль строк', 'level' => 1 , 'table_id' => 179  ],
            ['text' => 'сравнение(С04Г5, Ф11Т2000С06Г4, =, группы(*), графы())', 'comment' => 'межформенный контроль строк', 'level' => 1 , 'table_id' => 179  ],
            ['text' => 'сравнение(С05Г5, Ф11Т2000С07Г4, =, группы(*), графы())', 'comment' => 'межформенный контроль строк', 'level' => 1 , 'table_id' => 179  ],
            ['text' => 'сравнение(С06Г5, Ф11Т2000С08Г4, =, группы(*), графы())', 'comment' => 'межформенный контроль строк', 'level' => 1 , 'table_id' => 179  ],
            ['text' => 'сравнение(С07Г5, Ф11Т2000С14Г4, =, группы(*), графы())', 'comment' => 'межформенный контроль строк', 'level' => 1 , 'table_id' => 179  ],
            ['text' => 'сравнение(С08Г5, Ф11Т2000С15Г4, =, группы(*), графы())', 'comment' => 'межформенный контроль строк', 'level' => 1 , 'table_id' => 179  ],
            ['text' => 'сравнение(С09Г5, Ф11Т2000С16Г4, =, группы(*), графы())', 'comment' => 'межформенный контроль строк', 'level' => 1 , 'table_id' => 179  ],
            ['text' => 'сравнение(С10Г5, Ф11Т2000С17Г4, =, группы(*), графы())', 'comment' => 'межформенный контроль строк', 'level' => 1 , 'table_id' => 179  ],
            ['text' => 'сравнение(С11Г4, Ф36-плТ2100С6Г4+Ф36-плТ2140С6Г4, >=, группы(*), графы())', 'comment' => 'межформенный контроль строк', 'level' => 1 , 'table_id' => 179  ],
            ['text' => 'сравнение(С11Г5, Ф11Т2000С01Г4, =, группы(*), графы())', 'comment' => 'межформенный контроль строк', 'level' => 1 , 'table_id' => 179  ],
            ['text' => 'сравнение(С11Г6, Ф36-плТ2100С6Г6+Ф36-плТ2140С6Г7, >=, группы(*), графы())', 'comment' => 'межформенный контроль строк', 'level' => 1 , 'table_id' => 179  ],
            ['text' => 'сравнение(С11Г8, Ф36-плТ2100С6Г8+Ф36-плТ2140С6Г10, >=, группы(*), графы())', 'comment' => 'межформенный контроль строк', 'level' => 1 , 'table_id' => 179  ],
            ['text' => 'сравнение(Г4, Г5, >=, группы(*), строки(*))', 'comment' => 'контроль граф', 'level' => 1 , 'table_id' => 179  ],
            ['text' => 'сравнение(Г6, Г7, >=, группы(*), строки(*))', 'comment' => 'контроль граф', 'level' => 1 , 'table_id' => 179  ],
            ['text' => 'сравнение(Г8, Г10+Г11, >=, группы(*), строки(*))', 'comment' => 'контроль граф', 'level' => 1 , 'table_id' => 179  ],
            ['text' => 'сравнение(Г8, Г9, >=, группы(*), строки(*))', 'comment' => 'контроль граф', 'level' => 1 , 'table_id' => 179  ],

// 2101
            ['text' => 'сравнение(С1, С2+С4, >=, группы(*), графы(3))', 'comment' => 'внутритабличный контроль строк', 'level' => 1 ,'table_id' => 410  ],
            ['text' => 'сравнение(С2, С3, >=, группы(*), графы(3))', 'comment' => 'внутритабличный контроль строк', 'level' => 1 ,'table_id' => 410  ],
// 2102
            ['text' => 'сравнение(Г2, Г3+Г4+Г5, =, группы(*), строки(*))', 'comment' => 'контроль граф', 'level' => 1 , 'table_id' => 179  ],

// 2110
// 2130
// 2140
            ['text' => 'сравнение(С1Г3, Т2100С06Г7, <=, группы(*), графы())', 'comment' => 'внутриформенный контроль строк', 'level' => 1 , 'table_id' => 411 ],
            ['text' => 'сравнение(С2Г3, Т2100С07Г7, <=, группы(*), графы())', 'comment' => 'внутриформенный контроль строк', 'level' => 1 , 'table_id' => 411 ],
            ['text' => 'сравнение(С3Г3, Т2100С09Г7, <=, группы(*), графы())', 'comment' => 'внутриформенный контроль строк', 'level' => 1 , 'table_id' => 411 ],
            ['text' => 'сравнение(С4Г3, Т2100С10Г7, <=, группы(*), графы())', 'comment' => 'внутриформенный контроль строк', 'level' => 1 , 'table_id' => 411 ],

// 2150
            ['text' => 'сравнение(Г3, сумма(Г4:Г10), =, группы(*), строки(*))', 'comment' => 'контроль граф', 'level' => 1 , 'table_id' => 412  ],

// 2160
            ['text' => 'сравнение(С05, сумма(С01:С04), =, группы(*), графы(*))', 'comment' => 'внутритабличный контроль строк', 'level' => 1,'table_id'=> 413],
            ['text' => 'сравнение(С01Г3, Т2100С01Г6+Т2100С01Г8+Т2100С02Г6+Т2100С02Г8, <=, группы(*), графы())', 'comment' => 'внутриформенный контроль строк', 'level' => 1 , 'table_id' => 413  ],
            ['text' => 'сравнение(С02Г3, Т2100С06Г6+Т2100С06Г8, <=, группы(*), графы())', 'comment' => 'внутриформенный контроль строк', 'level' => 1 , 'table_id' => 413  ],
            ['text' => 'сравнение(С03Г3, Т2100С07Г6+Т2100С07Г8, <=, группы(*), графы())', 'comment' => 'внутриформенный контроль строк', 'level' => 1 , 'table_id' => 413  ],
            ['text' => 'сравнение(С04Г3, Т2100С08Г6+Т2100С08Г8+Т2100С09Г6+Т2100С09Г8+Т2100С10Г6+Т2100С10Г8, <=, группы(*), графы())', 'comment' => 'внутриформенный контроль строк', 'level' => 1 , 'table_id' => 413  ],
            ['text' => 'сравнение(С05Г3, Т2100С11Г6+Т2100С11Г8, <=, группы(*), графы())', 'comment' => 'внутриформенный контроль строк', 'level' => 1 , 'table_id' => 413  ],
            ['text' => 'сравнение(Г3, сумма(Г4:Г7), =, группы(*), строки(*))', 'comment' => 'контроль граф', 'level' => 1 , 'table_id' => 413  ],

// 2170
            ['text' => 'сравнение(С04, С01+С02+С03, =, группы(*), графы(*))', 'comment' => 'внутритабличный контроль строк', 'level' => 1 ,'table_id' => 414  ],
            ['text' => 'сравнение(С01Г3, Т2100С01Г4+Т2100С02Г4, <=, группы(*), графы())', 'comment' => 'внутриформенный контроль строк', 'level' => 1 , 'table_id' => 414  ],
            ['text' => 'сравнение(С02Г3, Т2100С06Г4, <=, группы(*), графы())', 'comment' => 'внутриформенный контроль строк', 'level' => 1 , 'table_id' =>414 ],
            ['text' => 'сравнение(С03Г3, Т2100С07Г4, <=, группы(*), графы())', 'comment' => 'внутриформенный контроль строк', 'level' => 1 , 'table_id' =>414 ],
            ['text' => 'сравнение(С04Г3, Т2100С11Г4, <, группы(*), графы())', 'comment' => 'внутриформенный контроль строк', 'level' => 1 , 'table_id' => 414 ],
            ['text' => 'сравнение(Г3, Г4+Г11, =, группы(*), строки(*))', 'comment' => 'контроль граф', 'level' => 1 , 'table_id' => 414  ],
            ['text' => 'сравнение(Г3, Г4+Г5, =, группы(*), строки(*))', 'comment' => 'контроль граф', 'level' => 1 , 'table_id' => 414  ],
            ['text' => 'сравнение(Г6, Г7+Г9+Г10, =, группы(*), строки(*))', 'comment' => 'контроль граф', 'level' => 1 , 'table_id' => 414  ],
            ['text' => 'сравнение(Г7, Г8, >=, группы(*), строки(*))', 'comment' => 'контроль граф', 'level' => 1 , 'table_id' => 414  ],

// 2200
            ['text' => 'сравнение(С01, С02, >, группы(*), графы(*))', 'comment' => 'внутритабличный контроль строк', 'level' => 1 ,'table_id' => 182  ],
            ['text' => 'зависимость(Г4, Г3, группы(*), строки(*))', 'comment' => 'контроль граф', 'level' => 1 , 'table_id' => 182  ],
            ['text' => 'кратность(диапазон(С01Г3:С04Г3), .25)', 'comment' => 'контроль граф', 'level' => 1 , 'table_id' => 182  ],


// 2210
            ['text' => 'кратность(диапазон(С01Г3:С06Г3), .25)', 'comment' => 'контроль граф', 'level' => 1 , 'table_id' => 415  ],


// 2300
            ['text' => 'сравнение(С02, С03+С04+С05, =, группы(*), графы(*))', 'comment' => 'внутритабличный контроль строк', 'level' => 1 ,'table_id' => 183  ],
            ['text' => 'сравнение(С08, С09+С10+С11+С12+С13, =, группы(*), графы(*))', 'comment' => 'внутритабличный контроль строк', 'level' => 1 ,'table_id' => 183  ],
            ['text' => 'сравнение(С18, С01+С02+С06+С07+С08+сумма(С14:С17), =, группы(*), графы(*))', 'comment' => 'внутритабличный контроль строк', 'level' => 1 ,'table_id' => 183  ],
            ['text' => 'сравнение(С19, С01+С02, <, группы(*), графы(*))', 'comment' => 'внутритабличный контроль строк', 'level' => 1 ,'table_id' => 183  ],
            ['text' => 'сравнение(С20, С08, <, группы(*), графы(*))', 'comment' => 'внутритабличный контроль строк', 'level' => 1 ,'table_id' => 183  ],
            ['text' => 'сравнение(С21, С07+С14, <, группы(*), графы(*))', 'comment' => 'внутритабличный контроль строк', 'level' => 1 ,'table_id' => 183  ],
            ['text' => 'сравнение(С23, С06+С08+С16, <=, группы(*), графы(*))', 'comment' => 'внутритабличный контроль строк', 'level' => 1 ,'table_id' => 183 ],
            ['text' => 'сравнение(С18Г4, Ф36Т2300С23Г4+Ф36Т2300С23Г5+Ф36Т2300С23Г6, >=, группы(*), графы())', 'comment' => 'межформенный контроль строк', 'level' => 1 , 'table_id' => 183  ],
            ['text' => 'сравнение(С18Г6, Ф36Т2300С23Г5, >=, группы(*), графы())', 'comment' => 'межформенный контроль строк', 'level' => 1 , 'table_id' => 183],
            ['text' => 'сравнение(С18Г7, Ф36Т2300С23Г6, >=, группы(*), графы())', 'comment' => 'межформенный контроль строк', 'level' => 1 , 'table_id' => 183],
            ['text' => 'сравнение(С18Г8, Ф36Т2300С23Г7, >=, группы(*), графы())', 'comment' => 'межформенный контроль строк', 'level' => 1 , 'table_id' => 183],
            ['text' => 'сравнение(С18Г9, Ф36Т2300С23Г8, >=, группы(*), графы())', 'comment' => 'межформенный контроль строк', 'level' => 1 , 'table_id' => 183],
            ['text' => 'сравнение(С18Г10, Ф36Т2300С23Г10, >=, группы(*), графы())', 'comment' => 'межформенный контроль строк', 'level' => 1 , 'table_id' => 183],
            ['text' => 'сравнение(С18Г12, Ф36Т2300С23Г11, >=, группы(*), графы())', 'comment' => 'межформенный контроль строк', 'level' => 1 ,'table_id' =>183],
            ['text' => 'сравнение(С18Г13, Ф36Т2300С23Г12+Ф36Т2300С23Г14+Ф36Т2300С23Г13, >=, группы(*), графы())', 'comment' => 'межформенный контроль строк', 'level' => 1 , 'table_id' => 183  ],
            ['text' => 'сравнение(Г4, Г6+Г7, >=, группы(*), строки(*))', 'comment' => 'контроль граф', 'level' => 1 , 'table_id' => 183  ],
            ['text' => 'сравнение(Г4, Г5, >=, группы(*), строки(*))', 'comment' => 'контроль граф', 'level' => 1 , 'table_id' => 183  ],
            ['text' => 'сравнение(Г4, Г8, >=, группы(*), строки(*))', 'comment' => 'контроль граф', 'level' => 1 , 'table_id' => 183  ],

// 2301
            ['text' => 'сравнение(С05, С01+С02+С04, =, группы(*), графы(*))', 'comment' => 'внутритабличный контроль строк', 'level' => 1 ,'table_id' => 416  ],
            ['text' => 'сравнение(Г3, Г4, >=, группы(*), строки(*))', 'comment' => 'контроль граф', 'level' => 1 , 'table_id' => 416  ],
            ['text' => 'сравнение(Г5, Г6, >=, группы(*), строки(*))', 'comment' => 'контроль граф', 'level' => 1 , 'table_id' => 416  ],
            ['text' => 'сравнение(Г7, Г8, >=, группы(*), строки(*))', 'comment' => 'контроль граф', 'level' => 1 , 'table_id' => 416  ],

// 2310
            ['text' => 'сравнение(С2, сумма(С3:С6), >=, группы(*), графы(3))', 'comment' => 'внутритабличный контроль строк', 'level' => 1 ,'table_id' => 184 ],
            ['text' => 'сравнение(С1Г3, Т2300С18Г4+Т2300С22Г4, <, группы(*), графы())', 'comment' => 'внутриформенный контроль строк', 'level' => 1 , 'table_id' => 184  ],
            ['text' => 'сравнение(С2Г3, Т2300С18Г4+Т2300С22Г4, <, группы(*), графы())', 'comment' => 'внутриформенный контроль строк', 'level' => 1 , 'table_id' => 184  ],

// 2320
            ['text' => 'сравнение(С05, сумма(С01:С04), =, группы(*), графы(*))', 'comment' => 'внутритабличный контроль строк', 'level' => 1 ,'table_id' =>418],
            ['text' => 'сравнение(Г3, Г4+Г7+Г8, =, группы(*), строки(*))', 'comment' => 'контроль граф', 'level' => 1 , 'table_id' => 418  ],
            ['text' => 'зависимость(Г4, Г5, группы(*), строки(*))', 'comment' => 'контроль граф', 'level' => 1 , 'table_id' => 418  ],
            ['text' => 'сравнение(Г4, Г6, >=, группы(*), строки(*))', 'comment' => 'контроль граф', 'level' => 1 , 'table_id' => 418  ],

// 2330
            ['text' => 'зависимость(С01, С02, группы(*), графы(*))', 'comment' => 'внутритабличный контроль строк', 'level' => 1 ,'table_id' => 419  ],
            ['text' => 'сравнение(С01Г3, Т2300С18Г10, <, группы(*), графы())', 'comment' => 'внутриформенный контроль строк', 'level' => 1 , 'table_id' => 419],
            ['text' => 'сравнение(Г3, сумма(Г4:Г9), >=, группы(*), строки(*))', 'comment' => 'контроль граф', 'level' => 1 , 'table_id' => 419  ],

// 2400
            ['text' => 'зависимость(Г4, Г6, группы(*), строки(*))', 'comment' => 'контроль граф', 'level' => 1 , 'table_id' => 187  ],
            ['text' => 'зависимость(Г5, Г7, группы(*), строки(*))', 'comment' => 'контроль граф', 'level' => 1 , 'table_id' => 187  ],
            ['text' => 'зависимость(Г6, Г7, группы(*), строки(*))', 'comment' => 'контроль граф', 'level' => 1 , 'table_id' => 187  ],

// 2500
            ['text' => 'зависимость(Г3, сумма(Г4:Г8), группы(*), строки(*))', 'comment' => 'контроль граф', 'level' => 1 , 'table_id' => 420 ],

// 2501
// ['text' => 'сравнение(С1Г3, +Т2500С01Г4+Т2500С01Г5+Т2500С01Г6+Т2500С02Г4+Т2500С02Г5+Т2500С02Г6, <=, группы(*), графы())', 'comment' => 'внутриформенный контроль строк', 'level' => 1 , 'table_id' => 483  ],


// 2600
            ['text' => 'сравнение(С1Г3, С2Г3+С4Г3+С5Г3, =, группы(*), графы())', 'comment' => 'внутритабличный контроль строк', 'level' => 1 ,'table_id' =>188],
            ['text' => 'сравнение(С2Г3, С3Г3, >=, группы(*), графы())', 'comment' => 'внутритабличный контроль строк', 'level' => 1 ,'table_id' => 188  ],

// 2700


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
