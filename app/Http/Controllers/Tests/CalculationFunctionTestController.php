<?php

namespace App\Http\Controllers\Tests;

use App\Medinfo\UnitTree;
use App\Table;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class CalculationFunctionTestController extends Controller
{
    //
    public function mocount()
    {
        $rule = "счетмо()"; //
        $list = "~село, поликлиники, сб";
        $table = 2; // форма 47 таблица 0100
        $document = \App\Document::find(19251);
        $trimed = preg_replace('/,+\s+/u', ' ', $list);
        $lists = array_unique(array_filter(explode(' ', $trimed)));
        $units = \App\Medinfo\DSL\FunctionCompiler::compileUnitList($lists);
        asort($units);
        $prop = '[' . implode(',', $units) . ']';

        $lexer = new \App\Medinfo\DSL\ControlFunctionLexer($rule);
        $tockenstack = $lexer->getTokenStack();
        $parser = new \App\Medinfo\DSL\ControlFunctionParser($tockenstack);
        $parser->func();
        $translator = \App\Medinfo\DSL\Translator::invoke($parser, Table::find($table));
        //$translator->setUnits($units);
        $translator->prepareIteration();
        //dd($translator);
        //dd($translator->getProperties());
        $props = $translator->getProperties();
        $props['units'] = $units;
        //dd($props);
        //$evaluator = \App\Medinfo\DSL\Evaluator::invoke($translator->parser->root, $translator->getProperties(), $document);
        $evaluator = \App\Medinfo\DSL\Evaluator::invoke($translator->parser->root, $props, $document);
        $evaluator->makeConsolidation();
        //dd($evaluator->calculationLog);
        echo $evaluator->evaluate();
    }

    public function calculation()
    {
        $rule = "показатель(Ф30Т2350С2.2Г3/Ф30Т2350С2Г3*100)"; //
        $list = "*";
        //$table = 2; // форма 47 таблица 0100
        $table = 1031; // форма 110-пр1 таблица 1000
        //$document = \App\Document::find(19251); // ф.47 2017 год
        $document = \App\Document::find(23753);  // ф. 110-пр1 2018 год Госуд.
        //$document = \App\Document::find(23756);  // ф. 110-пр1 2018 год Иркутск
        $level_descent_units = \App\Unit::getDescendants($document->ou_id);

        $trimed = preg_replace('/,+\s+/u', ' ', $list);
        $lists = array_unique(array_filter(explode(' ', $trimed)));
        $units = \App\Medinfo\DSL\FunctionCompiler::compileUnitList($lists, $level_descent_units);
        asort($units);
        //dd($units);
        //$prop = '[' . implode(',', $units) . ']';

        $lexer = new \App\Medinfo\DSL\ControlFunctionLexer($rule);
        $tockenstack = $lexer->getTokenStack();
        $parser = new \App\Medinfo\DSL\ControlFunctionParser($tockenstack);
        $parser->func();
        //dd($parser);
        $translator = \App\Medinfo\DSL\Translator::invoke($parser, Table::find($table));
        //$translator->setUnits($units);
        $translator->prepareIteration();
        //dd($translator);
        //dd($translator->getProperties());
        $props = $translator->getProperties();
        $props['units'] = $units;
        //dd($props);
        //$evaluator = \App\Medinfo\DSL\Evaluator::invoke($translator->parser->root, $translator->getProperties(), $document);
        $evaluator = \App\Medinfo\DSL\Evaluator::invoke($translator->parser->root, $props, $document);
        $evaluator->makeConsolidation();
        //dd($evaluator->calculationLog);
        dd($evaluator->evaluate());
        //echo $evaluator->evaluate();
        foreach ($evaluator->calculationLog as &$el) {
            $unit = \App\Unit::find($el['unit_id']);
            $el['unit_name'] = $unit->unit_name;
            $el['unit_code'] = $unit->unit_code;
        }

        $log_initial = collect($evaluator->calculationLog);
        //$log_sorted = $log_initial->sortBy('unit_code');
        $log_c_sorted = $log_initial->sortBy('unit_code');
        //dd($log);
        $log_sorted = [];
        foreach ($log_c_sorted as $el ) {
            $log_sorted[] = $el;
        }
        //dd($log_sorted);

        //echo(json_encode($log->toArray()));
        $log = json_encode($log_sorted);
        echo $log;
    }

    public function valuecount()
    {
        $rule = "счетзнач(Ф14дсТ1010С3Г3+Ф14дсТ1010С6Г3)"; //
        $list = "областные_больницы";
        $table = 2; // форма 47 таблица 0100
        $document = \App\Document::find(23942); // Ф 47 за 2018 год
        $trimed = preg_replace('/,+\s+/u', ' ', $list);
        $lists = array_unique(array_filter(explode(' ', $trimed)));
        $units = \App\Medinfo\DSL\FunctionCompiler::compileUnitList($lists);
        asort($units);
        $prop = '[' . implode(',', $units) . ']';

        $lexer = new \App\Medinfo\DSL\ControlFunctionLexer($rule);
        $tockenstack = $lexer->getTokenStack();
        $parser = new \App\Medinfo\DSL\ControlFunctionParser($tockenstack);
        $parser->func();
        $translator = \App\Medinfo\DSL\Translator::invoke($parser, Table::find($table));
        //$translator->setUnits($units);
        $translator->prepareIteration();
        //dd($translator);
        //dd($translator->getProperties());
        $props = $translator->getProperties();
        $props['units'] = $units;
        //dd($props);
        //$evaluator = \App\Medinfo\DSL\Evaluator::invoke($translator->parser->root, $translator->getProperties(), $document);
        $evaluator = \App\Medinfo\DSL\Evaluator::invoke($translator->parser->root, $props, $document);
        $evaluator->makeConsolidation();
        //dd($evaluator->calculationLog);
        //echo $evaluator->evaluate();
        foreach ($evaluator->calculationLog as &$el) {
            $unit = \App\Unit::find($el['unit_id']);
            $el['unit_name'] = $unit->unit_name;
            $el['unit_code'] = $unit->unit_code;
        }

        $log_initial = collect($evaluator->calculationLog);
        //$log_sorted = $log_initial->sortBy('unit_code');
        $log_c_sorted = $log_initial->sortBy('unit_code');
        //dd($log);
        $log_sorted = [];
        foreach ($log_c_sorted as $el ) {
            $log_sorted[] = $el;
        }
        //dd($log_sorted);

        //echo(json_encode($log->toArray()));
        $log = json_encode($log_sorted);
        echo $log;
    }

    public function interval()
    {
        $rule = "интервал(Ф30Т3100С1Г3, 400, 2000)"; //
        $list = " u47_100_03";
        $table = 2; // форма 47 таблица 0100
        $document = \App\Document::find(19251);
        $trimed = preg_replace('/,+\s+/u', ' ', $list);
        $lists = array_unique(array_filter(explode(' ', $trimed)));
        $units = \App\Medinfo\DSL\FunctionCompiler::compileUnitList($lists);
        asort($units);
        $prop = '[' . implode(',', $units) . ']';

        $lexer = new \App\Medinfo\DSL\ControlFunctionLexer($rule);
        $tockenstack = $lexer->getTokenStack();
        $parser = new \App\Medinfo\DSL\ControlFunctionParser($tockenstack);
        $parser->func();
        $translator = \App\Medinfo\DSL\Translator::invoke($parser, Table::find($table));
        //$translator->setUnits($units);
        $translator->prepareIteration();
        //dd($translator);
        //dd($translator->getProperties());
        $props = $translator->getProperties();
        $props['units'] = $units;
        //$evaluator = \App\Medinfo\DSL\Evaluator::invoke($translator->parser->root, $translator->getProperties(), $document);
        $evaluator = \App\Medinfo\DSL\Evaluator::invoke($translator->parser->root, $props, $document);
        $evaluator->makeConsolidation();
        //dd($evaluator->calculationLog);
        //echo $evaluator->evaluate();
        foreach ($evaluator->calculationLog as &$el) {
            $unit = \App\Unit::find($el['unit_id']);
            $el['unit_name'] = $unit->unit_name;
            $el['unit_code'] = $unit->unit_code;
        }

        $log_initial = collect($evaluator->calculationLog);
        //$log_sorted = $log_initial->sortBy('unit_code');
        $log_c_sorted = $log_initial->sortBy('unit_code');
        //dd($log);
        $log_sorted = [];
        foreach ($log_c_sorted as $el ) {
            $log_sorted[] = $el;
        }
        //dd($log_sorted);

        //echo(json_encode($log->toArray()));
        $log = json_encode($log_sorted);
        echo $log;
    }

}
