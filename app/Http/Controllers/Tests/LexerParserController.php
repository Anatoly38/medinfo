<?php

namespace App\Http\Controllers\Tests;

use App\Medinfo\DSL\ControlFunctionEvaluator;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Medinfo\DSL\ControlFunctionLexer;
use App\Medinfo\DSL\ControlFunctionParser;
use App\Medinfo\DSL\ControlPtreeTranslator;
use App\Document;
use App\Table;
use App\CFunction;

class LexerParserController extends Controller
{
    //
    public function lexerTest()
    {
        //$i = "сравнение(сумма(Ф32Т2120С1Г3:Ф32Т2120С18Г3),  Ф32Т2120С22Г3, >=, группы(*), графы())";
        //$i = "сравнение(сумма(С1Г3П0:С18Г3П0),  С22Г3П0, <=, группы(*), графы(*))";
        //$i = "сравнение(меньшее(С11, С16:С18, С20),  Ф32Т2120С22Г3, <=, группы(*), графы(3-9,15))";
        //$i = "сравнение(сумма(Ф32Т2110С1Г3П0:Ф32Т2110С1Г5П0, С16:С18, С6, С8, С20) - сумма(Ф30Т1100С11Г3, Ф30Т1100С13Г3:Ф30Т1100С15Г3)- сумма(Ф30Т1100С22Г3, Ф30Т1100С25Г3:Ф30Т1100С28Г3),
          //Ф32Т2120С22Г3, <=, группы(*), графы(*))";
        //$i = "сравнение(сумма(Ф32Т2120С16Г3:Ф32Т2120С18Г3),  Ф32Т2120С22Г3, >=, группы(*), графы())";
        //$i = 'сравнение(Ф36-плТ2100С6Г4, Ф36-плТ2100С6Г4+Ф36-плТ2140С5Г4, >=, группы(первичные, село, !оп), графы())';
        //$i = 'сравнение(Ф36-плТ2100С6Г4, Ф36-плТ2100С6Г4+Ф36-плТ2140С5Г4, >=, группы(!юрлица), графы())';
        //$i = "сравнение(С1, С6, >=, группы(*), графы(*))";
        //$i = "сравнение(С1, С6, >=, группы(*))";
        //$i = "сравнение((сумма(С1, С2, С16Г3:С18Г5, С20)+С31+С41)/2, С6, >=)";
        //$i = "сравнение((сумма(Г4, Г9:Г11, Г13)+Г16)/2, Г15, >=, группы(село, !сводные , !север, !юл), строки(1.0,5.4, 7.0-18.0))";
        //$i = "сравнение(С1, С6, >=, , графы(*))";
        //$i = "сравнение(С5.8Г16, С5.8Г15, =, группы(*), графы())";
        //$i = "сравнение(С4.2.1Г13, С4.2.1Г9, =, группы(*), строки())";
/*        $i = "сравнение(
                большее(Г4:Г9), 
                Г10, 
                =, 
                строки(1.0, 2.2, 4.1.1, 5.2, 5.2.1, 5.2.2, 7.1, 7.1.1, 7.1.2, 7.5.1, 7.8.1, 7.8.2, 7.9.1, 8.8, 
                    10.1, 10.2, 10.4.2, 10.4.3, 10.6.1, 10.6.2, 10.6.3, 10.6.4, 10.6.5, 10.8.2, 
                    11.3, 12.1, 12.5.1, 10.5.1, 10.5.2, 10.5.3
                )
            )";*/
        //$i = "сравнение(С6.0Г4-С6.1Г4, Ф10Т2000С1Г7, >=)";

        //$i = "зависимость(Г3, Г4+Г5, группы(оп), строки(*))";
        //$i = 'зависимость(Г3, сумма(Г4:Г8), группы(*), строки(*))';
        //$i = 'зависимость(Г3, Г4, группы(*),  строки(*))';

        //$i = "межгодовой(С1.0Г15+С1.0Г14-С1.0Г8,  С1.0Г15, 20)";
        //$i = "межгодовой(диапазон(С9Г4:С9Г16), 0)";
        //$i = "межгодовой(диапазон(С11Г3, С16Г3:С18Г3, С20Г3, С32Г3, С16Г3:С18Г3),  20)";
        //$i = "межгодовой(С1Г3+С4Г3, С1Г3,  20)";

        //$i = "кратность(диапазон(С01Г3:С02Г6),  .25)";
        //$i = "кратность(диапазон(С01Г3:С02Г6),  .25)";

        //$cellcount = preg_match_all('/Ф([а-я0-9.-]+)Т([\w.-]+)С([\w.-]+)Г(\d{1,})/u', $i, $matches, PREG_SET_ORDER);
        //$res = preg_match('|(?:\()(.*?),(.*?)\)|usei', $i, $matches);
        //$res = preg_match_all('/\((.*?)\(/u', $i, $matches, PREG_SET_ORDER);
        //dd($matches);

         //$lexer = new ControlFunctionLexer($i);
         //$tockenstack = $lexer->getTokenStack();
        //dd($lexer->normalizeInput());
        //dd($lexer);

         //$parser = new ControlFunctionParser($tockenstack);
         //$parser->func();
        //dd($parser);
        //dd($parser->root);
        //dd(json_decode(json_encode($parser->root)));

        //$table = Table::find(10);
         //$table = Table::find(112); // Ф12 Т2000
        //$table = Table::find(115); // Ф32 Т2120

         //$translator = new ControlPtreeTranslator($parser, $table);
        //$translator->setParentNodesFromRoot();
        //$translator->parseCellAdresses();
        //$translator->parseCellRanges();
        //$translator->validateVector();
         //$translator->prepareIteration();
        //dd($translator);
        //dd($translator->getProperties());
        //dd($translator->parser->root);
        //echo (json_encode($translator->parser->root, JSON_PARTIAL_OUTPUT_ON_ERROR));
        //echo json_last_error();
        //dd(json_decode(json_encode($translator->parser->root, JSON_PARTIAL_OUTPUT_ON_ERROR, JSON_FORCE_OBJECT), TRUE));
        //dd((array)$translator->parser->root);
        //dd(unserialize(serialize($translator->parser->root)));
        //dd($translator->parser->celladressStack);
        //dd($translator->parser->rcRangeStack);
        //dd($translator->parser->rcStack);
        //dd($translator->parser->excludeGroupStack);
        //dd($translator->scriptReadable);
        //dd($translator->iterations);
        //dd(json_decode(json_encode($translator->iterations), TRUE));
        //dd($translator->getProperties());

        $document = Document::find(13134); // 12 ф ГКБ№8 за 2016 год
        $cfunc = CFunction::find(2652); // сравнение(С8.0, С8.1+С8.2+С8.3+С8.4+С8.5+С8.6+С8.7+С8.8+С8.9+С8.10+С8.11+С8.12, >=)) ф. 12 т. 2000
        //$cfunc = CFunction::find(3280);
        //dd($cfunc);
        $pTree = unserialize(base64_decode($cfunc->ptree));
        //dd($pTree);
        $props = json_decode($cfunc->properties, true);
        //dd($props);
        //dd($iterations);
        //$evaluator = new ControlFunctionEvaluator($translator->parser->root, $translator->iterations, $document);
        $evaluator = new ControlFunctionEvaluator($pTree, $props, $document);

        //$evaluator->prepareCellValues();
        //$evaluator->prepareCAstack();

        //dd($evaluator->pTree);
        //dd($evaluator->caStack);
        //dd($evaluator->iterations);

        return $evaluator->makeControl();
        //return ($evaluator->iterations);
        //dd($evaluator->pTree);
    }

