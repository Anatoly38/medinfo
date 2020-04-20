<?php

namespace App\Http\Controllers\Admin;

use App\DicCfunctionType;
use Illuminate\Http\Request;

//use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Album;
use App\Form;
use App\CFunction;
use App\ControlCashe;
use App\DicErrorLevel;
use App\Medinfo\Lexer\ControlFunctionLexer;
use App\Medinfo\Lexer\ControlFunctionParser;
use App\Medinfo\DSL\ParseTree;
use App\Medinfo\Lexer\FunctionDispatcher;
use App\Table;

class CFunctionAdminController extends Controller
{
    //
    public $compile_error;
    public $functionIndex;

    public function __construct()
    {
        $this->middleware('admins');
    }

    public function index()
    {
        //$forms = Form::orderBy('form_code')->get(['id', 'form_code', 'form_name']);
        $error_levels = DicErrorLevel::all(['code', 'name']);
        return view('jqxadmin.cfunctions', compact( 'error_levels'));
    }

    public function cfunctionsAll()
    {
        $error_levels = DicErrorLevel::all(['code', 'name']);
        return view('jqxadmin.cfunctions_all', compact('error_levels'));
    }

    public function fetchCcfunctionsAll()
    {
        set_time_limit(180);
        return CFunction::orderBy('created_at', 'desc')->orderBy('updated_at', 'desc')->with('table.form')->with('level')->with('type')->get();
    }

    public function fetchControlFunctions(int $table)
    {
        return CFunction::OfTable($table)->orderBy('created_at', 'desc')
            ->with('table')->with('form')
            ->with('level')->with('type')
            ->orderBy('updated_at', 'desc')->get();
    }

    public function fetchCFofForm(int $form)
    {
        $cf =[];
        $default_album = Album::Default()->first()->id;
        $tables = Table::OfForm($form)->whereDoesntHave('excluded', function ($query) use($default_album) {
            $query->where('album_id', $default_album);
        })->get();
        //dd($tables);
        foreach ($tables as $table) {
            //dd(CFunction::OfTable($table->id)->with('table')->get()->toArray());
            $cf = array_merge($cf, CFunction::OfTable($table->id)->orderBy('updated_at')->with('table')->get()->toArray());
        }
        return $cf;
    }

    public function store(Table $table, Request $request)
    {
        $this->validate($request, $this->validateRules());
        $newfunction = new CFunction();
        $request->scope === '2' ? $newfunction->form_id = $request->form : $newfunction->form_id = null;
        $cache = $this->compile($request->script, $table, $newfunction->form_id);
        if (!$cache) {
            return ['error' => 422, 'message' => $this->compile_error];
        }
        $newfunction->table_id = $table->id;
        $newfunction->level = $request->level;
        $newfunction->script = $request->script;
        $newfunction->comment = $request->comment;
        // новая функция по умолчанию деблокирована
        $newfunction->blocked = false;
        //$newfunction->blocked = $request->blocked;
        $newfunction->type = $cache['properties']['type'];
        $newfunction->function =  $cache['properties']['function_id'];
        $newfunction->ptree = $cache['ptree'];
        $newfunction->properties = json_encode($cache['properties']);
        //$newfunction->save();
        try {
            $newfunction->save();
            $deleted_protocols =  ControlCashe::where('table_id', $table->id)->delete();
            return [ 'message' => 'Новая запись создана. Id:' . $newfunction->id . ' Удалено кэшированных протоколов контроля: ' . $deleted_protocols,
                'id' => $newfunction->id ];
        } catch (\Illuminate\Database\QueryException $e) {
            $errorCode = $e->errorInfo[0];
            switch ($errorCode) {
                case '23505':
                    $message = 'Запись не сохранена. Дублирующиеся значения.';
                    break;
                default:
                    $message = 'Новая запись не создана. Код ошибки ' . $errorCode . '.';
                    break;
            }
            return ['error' => 422, 'message' => $message];
        }
    }

    public function update(CFunction $cfunction, Request $request)
    {
        $this->validate($request, $this->validateRules());
        $table = Table::find($cfunction->table_id);
        if ($request->scope === '2') {
            $cfunction->form_id = $request->form;
        } elseif ($request->scope === '1') {
            $cfunction->form_id = null;
        }
        $cfunction->level = (int)$request->level;
        $cache = $this->compile($request->script, $table, $cfunction->form_id);
        if (!$cache) {
            return ['error' => 422, 'message' => $this->compile_error];
        }
        $cfunction->script = $request->script;
        $cfunction->comment = $request->comment;
        $cfunction->blocked = $request->blocked === '0' ? false : true;
        $cfunction->type = $cache['properties']['type'];
        $cfunction->function = $cache['properties']['function_id'];
        $cfunction->ptree = $cache['ptree'];
        $cfunction->properties = json_encode($cache['properties']);
        //$cfunction->save();
        try {
            $cfunction->save();
            $deleted_protocols =  ControlCashe::where('table_id', $table->id)->delete();
            $message = 'Запись id ' . $cfunction->id . ' сохранена.' . 'Удалено кэшированных протоколов контроля: ' . $deleted_protocols;
            $script = $cfunction->script;
            $comment = $cfunction->comment;
            $levelcode = $cfunction->level;
            $levelname = DicErrorLevel::find((string)$cfunction->level)->name;
            $typename = DicCfunctionType::find((string)$cfunction->type)->name;
            $function = $cfunction->function;
            $blocked = $cfunction->blocked;
            $form = $cfunction->form_id;
            $created_at = $cfunction->created_at->toDateTimeString();
            $updated_at = $cfunction->updated_at->toDateTimeString();
            return compact('message', 'script', 'comment', 'blocked', 'typename', 'function', 'levelname', 'levelcode', 'form',
                'created_at', 'updated_at' );
        } catch (\Illuminate\Database\QueryException $e) {
            $errorCode = $e->errorInfo[0];
            switch ($errorCode) {
                case '23505':
                    $message = 'Запись не сохранена. Дублирующиеся значения.';
                    break;
                default:
                    $message = 'Запись не сохранена. Код ошибки ' . $errorCode . '.';
                    break;
            }
            return ['error' => 422, 'message' => $message];
        }
    }

