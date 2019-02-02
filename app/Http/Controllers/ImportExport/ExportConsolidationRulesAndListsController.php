<?php

namespace App\Http\Controllers\ImportExport;

use Illuminate\Http\Request;

use App\Table;
use App\ConsUseRule;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class ExportConsolidationRulesAndListsController extends Controller
{
    //
    public function jsonRulesExport(Table $table)
    {
        $result = ['table' => ['id' => $table->id], 'rules' => [] ];
        $rows = $table->rows->sortBy('row_index');
        $cols = $table->columns->sortBy('column_index');

        foreach ($rows as $row) {
            foreach($cols as $col) {
                if ($col->content_type == \App\Column::DATA) {
                    $rule_using = ConsUseRule::OfRC($row->id, $col->id)->first();
                    if ($rule_using) {
                        $rule['row'] = $row->id;
                        $rule['row_code'] = $row->row_code;
                        $rule['column'] = $col->id;
                        $rule['column_code'] = $col->column_code;
                        $rule['script'] = $rule_using->rulescript->script;
                        $result['rules'][] = $rule;
                    }
                }
            }
        }
        $data = json_encode($result, JSON_UNESCAPED_UNICODE, JSON_PRETTY_PRINT);
        $fileName = time() . '_consolidation_rules_' . $table->id . '.json';
        \Storage::put(
            'exports/structures/forms/' . $fileName,
            $data
        );
        return response()->download(storage_path('app/exports/structures/forms/').$fileName);
    }
}
