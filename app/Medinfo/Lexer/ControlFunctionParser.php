<?php

namespace App\Medinfo\Lexer;

class ControlFunctionParser extends Parser {

    public function __construct(Lexer $input) {
        parent::__construct($input);
    }
    
    public function controlFunction() {
        $r = new ControlFunctionParseTree(__FUNCTION__);
        $o = $this->currentNode; // сохраняем текущий узел, что бы вернутся к нему в конце функции
        if ($this->root == null) {
            $this->root = $r;
        } else {
            $this->currentNode->addChild($r);
        }
        $this->currentNode = $r;
        $this->match(ControlFunctionLexer::NAME);
        $this->match(ControlFunctionLexer::LPARENTH);
        $this->expression(); // Первый аргумент - вычисление
        $this->match(ControlFunctionLexer::COMMA);
        $this->expression(); // Второй аргумент - вычисление
        $this->match(ControlFunctionLexer::COMMA);
        $this->compare_action();
        $this->match(ControlFunctionLexer::COMMA);
        $this->scope(); // Четвертый аргумент - функции ограничения применения данного контроля к группе учреждений
        $this->match(ControlFunctionLexer::COMMA);
        $this->iterations(); // Пятый аргумент - функции итерации по строкам и графам
        $this->match(ControlFunctionLexer::RPARENTH);
        $this->currentNode = $o;
        return $this->root;
        //dd($this->root);
        //dd($this->input->tokenstack);

    }
    // Первый аргумент в функции. В выражении может несколько элементов, разделенных (пока) знаком плюс
    function expression() {
        $r = new ControlFunctionParseTree(__FUNCTION__);
        $o = $this->currentNode; // сохраняем текущий узел, что бы вернутся к нему в конце функции
        if ($this->root == null) {
            $this->root = $r;
        } else {
            $this->currentNode->addChild($r);
        }
        $this->currentNode = $r;

        $this->element();
        while ($this->lookahead->type == ControlFunctionLexer::OPERATOR ) {
            $this->operator();
            $this->element();
        }

        $this->currentNode = $o;
    }
    // элемент в выражении - или адрес ячейки, или функция "сумма"
    public function element() {
        $r = new ControlFunctionParseTree(__FUNCTION__);
        $o = $this->currentNode;
        $this->currentNode->addChild($r);
        $this->currentNode = $r;

        if ($this->lookahead->type == ControlFunctionLexer::FORMADRESS ) {
            $this->celladress();
        } elseif ($this->lookahead->type == ControlFunctionLexer::NAME && $this->lookahead->text == 'сумма') {
            $this->summfunction();
        } else {
            throw new \Exception("Ожидался адрес ячейки или функция сумма. Найдено: "  . $this->lookahead);
        }

        $this->currentNode = $o;
    }

    public function operator()
    {
        $r = new ControlFunctionParseTree(__FUNCTION__);
        $o = $this->currentNode;
        $this->currentNode->addChild($r);
        $this->currentNode = $r;

        $this->match(ControlFunctionLexer::OPERATOR); // Пока предусмотрено только сложение и вычитание

        $this->currentNode = $o;
    }

    public function celladress()
    {
        $r = new ControlFunctionParseTree(__FUNCTION__);
        $o = $this->currentNode;
        $this->currentNode->addChild($r);
        $this->currentNode = $r;

        $this->match(ControlFunctionLexer::FORMADRESS);
        $this->match(ControlFunctionLexer::TABLEADRESS);
        $this->match(ControlFunctionLexer::ROWADRESS);
        $this->match(ControlFunctionLexer::COLUMNADRESS);

        $this->currentNode = $o;
    }

    public function cellrange()
    {
        $r = new ControlFunctionParseTree(__FUNCTION__);
        $o = $this->currentNode;
        $this->currentNode->addChild($r);
        $this->currentNode = $r;

        $this->celladress();
        $this->match(ControlFunctionLexer::COLON);
        $this->celladress();

        $this->currentNode = $o;
    }

    public function summfunction()
    {
        $r = new ControlFunctionParseTree(__FUNCTION__);
        $o = $this->currentNode;
        $this->currentNode->addChild($r);
        $this->currentNode = $r;

        $this->match(ControlFunctionLexer::NAME);
        $this->match(ControlFunctionLexer::LPARENTH);
        $this->cellrange();
        $this->match(ControlFunctionLexer::RPARENTH);

        $this->currentNode = $o;
    }

    public function compare_action()
    {
        $r = new ControlFunctionParseTree(__FUNCTION__);
        $o = $this->currentNode;
        $this->currentNode->addChild($r);
        $this->currentNode = $r;

        $this->match(ControlFunctionLexer::BOOLEAN); // Третий агрумент - пока только знак сравнения - нужно затем заменить на булевые символы

        $this->currentNode = $o;
    }

    public function iterations()
    {
        $r = new ControlFunctionParseTree(__FUNCTION__);
        $o = $this->currentNode; // сохраняем текущий узел, что бы вернутся к нему в конце функции
        $this->currentNode->addChild($r);
        $this->currentNode = $r;

        $this->match(ControlFunctionLexer::NAME);
        $this->match(ControlFunctionLexer::LPARENTH);
        $this->iteration_range();
        $this->match(ControlFunctionLexer::RPARENTH);

        $this->currentNode = $o;
    }

    public function scope()
    {
        $r = new ControlFunctionParseTree(__FUNCTION__);
        $o = $this->currentNode; // сохраняем текущий узел, что бы вернутся к нему в конце функции
        $this->currentNode->addChild($r);
        $this->currentNode = $r;

        $this->match(ControlFunctionLexer::NAME);
        $this->match(ControlFunctionLexer::LPARENTH);
        $this->group_name();
        $this->match(ControlFunctionLexer::RPARENTH);

        $this->currentNode = $o;
    }

    public function group_name()
    {
        $r = new ControlFunctionParseTree(__FUNCTION__);
        $o = $this->currentNode; // сохраняем текущий узел, что бы вернутся к нему в конце функции
        $this->currentNode->addChild($r);
        $this->currentNode = $r;

        if ($this->lookahead->type == ControlFunctionLexer::MULTIPLY ) {
            $this->match(ControlFunctionLexer::MULTIPLY);
        } elseif ($this->lookahead->type == ControlFunctionLexer::NAME) {
            $this->match(ControlFunctionLexer::NAME);
        }
        $this->currentNode = $o;
    }

    public function iteration_range()
    {
        $r = new ControlFunctionParseTree(__FUNCTION__);
        $o = $this->currentNode; // сохраняем текущий узел, что бы вернутся к нему в конце функции
        $this->currentNode->addChild($r);
        $this->currentNode = $r;

        if ($this->lookahead->type == ControlFunctionLexer::MULTIPLY ) {
            $this->match(ControlFunctionLexer::MULTIPLY);
        }
        $this->currentNode = $o;
    }
}

?>