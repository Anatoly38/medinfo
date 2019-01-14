<?php

namespace App\Http\Controllers\ImportExport;

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
        }


        dd($this->structure);
    }
}