    public function func_parser()
    {
        //$i = "сравнение(сумма(Ф32Т2120С1Г3:Ф32Т2120С18Г3),  Ф32Т2120С22Г3, >=, группы(*), графы())";
        //$i = "сравнение(сумма(С1Г3П0:С18Г3П0),  С22Г3П0, <=, группы(*), графы())";
        //$i = "сравнение(меньшее(С11, С16:С18, С20),  Ф32Т2120С22Г3, <=, группы(*), графы(3))";
        //$i = "сравнение(сумма(С11, С16:С18, С20) - сумма(Ф30Т1100С11Г3, Ф30Т1100С13Г3:Ф30Т1100С15Г3)- сумма(Ф30Т1100С11Г3, Ф30Т1100С13Г3:Ф30Т1100С15Г3),  Ф32Т2120С22Г3, <=, группы(*), графы(3))";
        //$i = "сравнение(сумма(Ф32Т2120С16Г3:Ф32Т2120С18Г3),  Ф32Т2120С22Г3, >=, группы(*), графы())";
        //$i = "межгодовой(диапазон(С9Г4:С9Г16), 0)";
        //$i = "зависимость(Г3, Г4+Г5, группы(оп), строки(*))";
        //$i = "сравнение(С1, С6, >=, группы(*), графы(*))";
        $i = "межгодовой(С1.0Г15+С1.0Г14-С1.0Г8,  С1.0Г15, 20)";

        //$i = "межгодовой(диапазон(С11Г3, С16Г3:С18Г3, С20Г3, С32Г3, С16Г3:С18Г3),  20)";
        //$i = "межгодовой(С1Г3+С4Г3, С1Г3,  20)";
        //$i = "кратность(диапазон(С01Г3:С02Г6),  .25)";
        //$i = "кратность(диапазон(С01Г3:С02Г6),  .25)";
        //$i = 'зависимость(Г3, сумма(Г4:Г8), группы(*), строки(*))';
        //$i = 'сравнение(Ф36-плТ2100С6Г4, Ф36-плТ2100С6Г4+Ф36-плТ2140С5Г4, >=, группы(первичные, село, !оп), графы())';
        //$i = 'сравнение(Ф36-плТ2100С6Г4, Ф36-плТ2100С6Г4+Ф36-плТ2140С5Г4, >=, группы(!юрлица), графы())';
        //$i = 'зависимость(Г3, Г4, группы(*),  строки(*))';
        //try {
        //$table = Table::find(254); // т. 8000 30 формы
        //$table = Table::find(4); // т. 1001 30 формы
        //$table = Table::find(980); // т. 2710 30 формы
        //$table = Table::find(10);
        //$table = Table::find(115); // т. 2120 32 формы
        //$table = Table::find(950); // т. 2120 54 формы
        //$table = Table::find(948); // т. 2101 54 формы
        //$table = Table::find(420); // т. 2500 37 формы
        //$table = Table::find(179); // т. 2100 37 формы
        //$table = Table::find(5); // т. 2100 37 формы
        $table = Table::find(113); // т. 3000 12 формы
        //$document = Document::find(7011);
        //$document = Document::find(7062);
        //$document = Document::find(7758); // 12 форма
        //$document = Document::find(12402); // 54 форма
        //$document = Document::find(10654); // 37 форма 2015
        //$document = Document::find(8429); // 37 форма 2015 - Ольхонская районная больница
        //$document = Document::find(9158); // 30 форма 2015 - Шелеховская районная больница
        //$document = Document::find(15105); // 30 форма 2016 - ПАБ
        //$document = Document::find(11433); // 30 форма 2016 - Листвянка
        //$document = Document::find(12268); // 30 форма 2016 - все
        $document = Document::find(13425); // 12 форма 2016 ГБ3 Братск
        //$document = Document::find(7015); // 32 форма
        //$lexer = new ControlFunctionLexer($i);

        //$parser = new ControlFunctionParser($lexer);
        //$r = $parser->run();
        //dd($r);
        //$interpret = new CompareControlInterpreter($r, $table);
        //$interpret = new InterannualControlInterpreter($r, $table);
        //$interpret = new DependencyControlInterpreter($r, $table);
        //$interpret = new FoldControlInterpreter($r, $table);
        //dd($interpret);
        //dd( $interpret->exec($document));
        //$result = $interpret->exec($document) ? 'Правильно' : 'Ошибка';
        //echo 'Результат выполнения контроля: ' . $result;
        //dd($interpret);
        //}
        //catch (\Exception $e) {
        //echo " Ошибка при обработке правила контроля " . $e->getMessage();
        //}

    }

