<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams
 * Created:  2016-02-01
 *
 * @package flexio
 * @subpackage Base
 */


declare(strict_types=1);
namespace Flexio\Base;


class ExprValue
{
    public $val = null;
    public $text = ''; // exact text of the value

    public function getNodeType() { return ExprParser::NODETYPE_VALUE; }
};

class ExprOperator
{
    public $name = '';
    public $unary = false;
    public $index = false;  // index of the operators in ExprParser::operators[]
    public $params = [];

    public function getNodeType() { return ExprParser::NODETYPE_OPERATOR; }
};

class ExprFunction
{
    public $name = '';
    public $params = [];

    public function getNodeType() { return ExprParser::NODETYPE_FUNCTION; }
};

class ExprVariable
{
    public $name = '';

    public function getNodeType() { return ExprParser::NODETYPE_VARIABLE; }
};

class ExprParser
{
    public const TYPE_INVALID   = 0;
    public const TYPE_UNDEFINED = 1;
    public const TYPE_NULL      = 2;
    public const TYPE_STRING    = 3;
    public const TYPE_INTEGER   = 4;
    public const TYPE_FLOAT     = 5;
    public const TYPE_BOOLEAN   = 6;
    public const TYPE_DATE      = 7;
    public const TYPE_DATETIME  = 8;

    public const NODETYPE_VALUE     = 1;
    public const NODETYPE_OPERATOR  = 2;
    public const NODETYPE_FUNCTION  = 3;
    public const NODETYPE_VARIABLE  = 4;

    // normally, users should supply their own operator table; the operator
    // table below is a good example of how it should look like

    public $operators = [
        [ 'name' => '-',  'prec' => 5, 'unary' => true, ],
        [ 'name' => '+',  'prec' => 5, 'unary' => true, ],
        [ 'name' => '!',  'prec' => 9, 'unary' => true,  'assoc' => 'R' ],
        [ 'name' => '*',  'prec' => 4, 'unary' => false, 'assoc' => 'L' ],
        [ 'name' => '/',  'prec' => 4, 'unary' => false, 'assoc' => 'L' ],
        [ 'name' => '-',  'prec' => 3, 'unary' => false, 'assoc' => 'L' ],
        [ 'name' => '+',  'prec' => 3, 'unary' => false, 'assoc' => 'L' ],
        [ 'name' => '<',  'prec' => 2, 'unary' => false, 'assoc' => 'L' ],
        [ 'name' => '<=', 'prec' => 2, 'unary' => false, 'assoc' => 'L' ],
        [ 'name' => '>',  'prec' => 2, 'unary' => false, 'assoc' => 'L' ],
        [ 'name' => '>=', 'prec' => 2, 'unary' => false, 'assoc' => 'L' ],
        [ 'name' => '=',  'prec' => 1, 'unary' => false, 'assoc' => 'L' ]
    ];

    private $expr = '';
    private $expr_len = 0;
    private $off = 0;


    public function setOperators($operators)
    {
        $this->operators = $operators;
    }

    private function lookupBinaryOperator($s)
    {
        $idx = 0;
        foreach ($this->operators as $oper)
        {
            if (0 == strcasecmp($oper['name'],(string)$s) && !$oper['unary'])
                return $idx;
            ++$idx;
        }
        return false;
    }

    private function lookupUnaryOperator($s)
    {
        $idx = 0;
        foreach ($this->operators as $oper)
        {
            if (0 == strcasecmp($oper['name'],(string)$s) && $oper['unary'])
                return $idx;
            ++$idx;
        }
        return false;
    }

