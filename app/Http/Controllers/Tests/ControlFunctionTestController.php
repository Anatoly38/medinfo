<?php

namespace App\Http\Controllers\Tests;

use App\Medinfo\DSL\ControlFunctionLexer;
use App\Medinfo\DSL\ControlFunctionParser;
use App\Medinfo\DSL\ControlPtreeTranslator;
use App\Medinfo\DSL\Translator;
use App\Medinfo\DSL\Evaluator;
use App\Document;
use App\Table;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class ControlFunctionTestController extends Controller
{

    // тест контроля на сравнение
    public function compare()
    {
        $table = Table::find(10);     // Ф30 Т1100
        $document = Document::find(4519); // 30 ф Салтыковский детский дом 3 кв. МСК
        $function = "сравнение(Фрп-123Т100С1Г11, Ф102-оксТ100С1Г1, =)";

        $lexer = new ControlFunctionLexer($function);
        $tockenstack = $lexer->getTokenStack();
        dd($tockenstack);
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
        //$prop = $translator->getProperties();
        //dd($prop['iterations'][0]['С1Г3|0']);
        $evaluator = Evaluator::invoke($translator->parser->root, $translator->getProperties(), $document);
        //$evaluator = new ControlFunctionEvaluator($translator->parser->root, $translator->getProperties(), $document);
        //$evaluator = new ControlFunctionEvaluator($pTree, $props, $document);
        //$evaluator->prepareCellValues();
        //$evaluator->prepareCAstack();
        //dd($evaluator->arguments);
        //dd($evaluator->pTree);
        //dd($evaluator->caStack);
        //dd($evaluator->iterations);
        //return $evaluator->evaluate();

        //dd($evaluator->makeControl());
        //$evaluator->makeControl();
        //dd($evaluator);
        //return ($evaluator->iterations);
        //return ($evaluator->properties);
        return $evaluator->makeControl();
    }

    // тест контроля кратности
    public function fold()
    {
        $table = Table::find(10);     // Ф30 Т1100
        $document = Document::find(28181); // 30 ф Сводная Качугский район

        $function = "кратность(диапазон(С1Г3:С224Г8, С1Г3.1:С224Г3.1, С1Г3.2:С224Г3.2), 0.25 )";

        $lexer = new ControlFunctionLexer($function);
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
        //$prop = $translator->getProperties();
        //dd($prop['iterations'][0]['С1Г3|0']);
        $evaluator = Evaluator::invoke($translator->parser->root, $translator->getProperties(), $document);
        //$evaluator = new ControlFunctionEvaluator($translator->parser->root, $translator->getProperties(), $document);
        //$evaluator = new ControlFunctionEvaluator($pTree, $props, $document);
        //$evaluator->prepareCellValues();
        //$evaluator->prepareCAstack();
        //dd($evaluator->arguments);
        //dd($evaluator->pTree);
        //dd($evaluator->caStack);
        //dd($evaluator->iterations);
        //return $evaluator->evaluate();

        //dd($evaluator->makeControl());
        //$evaluator->makeControl();
        //dd($evaluator);
        //return ($evaluator->iterations);
        //return ($evaluator->properties);
        return $evaluator->makeControl();
    }

    // тестирование функций сравнения между формами в разных периодах (месячные отчеты)
    public function compare_btw_forms_periods()
    {
        $table = Table::find(1264);     // Ф2-нп Т2001
        $document = Document::find(73329); // 2-нп ф 8-й месяц Бронницкая городская больница
        $function = "сравнение(С1Г3, Ф2-нпТ2001С1Г3П7, >=)";

        $lexer = new ControlFunctionLexer($function);
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
        //$evaluator = new ControlFunctionEvaluator($translator->parser->root, $translator->getProperties(), $document);
        //$evaluator = new ControlFunctionEvaluator($pTree, $props, $document);
        //$evaluator->prepareCellValues();
        //$evaluator->prepareCAstack();
        //dd($evaluator->arguments);
        //dd($evaluator->pTree);
        //dd($evaluator->caStack);
        //dd($evaluator->iterations);
        //return $evaluator->evaluate();

        //dd($evaluator->makeControl());
        //$evaluator->makeControl();
        //dd($evaluator);
        //return ($evaluator->iterations);
        //return ($evaluator->properties);
        return $evaluator->makeControl();
    }

    // Тестирование МФК для форм с ежемесячной периодикой
    public function compare_btw_month_forms()
    {
        $table = Table::find(1264);     // Ф2-нп Т2001
        $document = Document::find(73329); // 2-нп ф 8-й месяц Бронницкая городская больница
        $function = "сравнение(С1Г3, Ф4-нпТ4000С1Г3, >=)";
        $lexer = new ControlFunctionLexer($function);
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
        //$evaluator = new ControlFunctionEvaluator($translator->parser->root, $translator->getProperties(), $document);
        //$evaluator = new ControlFunctionEvaluator($pTree, $props, $document);
        //$evaluator->prepareCellValues();
        //$evaluator->prepareCAstack();
        //dd($evaluator->arguments);
        //dd($evaluator->pTree);
        //dd($evaluator->caStack);
        //dd($evaluator->iterations);
        //return $evaluator->evaluate();

        //dd($evaluator->makeControl());
        //$evaluator->makeControl();
        //dd($evaluator);
        //return ($evaluator->iterations);
        //return ($evaluator->properties);
        return $evaluator->makeControl();
    }

    // Тестирование МФК для сравнения форм с разной периодикой - годовой -> месячный и наоборот
    public function compare_btw_year2month_forms()
    {
        // проверка годовой -> месячный
        //$table = Table::find(1205);     // Ф125рп Т0001
        //$document = Document::find(63050); // 125рп ф 2019 год Бронницкая городская больница
        //$function = "сравнение(С1Г3, Ф2-нпТ2000С1Г3П8, >=)";

        // проверка месячный -> годовой
        $table = Table::find(1264);     // Ф2-нп Т2001
        $document = Document::find(73329); // 2-нп ф 8-й месяц Бронницкая городская больница
        //$function = "сравнение(С1Г3, Ф13Т1000С1Г4П0, >=)";
        $function = "сравнение(С1Г3, Ф125рпТ0001С1Г3ПV, >=)";
        $lexer = new ControlFunctionLexer($function);
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
        //$evaluator = new ControlFunctionEvaluator($translator->parser->root, $translator->getProperties(), $document);
        //$evaluator = new ControlFunctionEvaluator($pTree, $props, $document);
        //$evaluator->prepareCellValues();
        //$evaluator->prepareCAstack();
        //dd($evaluator->arguments);
        //dd($evaluator->pTree);
        //dd($evaluator->caStack);
        //dd($evaluator->iterations);
        //return $evaluator->evaluate();

        //dd($evaluator->makeControl());
        //$evaluator->makeControl();
        //dd($evaluator);
        //return ($evaluator->iterations);
        //return ($evaluator->properties);
        return $evaluator->makeControl();
    }

    public function compare_periods()
    {
        // межгодовой контроль по диапазонам ячеек
        //$function = "мгдиапазон(диапазон(С131Г4:С132Г4),  0)";
        //$function = "сравнение(С02Г8П0 + С02Г4 - С02Г6, С02Г8 , =, группы(п0, пi))";
        //$function = "сравнение(С1Г3, Ф2-нпТ2001С1Г3П-1, >=)";
        $function = "сравнение(С1Г3, Ф2-нпТ2001С1Г3П-1 +(С1Г4 - Ф2-нпТ2001С1Г4П-1) -(С1Г7-Ф2-нпТ2001С1Г7П-1), =)";
        //$table = Table::find(4);     // Ф30 Т1001
        //$table = Table::find(179);     // Ф37 Т2100
        $table = Table::find(1264);     // Ф2-нп Т2001
        //$document = Document::find(28181); // 30 ф Сводная Качугский район
        //$document = Document::find(27829); // 2-рп ф Казач-Ленский
        //$document = Document::find(99246); // 2-нп ф Бронницкая ГБ - 2-й месяц
        $document = Document::find(101996); // 2-нп ф Бронницкая ГБ - 3-й месяц
        $lexer = new ControlFunctionLexer($function);
        $tockenstack = $lexer->getTokenStack();
        $parser = new ControlFunctionParser($tockenstack);
        $parser->func();
        //dd($parser);
        $translator = Translator::invoke($parser, $table);
        //dd($translator);
        $translator->prepareIteration();
        //dd($translator->getProperties());
        $evaluator = Evaluator::invoke($translator->parser->root, $translator->getProperties(), $document);
        //dd($evaluator);
        return $evaluator->makeControl();
    }

}
