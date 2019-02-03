<?php

namespace App\Http\Controllers\ImportExport;

use App\ConsUseRule;
use App\Table;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class ImportConsolidationRulesAndListsController extends Controller
{
    //
    public function jsonRulesImport(Table $table)
    {
        $content = file_get_contents(storage_path('app/imports/rules/1549095239_consolidation_rules_284.json'));
        $rules = json_decode($content);
        $errors = [];
        $i = 0;
        //dd($table->id);
        if ($table->id !== $rules->table->id) {
            throw new \Exception("В загружаемом файле ссылка на таблицу с другим кодом");
        }
        foreach ($rules->rules as $rule) {
            $hashed  =  sprintf("%u", crc32(preg_replace('/\s+/u', '', $rule->script)));
            try {
                $compiled = \App\Medinfo\DSL\FunctionCompiler::compileRule($rule->script, $table);
                $stored = \App\ConsolidationCalcrule::firstOrNew(['hash' => $hashed]);
                $stored->script = $rule->script;
                $stored->ptree = $compiled['ptree'];
                $stored->properties = json_encode($compiled['properties']);
                $stored->save();
                $apply_rule = ConsUseRule::firstOrNew(['row_id' => $rule->row, 'col_id' => $rule->column ]);
                $apply_rule->script = $stored->id;
                $apply_rule->save();
                $i++;
            } catch (\Exception $e) {
                $errors[] = $e->getMessage();
            }
        }

        return ['affected_cells' => $i, 'errors' => $errors ];
    }
}
