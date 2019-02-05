<?php

namespace App\Http\Controllers\ImportExport;

use App\Column;
use App\ConsUseRule;
use App\Form;
use App\Table;
use App\Row;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class ImportConsolidationRulesAndListsController extends Controller
{
    //
    public function jsonRulesImport(Table $table)
    {
        $file = file_get_contents(storage_path('app/imports/rules/rules.json'));
        $data = json_decode($file);
        $errors = [];
        $i = 0;
        $form = Form::OfCode($data->form->form_code)->first();
        $source_table = Table::OfFormTableCode($form->id, $data->table->table_code)->first();
        if ($table->table_code !== $source_table->table_code) {
            throw new \Exception("В загружаемом файле ссылка на таблицу с другим кодом");
        }
        foreach ($data->rules as $rule) {
            $hashed = sprintf("%u", crc32(preg_replace('/\s+/u', '', $rule->script)));
            try {
                $compiled = \App\Medinfo\DSL\FunctionCompiler::compileRule($rule->script, $table);
                $stored = \App\ConsolidationCalcrule::firstOrNew(['hash' => $hashed]);
                $stored->script = $rule->script;
                $stored->ptree = $compiled['ptree'];
                $stored->properties = json_encode($compiled['properties']);
                $stored->save();
                $row = Row::OfTableRowCode($table->id, $rule->row_code)->first();
                $column = Column::OfTableColumnCode($table->id, $rule->column_code)->first();
                $apply_rule = ConsUseRule::firstOrNew(['row_id' => $row->id, 'col_id' => $column->id ]);
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
