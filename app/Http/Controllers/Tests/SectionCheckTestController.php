<?php

namespace App\Http\Controllers\Tests;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Medinfo\DSL\ControlFunctionLexer;
use App\Medinfo\DSL\ControlFunctionParser;
use App\Medinfo\DSL\ControlPtreeTranslator;
use App\Medinfo\DSL\Translator;
use App\Medinfo\DSL\Evaluator;
use App\Document;
use App\Table;

class SectionCheckTestController extends Controller
{
    // Тестирование функции контроля разрезов форм
    public function SectionCheckTest()
    {
        //$table = Table::find(111);     // Ф12 Т1000
        //$table = Table::find(384);     // Ф19 Т1000
        //$table = Table::find(958);     // Ф54 Т2310
        $table = Table::find(994);     // Ф301 Т1001

        //$document = Document::find(20464); // 301 ф Гос учр. за 2018 год
        $document = Document::find(23490); // 301 ф Обл. б-ца №2 за 2018 год
        //$document = Document::find(20221); // 12 ф Бодайбинская РБ 2018 год - нулевой разрез
        //$i = "разрез(12, 1201, >)";
        //$i = "разрез(301, 30, <=)";
        $i = "разрез(301, 30, =, группы(село))";
        //$i = "разрез(30, 301, >=)";

        $lexer = new ControlFunctionLexer($i);
        $tockenstack = $lexer->getTokenStack();
        //dd($tockenstack);
        //dd($lexer->normalizeInput());
        //dd($lexer);

        $parser = new ControlFunctionParser($tockenstack);
        $parser->func();
        //dd($parser);
        //dd($parser->root);
        //dd($parser->function_index);
        //dd($parser->celladressStack);
        //dd($parser->cellrangeStack);
        //dd($parser->argStack);

        $translator = Translator::invoke($parser, $table);
        //dd($translator);
        $translator->prepareIteration();
        //dd($translator->getProperties());
        //dd($translator->parser->root);
        $evaluator = Evaluator::invoke($translator->parser->root, $translator->getProperties(), $document);
        return $evaluator->makeControl();
    }

    public function setLimitByFormSection()
    {
        $table = Table::find(111);     // Ф12 Т1000
        $section = 7;  // разрез 00 формы 12
        //$section = 43;  // разрез 01 формы 12
        $document = Document::find(20669); // 1201 ф Бодайбинская РБ 2018 год
        //$document = Document::find(20221); // 12 ф Бодайбинская РБ 2018 год - нулевой разрез
        $i = "сравнение(С6.1Г9, Ф11Т2000С01Г6, =)";
        $lexer = new ControlFunctionLexer($i);
        $tockenstack = $lexer->getTokenStack();
        //dd($tockenstack);
        //dd($lexer->normalizeInput());
        //dd($lexer);

        $parser = new ControlFunctionParser($tockenstack);
        $parser->func();
        //dd($parser);
        //dd($parser->root);
        //dd($parser->function_index);
        //dd($parser->celladressStack);
        //dd($parser->cellrangeStack);
        //dd($parser->argStack);

        $translator = Translator::invoke($parser, $table);
        // устанавливаем ограничение по разрезу формф
        $translator->setSection($section);
        //dd($translator);
        $translator->prepareIteration();
        //dd($translator->getProperties());
        //dd($translator->parser->root);

        $evaluator = Evaluator::invoke($translator->parser->root, $translator->getProperties(), $document);
        //$evaluator->evaluate();
        $evaluator->makeControl();
        dump($evaluator);
        //return $evaluator->evaluate();
        dd($evaluator->makeControl());
    }
}
