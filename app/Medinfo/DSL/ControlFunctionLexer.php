<?php
/**
 * Created by PhpStorm.
 * User: shameev
 * Date: 29.08.2017
 * Time: 16:53
 */

namespace App\Medinfo\DSL;

class ControlFunctionLexer extends Lexer
{
    // При изменении индексов токенов потребуется перекомпилирование функций контроля сохраненных в базе данных
    const LPARENTH      = 2;
    const RPARENTH      = 3;
    const PLUS          = 4;
    const MINUS         = 5;
    const MULTIPLY      = 6;
    const DIVIDE        = 7;
    const DIVIDEMOD     = 8; // Деление по модулю
    const NUMBER        = 9;
    const COMMA         = 10;
    const BOOLEAN       = 11;
    const COLON         = 12;
    const EXCLAMATION   = 13;
    const TILDE         = 14;
    const NAME          = 15;
    const CELLADRESS    = 16;
    const ELCODE        = 17; // Код элемента (строки, графы)
    // Следующие типы для парсера
    const ARG           = 18;
    const INGROUP       = 19;
    const OUTGROUP      = 20;
    const UNIT          = 21;
    const CELLRANGE     = 22;
    const RCRANGE       = 23;


    public static $tokenNames = [
        "n/a",
        "EOF",
        "LPARENTH",
        "RPARENTH",
        "PLUS",
        "MINUS",
        "MULTIPLY",
        "DIVIDE",
        "DIVIDEMOD",
        "NUMBER",
        "COMMA",
        "BOOLEAN",
        "COLON",
        "EXCLAMATION",
        "TILDE",
        "NAME",
        "CELLADRESS",
        "ELCODE",
        "ARG",
        // Следующие типы для парсера
        "INGROUP",
        "OUTGROUP",
        "UNIT",
        "CELLRANGE",
        "RCRANGE",
    ];
    public $celladressStack;

    public function __construct($input)
    {
        parent::__construct($input);
        $this->celladressStack = new \SplDoublyLinkedList;
    }

    public function getTokenName($x)
    {
        return self::$tokenNames[$x];
    }

    public function getTokenType($x)
    {
        return self::$tokenNames[$x];
    }

    public function convertSDLLtoArray(\SplDoublyLinkedList $list)
    {
        $list->rewind();
        $simlple_array = [];
        foreach($list as $k => $v) { $simlple_array[$k] = $v; }
        return $simlple_array;
    }

    public function getTokenStack()
    {
        $token = $this->nextToken();
        while($token->type !== CalculationFunctionLexer::EOF_TYPE) {
            $token = $this->nextToken();
        }
        return $this->tokenstack;
    }

    public function normalizeInput()
    {
        $replacements = self::convertSDLLtoArray($this->celladressStack);
        $rep_count = count($replacements);
        $pattern = "/(?:Ф[а-я0-9.-]*)?(?:Т[а-я0-9.-]*)?(?:(?:(?:С[0-9.-]*)|(?:Г\d{1,3})))+(?:П[01])?/u";
        $match_count = preg_match_all($pattern, $this->input, $matches);
        if ($rep_count !== $match_count) {
            throw new \Exception("Кол-во замен ($rep_count) не совпадает с кол-вом найденных ссылок на ячейки ($match_count)");
        }
        return str_replace($matches[0], $replacements, $this->input);
    }

