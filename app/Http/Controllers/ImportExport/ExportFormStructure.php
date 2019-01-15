<?php

namespace App\Http\Controllers\ImportExport;

use File;
use Response;
use App\Form;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class ExportFormStructure extends Controller
{
    //
    public $structure = [];

    public function jsonFormExport(Form $form)
    {
        $this->structure = ['form' => []];
        $this->structure['form']['form_code'] = $form->form_code;
        $this->structure['form']['form_name'] = $form->form_name;
        $this->structure['form']['tables'] = [];
        foreach ($form->tables as $table ) {
            $t = &$this->structure['form']['tables'][$table->table_code];
            $t['code'] = $table->table_code;
            $t['name'] = $table->table_name;
            $t['rows'] = $this->getRows($table);
        }
        $data = json_encode($this->structure, JSON_UNESCAPED_UNICODE, JSON_PRETTY_PRINT);
        $fileName = time() . '_form_struture_' . $form->form_code . '.json';
        \Storage::put(
            'exports/structures/forms/' . $fileName,
            $data
        );
        return response()->download(storage_path('app/exports/structures/forms/').$fileName);
        //return Response::download(storage_path('app/exports/structures/forms/').$fileName);
    }

    public function getRows($table)
    {
        $rows = [ ];
        foreach ($table->rows->sortBy('row_index') as $row) {
            $rows[$row->row_code] = ['row_code' => $row->row_code, 'row_name' => $row->row_name];
        }
        return $rows;

    }
}