    public function test_making_AST()
    {
        $input = "С11Г13 + С12Г15 - 40 - 23/2";
        //$input = "20-23/2+40";
        //$input = "((20+23))/2-40";
        //$input = "20*3+40/2+50+60";
        //$input = "20*3/2*100";
        //$input = "7 + 3 * (10 / (12 / (3 + 1) - 1))";
        //$input = "7 + 3 * (10 / (12 / (3 + 1) - 1)) / (2 + 3) - 5 - 3 + (8)";
        //$input = "7 + (((3 + 2)))";
        //$input = "20 + 10/2 + 3*6";
        //echo eval("return $input;");
        $lexer = new \App\Medinfo\DSL\CalculationFunctionLexer($input);
        $tokenstack = $lexer->getTokenStack();
        dd($lexer);
        //$tokenstack->rewind();
        //dd($tokenstack);
        $parcer = new \App\Medinfo\DSL\CalculationFunctionParser($tokenstack);
        $parcer->expression();
        //dd($parcer->celladressStack);
        $eval = new \App\Medinfo\DSL\Evaluator($parcer->expression());
        //dd($eval->evaluate());
    }

    public function test_celllexer()
    {
        //$c = '0';
        //dd($c >= 'а' && $c <= 'я');
        //$input = '[ F30T1100, F12T2000, F121T1010, F162T3000 ]';
        //$input = '[ Ф30Т1100, Ф12Т2000, Ф121Т1010 ]';
        //$input = "сравнение(меньшее(С16, Ф32Т2120С18Г3),  Ф32Т2120С22Г3, >=, группы(*), графы())";
        //$input = "кратность(диапазон(С11Г3, С16Г3:С18Г3, С20Г3, С32Г3, С16Г3:С18Г3), .25 )";
        //$input = "межгодовой(С11Г3, С16Г3,  20)";
        $input = "20+Г3-Г5/2";

        //$lexer = new ControlFunctionLexer($input);
        $lexer = new \App\Medinfo\DSL\CalculationFunctionLexer($input);
        $token = $lexer->nextToken();
        echo '<pre>';
        while($token->type != \App\Medinfo\DSL\CalculationFunctionLexer::EOF_TYPE) {
            echo $token . "\n";
            $token = $lexer->nextToken();
        }
        echo '</pre>';
        //foreach ($lexer->tokenstack->stack as $item) {
        //  var_dump($lexer->getTokenType($item->type));
        //}
        $t = $lexer->getTokenStack();
        $t->rewind();
        while ($t->valid()) {
            echo $t->key(), $t->current(), PHP_EOL;
            $t->next();
        }

        //$lexer->tokenstack->stack->top();
        //$lexer->tokenstack->stack->rewind();
        //dd($lexer->tokenstack->stack->valid());
        //echo($lexer->tokenstack->stack->key());
    }
}