    public function nextToken()
    {
        while ($this->c != self::EOF) {
            switch ($this->c) {
                case " " :
                case "\t":
                case "\n":
                case "\r":
                    $this->ws();
                    continue;
                case ',' :
                    $this->consume();
                    $token = new Token(self::COMMA, ',');
                    $this->tokenstack->push($token);
                    return $token;
                case '(' :
                    $this->consume();
                    $token = new Token(self::LPARENTH, '(');
                    $this->tokenstack->push($token);
                    return $token;
                case ')' :
                    $this->consume();
                    $token = new Token(self::RPARENTH, ')');
                    $this->tokenstack->push($token);
                    return $token;
                case '*' :
                    $this->consume();
                    $token = new Token(self::MULTIPLY, '*');
                    $this->tokenstack->push($token);
                    return $token;
                case '/' :
                    $this->consume();
                    $token = new Token(self::DIVIDE, '/');
                    $this->tokenstack->push($token);
                    return $token;
                case '%' :
                    $this->consume();
                    $token = new Token(self::DIVIDEMOD, '%');
                    $this->tokenstack->push($token);
                    return $token;
                case '+' :
                    $this->consume();
                    $token = new Token(self::PLUS, '+');
                    $this->tokenstack->push($token);
                    return $token;
                case '-' :
                    $this->consume();
                    $token = new Token(self::MINUS, '-');
                    $this->tokenstack->push($token);
                    return $token;
                case '=' :
                case '>' :
                case '<' :
                case '^' :
                    return $this->boolean_sign();
                case ':' :
                    $this->consume();
                    $token = new Token(self::COLON, ':');
                    $this->tokenstack->push($token);
                    return $token;
                case '!' :
                    $this->consume();
                    $token = new Token(self::EXCLAMATION, '!');
                    $this->tokenstack->push($token);
                    return $token;
                case '~' :
                    $this->consume();
                    $token = new Token(self::TILDE, '~');
                    $this->tokenstack->push($token);
                    return $token;
                case '0' :
                case '.' :
                case $this->c >= '1' && $this->c <= '9':
                    return $this->number();
                case $this->c >= 'а' && $this->c <= 'я' :
                case $this->c >= 'a' && $this->c <= 'z' :
                    return $this->name();
                case 'Ф':
                case 'Т':
                case 'С':
                case 'Г':
                case 'П':
                    return $this->cellAdress();
                case 'a': // Латинское a
                    return $this->argument();
                default :
                    throw new \Exception("Неверный символ: " . $this->c);
            }
        }
        $token = new Token(self::EOF_TYPE,"EOF");
        $this->tokenstack->push($token);
        return $token;
    }

    /** WS : (' '|'\t'|'\n'|'\r')* ; // игнорируем все пробелы, табуляции, переносы строк ... */
    public function ws()
    {
        while(ctype_space($this->c)) {
            $this->consume();
        }
    }

    public function boolean_sign()
    {
        $buf = $this->c;
        $this->consume();
        if ($this->c == '=') {
            $buf .= $this->c;
            $this->consume();
        }
        $token = new Token(self::BOOLEAN, $buf);
        $this->tokenstack->push($token);
        return $token;
    }

    public function name()
    {
        $buf = '';
        do {
            $buf .= $this->c;
            $this->consume();
        } while ($this->isFUNCNAME());
        $token = new Token(self::NAME, $buf);
        $this->tokenstack->push($token);
        return $token;
    }

