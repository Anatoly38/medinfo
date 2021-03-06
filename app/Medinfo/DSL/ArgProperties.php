<?php
/**
 * Created by PhpStorm.
 * User: shameev
 * Date: 17.10.2017
 * Time: 10:26
 */

namespace App\Medinfo\DSL;


class ArgProperties
{
    const EOP = null;
    protected $input;
    protected $position = 0;
    //protected $property;
    protected $propstack;
    public $argType; // Первое свойство условно принимаем как Тип аргумента
    public $argRequired = false;
    public $argDefaultValue = null;

    const EXPRESSION    = 2;
    const SUBFUNCTION   = 3;
    const BOOLEAN       = 4;
    const FACTOR        = 5;
    const FLOAT         = 6;
    const DIAPAZON      = 7;
    const REQUIRED      = 8;
    const GROUPS        = 9;
    const ROWS          = 10;
    const COLUMNS       = 11;
    const ITERATOR      = 12;
    const THISYEAR      = 13;
    const PREVYEAR      = 14;
    const UNITLIST      = 15;
    const BOOL          = 16; // true or false
    const DEFAULT       = 17;

    public static $propNames = [
        "n/a",
        "EOP",
        "expression",
        "subfunction",
        "boolean",
        "factor",
        "float",
        "diapazon",
        "required",
        "группы",
        "строки",
        "графы",
        "iterator",
        "thisyear",
        "prevyear",
        "unitlist",
        "bool",
        "default",
    ];

    public function __construct($input) {
        $this->input =  explode('|', $input);
        //$this->property = $this->input[0];
        $this->argType = $this->input[0];
        $this->makeStack();
    }

/*    public function consume() {
        $this->position++;
        if ($this->position >= count($this->input)) {
            $this->property = self::EOP;
        }
        else {
            $this->property = $this->input[$this->position];
        }
    }

    public function match($x) {
        if ( $this->property == $x) {
            $this->consume();
        } else {
            throw new \Exception("Ожидалось свойство аргумента " . $x . "; получено " . $this->property );
        }
    }*/

    public function makeStack()
    {
        foreach ($this->input as $prop) {
            switch ($prop) {
                case 'expression' :
                    $this->propstack[] = self::EXPRESSION;
                    break;
                case 'subfunction' :
                    $this->propstack[] = self::SUBFUNCTION;
                    break;
                case 'boolean' :
                    $this->propstack[] = self::BOOLEAN;
                    break;
                case 'factor' :
                    $this->propstack[] = self::FACTOR;
                    break;
                case 'float' :
                    $this->propstack[] = self::FLOAT;
                    break;
                case 'diapazon' :
                    $this->propstack[] = self::DIAPAZON;
                    break;
                case 'required' :
                    $this->argRequired = true;
                    $this->propstack[] = self::REQUIRED;
                    break;
                case 'группы' :
                    $this->propstack[] = self::GROUPS;
                    break;
                case 'строки' :
                    $this->propstack[] = self::ROWS;
                    break;
                case 'графы' :
                    $this->propstack[] = self::COLUMNS;
                    break;
                case 'iterator' :
                    $this->propstack[] = self::COLUMNS;
                    break;
                case 'thisyear' :
                    $this->propstack[] = self::THISYEAR;
                    break;
                case 'prevyear' :
                    $this->propstack[] = self::PREVYEAR;
                    break;
                case 'unitlist' :
                    $this->propstack[] = self::UNITLIST;
                    break;
                case 'bool' :
                    $this->propstack[] = self::BOOL;
                    break;
                case explode(':', $prop)[0] === 'default' :
                    $this->argDefaultValue =  explode(':', $prop)[1];
                    $this->propstack[] = self::DEFAULT;
                    break;
                default :
                    throw new \Exception("Неизвестное ствойство аргумента: " . $prop);

            }
        }
    }
}