    public function recompileForm($scopeForm)
    {
        $protocol = [];
        $form = Form::find($scopeForm);
        $tables = Table::OfForm($scopeForm)->orderBy('table_index')->get();
        //dd($tables);
        foreach ($tables as $table) {
            //dd($table);
            $protocol[$table->table_code] = $this->recompile($table);
            //dd($protocol);
        }
        //return $protocol;
        return view('jqxadmin.recompileformprotocol', compact('form','protocol'));
    }

    public function recompileAll()
    {
        $protocol = [];
        $forms = Form::all();
        foreach ($forms as $form) {
            $tables = Table::OfForm($form->id)->orderBy('table_index')->get();
            foreach ($tables as $table) {
                $protocol[$form->form_code][$table->table_code] = $this->recompile($table);
            }
        }
        return $protocol;
        //return view('jqxadmin.recompileformprotocol', compact('form','protocol'));
    }

    public function recompileTable($scopeTable)
    {
        //if ($scopeTable === 0) {
          //  $functions = CFunction::all();
        //} else {
            //$functions = CFunction::ofTable($scopeTable)->get();
        //}
        $table = Table::find($scopeTable);
        $form = Form::find($table->form_id);
        $protocol = $this->recompile($table);
        return view('jqxadmin.recompileprotocol', compact('form','table', 'protocol'));
    }

    public function recompile($table)
    {
        $functions = CFunction::ofTable($table->id)->get();
        $protocol = [];
        $i = 1;
        foreach ($functions as $function) {
            $f = [];
            $f['i'] = $i;
            $f['script'] = $function->script;
            $cache = $this->compile($function->script, $table, $function->form_id);
            if ($cache) {
                //echo $i . ' Компиляция функции: ' . $function->script . '<br/>';
                $function->type = $cache['properties']['type'];
                $function->function = $cache['properties']['function_id'];
                $function->ptree = $cache['ptree'];
                $function->properties = json_encode($cache['properties']);
                $function->save();
                $f['result'] = true;
                $f['comment'] = 'Успешно';
            } else {
                $f['result'] = false;
                $f['comment'] = $this->compile_error;
            }
            $protocol[] = $f;
            //flush();
            //usleep(50000);
            $i++;
        }
        return $protocol;
    }

    public function compile($script, Table $table, $section = null)
    {
        //$ns = '\\App\\Medinfo\\DSL\\';
        $properties = [];

        try {
            $lexer = new \App\Medinfo\DSL\ControlFunctionLexer($script);
            $tockenstack = $lexer->getTokenStack();
            $parser = new \App\Medinfo\DSL\ControlFunctionParser($tockenstack);
            $parser->func();
            //$translator = new \App\Medinfo\DSL\ControlPtreeTranslator($parser, $table);
            $translator = \App\Medinfo\DSL\Translator::invoke($parser, $table);
            if ($section) {
                $translator->setSection($section);
            }
            $translator->prepareIteration();
            $compiled_cache['ptree'] = base64_encode(serialize($translator->parser->root));
            $compiled_cache['properties'] = $translator->getProperties();
            return $compiled_cache;
        } catch (\Exception $e) {
            $this->compile_error = "Ошибка при компилляции функции: " . $e->getMessage();
            return false;
        }
    }

    public function delete(CFunction $cfunction)
    {
        $cfunction->delete();
        $deleted_protocols =  ControlCashe::where('table_id', $cfunction->id)->delete();
        return ['message' => 'Удалена функция Id' . $cfunction->id . 'Удалено кэшированных протоколов контроля: ' . $deleted_protocols];
    }

    protected function validateRules()
    {
        return [
            'form'   => 'integer',
            'level'     => 'required|integer',
            'script'    => 'required|max:512',
            'comment'   => 'max:256',
            'blocked'   => 'required|in:1,0',
        ];
    }

    public function excelExport(Form $form)
    {
        $tables = Table::OfForm($form->id)->orderBy('table_index')->get();
        $excel = \Excel::create('Функции контроля по форме ' . $form->form_code);
        foreach ($tables as $table) {
            $functions =  CFunction::ofTable($table->id)->get();
            $excel->sheet($table->table_code , function($sheet) use ($table, $functions) {
                $sheet->loadView('reports.cfunctions_excel', compact('table', 'functions'));
                $sheet->getStyle(\App\Medinfo\ExcelExport::getCellByRC(3, 1) . ':' . \App\Medinfo\ExcelExport::getCellByRC(count($functions)+3, 5))->getAlignment()->setWrapText(true);
                $sheet->getStyle(\App\Medinfo\ExcelExport::getCellByRC(3, 1) . ':' . \App\Medinfo\ExcelExport::getCellByRC(count($functions)+3, 5))->getBorders()
                    ->getAllBorders()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);
            });
        }
        $excel->setActiveSheetIndex(0);
        $excel->export('xlsx');
    }

}