    public function number()
    {
        $buf = '';
        $decimal_separator = 0;
        $token_type = self::NUMBER;
        do {
            if ($this->c === '.') {
                $decimal_separator++;
            }
/*                if ($decimal_separator) {
                    throw new \Exception("Лишний десятичный разделитель в числе " . $buf);
                } else {
                    $decimal_separator = true;
                }
            }*/
            $buf .= $this->c;
            $this->consume();
        } while ($this->isNUMBER());
        if ($decimal_separator < 2) {
            $token = new Token(self::NUMBER, $buf);
        } else {
            $token = new Token(self::ELCODE, $buf);
        }
        $this->tokenstack->push($token);
        return $token;
    }
    // Добавляем и редуцируем
    public function cellAdress()
    {
        $buf = '';
        $f = '';
        $t = '';
        $r = '';
        $c = '';
        $rc = '';
        $p = '';
        if ($this->c === 'Ф') {
            do {
                //$buf .= $this->c;
                $f .= $this->c;
                $this->consume();
            } while ($this->isFORMCODE());

        }
        if ($this->c === 'Т') {
            do {
                $t .= $this->c;
                $this->consume();
            } while ($this->isCODE());

        }
        // Если не указан код таблицы, код формы обнуляем (это ошибка)
        if ($this->c === 'С') {
            do {
                $r .= $this->c;
                $this->consume();
            } while ($this->isROWCODE());
        }
        // Если код строки пуст, то убираем и ссылки на форму и таблицу.
        // "Неполные" адреса строк и граф могут относится только к текущей форме

        if ($this->c === 'Г') {
            do {
                $c .= $this->c;
                $this->consume();
            } while ($this->isCOLUMNCODE());

        }
        // Если код графы пуст, то оставляем только ссылку на код строки таблицы. Подразумевается, что они должны быть указаны.
        // При этом удаляем коды формы и таблицы, если они были указаны.

        if (mb_strlen($f) < 2) $f = '';
        if (mb_strlen($t) < 2) $t = '';
        if (mb_strlen($r) < 2) $r = '';
        if (mb_strlen($c) < 2) $c = '';
        $buf = $f;
        if ($t == '') $buf = '';
        // Закоментировано обнуление буфера при неполных ссылках на строку/графу в межформенных контролях
        //if ($r == '') $buf = '';
        //if ($c == '') $buf = '';
        $f == '' && $t == '' ? $rc = $r.$c : $rc = $t.$r.$c;
        if ($rc == '') throw new \Exception("В адресе ячейки $f$t$r$c$p не указаны ни код строки, ни код графы. Не действительная ссылка");
        $buf .= $rc;
        //dd($buf);
        if (mb_strlen($buf) === 0) {
            throw new \Exception("В адресе ячейки $f$t$r$c$p заполнены не все необходимые коды элементов");
        }
        if ($this->c == 'П') {
            //$p .= $this->c;
            //$this->consume();
            //if ($this->c == '0') $p .= $this->c; $this->consume();

             do {
                $p .= $this->c;
                $this->consume();
            } while ($this->isPERIODCODE());
            mb_strlen($buf) > 1 && mb_strlen($p) > 1 ? $buf .= $p : true;
        }
        $this->celladressStack->push($buf);
        //$token = new Token(self::CELLADRESS, '%'. ($this->argStack->count()-1));
        $token = new Token(self::CELLADRESS, $buf);
        $this->tokenstack->push($token);
        return $token;
    }

    public function argument()
    {
        $buf = '';
        do {
            $buf .= $this->c;
            $this->consume();
        } while ($this->isINTEGER());
        $token = new Token(self::ARG, $buf);
        $this->tokenstack->push($token);
        return $token;
    }

    public function isFUNCNAME()
    {
        return ($this->c >= 'а' && $this->c <= 'я') || ($this->c >= 'a' && $this->c <= 'z') || ($this->c >= '0' && $this->c <= '9') || $this->c === '_';
    }

    public function isNUMBER()
    {
        return $this->c == '.' || ($this->c >= '0' && $this->c <= '9');
    }

    public function isINTEGER()
    {
        return $this->c >= '0' && $this->c <= '9';
    }

    public function isFORMCODE()
    {
        return
            $this->c !== 'Т' && (
                ($this->c >= '0' && $this->c <= '9') ||
                ($this->c >= 'а' && $this->c <= 'я') ||
                $this->c == '.' ||
                $this->c == '-'
            );
    }

    public function isCODE()
    {
        return $this->c >= '0' && $this->c <= '9';
    }

    public function isCOLUMNCODE()
    {
        return
            $this->c != 'П' && (
                ($this->c >= '0' && $this->c <= '9') || $this->c == '.'
            );
    }

    public function isROWCODE()
    {
        return
            $this->c != 'Г' && (
                ($this->c >= '0' && $this->c <= '9') || $this->c == '.'
            );
    }

    public function isPERIODCODE()
    {
        return ($this->c >= '0' && $this->c <= '9') || $this->c == 'I' || $this->c == 'V' || $this->c == '-' || $this->c == '+';
    }
}