    private function peekNext()
    {
        $off = $this->off;

        while ($off < $this->expr_len && ctype_space($this->expr[$off]))
            $off++;

        if ($off >= $this->expr_len)
            return false;

        $val = '';

        if (false !== strpos('(),', $this->expr[$off]))
        {
            $this->last_peek_end = $off+1;
            return $this->expr[$off];
        }

        if (ctype_digit($this->expr[$off]) || $this->expr[$off] == '.')
        {
            $period_count = 0;

            $val = '';
            do
            {
                $val .= $this->expr[$off];
                ++$off;

                if ($off < $this->expr_len && ($this->expr[$off] == 'e' || $this->expr[$off] == 'E'))
                {
                    $val .= $this->expr[$off];
                    $off++;
                    if ($off < $this->expr_len && ($this->expr[$off] == '-' || $this->expr[$off] == '+'))
                    {
                        $val .= $this->expr[$off];
                        $off++;
                    }
                    continue;
                }
            } while ($off < $this->expr_len && (ctype_alnum($this->expr[$off]) || $this->expr[$off] == '.'));

            $this->last_peek_end = $off;
            return $val;
        }

        if ($this->expr[$off] == '"' || $this->expr[$off] == "'")
        {
            $quote_char = $this->expr[$off];
            $terminated = false;
            $val = $quote_char;
            ++$off;
            while ($off < $this->expr_len)
            {
                $val .= $this->expr[$off];
                if ($this->expr[$off] == $quote_char)
                {
                    if ($off+1 < $this->expr_len && $this->expr[$off+1] == $quote_char)
                    {
                        // escaped quote
                        $off++;
                        $val .= $this->expr[$off];
                    }
                     else
                    {
                        $off++;
                        $terminated = true;
                        break;
                    }
                }
                $off++;
            }

            if (!$terminated)
                return false; // unterminated string

            $this->last_peek_end = $off;
            return $val;
        }

        if ($this->expr[$off] == '[')
        {
            $val = '';
            while ($off < $this->expr_len)
            {
                $val .= $this->expr[$off];
                if ($this->expr[$off] == ']')
                {
                    $off++;
                    $this->last_peek_end = $off;
                    return $val;
                }
                $off++;
            }

            return false; // unterminated quoted symbol
        }

        // look for an operator; we want to choose the operator that matches
        // the greatest length.  For example, we want to match <= instead of just <

        $oper_choice_len = 0;
        foreach ($this->operators as $oper)
        {
            $oper_len = strlen($oper['name']);
            if (0 == substr_compare($this->expr, $oper['name'], $off, $oper_len, true))
            {
                if (ctype_alnum($oper['name'][0]))
                {
                    // not an operator -- not on a word boundry
                    if ($off + $oper_len < $this->expr_len && ctype_alnum($this->expr[$off + $oper_len]))
                        continue;
                }

                $oper_choice_len = max($oper_choice_len, $oper_len);
            }
        }

        if ($oper_choice_len > 0)
        {
            $this->last_peek_end = $off + $oper_choice_len;
            return substr($this->expr, $off, $oper_choice_len);
        }


        // look for an identifier
        if (ctype_alpha($this->expr[$off]) || false !== strpos('_@#.', $this->expr[$off]))
        {
            $val = $this->expr[$off];
            ++$off;
            while ($off < $this->expr_len && (ctype_alnum($this->expr[$off]) || false !== strpos('_@#.', $this->expr[$off])))
            {
                $val .= $this->expr[$off];
                $off++;
            }

            $this->last_peek_end = $off;
            return $val;
        }

        // unexpected token
        return false;
    }

    private function consume()
    {
        $this->off = $this->last_peek_end;
    }

    private function parseElement() // P
    {
        $next = $this->peekNext();
        if ($next === false || strlen($next) == 0)
            return false;

        if (false !== ($oper_idx = $this->lookupUnaryOperator($next)))
        {
            $oper = $this->operators[$oper_idx];
            $this->consume();

            // get operand
            $operand = $this->peekNext();
            if (false === $operand)
            {
                // missing operand
                return false;
            }

            $param = $this->parseExpression($oper['prec']);
            if (!$param)
                return false;

            $ret = new ExprOperator;
            $ret->name = $next;
            $ret->unary = true;
            $ret->index = $oper_idx;
            $ret->params = [ $param ];

            return $ret;
        }
         else if ($next == '(')
        {
            $this->consume();
            $ret = $this->parseExpression(0);
            if ($this->peekNext() != ')')
            {
                // missing close parenthesis
                return false;
            }
            $this->consume();
            return $ret;
        }
         else if (ctype_digit($next[0]) || $next[0] == '.')
        {
            $this->consume();
            $ret = new ExprValue;
            $ret->text = $next;
            if (!is_numeric($next))
            {
                // not a valid numeric constant
                return false;
            }
            if (false === strpos($next, '.') && false === strpos(strtolower($next), 'e'))
            {
                $ret->val = (int)$next;
            }
             else
            {
                $ret->val = (float)$next;
            }
            return $ret;
        }
         else if (0 == strcasecmp($next,'true') || 0 == strcasecmp($next,'false'))
        {
            $this->consume();
            $ret = new ExprValue;
            $ret->text = $next;
            $ret->val = (0 == strcasecmp($next,'true')) ? true : false;
            return $ret;
        }
         else if (0 == strcasecmp($next,'null'))
        {
            $this->consume();
            $ret = new ExprValue;
            $ret->text = $next;
            $ret->val = null;
            return $ret;
        }
         else if (ctype_alpha($next[0]) || false !== strpos('_@#', $next[0]))
        {
            $this->consume();

            if ($this->peekNext() == '(')
            {
                // it's a function
                $this->consume();

                $ret = new ExprFunction;
                $ret->name = $next;
                $ret->params = [];

                // parse parameters

                while (true)
                {
                    $next = $this->peekNext();

                    if ($next === false)
                    {
                        // unexpected end of token stream
                        return false;
                    }

                    if ($next == ')')
                    {
                        // found close parenthesis; done
                        $this->consume();
                        break;
                    }

                    // parse parameter
                    $s = $this->parseExpression(0);
                    if ($s === false)
                        return false;
                    $ret->params[] = $s;

                    if ($this->peekNext() == ',')
                    {
                        // consume comma
                        $this->consume();

                        if ($this->peekNext() == ')')
                        {
                            // empty parameter, not allowed
                            return false;
                        }
                    }
                }

                return $ret;
            }
             else
            {
                // it's a variable
                $ret = new ExprVariable;
                $ret->name = $next;
                return $ret;
            }
        }
         else if ($next[0] == '"' || $next[0] == "'")
        {
            $this->consume();
            $ret = new ExprValue;
            $ret->val = str_replace($next[0].$next[0], $next[0], substr($next, 1, -1));
            return $ret;
        }
         else if ($next[0] == '[')
        {
            $this->consume();
            if (strlen($next) < 2)
                return false;
            $ret = new ExprVariable;
            $ret->name = substr($next,1,strlen($next)-2);
            return $ret;
        }
         else
        {
            return false;
        }
    }

    private function parseExpression($prec)
    {
        $e = $this->parseElement();
        if ($e === false)
            return false;

        while (true)
        {
            $next = $this->peekNext();
            $oper_idx = $this->lookupBinaryOperator($next);
            if ($oper_idx === false)
                break;
            $oper = $this->operators[$oper_idx];
            if ($oper['prec'] < $prec)
                break;
            $this->consume();

            if ($oper['assoc'] == 'L')      // left associated operator
                $e2 = $this->parseExpression($oper['prec'] + 1);
            else if ($oper['assoc'] == 'R') // right associated operator
                $e2 = $this->parseExpression($oper['prec']);
            else
                return false;

            if (!$e2)
            {
                // error occurred parsing the right side
                return false;
            }

            $e1 = $e;
            $e = new ExprOperator;
            $e->name = $next;
            $e->unary = false;
            $e->index = $oper_idx;
            $e->params = array($e1, $e2);
            unset($e1);
            unset($e2);
        }

        return $e;
    }

    public function parse($s)
    {
        // input must be a string
        if (!is_string($s))
            return false;

        $this->expr = trim($s);
        $this->expr_len = strlen($this->expr);
        $this->off = 0;

/*
        $cnt = 0;
        while (true)
        {
            $next = $this->peekNext();
            if ($next === false)
                break;
            $this->consume();
            echo "Token $cnt: *$next*\n";
            ++$cnt;
        }
        $this->off = 0;
        die();
*/

        $ret = $this->parseExpression(0);

        // check for trailing tokens
        //if ($this->peekNext() !== false)
        if ($this->off < $this->expr_len)
        {
            // trailing tokens found
            return false;
        }

        return $ret;
    }
}
