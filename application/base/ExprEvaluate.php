<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams
 * Created:  2016-02-08
 *
 * @package flexio
 * @subpackage Base
 */


declare(strict_types=1);
namespace Flexio\Base;


class ExprEvaluate
{
    private const EPSILON = 0.000000000001;

    public $fields = [];
    public $parser = null;
    public $parse_result = null;
    public $data = [];

    public static function evaluate($expr, $data, $structure, &$retval)
    {
        $obj = new ExprEvaluate;
        $success = $obj->prepare($expr, $structure);
        if ($success === false)
            return false;

        return $obj->execute($data, $retval);

/*
TODO: remove deprecated implementation; following was split into two functions,
// prepare() and execute() to allow the expression to be parsed once and then
// evaluated again and again against new sets of data

        $obj->setData($data);
        $obj->setStructure($structure);
        if (!$obj->parse($expr))
            return false;


        $ret = $obj->doEval($obj->parse_result, $retval);
        if (!$ret)
            return false;

        $type = $obj->getType();
        if ($type == ExprParser::TYPE_DATE || $type == ExprParser::TYPE_DATETIME)
        {
            if ($retval)
                $retval = $retval->toString();
            return true;
        }

        return true;
*/
    }

    public static function getLastError()
    {
        return null;
    }

    public function prepare($expr, $structure)
    {
        // works in conjunction with execute to parse an expression
        // once and use it multiple times against different data
        // sets; equivalent to calling evaluate() with all the params,
        // but without the overhead of parsing each time data is set

        $this->setStructure($structure);
        $this->setData([]);

        $success = $this->parse($expr);
        if ($success === false)
            return false;

        return true;
    }

    public function execute($data, &$retval)
    {
        // works in conjunction with prepare to parse an expression
        // once and use it multiple times against different data
        // sets; equivalent to calling evaluate() with all the params,
        // but without the overhead of parsing each time data is set

        $retval = null;
        $this->setData($data);
        $ret = $this->doEval($this->parse_result, $retval);
        if (!$ret)
            return false;

        $type = $this->getType();
        if ($type == ExprParser::TYPE_DATE || $type == ExprParser::TYPE_DATETIME)
        {
            if ($retval)
                $retval = $retval->toString();
            return true;
        }

        return true;
    }

    public function setStructure($structure)
    {
        $this->fields = [];
        if (is_null($structure))
        {
            // generate default structure from data
            foreach ($this->data as $k => $v)
            {
                if (is_bool($v))
                    $type = 'boolean';
                else if (is_string($v))
                    $type = 'text';
                else if (is_numeric($v))
                    $type = 'double';
                else
                    $type = 'text';

                $this->fields[strtolower($k)] = [ 'name' => $k, 'type' => $type ];
            }
        }
         else
        {
            foreach ($structure as $fld)
            {
                if (isset($fld['name']))
                    $this->fields[strtolower($fld['name'])] = $fld;
            }
        }
    }

    public function setData($data)
    {
        $this->data = $data;
    }

    public function parse($expr)
    {
        $parser = new ExprParser;
        $parser->setOperators($this->operators);
        $this->parse_result = $parser->parse($expr);
        if (!$this->parse_result)
            return false;

        // check if the expression yields a bad type; this indicates
        // bad/unsupported parameter types in operators and functions
        if ($this->getType($this->parse_result) == ExprParser::TYPE_INVALID)
            return false;

        //$ret = $this->doEval($ret);
        //die($ret);

        return true;
    }

    public function getResult()
    {
        if (!$this->parse_result)
            return false;
        return $this->doEval($this->parse_result);
    }

    public function getType($node = null)
    {
        if (!$node)
            $node = $this->parse_result;
        if (isset($node->return_type))
            return $node->return_type;
        $node->return_type = $this->_getType($node);
        return $node->return_type;
    }

    public function getTypeAsString($node = null)
    {
        switch ($this->getType($node))
        {
            case ExprParser::TYPE_INVALID:   return 'invalid';
            case ExprParser::TYPE_UNDEFINED: return 'undefined';
            case ExprParser::TYPE_NULL:      return 'null';
            case ExprParser::TYPE_STRING:    return 'character';
            case ExprParser::TYPE_INTEGER:   return 'integer';
            case ExprParser::TYPE_FLOAT:     return 'double';
            case ExprParser::TYPE_BOOLEAN:   return 'boolean';
            case ExprParser::TYPE_DATE:      return 'date';
            case ExprParser::TYPE_DATETIME:  return 'datetime';
            default:                         return false;
        }

    }

    private function _getType($node)
    {
        $node_type = $node->getNodeType();

        if ($node_type == ExprParser::NODETYPE_VALUE)
        {
            if (is_string($node->val))
            {
                return ExprParser::TYPE_STRING;
            }
             else if (is_bool($node->val))
            {
                return ExprParser::TYPE_BOOLEAN;
            }
             else if (is_int($node->val))
            {
                return ExprParser::TYPE_INTEGER;
            }
             else if (is_float($node->val))
            {
                return ExprParser::TYPE_FLOAT;
            }
             else if (is_null($node->val))
            {
                return ExprParser::TYPE_NULL;
            }
             else if (is_a($node->val, '\Flexio\Base\ExprDateTime'))
            {
                if ($node->val->hasTimePart())
                    return ExprParser::TYPE_DATETIME;
                     else
                    return ExprParser::TYPE_DATE;
            }
             else
            {
                return ExprParser::TYPE_INVALID;
            }
        }
         else if ($node_type == ExprParser::NODETYPE_OPERATOR)
        {
            $oper = $this->operators[$node->index];
            $spec = $oper['types'];

            $param_types = [];

            $type = $this->getType($node->params[0]);
            if ($type === ExprParser::TYPE_INVALID)
                return $type;
            $param_types[] = $type;

            if (count($node->params) > 1)
            {
                $type = $this->getType($node->params[1]);
                if ($type === ExprParser::TYPE_INVALID)
                    return $type;
                $param_types[] = $type;
            }

            return $this->getReturnTypeFromSpec($spec, $param_types);
        }
         else if ($node_type == ExprParser::NODETYPE_FUNCTION)
        {
            $funcname = strtolower($node->name);
            if (!isset($this->functions[$funcname]))
                return ExprParser::TYPE_INVALID;

            $func = $this->functions[$funcname];

            $param_types = [];

            for ($i = 0; $i < count($node->params); ++$i)
            {
                $type = $this->getType($node->params[$i]);
                if ($type === ExprParser::TYPE_INVALID)
                    return $type;
                $param_types[] = $type;
            }

            return $this->getReturnTypeFromSpec($func['types'], $param_types);
        }
         else if ($node_type == ExprParser::NODETYPE_VARIABLE)
        {
            $key = strtolower($node->name);
            $type = null;

            if (isset($this->values[$key]['type']))
            {
                switch ($this->values[$key]['type'])
                {
                    case 's': return ExprParser::TYPE_STRING;
                    case 'f': return ExprParser::TYPE_FLOAT;
                    case 'd': return ExprParser::TYPE_DATE;
                    case 't': return ExprParser::TYPE_DATETIME;
                    case 'b': return ExprParser::TYPE_BOOLEAN;
                    case 'i': return ExprParser::TYPE_INTEGER;
                    default:
                };
            }
             else if (isset($this->fields[$key]['type']))
            {
                switch ($this->fields[$key]['type'])
                {
                    case 'text':
                    case 'character':
                    case 'widecharacter':
                        return ExprParser::TYPE_STRING;
                    case 'integer':
                        return ExprParser::TYPE_INTEGER;
                    case 'numeric':
                    case 'double':
                    case 'float':
                        return ExprParser::TYPE_FLOAT;
                    case 'date':
                        return ExprParser::TYPE_DATE;
                    case 'datetime':
                        return ExprParser::TYPE_DATETIME;
                    case 'boolean':
                        return ExprParser::TYPE_BOOLEAN;
                    default:
                        return ExprParser::TYPE_INVALID;
                }
            }
             else
            {
                return ExprParser::TYPE_INVALID;
            }
        }
         else
        {
            return false;
        }
    }

    public function doEval($node, &$retval)
    {
        $node_type = $node->getNodeType();

        if ($node_type == ExprParser::NODETYPE_VALUE)
        {
            if (is_float($node->val))
            {
                $retval = (float)$node->text;
            }
             else if (is_int($node->val))
            {
                $retval = (int)$node->text;
            }
             else
            {
                $retval = $node->val;
            }

            return true;
        }
         else if ($node_type == ExprParser::NODETYPE_OPERATOR)
        {
            $oper = $this->operators[$node->index];
            return call_user_func_array([ $this, $oper['func'] ], [ $oper, $node->params, &$retval ] );
        }
         else if ($node_type == ExprParser::NODETYPE_FUNCTION)
        {
            $funcname = strtolower($node->name);
            if (!isset($this->functions[$funcname]))
                return false; // function doesn't exist

            $func = $this->functions[$funcname];
            return call_user_func_array([ $this, $func['func'] ], [ $funcname, $node->params, &$retval ] );
        }
         else if ($node_type == ExprParser::NODETYPE_VARIABLE)
        {
            $key = strtolower($node->name);

            // look up variable to see if it's allowed; note: use
            // array_key_exists() instead of isset() since an data value
            // may exist that's set to null: [["f":"A"],["f":null],["f":"C"]]
            if (array_key_exists($key, $this->data))
            {
                $retval = $this->data[$key];
                return true;
            }
             else if (array_key_exists($key, $this->values))
            {
                if (isset($this->values[$key]['func']))
                {
                    // for pi and current_date
                    return call_user_func_array([ $this, $this->values[$key]['func'] ], [ '', [], &$retval ] );
                }
                 else
                {
                    // built-in values (used for cast function)
                    $retval = $this->values[$key]['name'];
                }
                return true;
            }
             else
            {
                return false;
            }

        }
         else
        {
            return false;
        }
    }

    private function getReturnTypeFromSpec($specs, $param_types)
    {
        foreach ($specs as $spec)
        {
            $spec = rtrim($spec, ')');
            $parts = explode('(', $spec);
            $flex_param = null;

            $min_param_count = strpos($parts[1], '[');
            if ($min_param_count === false)
            {
                $min_param_count = strlen($parts[1]);
                $max_param_count = $min_param_count;
            }
             else
            {
                $ellipsis = strpos($parts[1], '...');
                if (false === $ellipsis)
                {
                    $parts[1] = str_replace(array('[',']'), '', $parts[1]);
                    $max_param_count = strlen($parts[1]);
                }
                 else
                {
                    $flex_param = $parts[1][$ellipsis-1];
                    $parts[1] = str_replace(array('...','[',']'), '', $parts[1]);
                    $max_param_count = 999;
                }

            }
            $passed_param_count = count($param_types);

            if ($passed_param_count < $min_param_count || $passed_param_count > $max_param_count)
                continue;

            switch ($parts[0])
            {
                case 's':  $result = ExprParser::TYPE_STRING;   break;
                case 'f':  $result = ExprParser::TYPE_FLOAT;    break;
                case 'i':  $result = ExprParser::TYPE_INTEGER;  break;
                case 'b':  $result = ExprParser::TYPE_BOOLEAN;  break;
                case 'd':  $result = ExprParser::TYPE_DATE;     break;
                case 't':  $result = ExprParser::TYPE_DATETIME; break;
                case 'N':  $result = ExprParser::TYPE_NULL;     break;
                default:
                    return ExprParser::TYPE_INVALID;  // unknown return type
            }

            for ($i = 0; $i < $max_param_count; ++$i)
            {
                if ($i < strlen($parts[1]))
                    $ch = $parts[1][$i];
                     else
                    $ch = $flex_param;

                switch ($ch)
                {
                    case '*':  break;
                    case 's':  if ($param_types[$i] != ExprParser::TYPE_STRING)   $result = ExprParser::TYPE_INVALID;  break;
                    case 'f':  if ($param_types[$i] != ExprParser::TYPE_FLOAT)    $result = ExprParser::TYPE_INVALID;  break;
                    case 'i':  if ($param_types[$i] != ExprParser::TYPE_INTEGER)  $result = ExprParser::TYPE_INVALID;  break;
                    case 'n':  if ($param_types[$i] != ExprParser::TYPE_INTEGER &&
                                   $param_types[$i] != ExprParser::TYPE_FLOAT)    $result = ExprParser::TYPE_INVALID;  break;
                    case 'b':  if ($param_types[$i] != ExprParser::TYPE_BOOLEAN)  $result = ExprParser::TYPE_INVALID;  break;
                    case 'd':  if ($param_types[$i] != ExprParser::TYPE_DATE)     $result = ExprParser::TYPE_INVALID;  break;
                    case 't':  if ($param_types[$i] != ExprParser::TYPE_DATETIME) $result = ExprParser::TYPE_INVALID;  break;
                    case 'N':  if ($param_types[$i] != ExprParser::TYPE_NULL)     $result = ExprParser::TYPE_INVALID;  break;
                    default:
                        return ExprParser::TYPE_INVALID;  // unknown parameter type
                }

                if ($i+1 >= $passed_param_count)
                    break;
            }

            if ($result != ExprParser::TYPE_INVALID)
                return $result;
        }

        return ExprParser::TYPE_INVALID;
    }



    // s = string; i = integer; f = float; n = (any numeric); b = boolean; * = any type

    public $operators = [
        [ 'name' => '-',   'prec' => 9, 'unary' => true,                  'types' => [ 'f(f)', 'i(i)' ], 'func' => 'oper_unary_minus' ],
        [ 'name' => '+',   'prec' => 9, 'unary' => true,                  'types' => [ 'f(f)', 'i(i)' ], 'func' => 'oper_unary_plus' ],
        [ 'name' => 'not', 'prec' => 9, 'unary' => true,  'assoc' => 'R', 'types' => [ 'b(b)' ], 'func' => 'oper_not' ],
        [ 'name' => '!',   'prec' => 9, 'unary' => true,  'assoc' => 'R', 'types' => [ 'b(b)' ], 'func' => 'oper_not' ],
        [ 'name' => '*',   'prec' => 8, 'unary' => false, 'assoc' => 'L', 'types' => [ 'f(ff)', 'i(ii)', 'f(fi)', 'f(if)' ], 'func' => 'oper_mul' ],
        [ 'name' => '/',   'prec' => 8, 'unary' => false, 'assoc' => 'L', 'types' => [ 'f(ff)', 'i(ii)', 'f(fi)', 'f(if)' ], 'func' => 'oper_div' ],
        [ 'name' => '%',   'prec' => 8, 'unary' => false, 'assoc' => 'L', 'types' => [ 'f(ff)', 'i(ii)', 'f(fi)', 'f(if)' ], 'func' => 'oper_mod' ],
        [ 'name' => '-',   'prec' => 7, 'unary' => false, 'assoc' => 'L', 'types' => [ 'f(ff)', 'i(ii)', 'f(fi)', 'f(if)' ], 'func' => 'oper_sub' ],
        [ 'name' => '+',   'prec' => 7, 'unary' => false, 'assoc' => 'L', 'types' => [ 'f(ff)', 'i(ii)', 'f(fi)', 'f(if)' ], 'func' => 'oper_add' ],
        [ 'name' => '<',   'prec' => 6, 'unary' => false, 'assoc' => 'L', 'types' => [ 'b(ss)', 'b(nn)', 'b(bb)', 'b(dd)', 'b(tt)', 'b(ds)', 'b(ts)', 'b(sd)', 'b(st)' ], 'func' => 'oper_compare' ],
        [ 'name' => '<=',  'prec' => 6, 'unary' => false, 'assoc' => 'L', 'types' => [ 'b(ss)', 'b(nn)', 'b(bb)', 'b(dd)', 'b(tt)', 'b(ds)', 'b(ts)', 'b(sd)', 'b(st)' ], 'func' => 'oper_compare' ],
        [ 'name' => '>',   'prec' => 6, 'unary' => false, 'assoc' => 'L', 'types' => [ 'b(ss)', 'b(nn)', 'b(bb)', 'b(dd)', 'b(tt)', 'b(ds)', 'b(ts)', 'b(sd)', 'b(st)' ], 'func' => 'oper_compare' ],
        [ 'name' => '>=',  'prec' => 6, 'unary' => false, 'assoc' => 'L', 'types' => [ 'b(ss)', 'b(nn)', 'b(bb)', 'b(dd)', 'b(tt)', 'b(ds)', 'b(ts)', 'b(sd)', 'b(st)' ], 'func' => 'oper_compare' ],
        [ 'name' => '~',   'prec' => 5, 'unary' => false, 'assoc' => 'L', 'types' => [ 'b(ss)' ], 'func' => 'oper_regex' ],
        [ 'name' => '~*',  'prec' => 5, 'unary' => false, 'assoc' => 'L', 'types' => [ 'b(ss)' ], 'func' => 'oper_regex' ],
        [ 'name' => '!~',  'prec' => 5, 'unary' => false, 'assoc' => 'L', 'types' => [ 'b(ss)' ], 'func' => 'oper_regex' ],
        [ 'name' => '!~*', 'prec' => 5, 'unary' => false, 'assoc' => 'L', 'types' => [ 'b(ss)' ], 'func' => 'oper_regex' ],
        [ 'name' => '=',   'prec' => 5, 'unary' => false, 'assoc' => 'L', 'types' => [ 'b(ss)', 'b(nn)', 'b(bb)', 'b(dd)', 'b(tt)', 'b(ds)', 'b(ts)', 'b(sd)', 'b(st)' ], 'func' => 'oper_compare' ],
        [ 'name' => '!=',  'prec' => 5, 'unary' => false, 'assoc' => 'L', 'types' => [ 'b(ss)', 'b(nn)', 'b(bb)', 'b(dd)', 'b(tt)', 'b(ds)', 'b(ts)', 'b(sd)', 'b(st)' ], 'func' => 'oper_compare' ],
        [ 'name' => '<>',  'prec' => 5, 'unary' => false, 'assoc' => 'L', 'types' => [ 'b(ss)', 'b(nn)', 'b(bb)', 'b(dd)', 'b(tt)', 'b(ds)', 'b(ts)', 'b(sd)', 'b(st)' ], 'func' => 'oper_compare' ],
        [ 'name' => 'and', 'prec' => 4, 'unary' => false, 'assoc' => 'L', 'types' => [ 'b(bb)' ], 'func' => 'oper_and' ],
        [ 'name' => 'or',  'prec' => 3, 'unary' => false, 'assoc' => 'L', 'types' => [ 'b(bb)' ], 'func' => 'oper_or' ]
    ];

    public $functions = [
        'abs'          => [ 'types' => [ 'f(n)', 'f(s)', 'f(N)' ], 'func' => 'func_abs' ],
        'acos'         => [ 'types' => [ 'f(n)', 'f(s)', 'f(N)' ], 'func' => 'func_acos' ],
        'ascii'        => [ 'types' => [ 'i(s)', 'i(n)', 'i(b)', 'i(N)' ], 'func' => 'func_ascii' ],
        'asin'         => [ 'types' => [ 'f(n)', 'f(s)', 'f(N)' ], 'func' => 'func_asin' ],
        'atan'         => [ 'types' => [ 'f(n)', 'f(s)', 'f(N)' ], 'func' => 'func_atan' ],
        'cast'         => [ 'types' => [ 's(*s)', 'f(*f)', 'i(*i)', 'd(*d)', 't(*t)', 'b(*b)' ], 'func' => 'func_cast' ],
        'ceiling'      => [ 'types' => [ 'f(n)', 'f(s)', 'f(N)' ], 'func' => 'func_ceiling' ],
        'concat'       => [ 'types' => [ 's(*[*...])' ], 'func' => 'func_concat' ],
        'contains'     => [ 'types' => [ 'b(ss)', 'b(ns)', 'b(bs)', 'b(Ns)' ], 'func' => 'func_contains' ],
        'cos'          => [ 'types' => [ 'f(n)', 'f(s)', 'f(N)' ], 'func' => 'func_cos' ],
        'current_date' => [ 'types' => [ 'd()' ], 'func' => 'func_current_date' ],
        'day'          => [ 'types' => [ 'f(d)', 'f(t)', 'f(s)', 'f(N)' ], 'func' => 'func_day' ],
        'exp'          => [ 'types' => [ 'f(n)', 'f(s)', 'f(N)' ], 'func' => 'func_exp' ],
        'floor'        => [ 'types' => [ 'f(n)', 'f(s)', 'f(N)' ], 'func' => 'func_floor' ],
        'hour'         => [ 'types' => [ 'f(d)', 'f(t)', 'f(N)', 'f(s)' ], 'func' => 'func_hour' ],
        'if'           => [ 'types' => [ 's(bss)', 'f(bff)', 'i(bii)', 'f(bfi)', 'f(bif)', 'b(bbb)', 'd(bdd)', 't(btt)' ], 'func' => 'func_iif' ],
        'initcap'      => [ 'types' => [ 's(s)', 's(n)', 's(b)', 's(N)' ], 'func' => 'func_initcap' ],
        'iskindof'     => [ 'types' => [ 'b(*s)' ], 'func' => 'func_iskindof' ],
        'isnull'       => [ 'types' => [ 'b(*)' ], 'func' => 'func_isnull' ],
        'left'         => [ 'types' => [ 's(si)', 's(ni)', 's(bi)', 's(Ni)' ], 'func' => 'func_left' ],
        'length'       => [ 'types' => [ 'i(s)', 'i(n)', 'i(b)', 'i(N)' ], 'func' => 'func_length' ],
        'ln'           => [ 'types' => [ 'f(n)', 'f(s)', 'f(N)' ], 'func' => 'func_ln' ],
        'log'          => [ 'types' => [ 'f(n)', 'f(s)', 'f(N)' ], 'func' => 'func_log' ],
        'lower'        => [ 'types' => [ 's(s)', 's(n)', 's(b)', 's(N)' ], 'func' => 'func_lower' ],
        'lpad'         => [ 'types' => [ 's(si)', 's(sis)', 's(ni)', 's(nis)', 's(bi)', 's(bis)', 's(Ni)', 's(Nis)' ], 'func' => 'func_lpad' ],
        'ltrim'        => [ 'types' => [ 's(s)', 's(ss)', 's(n)', 's(ns)', 's(b)', 's(bs)', 's(N)', 's(Ns)' ], 'func' => 'func_ltrim' ],
        'md5'          => [ 'types' => [ 's(s)', 's(n)', 's(b)', 's(N)' ], 'func' => 'func_md5' ],
        'minute'       => [ 'types' => [ 'f(d)', 'f(t)', 'f(N)', 'f(s)' ], 'func' => 'func_minute' ],
        'mod'          => [ 'types' => [ 'f(nn)', 'f(ns)', 'f(nN)', 'f(sn)', 'f(ss)', 'f(sN)', 'f(Nn)', 'f(Ns)', 'f(NN)' ], 'func' => 'func_mod' ],
        'month'        => [ 'types' => [ 'f(d)', 'f(t)', 'f(N)', 'f(s)' ], 'func' => 'func_month' ],
        'now'          => [ 'types' => [ 't()' ], 'func' => 'func_now' ],
        'pi'           => [ 'types' => [ 'f()' ], 'func' => 'func_pi' ],
        'pow'          => [ 'types' => [ 'f(nn)', 'f(ns)', 'f(nN)', 'f(sn)', 'f(ss)', 'f(sN)', 'f(Nn)', 'f(Ns)', 'f(NN)' ], 'func' => 'func_pow' ],
        'regexp_replace'=>[ 'types' => [ 's(sss)', 's(ssss)', 's(nss)', 's(nsss)', 's(bss)', 's(bsss)', 's(Nss)', 's(Nsss)' ], 'func' => 'func_regexp_replace' ],
        'replace'      => [ 'types' => [ 's(sss)', 's(nss)', 's(bss)', 's(Nss)' ], 'func' => 'func_replace' ],
        'reverse'      => [ 'types' => [ 's(s)', 's(n)', 's(b)', 's(N)' ], 'func' => 'func_reverse' ],
        'right'        => [ 'types' => [ 's(si)', 's(ni)', 's(bi)', 's(Ni)' ], 'func' => 'func_right' ],
        'round'        => [ 'types' => [ 'f(n)', 'f(s)', 'f(N)', 'f(ni)', 'f(si)', 'f(Ni)' ], 'func' => 'func_round' ],
        'rpad'         => [ 'types' => [ 's(si)', 's(sis)', 's(ni)', 's(nis)', 's(bi)', 's(bis)', 's(Ni)', 's(Nis)' ], 'func' => 'func_rpad' ],
        'rtrim'        => [ 'types' => [ 's(s)', 's(ss)', 's(n)', 's(ns)', 's(b)', 's(bs)', 's(N)', 's(Ns)' ], 'func' => 'func_rtrim' ],
        'second'       => [ 'types' => [ 'f(d)', 'f(t)', 'f(N)', 'f(s)' ], 'func' => 'func_second' ],
        'sign'         => [ 'types' => [ 'f(n)', 'f(s)', 'f(N)' ], 'func' => 'func_sign' ],
        'sin'          => [ 'types' => [ 'f(n)', 'f(s)', 'f(N)' ], 'func' => 'func_sin' ],
        'strpart'      => [ 'types' => [ 's(*si)' ], 'func' => 'func_strpart' ],
        'strpos'       => [ 'types' => [ 'i(ss)', 'i(ns)', 'i(bs)', 'i(Ns)' ], 'func' => 'func_strpos' ],
        'substr'       => [ 'types' => [ 's(si[i])', 's(ni[i])', 's(bi[i])', 's(Ni[i])' ], 'func' => 'func_substr' ],
        'tan'          => [ 'types' => [ 'f(n)', 'f(s)', 'f(N)' ], 'func' => 'func_tan' ],
        'to_char'      => [ 'types' => [ 's(s)', 's(n)', 's(d)', 's(t)', 's(b)', 's(N)', 's(ns)', 's(ds)', 's(ts)', 's(Ns)' ], 'func' => 'func_to_char' ],
        'to_date'      => [ 'types' => [ 'd(s[s])', 'd(n[s])', 'd(d[s])', 'd(b[s])', 'd(N[s])' ], 'func' => 'func_to_date' ],
        'to_datetime'  => [ 'types' => [ 't(s[s])', 't(n[s])', 't(d[s])', 't(b[s])', 't(N[s])' ], 'func' => 'func_to_timestamp' ], // alias for to_timestamp
        'to_number'    => [ 'types' => [ 'f(s[s])', 'f(n[s])', 'f(d[s])', 'f(b[s])', 'f(N[s])' ], 'func' => 'func_to_number' ],
        'to_timestamp' => [ 'types' => [ 't(s[s])', 't(n[s])', 't(d[s])', 't(b[s])', 't(N[s])' ], 'func' => 'func_to_timestamp' ],
        'trim'         => [ 'types' => [ 's(s)', 's(ss)', 's(n)', 's(ns)', 's(b)', 's(bs)', 's(N)', 's(Ns)' ], 'func' => 'func_trim' ],
        'trunc'        => [ 'types' => [ 'f(n)', 'f(s)', 'f(N)' ], 'func' => 'func_trunc' ],
        'unix_timestamp'=>[ 'types' => [ 'i()', 'i(d)', 'i(t)', 'i(s)' ], 'func' => 'func_unix_timestamp' ],
        'upper'        => [ 'types' => [ 's(s)', 's(n)', 's(b)', 's(N)' ], 'func' => 'func_upper' ],
        'year'         => [ 'types' => [ 'f(d)', 'f(t)', 'f(N)', 'f(s)' ], 'func' => 'func_year' ],

        'text'         => [ 'types' => [ 's(i)' ] ],
        'character'    => [ 'types' => [ 's(i)' ] ],
        'numeric'      => [ 'types' => [ 'f(i)', 'f(ii)' ] ]
    ];


    public $values = [
        'current_date' => [ 'type' => 'd', 'func' => 'func_current_date' ],
        'pi'           => [ 'type' => 'f', 'func' => 'func_pi' ],

        'text'         => [ 'type' => 's', 'name' => 'text' ],
        'character'    => [ 'type' => 's', 'name' => 'character' ],
        'numeric'      => [ 'type' => 'f', 'name' => 'numeric' ],
        'double'       => [ 'type' => 'f', 'name' => 'double' ],
        'integer'      => [ 'type' => 'i', 'name' => 'integer' ],
        'boolean'      => [ 'type' => 'b', 'name' => 'boolean' ],
        'date'         => [ 'type' => 'd', 'name' => 'date' ],
        'datetime'     => [ 'type' => 't', 'name' => 'datetime' ]
    ];

    public function oper_and($oper, $params, &$retval)
    {
        if (!$this->doEval($params[0], $param0)) return false;
        if (!$this->doEval($params[1], $param1)) return false;
        $param0 = self::exprToBool($param0);
        $param1 = self::exprToBool($param1);
        $retval = ($param0 && $param1);
        return true;
    }

    public function oper_compare($oper, $params, &$retval)
    {
        if (!$this->doEval($params[0], $param0)) return false;
        if (!$this->doEval($params[1], $param1)) return false;

        if (is_string($param0) && is_string($param1))
        {
            if (isset($GLOBALS['g_expr_collator']))
            {
                $coll = $GLOBALS['g_expr_collator'];
            }
             else
            {
                $coll = $GLOBALS['g_expr_collator'] = collator_create('en_US');
            }

            switch ($oper['name'])
            {
                case '=':  $retval = ($param0 == $param1); return true;
                case '!=':
                case '<>': $retval = ($param0 != $param1); return true;
                case '<':  $retval = (collator_compare($coll, $param0, $param1) < 0);  return true;
                case '<=': $retval = (collator_compare($coll, $param0, $param1) <= 0); return true;
                case '>':  $retval = (collator_compare($coll, $param0, $param1) > 0);  return true;
                case '>=': $retval = (collator_compare($coll, $param0, $param1) >= 0); return true;
            }
        }
         else  if (is_double($param0) && is_double($param1))
        {
            switch ($oper['name'])
            {
                case '=':  $retval = (self::dblcompare($param0, $param1) == 0); return true;
                case '!=':
                case '<>': $retval = (self::dblcompare($param0, $param1) != 0); return true;
                case '<':  $retval = (self::dblcompare($param0, $param1) < 0); return true;
                case '<=': $retval = (self::dblcompare($param0, $param1) <= 0); return true;
                case '>':  $retval = (self::dblcompare($param0, $param1) > 0); return true;
                case '>=': $retval = (self::dblcompare($param0, $param1) >= 0); return true;
            }
        }
         else
        {
            switch ($oper['name'])
            {
                case '=': $retval = ($param0 ==  $param1); return true;
                case '!=':
                case '<>': $retval = ($param0 != $param1); return true;
                case '<':  $retval = ($param0 <  $param1); return true;
                case '<=': $retval = ($param0 <= $param1); return true;
                case '>':  $retval = ($param0 >  $param1); return true;
                case '>=': $retval = ($param0 >= $param1); return true;
            }
        }

        return false;
    }

    public function oper_add($oper, $params, &$retval)
    {
        if (!$this->doEval($params[0], $param0)) return false;
        if (!$this->doEval($params[1], $param1)) return false;
        $param0 = self::exprToNumber($param0);
        $param1 = self::exprToNumber($param1);
        $retval = ($param0 + $param1);
        return true;
    }

    public function oper_div($oper, $params, &$retval)
    {
        if (!$this->doEval($params[0], $param0)) return false;
        if (!$this->doEval($params[1], $param1)) return false;
        $param0 = self::exprToNumber($param0);
        $param1 = self::exprToNumber($param1);

        if ($param1 == 0)
        {
            $retval = null;
            return true;
        }

        $retval = ((double)$param0 / (double)$param1);
        return true;
    }

    public function oper_mod($oper, $params, &$retval)
    {
        if (!$this->doEval($params[0], $param0)) return false;
        if (!$this->doEval($params[1], $param1)) return false;
        $param0 = self::exprToNumber($param0);
        $param1 = self::exprToNumber($param1);

        if ($param1 == 0)
        {
            $retval = null;
            return true;
        }

        if (is_int($param0) && is_int($param1))
            $retval = ($param0 % $param1);
             else
            $retval = self::fmod_sql($param0, $param1);
        return true;
    }

    public function oper_mul($oper, $params, &$retval)
    {
        if (!$this->doEval($params[0], $param0)) return false;
        if (!$this->doEval($params[1], $param1)) return false;
        $param0 = self::exprToNumber($param0);
        $param1 = self::exprToNumber($param1);
        $retval = ($param0 * $param1);
        return true;
    }

    public function oper_not($oper, $params, &$retval)
    {
        if (!$this->doEval($params[0], $param0)) return false;
        $param0 = self::exprToBool($param0);
        $retval = ($param0 === false);
        return true;
    }

    public function oper_or($oper, $params, &$retval)
    {
        if (!$this->doEval($params[0], $param0)) return false;
        if (!$this->doEval($params[1], $param1)) return false;
        $param0 = self::exprToBool($param0);
        $param1 = self::exprToBool($param1);
        $retval = ($param0 || $param1);
        return true;
    }

    public function oper_regex($oper, $params, &$retval)
    {
        if (!$this->doEval($params[0], $param0)) return false;
        if (!$this->doEval($params[1], $param1)) return false;
        $param0 = self::exprToString($param0);
        $param1 = self::exprToString($param1);

        if (!isset($param1))
            return false;

        $subject = $param0;
        $pattern = '/' . str_replace('/','\/',$param1) . '/u'; // TODO: escape forward slashes?

        switch ($oper['name'])
        {
            // matches, case-sensitive
            case '~':
                $result = @preg_match($pattern, $subject);
                if ($result === false)
                    return false;
                $retval = $result === 1 ? true : false;
                return true;

            // matches, case-insensitive
            case '~*':
                $pattern .= 'i'; // add case-insensitive flag
                $result = @preg_match($pattern, $subject);
                if ($result === false)
                    return false;
                $retval = $result === 1 ? true : false;
                return true;

            // does not match, case-sensitive
            case '!~':
                $result = @preg_match($pattern, $subject);
                if ($result === false)
                    return false;
                $retval = $result === 0 ? true : false;
                return true;

            // does not match, case-insensitive
            case '!~*':
                $pattern .= 'i'; // add case-insensitive flag
                $result = @preg_match($pattern, $subject);
                if ($result === false)
                    return false;
                $retval = $result === 0 ? true : false;
                return true;
        }

        return false;
    }

    public function oper_sub($oper, $params, &$retval)
    {
        if (!$this->doEval($params[0], $param0)) return false;
        if (!$this->doEval($params[1], $param1)) return false;
        $param0 = self::exprToNumber($param0);
        $param1 = self::exprToNumber($param1);
        $retval = ($param0 - $param1);
        return true;
    }

    public function oper_unary_minus($oper, $params, &$retval)
    {
        if (!$this->doEval($params[0], $param0)) return false;
        $param0 = self::exprToNumber($param0);
        $retval = (-1 * $param0);
        return true;
    }

    public function oper_unary_plus($oper, $params, &$retval)
    {
        if (!$this->doEval($params[0], $param0)) return false;
        $param0 = self::exprToNumber($param0);
        $retval = $param0;
        return true;
    }

    public function func_abs($func, $params, &$retval)
    {
        if (!$this->doEval($params[0], $param0)) return false;
        if (is_null($param0))
            $retval = null;
             else
            $retval = abs(self::exprToNumber($param0));
        return true;
    }

    public function func_acos($func, $params, &$retval)
    {
        if (!$this->doEval($params[0], $param0)) return false;
        if (is_null($param0))
            $retval = null;
             else
            $retval = acos(self::exprToNumber($param0));
        return true;
    }

    public function func_ascii($func, $params, &$retval)
    {
        if (!$this->doEval($params[0], $param0)) return false;
        $param0 = self::exprToString($param0);
        if (strlen($param0) == 0)
            $retval = 0;
             else
            $retval = ord(iconv("UTF-8", "ISO-8859-1", mb_substr($param0,0,1)));
        return true;
    }

    public function func_asin($func, $params, &$retval)
    {
        if (!$this->doEval($params[0], $param0)) return false;
        if (is_null($param0))
            $retval = null;
             else
            $retval = asin(self::exprToNumber($param0));
        return true;
    }

    public function func_atan($func, $params, &$retval)
    {
        if (!$this->doEval($params[0], $param0)) return false;
        if (is_null($param0))
            $retval = null;
             else
            $retval = atan(self::exprToNumber($param0));
        return true;
    }

    public function func_cast($func, $params, &$retval)
    {
        if (!$this->doEval($params[0], $param0)) return false;
        //if (!$this->doEval($params[1], $param1)) return false;

        if ($params[1]->getNodeType() == ExprParser::NODETYPE_FUNCTION && ($params[1]->name == 'text' || $params[1]->name == 'character'))
        {
            $func = $params[1];
            if ($func->params[0]->getNodeType() != ExprParser::NODETYPE_VALUE)
                return false; // can't specify variable width
            $type = 'text';
            $width = $func->params[0]->val;
        }
         else if ($params[1]->getNodeType() == ExprParser::NODETYPE_FUNCTION && $params[1]->name == 'numeric')
        {
            $func = $params[1];
            if ($func->params[0]->getNodeType() != ExprParser::NODETYPE_VALUE)
                return false; // can't specify variable width
            $type = 'numeric';
            $width = $func->params[0]->val;
            if (count($func->params) > 1)
            {
                if ($func->params[1]->getNodeType() != ExprParser::NODETYPE_VALUE)
                    return false; // can't specify variable scale
                $scale = $func->params[1]->val;
            }
        }
         else if ($params[1]->getNodeType() == ExprParser::NODETYPE_VARIABLE)
        {
            $type = $params[1]->name;
            $width = null;
            $scale = null;
        }
         else
        {
            // must be one of text, numeric, numeric(x,y), etc
            return false;
        }

        switch ($type)
        {
            case 'text':
            case 'character':
                if (is_null($param0))
                {
                    $retval = null;
                    return true;
                }

                if (is_bool($param0))
                {
                    $retval = $param0 ? "true":"false";
                }
                else if ($param0 instanceof ExprDateTime)
                {
                    if ($param0->hasTimePart())
                    {
                        $retval = sprintf("%04d-%02d-%02d %02d:%02d:%02d+00", $param0->values['year'], $param0->values['month'], $param0->values['day'], $param0->values['hour'], $param0->values['minute'], $param0->values['second']);
                    }
                    else
                    {
                        $retval = sprintf("%04d-%02d-%02d", $param0->values['year'], $param0->values['month'], $param0->values['day']);
                    }
                }
                else
                {
                    $retval = is_null($param0) ? null : (string)$param0;
                }
                return true;

            case 'numeric':
            case 'double':
                if (is_null($param0))
                {
                    $retval = null;
                    return true;
                }
                else if ($param0 instanceof ExprDateTime)
                {
                    if ($param0->hasTimePart())
                    {
                        $retval = (float)sprintf("%04d%02d%02d%02d%02d%02d", $param0->values['year'], $param0->values['month'], $param0->values['day'], $param0->values['hour'], $param0->values['minute'], $param0->values['second']);
                    }
                    else
                    {
                        $retval = (float)sprintf("%04d%02d%02d", $param0->values['year'], $param0->values['month'], $param0->values['day']);
                    }
                }
                else
                {
                    if (isset($scale))
                    {
                        $retval = round(floatval(''.$param0),$scale);
                    }
                     else
                    {
                        $retval = floatval(''.$param0);
                    }
                }
                return true;

            case 'integer':
                if (is_null($param0))
                {
                    $retval = null;
                    return true;
                }
                else if ($param0 instanceof ExprDateTime)
                {
                    if ($param0->hasTimePart())
                    {
                        $retval = (float)sprintf("%04d%02d%02d%02d%02d%02d", $param0->values['year'], $param0->values['month'], $param0->values['day'], $param0->values['hour'], $param0->values['minute'], $param0->values['second']);
                    }
                    else
                    {
                        $retval = (float)sprintf("%04d%02d%02d", $param0->values['year'], $param0->values['month'], $param0->values['day']);
                    }
                }
                else if (is_bool($param0))
                {
                    $retval = $param0 ? 1 : 0;
                }
                else
                {
                    // cast to float for consistency with postgres engine
                    $retval = (float)intval(''.$param0);
                }
                return true;

            case 'boolean':
            {
                if (is_null($param0))
                {
                    $retval = null;
                    return true;
                }

                     if ($param0 === true)  { $retval = true;  return true; }
                else if ($param0 === false) { $retval = false; return true; }

                $param0 = strtoupper($param0);
                if ($param0 == '1' || $param0 == 'T' || $param0 == 'TRUE')
                    $retval = true;
                     else
                    $retval = false;
                return true;
            }

            case 'date':
            case 'datetime':
            {
                if (is_null($param0) || is_bool($param0))
                {
                    $retval = null;
                    return true;
                }

                $dt = new ExprDateTime();
                if (!$dt->parse($param0))
                {
                    $retval = null;
                    return true;
                }

                if ($type == 'date')
                    $dt->truncateTime();
                else
                    $dt->makeDateTime();

                $retval = $dt;
                return true;
            }

            default:
                // unknown type
                return false;
        }

        return false;
    }

    public function func_ceiling($func, $params, &$retval)
    {
        if (!$this->doEval($params[0], $param0)) return false;
        $retval = is_null($param0) ? null : ceil(self::exprToNumber($param0));
        return true;
    }

    public function func_concat($func, $params, &$retval)
    {
        $retval = '';
        foreach ($params as $p)
        {
            $evaluated_p = null;
            if (!$this->doEval($p, $evaluated_p))
                return false;

            if (!isset($evaluated_p))
            {
                $retval = null;
                break;
            }

            $retval .= self::exprToString($evaluated_p);
        }

        return true;
    }

    public function func_contains($func, $params, &$retval)
    {
        if (!$this->doEval($params[0], $param0)) return false;
        if (!$this->doEval($params[1], $param1)) return false;

        if (is_null($param0) || is_null($param1))
        {
            $retval = null;
            return true;
        }

        $param0 = self::exprToString($param0);
        $param1 = self::exprToString($param1);

        if (strlen($param1) === 0)
        {
            $retval = true;
            return true;
        }

        if (strlen($param0) === 0)
        {
            $retval = false;
            return true;
        }

        $retval = false;
        if (strlen($param1) > 0 && strpos($param0, $param1) !== false)
            $retval = true;

        return true;
    }

    public function func_cos($func, $params, &$retval)
    {
        if (!$this->doEval($params[0], $param0)) return false;
        $retval = is_null($param0) ? null : cos(self::exprToNumber($param0));
        return true;
    }

    public function func_current_date($func, $params, &$retval)
    {
        $e = new ExprDateTime();
        $e->setToCurrentDate();
        $retval = $e;
        return true;
    }

    public function func_day($func, $params, &$retval)
    {
        if (!$this->doEval($params[0], $param0)) return false;
        if (is_null($param0))
        {
            $retval = null;
            return true;
        }
        $param0 = self::exprToDate($param0);
        $retval = $param0 !== false ? (double)$param0->getDay() : null;
        return true;
    }

    public function func_exp($func, $params, &$retval)
    {
        if (!$this->doEval($params[0], $param0)) return false;
        $retval = is_null($param0) ? null : exp(self::exprToNumber($param0));
        return true;
    }

    public function func_floor($func, $params, &$retval)
    {
        if (!$this->doEval($params[0], $param0)) return false;
        $retval = is_null($param0) ? null : floor(self::exprToNumber($param0));
        return true;
    }

    public function func_hour($func, $params, &$retval)
    {
        if (!$this->doEval($params[0], $param0)) return false;
        if (is_null($param0))
        {
            $retval = null;
            return true;
        }
        $param0 = self::exprToDate($param0);
        if ($param0 === false)
        {
            $retval = null;
            return true;
        }
        $retval = (double)($param0->hasTimePart() ? $param0->getHour() : 0);
        return true;
    }

    public function func_iif($func, $params, &$retval)
    {
        if (!$this->doEval($params[0], $param0)) return false;
        if (!$this->doEval($params[1], $param1)) return false;
        if (!$this->doEval($params[2], $param2)) return false;
        $param0 = self::exprToBool($param0);
        if (is_double($param1) || is_double($param2))
            $retval = ($param0 === true ? (double)$param1 : (double)$param2);
             else
            $retval = ($param0 === true ? $param1 : $param2);
        return true;
    }

    public function func_initcap($func, $params, &$retval)
    {
        if (!$this->doEval($params[0], $param0)) return false;
        $retval = is_null($param0) ? null : self::mb_ucwords(self::exprToString($param0));
        return true;
    }

    public function func_iskindof($func, $params, &$retval)
    {
        if (!$this->doEval($params[0], $param0)) return false;
        if (!$this->doEval($params[1], $param1)) return false;

        if (is_null($param0) || is_null($param1))
        {
            $retval = null;
            return true;
        }

        $param0 = self::exprToString($param0);
        $param1 = self::exprToString($param1);

        switch ($param1)
        {
            // TODO: add other formats? namespace format parameters?

            default:
                $retval = false;
                return true;

            case 'email':
                $retval = \Flexio\Base\Util::isValidEmail($param0);
                break;

            case 'english':
                $result = @preg_match('/^[\\x00-\\x7F]*$/', $param0);
                if ($result === false)
                    return false;
                $retval = $result === 1 ? true : false;
                break;

            case 'url':
                $retval = \Flexio\Base\Util::isValidUrl($param0);
                break;
        }

        return true;
    }

    public function func_isnull($func, $params, &$retval)
    {
        if (!$this->doEval($params[0], $param0)) return false;
        $retval = !isset($param0);
        return true;
    }

    public function func_left($func, $params, &$retval)
    {
        if (!$this->doEval($params[0], $param0)) return false;
        if (!$this->doEval($params[1], $param1)) return false;
        if (is_null($param0) || is_null($param1))
        {
            $retval = null;
            return true;
        }
        $retval = is_null($param0) ? null : mb_substr(self::exprToString($param0), 0, self::exprToNumber($param1));
        return true;
    }

    public function func_length($func, $params, &$retval)
    {
        if (!$this->doEval($params[0], $param0)) return false;
        $retval = is_null($param0) ? null : mb_strlen(self::exprToString($param0));
        return true;
    }

    public function func_ln($func, $params, &$retval)
    {
        if (!$this->doEval($params[0], $param0)) return false;
        $retval = is_null($param0) ? null : log(self::exprToNumber($param0));
        return true;
    }

    public function func_log($func, $params, &$retval)
    {
        if (!$this->doEval($params[0], $param0)) return false;
        $retval = is_null($param0) ? null : log10(self::exprToNumber($param0));
        return true;
    }

    public function func_lower($func, $params, &$retval)
    {
        if (!$this->doEval($params[0], $param0)) return false;
        $retval = is_null($param0) ? null : mb_strtolower(self::exprToString($param0));
        return true;
    }

    public function func_lpad($func, $params, &$retval)
    {
        $default_pad_char = ' ';

        if (count($params) < 3)
        {
            if (!$this->doEval($params[0], $param0)) return false;
	        if (!$this->doEval($params[1], $param1)) return false;
            if (is_null($param0) || is_null($param1))
            {
                $retval = null;
                return true;
            }
            $retval = self::mb_str_pad(self::exprToString($param0), self::exprToNumber($param1), $default_pad_char, STR_PAD_LEFT);
        }
        if (count($params) >= 3)
        {
            if (!$this->doEval($params[0], $param0)) return false;
	        if (!$this->doEval($params[1], $param1)) return false;
	        if (!$this->doEval($params[2], $param2)) return false;
            if (is_null($param0) || is_null($param1) || is_null($param2))
            {
                $retval = null;
                return true;
            }
            $retval = self::mb_str_pad(self::exprToString($param0), self::exprToNumber($param1), self::exprToString($param2), STR_PAD_LEFT);
        }

        return true;
    }

    public function func_ltrim($func, $params, &$retval)
    {
        if (count($params) < 2)
        {
            if (!$this->doEval($params[0], $param0)) return false;
            $retval = is_null($param0) ? null : ltrim(self::exprToString($param0));
        }
        if (count($params) >= 2)
        {
            if (!$this->doEval($params[0], $param0)) return false;
	        if (!$this->doEval($params[1], $param1)) return false;
            $retval = is_null($param0) ? null : self::mb_ltrim(self::exprToString($param0), self::exprToString($param1));
        }

        return true;
    }

    public function func_md5($func, $params, &$retval)
    {
        if (!$this->doEval($params[0], $param0)) return false;
        $retval = is_null($param0) ? null : md5(self::exprToString($param0));
        return true;
    }

    public function func_minute($func, $params, &$retval)
    {
        if (!$this->doEval($params[0], $param0)) return false;
        if (is_null($param0))
        {
            $retval = null;
            return true;
        }
        $param0 = self::exprToDate($param0);
        if ($param0 === false)
        {
            $retval = null;
            return true;
        }
        $retval = (double)($param0->hasTimePart() ? $param0->getMinute() : 0);
        return true;
    }

    public function func_mod($func, $params, &$retval)
    {
        if (!$this->doEval($params[0], $param0)) return false;
        if (!$this->doEval($params[1], $param1)) return false;

        if (is_null($param0) || is_null($param1))
        {
            $retval = null;
            return true;
        }

        if ($param1 == 0)
        {
            $retval = null;
            return true;
        }

        if (is_int($param0+0) && is_int($param1+0))
            $retval = ($param0 % $param1);
             else
            $retval = self::fmod_sql(self::exprToNumber($param0), self::exprToNumber($param1));
        return true;
    }

    public function func_month($func, $params, &$retval)
    {
        if (!$this->doEval($params[0], $param0)) return false;
        if (is_null($param0))
        {
            $retval = null;
            return true;
        }
        $param0 = self::exprToDate($param0);
        $retval = $param0 !== false ? (double)$param0->getMonth() : null;
        return true;
    }

    public function func_now($func, $params, &$retval)
    {
        $e = new ExprDateTime();
        $e->setToCurrentDateTime();
        $retval = $e;
        return true;
    }

    public function func_pi($func, $params, &$retval)
    {
        $retval = pi();
        return true;
    }

    public function func_pow($func, $params, &$retval)
    {
        if (!$this->doEval($params[0], $param0)) return false;
        if (!$this->doEval($params[1], $param1)) return false;
        if (is_null($param0) || is_null($param1))
        {
            $retval = null;
            return true;
        }
        $retval = pow((double)self::exprToNumber($param0), (double)self::exprToNumber($param1));
        return true;
    }

    public function func_regexp_replace($func, $params, &$retval)
    {
        if (count($params) < 3 || count($params) > 4)
            return false;

        if (!$this->doEval($params[0], $param0)) return false;
        if (!$this->doEval($params[1], $param1)) return false;
        if (!$this->doEval($params[2], $param2)) return false;

        $param3 = null;
        if (count($params) === 4)
            if (!$this->doEval($params[3], $param3)) return false;

        $subject_str = self::exprToString($param0);
        $pattern_str = self::exprToString($param1);
        $replace_str = self::exprToString($param2);
        $modifier_str = is_null($param3) ? false : self::exprToString($param3);

        if (is_null($subject_str))
        {
            $retval = null;
            return true;
        }

        $items_to_replace = 1; // by default only place one item
        $case_insensitive_match = false; // by default match on case

        // if we have a modifier string, set the appropriate match modifiers;
        // note: take the modifiers as they come; modifiers occuring later in the
        // string supercede modifiers specified earlier (e.g. "ic" will match on
        // case because the "c" modifier", which denotes matching on case, comes
        // after the "i" modifier, which denotes a case insensitive match)

        if ($modifier_str !== false)
        {
            $modifiers = str_split($modifier_str);
            foreach ($modifiers as $m)
            {
                if ($m === 'g')
                    $items_to_replace = -1; // replace all matched items

                if ($m === 'i')
                    $case_insensitive_match = true;

                if ($m === 'c')
                    $case_insensitive_match = false;
            }
        }

        $pattern_str = '/' . str_replace('/','\/',$pattern_str) . '/' . ($case_insensitive_match === true ? 'i' : '');
        $retval = @preg_replace($pattern_str, $replace_str, $subject_str, $items_to_replace);

        return true;
    }

    public function func_replace($func, $params, &$retval)
    {
        if (!$this->doEval($params[0], $param0)) return false;
        if (!$this->doEval($params[1], $param1)) return false;
        if (!$this->doEval($params[2], $param2)) return false;
        $retval = is_null($param0) ? null : str_replace(self::exprToString($param1), self::exprToString($param2), self::exprToString($param0));
        return true;
    }

    public function func_reverse($func, $params, &$retval)
    {
        if (!$this->doEval($params[0], $param0)) return false;
        $retval = is_null($param0) ? null : self::mb_strrev(self::exprToString($param0));
        return true;
    }

    public function func_right($func, $params, &$retval)
    {
        if (!$this->doEval($params[0], $param0)) return false;
        if (!$this->doEval($params[1], $param1)) return false;
        $param0 = self::exprToString($param0);
        $param1 = self::exprToNumber($param1);
        if (is_null($param0) || is_null($param1))
        {
            $retval = null;
            return true;
        }
        if ($param1 < 0)
        {
            $retval = mb_substr($param0, abs($param1));
        }
         else
        {
            $retval = mb_substr($param0, max(mb_strlen($param0)-$param1,0), $param1);
        }
        return true;
    }

    public function func_round($func, $params, &$retval)
    {
        if (count($params) < 2)
        {
            if (!$this->doEval($params[0], $param0)) return false;
            $retval = is_null($param0) ? null : round(self::exprToNumber($param0), 0);
        }
        if (count($params) >= 2)
        {
            if (!$this->doEval($params[0], $param0)) return false;
            if (!$this->doEval($params[1], $param1)) return false;
            $retval = is_null($param0) ? null : round(self::exprToNumber($param0), self::exprToNumber($param1));
        }

        return true;
    }

    public function func_rpad($func, $params, &$retval)
    {
        $default_pad_char = ' ';

        if (count($params) < 3)
        {
            if (!$this->doEval($params[0], $param0)) return false;
	        if (!$this->doEval($params[1], $param1)) return false;
            if (is_null($param0) || is_null($param1))
            {
                $retval = null;
                return true;
            }
            $retval = self::mb_str_pad(self::exprToString($param0), self::exprToNumber($param1), $default_pad_char, STR_PAD_RIGHT);
        }
        if (count($params) >= 3)
        {
            if (!$this->doEval($params[0], $param0)) return false;
	        if (!$this->doEval($params[1], $param1)) return false;
	        if (!$this->doEval($params[2], $param2)) return false;
            if (is_null($param0) || is_null($param1) || is_null($param2))
            {
                $retval = null;
                return true;
            }
            $retval = self::mb_str_pad(self::exprToString($param0), self::exprToNumber($param1), self::exprToString($param2), STR_PAD_RIGHT);
        }

        return true;


/*
        $default_pad_char = ' ';

        if (count($params) < 3)
        {
            if (!$this->doEval($params[0], $param0)) return false;
	        if (!$this->doEval($params[1], $param1)) return false;
            if (is_null($param0) || is_null($param1))
            {
                $retval = null;
                return true;
            }
            $retval = self::mb_str_pad(self::exprToString($param0), self::exprToNumber($param1), $default_pad_char, STR_PAD_LEFT);
        }
        if (count($params) >= 3)
        {
            if (!$this->doEval($params[0], $param0)) return false;
	        if (!$this->doEval($params[1], $param1)) return false;
	        if (!$this->doEval($params[2], $param2)) return false;
            if (is_null($param0) || is_null($param1) || is_null($param2))
            {
                $retval = null;
                return true;
            }
            $retval = self::mb_str_pad(self::exprToString($param0), self::exprToNumber($param1), self::exprToString($param2), STR_PAD_LEFT);
        }

        return true;

*/


    }

    public function func_rtrim($func, $params, &$retval)
    {
        if (count($params) < 2)
        {
            if (!$this->doEval($params[0], $param0)) return false;
            $retval = is_null($param0) ? null : rtrim(self::exprToString($param0));
        }
        if (count($params) >= 2)
        {
            if (!$this->doEval($params[0], $param0)) return false;
	        if (!$this->doEval($params[1], $param1)) return false;
            $retval = is_null($param0) ? null : self::mb_rtrim(self::exprToString($param0), self::exprToString($param1));
        }

        return true;
    }

    public function func_second($func, $params, &$retval)
    {
        if (!$this->doEval($params[0], $param0)) return false;
        if (is_null($param0))
        {
            $retval = null;
            return true;
        }
        $param0 = self::exprToDate($param0);
        if ($param0 === false)
        {
            $retval = null;
            return true;
        }
        $retval = (double)($param0->hasTimePart() ? $param0->getSecond() : 0);
        return true;
    }

    public function func_sign($func, $params, &$retval)
    {
        if (!$this->doEval($params[0], $param0)) return false;

        if (is_null($param0))
        {
            $retval = null;
            return true;
        }

        $retval = (float)0;
        if (self::exprToNumber($param0) > 0)
            $retval = (float)1;
        if (self::exprToNumber($param0) < 0)
            $retval = (float)-1;

        return true;
    }

    public function func_sin($func, $params, &$retval)
    {
        if (!$this->doEval($params[0], $param0)) return false;
        $retval = is_null($param0) ? null : sin(self::exprToNumber($param0));
        return true;
    }

    public function func_strpart($func, $params, &$retval)
    {
        if (!$this->doEval($params[0], $param0)) return false;
        if (!$this->doEval($params[1], $param1)) return false;
        if (!$this->doEval($params[2], $param2)) return false;

        if (is_null($param0))
        {
            $retval = null;
            return true;
        }

        $param0 = self::exprToString($param0);
        $param1 = self::exprToString($param1);
        $param2 = self::exprToNumber($param2);

        $retval = '';

        if (strlen($param1) === 0)
        {
            // with an empty delimiter, the first indexed element is empty
            // and the second indexed element is the whole string
            if ($param2 === 1)
                $retval = $param0;
        }

        if (strlen($param1) > 0)
        {
            $parts = explode($param1, $param0);
            if (isset($parts[$param2-1]))
                $retval = $parts[$param2-1];
        }

        return true;
    }

    public function func_strpos($func, $params, &$retval)
    {
        if (!$this->doEval($params[0], $param0)) return false;
        if (!$this->doEval($params[1], $param1)) return false;

        if (is_null($param0) || is_null($param1))
        {
            $retval = null;
            return true;
        }

        $param0 = self::exprToString($param0);
        $param1 = self::exprToString($param1);

        if (strlen($param1) === 0)
        {
            $retval = 1;
            return true;
        }

        if (strlen($param0) === 0)
        {
            $retval = 0;
            return true;
        }

        $retval = 0;
        $result = mb_strpos($param0, $param1);
        if ($result !== false)
            $retval = $result + 1;

        return true;
    }

    public function func_substr($func, $params, &$retval)
    {
        if (count($params) < 3)
        {
            if (!$this->doEval($params[0], $param0)) return false;
            if (!$this->doEval($params[1], $param1)) return false;
            $param0 = self::exprToString($param0);
            $param1 = self::exprToNumber($param1);
            if ($param1 <= 0) $param1 = 1;
            $retval = is_null($param0) ? null : mb_substr($param0, $param1 - 1);
        }
        if (count($params) >= 3)
        {
            if (!$this->doEval($params[0], $param0)) return false;
            if (!$this->doEval($params[1], $param1)) return false;
            if (!$this->doEval($params[2], $param2)) return false;
            $param0 = self::exprToString($param0);
            $param1 = self::exprToNumber($param1);
            $param2 = self::exprToNumber($param2);
            if ($param2 <= 0)
            {
                $retval = '';
                return true;
            }
            if ($param1 <= 0)
            {
                $param2 -= (1 - $param1);
                $param1 = 1;
            }
            $retval = is_null($param0) ? null : mb_substr($param0, $param1 - 1, $param2);
        }

        return true;
    }

    public function func_tan($func, $params, &$retval)
    {
        if (!$this->doEval($params[0], $param0)) return false;
        $retval = is_null($param0) ? null : tan(self::exprToNumber($param0));
        return true;
    }


    private static function getDayOfWeek($year, $month, $day) // returns 0 as sunday, 1 as monday...
    {
        $a = intdiv(14-$month, 12);
        $y = $year - $a;
        $m = $month + (12*$a) - 2;
        return (($day + $y + intdiv($y,4) - intdiv($y,100) + intdiv($y,400) + intdiv(31*$m,12)) % 7);
    }

    private static function isFormatString($format, $idx, $str, $ignore_case = false)
    {
        $substr = substr($format, $idx, strlen($str));
        if ($ignore_case)
        {
            $substr = strtolower($substr);
            $str = strtolower($str);
        }
        return ($substr == $str);
    }

    private static function tochar_date($dt, $format)
    {
        $res = '';
        $padding = true;

        // if we have a null date, return an empty string
        if ($dt->isNull())
        {
            return "";
        }
/*
        struct tm tm;
        $tm_sec = $dt->getSecond();
        $tm_min = $dt->getMinute();
        $tm_hour = $dt->getHour();
        $tm_mday = $dt->getDay();
        $tm_mon = $dt->getMonth()-1;
        $tm_year = $dt->getYear()-1900;
        $tm_wday = getDayOfWeek($dt->getYear(), $dt->getMonth(), $dt->getDay());
*/


        $format_length = strlen($format);
        $f = 0;
        while ($f < $format_length)
        {
            $ch = $format[$f];
            $nextch = ($f+1 < $format_length ? $format[$f+1] : '');

            if (self::isFormatString($format, $f, "FM", true))
            {
                // turn $padding off
                $padding = false;
                $f += 2;
            }
            else if (self::isFormatString($format, $f, "SYYYY"))
            {
                if ($padding)
                    $res .= sprintf("%c%04d", $dt->getYear() < 0 ? '-':' ', abs($dt->getYear()));
                     else
                    $res .= sprintf("%04d",  abs($dt->getYear()));
                $f += 5;
            }
            else if (self::isFormatString($format, $f, "YYYY"))
            {
                $buf = sprintf("%04d", abs($dt->getYear()));
                $res .= $buf;
                $f += 4;
            }
            else if (self::isFormatString($format, $f, "YYY"))
            {
                $buf = sprintf("%03d", $dt->getYear() % 1000);
                $res .= $buf;
                $f += 3;
            }
            else if (self::isFormatString($format, $f, "YY"))
            {
                $buf = sprintf("%02d", $dt->getYear() % 100);

                $res .= $buf;
                $f += 2;
            }
            else if (self::isFormatString($format, $f, "Y"))
            {
                $buf = sprintf("%d", $dt->getYear() % 10);
                $res .= $buf;
                $f += 1;
            }
            else if (self::isFormatString($format, $f, "MM"))
            {
                $buf = sprintf("%02d", $dt->getMonth());
                $res .= $buf;
                $f += 2;
            }
            else if (self::isFormatString($format, $f, "DDTH", true))
            {
                $dd = $dt->getDay();
                $buf = sprintf("%02d", $dd);

                if (($dd >= 1 && $dd <= 3) ||
                    ($dd >= 21 && $dd <= 23) ||
                    ($dd > 30))
                {
                    switch ($dd % 10)
                    {
                        case 1: $buf .= "ST"; break;
                        case 2: $buf .= "ND"; break;
                        case 3: $buf .= "RD"; break;
                        default: $buf .= "TH"; break;
                    }
                }
                else
                {
                    $buf .= "TH";
                }

                if ($format[$f] == 'd')
                    $buf = strtolower($buf);

                $res .= $buf;
                $f += 4;
            }
            else if (self::isFormatString($format, $f, "DD", true))
            {
                $res .= sprintf("%02d", $dt->getDay());
                $f += 2;
            }
            else if (self::isFormatString($format, $f, "DY", true) || self::isFormatString($format, $f, "DAY", true))
            {
                $offset = 0;

                if (self::isFormatString($format, $f, "DAY", true))
                {
                    $buf = strftime("%A", strtotime(sprintf("%04d-%02d-%02d", $dt->getYear(), $dt->getMonth(), $dt->getDay())));
                    $buf = strtoupper($buf);
                    if ($padding)
                    {
                        //$max_len = self::getMaxLocaleWeekdayLength();
                        $max_len = 9;
                        while (strlen($buf) < $max_len)
                            $buf .= ' ';
                    }
                    $offset = 3;
                }
                else
                {
                    $buf = strftime("%a", strtotime(sprintf("%04d-%02d-%02d", $dt->getYear(), $dt->getMonth(), $dt->getDay())));
                    $buf = strtoupper($buf);
                    $offset = 2;
                }

                if ($ch == 'd')
                {
                    // "dy"/"day"
                    $buf = strtolower($buf);
                }
                else
                {
                    if ($nextch == 'a' || $nextch == 'y')
                    {
                        // "Month"
                        $buf = strtolower($buf);
                        $buf[0] = strtoupper($buf[0]);
                    }
                }

                $res .= $buf;
                $f += $offset;
            }
            else if (self::isFormatString($format, $f, "D", true))
            {
                $res .= sprintf("%d", self::getDayOfWeek($dt->getYear(), $dt->getMonth(), $dt->getDay()) + 1);
                $f += 1;
            }
            else if (self::isFormatString($format, $f, "MON", true) || self::isFormatString($format, $f, "MONTH", true))
            {
                $offset = 0;

                if (self::isFormatString($format, $f, "MONTH", true))
                {
                    $buf = strftime("%B", strtotime(sprintf("%04d-%02d-%02d", $dt->getYear(), $dt->getMonth(), $dt->getDay())));
                    $buf = strtoupper($buf);
                    if ($padding)
                    {
                        //$max_len = self::getMaxLocaleMonthLength();
                        $max_len = 9;
                        while (strlen($buf) < $max_len)
                            $buf .= ' ';
                    }
                    $offset = 5;
                }
                else
                {
                    $buf = strftime("%b", strtotime(sprintf("%04d-%02d-%02d", $dt->getYear(), $dt->getMonth(), $dt->getDay())));
                    $buf = strtoupper($buf);
                    $offset = 3;
                }

                if ($ch == 'm')
                {
                    // "month"
                    $buf = strtolower($buf);
                }
                else
                {
                    if ($nextch == 'o')
                    {
                        // "Month"
                        $buf = strtolower($buf);
                        $buf[0] = strtoupper($buf[0]);
                    }
                }

                $res .= $buf;
                $f += $offset;
            }
            else if (self::isFormatString($format, $f, "HH24", true))
            {
                $res .= sprintf("%02d", $dt->getHour());
                $f += 4;
            }
            else if (self::isFormatString($format, $f, "HH12", true))
            {
                $hh = $dt->getHour();
                if ($hh > 12)
                    $hh -= 12;
                $res .= sprintf("%02d", $hh);
                $f += 4;
            }
            else if (self::isFormatString($format, $f, "HH", true))
            {
                $hh = $dt->getHour();
                if ($hh > 12)
                    $hh -= 12;
                $res .= sprintf("%02d", $hh);
                $f += 2;
            }
            else if (self::isFormatString($format, $f, "MI", true))
            {
                $res .= sprintf("%02d", $dt->getMinute());
                $f += 2;
            }
            else if (self::isFormatString($format, $f, "SS", true))
            {
                $res .= sprintf("%02d", $dt->getSecond());
                $f += 2;
            }
            else if (self::isFormatString($format, $f, "PM", true) || self::isFormatString($format, $f, "AM", true))
            {
                if (strtolower($ch) != $ch)
                {
                    // upper case
                    if ($dt->getHour() > 12)
                        $res .= "PM";
                        else
                        $res .= "AM";
                }
                else
                {
                    // upper case
                    if ($dt->getHour() > 12)
                        $res .= "pm";
                        else
                        $res .= "am";
                }
                $f += 2;
            }
            else if (self::isFormatString($format, $f, "P.M.", true) || self::isFormatString($format, $f, "A.M.", true))
            {
                if (strtolower($ch) != $ch)
                {
                    // upper case
                    if ($dt->getHour() > 12)
                        $res .= "P.M.";
                        else
                        $res .= "A.M.";
                }
                else
                {
                    // upper case
                    if ($dt->getHour() > 12)
                        $res .= "p.m.";
                        else
                        $res .= "a.m.";
                }
                $f += 4;
            }
            else if (self::isFormatString($format, $f, "RM", true))
            {
                switch ($dt->getMonth())
                {
                    default:
                    case  1: $buf = "I";    break;
                    case  2: $buf = "II";   break;
                    case  3: $buf = "III";  break;
                    case  4: $buf = "IV";   break;
                    case  5: $buf = "V";    break;
                    case  6: $buf = "VI";   break;
                    case  7: $buf = "VI";   break;
                    case  8: $buf = "VIII"; break;
                    case  9: $buf = "IX";   break;
                    case 10: $buf = "X";    break;
                    case 11: $buf = "XI";   break;
                    case 12: $buf = "XII";  break;
                }

                if ($padding)
                {
                    while (strlen($buf) < 4)
                        $buf .= ' ';
                }

                if ($ch == 'r')
                {
                    $buf = strtolower($buf);
                }
                else
                {
                    if ($nextch == 'm')
                    {
                        // Rm
                        $buf = strtolower($buf);
                        $buf[0] = strtoupper($buf[0]);
                    }
                }

                $res .= $buf;
                $f += 2;
            }
            else
            {
                if ($format[$f] == '"')
                {
                    $f++;
                    while ($f < $format_length)
                    {
                        if ($format[$f] == '"')
                        {
                            ++$f;
                            if ($format[$f] == '"')
                                $res .= '"';
                                else
                                break;
                        }
                        else
                        {
                            $res .= $format[$f];
                        }

                        ++$f;
                    }
                }
                else
                {
                    if (!ctype_alpha($format[$f]))
                    {
                        $res .= $format[$f];
                        $f++;
                    }
                    else
                    {
                        // invalid character
                        return '';
                    }
                }
            }

        }


        return $res;
    }

    private static function tochar_number_toroman($number)
    {
        $lookup = array('M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400, 'C' => 100,
                        'XC' => 90, 'L' => 50, 'XL' => 40, 'X' => 10,
                        'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1);

        $result = '';
        $number = intval($number);

        foreach ($lookup as $roman => $value)
        {
            $matches = intval($number/$value);
            $result .= str_repeat($roman,$matches);
            $number = $number % $value;
        }

        return $result;
    }

    private static function tochar_number($number, $format)
    {
        $is_negative = false;
        $format_has_decimal = false;
        $left_format_digits = 0;  // number of left digits in the format string
        $right_format_digits = 0; // number of right digits in the format string
        $total_left_digits = 0;   // number of left digits in the number
        $total_right_digits = 0;  // number of right digits in the number
        $zero_left_digits = 0;    // number of left digits in the number
        $zero_right_digits = 0;   // number of right digits in the number
        $currency_sign = '';
        $sign_left = true;        // sign is on the left side
        $sign_always = false;
        $pr = false;
        $eeee = false;
        $padding = true;          // true if padding is active
        $digit_encountered = false;
        $digit_printed = false;
        $padlen = 1;              // reserve one char for sign +/-
        $overflow = false;

        // count the number of positions to the left
        // of the decimal in the format string
        $format_len = strlen($format);
        if ($format_len == 0)
            return '';

        $p = $format;
        $left = true;
        for ($p = 0; $p < $format_len; ++$p)
        {
            $ch = $format[$p];
            if ($p+1 < $format_len)
                $ch_next = $format[$p+1];
                 else
                $ch_next = null;

            if ($ch == '.' || $ch == 'D' || $ch == 'd')
            {
                $left = false;
                $format_has_decimal = true;
                $padlen++;
            }
            if ($ch == ',' || $ch == 'G' || $ch == 'g')
            {
                $padlen++;
            }
            if ($ch == ' ')
            {
                $padlen++;
            }
            if ($ch == '$' || $ch == 'L' || $ch == 8364 /* euro symbol */)
            {
                $padlen++;
            }
            if ($ch == '9' || $ch == '0')
            {
                $digit_encountered = true;
                if ($left)
                    $left_format_digits++;
                    else
                    $right_format_digits++;
                $padlen++;
            }
            if ($ch == 'S' || $ch == 's')
            {
                $sign_always = true;
                $sign_encountered_at = $left_format_digits;
            }
            if (($ch == 'M' || $ch == 'm') && ($ch_next == 'I' || $ch_next == 'i'))
            {
                $sign_left = null; // minus sign will be placed whereever MI is
                $p++;
                continue;
            }
            if (($ch == 'P' || $ch == 'p') && ($ch_next == 'L' || $ch_next == 'l'))
            {
                //$sign_left = null; // plus sign will be placed whereever PL is
                $padlen++;
                $p++;
                continue;
            }
            if (($ch == 'P' || $ch == 'p') && ($ch_next == 'R' || $ch_next == 'r'))
            {
                // angle brackets for negative numbers
                $sign_left = null;
                $pr = true;
                $p++;
                continue;
            }
            if (($ch == 'R' || $ch == 'r') && ($ch_next == 'N' || $ch_next == 'n'))
            {
                // roman numerals
                $number = intval($number);
                if ($number < 1 || $number > 3999)
                    return '###############';
                return str_pad(self::tochar_number_toroman($number), 15, ' ', STR_PAD_LEFT);
            }
            if (($ch == 'E' || $ch == 'e') && strtoupper(substr($format, $p, 4)) == 'EEEE')
            {
                $eeee = true;
                $p += 3;
            }
            if ($left)
            {
                if ($ch == '0')
                    $zero_left_digits++;
                if ($ch == '9' && $zero_left_digits > 0)
                    $zero_left_digits++;
            }
            else
            {
                if ($ch == '0')
                    $zero_right_digits = $right_format_digits;
            }
        }



        if (isset($sign_encountered_at) && $sign_encountered_at >= $left_format_digits)
        {
            $sign_left = false;
        }


        if ($right_format_digits == 0 && $format_has_decimal)
        {
            // no digits in format to the right of decimal, ignore decimal in fmt
            $padlen--;
        }


        if ($eeee)
        {
            $dec = $right_format_digits;
            $s = sprintf("%.{$dec}e", round($number, $dec+1));
            $s = str_replace(',', '.', $s);
            $epos = strpos($s, 'e');
            if ($epos === false)
                return '###';
            $exp = intval(substr($s, $epos+1));
            $s = substr($s, 0, $epos);
            $s .= sprintf('e%s%02d', ($exp>=0?'+':'-'), abs($exp));

            $pad = 6; // space for 1e+NN
            if ($dec > 0)
            {
                $pad += ($dec+1); // +1 because of decimal point
            }

            return str_pad($s, $pad, ' ', STR_PAD_LEFT);
        }

        // find decimal point in the input number
        $strnum = (string)round($number, $right_format_digits);
        $strnum_len = strlen($strnum);
        $dec = null;
        $left = true;
        for ($p = 0; $p < $strnum_len; ++$p)
        {
            $ch = $strnum[$p];
            if ($ch >= '0' && $ch <= '9')
            {
                if ($left)
                    $total_left_digits++;
                     else
                    $total_right_digits++;
            }
            if ($ch == '.' || $ch == ',')
            {
                $left = false;
                $dec = $p;
            }
        }
        if (is_null($dec))
            $dec = $strnum_len;


        // if the number of left decimals surpasses the number of
        // spaces in the number format, return an overflow string
        if ($total_left_digits > $left_format_digits)
        {
            $overflow = true;
        }


        if (self::dblcompare($number, 0.0) < 0)
            $is_negative = true;

        $result = '';
        $l = $left_format_digits;
        $r = 0;
        $left = true;

        for ($p = 0; $p < $format_len; ++$p)
        {
            $ch = $format[$p];
            if ($p+1 < $format_len)
                $ch_next = $format[$p+1];
                 else
                $ch_next = null;

            if (($ch == 'F' || $ch == 'f') && ($ch_next == 'M' || $ch_next == 'm'))
            {
                $padding = false;
                $p++;
                continue;
            }
            else if (($ch == 'M' || $ch == 'm') && ($ch_next == 'I' || $ch_next == 'i'))
            {
                $result .= ($is_negative ? '-' : ' ');
                $p++;
                continue;
            }
            else if (($ch == 'P' || $ch == 'p') && ($ch_next == 'L' || $ch_next == 'l'))
            {
                $result .= ($is_negative ? ' ' : '+');
                $p++;
                continue;
            }
             else if ($ch == '.' || $ch == 'D' || $ch == 'd')
            {
                if ($right_format_digits > 0)
                {
                    if ($ch == '.')
                        $result .= $ch;
                        else
                        $result .= '.'; // getDecimalChar() from locale
                }
                $left = false;
                $digit_printed = true;
            }
             else if ($ch == ',' || $ch == 'G' || $ch == 'g')
            {
                if ($digit_printed)
                {
                    $result .= ','; // getThousandsSeparatorChar() from locale
                }
            }
            else if ($ch == '$' || $ch == 8364 /* euro symbol */)
            {
                //$currency_sign = $ch;
                $result .= $ch;
            }
            else if ($ch == 'L')
            {
                $result .= '$';
            }
            else if ($ch == ' ')
            {
                $result .= ' ';
            }
            else if ($ch == '9' || $ch == '0')
            {
                if ($left)
                {
                    $digit = $overflow ? '#' : self::getLeftDigit($strnum, $dec, $l);

                    //if ($ch == '0' || $digit != '0' || $digit_printed || ($l == 1 && (!$format_has_decimal || !$padding)))
                    if ($ch == '0' || $digit != '0' || $digit_printed || $l == 1)
                    {
                        if (!$digit_printed)
                        {
                            if ($pr)
                            {
                                $result .= ($is_negative ? '<' : ' ');
                            }
                            else if ($sign_left === true)
                            {
                                if ($is_negative)
                                {
                                    $result .= '-';
                                }
                                else
                                {
                                    if ($sign_always)
                                    {
                                        $result .= '+';
                                    }
                                    else
                                    {
                                        if ($padding)
                                            $result .= ' ';
                                    }
                                }
                            }
                        }

                        if (!$digit_printed && $ch == '9' && $digit == '0' && !($l == 1 && !$format_has_decimal))
                        {
                        }
                         else
                        {
                            $result .= $digit;
                            $digit_printed = true;
                        }
                    }

                    --$l;
                }
                else
                {
                    ++$r;
                    $digit = $overflow ? '#' : self::getRightDigit($strnum, $dec, $r);

                    if ($r > $zero_right_digits)
                    {
                        if ($digit != '0' || $r <= $total_right_digits)
                        {
                            $result .= $digit;
                        }
                        else
                        {
                            if ($padding)
                                $result .= '0';
                        }
                    }
                    else
                    {
                        $result .= $digit;
                    }
                }
            }
        }

        if ($pr)
        {
            $result .= ($is_negative ? '>' : ' ');
        }
        else if ($sign_left === false)
        {
            if ($is_negative)
            {
                $result .= '-';
            }
            else
            {
                if ($sign_always)
                {
                    $result .= '+';
                }
                else
                {
                    if ($padding)
                        $result .= ' ';
                }
            }
        }

        if ($padding)
        {
            //echo $padlen;
            $result = str_pad($result, $padlen, ' ', STR_PAD_LEFT);
        }

        return $result;
    }

    public function func_to_char($func, $params, &$retval)
    {
        if (count($params) < 2)
        {
            if (!$this->doEval($params[0], $param0)) return false;
            if (is_null($param0))
                $retval = null;
            else if (is_bool($param0))
                $retval = $param0 ? 'true' : 'false';
            else if ($param0 instanceof ExprDateTime)
            {
                if ($param0->hasTimePart())
                {
                    $retval = sprintf("%04d-%02d-%02d %02d:%02d:%02d+00", $param0->values['year'], $param0->values['month'], $param0->values['day'], $param0->values['hour'], $param0->values['minute'], $param0->values['second']);
                }
                 else
                {
                    $retval = sprintf("%04d-%02d-%02d", $param0->values['year'], $param0->values['month'], $param0->values['day']);
                }
            }
            else
                $retval = '' . $param0;
            return true;
        }

        if (!$this->doEval($params[0], $param0)) return false;
        if (!$this->doEval($params[1], $param1)) return false;

        if (is_null($param0))
        {
            $retval = null;
            return true;
        }

        // TODO: make database-conformant implementation, nail down with test suite
        $type0 = $this->getType($params[0]);
        if ($type0 == ExprParser::TYPE_DATE || $type0 == ExprParser::TYPE_DATETIME)
        {
            // default format "YYYY-MM-DD"
            $retval = (string)self::tochar_date($param0, $param1);
        }
         else
        {
            $retval = (string)self::tochar_number($param0, $param1);
        }
        return true;
    }

    public function func_to_date($func, $params, &$retval)
    {
        if (count($params) < 1)
        {
            $retval = null;
            return true;
        }

        if (count($params) == 1)
        {
            // same as cast(fld, date)
            $type_constant = new \Flexio\Base\ExprVariable;
            $type_constant->name = 'date';

            $call_params = [ $params[0], $type_constant ];
            return $this->func_cast($func, $call_params, $retval);
        }


        if (!$this->doEval($params[0], $param0)) return false;
        if (!$this->doEval($params[1], $param1)) return false;

        $dt = new ExprDateTime();
        if (!$dt->parse($param0, $param1))
        {
            $retval = null;
            return true;
        }

        $dt->truncateTime();
        $retval = $dt;
        return true;
    }

    public function func_to_number($func, $params, &$retval)
    {
        if (count($params) < 1)
        {
            $retval = null;
            return true;
        }

        if (count($params) == 1)
        {
            // same as cast(fld, datetime)
            $type_constant = new \Flexio\Base\ExprVariable;
            $type_constant->name = 'numeric';

            $call_params = [ $params[0], $type_constant ];
            return $this->func_cast($func, $call_params, $retval);
        }

        if (!$this->doEval($params[0], $param0)) return false;
        if (!$this->doEval($params[1], $param1)) return false;

        // TODO: make database-conformant implementation, nail down with test suite

        if (is_string($param0))
        {
            $param0 = str_replace(',','', $param0);
        }

        $retval = (double)$param0;
        return true;
    }

    public function func_to_timestamp($func, $params, &$retval)
    {
        if (count($params) < 1)
        {
            $retval = null;
            return true;
        }

        if (count($params) == 1)
        {
            // same as cast(fld, datetime)
            $type_constant = new \Flexio\Base\ExprVariable;
            $type_constant->name = 'datetime';

            $call_params = [ $params[0], $type_constant ];
            return $this->func_cast($func, $call_params, $retval);
        }


        if (!$this->doEval($params[0], $param0)) return false;
        if (!$this->doEval($params[1], $param1)) return false;

        $dt = new ExprDateTime();
        if (!$dt->parse($param0, $param1))
        {
            $retval = null;
            return true;
        }

        $retval = $dt;
        return true;
    }

    public function func_trim($func, $params, &$retval)
    {
        if (count($params) < 2)
        {
            if (!$this->doEval($params[0], $param0)) return false;
            if (is_null($param0))
            {
                $retval = null;
                return true;
            }
            $param0 = self::exprToString($param0);
            $retval = trim(self::exprToString($param0));
        }
        if (count($params) >= 2)
        {
            if (!$this->doEval($params[0], $param0)) return false;
	        if (!$this->doEval($params[1], $param1)) return false;
            if (is_null($param0))
            {
                $retval = null;
                return true;
            }
            $retval = self::mb_trim(self::exprToString($param0), self::exprToString($param1));
        }

        return true;
    }

    public function func_trunc($func, $params, &$retval)
    {
        if (!$this->doEval($params[0], $param0)) return false;

        if (is_null($param0))
        {
            $retval = null;
            return true;
        }

        $retval = (float)0;
        if ($param0 > 0)
            $retval = floor(self::exprToNumber($param0));
        if ($param0 < 0)
            $retval = ceil(self::exprToNumber($param0));

        return true;
    }

    public function func_unix_timestamp($func, $params, &$retval)
    {
        if (count($params) == 0)
        {
            $retval = time();
        }
         else
        {
            if (!$this->doEval($params[0], $param0)) return false;

            if (is_null($param0))
            {
                $retval = null;
                return true;
            }

            if ($param0 instanceof ExprDateTime)
            {
                $retval = $param0->getUnixTime();
            }
             else
            {
                $retval = strtotime($param0);
            }
        }

        return true;
    }

    public function func_upper($func, $params, &$retval)
    {
        if (!$this->doEval($params[0], $param0))
            return false;

        $retval = is_null($param0) ? null : mb_strtoupper(self::exprToString($param0));
        return true;
    }

    public function func_year($func, $params, &$retval)
    {
        if (!$this->doEval($params[0], $param0)) return false;
        if (is_null($param0))
        {
            $retval = null;
            return true;
        }
        $param0 = self::exprToDate($param0);
        $retval = $param0 !== false ? (double)$param0->getYear() : null;
        return true;
    }

    private static function getLeftDigit($str, $dec, $digit)
    {
        $digit_ptr = $dec - $digit;
        if ($digit_ptr < 0 || $str[$digit_ptr] < '0' || $str[$digit_ptr] > '9')
            return '0';
        return $str[$digit_ptr];
    }

    private static function getRightDigit($str, $dec, $digit)
    {
        $digit_ptr = $dec + $digit;
        if ($digit_ptr >= strlen($str))
            return '0';
        return $str[$digit_ptr];
    }

    private static function exprToString($value)
    {
        if (is_string($value))
            return $value;

        if (is_numeric($value))
            return (string)$value;

        if (is_bool($value))
            return ($value === true ? 'true' : 'false');

        return null;
    }

    private static function exprToNumber($value)
    {
        if (is_numeric($value) && !is_string($value)) // is_numeric() will return true for a string that's a number (e.g. '1')
            return $value;

        if (is_string($value))
            return (float)$value;

        if (is_bool($value))
            return ($value === true ? 1 : 0);

        return null;
    }

    private static function exprToDate($value)
    {
        if (is_a($value, '\Flexio\Base\ExprDateTime'))
            return $value;

        $e = new ExprDateTime();
        if (!$e->parse(''.$value))
        {
            return false;
        }

        return $e;
    }

    private static function exprToBool($value)
    {
        if (is_bool($value))
            return $value;

        if (is_numeric($value))
            return ((int)$value === 1 ? true : false);

        if (is_string($value))
            return (strtolower($value) === 'true' ? true : false);

        return null;
    }

    private static function getMaxLocaleMonthLength()
    {
        // TODO: implement
    }

    private static function getMaxLocaleWeekdayLength()
    {
        // TODO: implement
    }

    private static function mb_str_pad($input, $pad_length, $pad_string = ' ', $pad_type = STR_PAD_RIGHT, $encoding = null)
    {
        $len = mb_strlen($input);

        // if the pad length is less than the length of the string, return
        // nothing or just a portion of the string
        if ($pad_length <= 0)
            return '';
        if ($pad_length <= $len)
            return mb_substr($input, 0, $pad_length);

        // parse pad_string into separate characters
        $arr = array();
        $pad_string_len = mb_strlen($pad_string);
        for ($i = 0; $i < $pad_string_len; ++$i)
        {
            $arr[] = mb_substr($pad_string, $i, 1);
        }

        $added = '';
        for ($i = 0; $i < $pad_length - $len; ++$i)
        {
            $added .= $arr[ $i % $pad_string_len ];
        }

        if ($pad_type == STR_PAD_RIGHT)
            return $input . $added;
             else
            return $added . $input;
    }

    private static function mb_ucwords($str)
	{
		return mb_convert_case($str, MB_CASE_TITLE, 'UTF-8');
	}

    private static function mb_strrev($str, $encoding = 'UTF-8')
    {
        return mb_convert_encoding( strrev( mb_convert_encoding($str, 'UTF-16BE', $encoding) ), $encoding, 'UTF-16LE');
    }

    private static function mb_trim($str, $chars = null)
    {
        if (is_null($chars))
        {
            return trim($str);
        }
         else
         {
            $charregex = '(';
            $len = mb_strlen($chars);
            for ($i = 0; $i < $len; ++$i)
            {
                if ($i > 0) $charregex .= '|';
                $charregex .= preg_quote(mb_substr($chars, $i, 1), '/');
            }
            $charregex .= ')';

            return preg_replace("/(^$charregex+)|($charregex+$)/us", '', $str);
        }
    }

    private static function mb_rtrim($str, $chars = null)
    {
        if (is_null($chars))
        {
            return rtrim($str);
        }
         else
        {
            $charregex = '(';
            $len = mb_strlen($chars);
            for ($i = 0; $i < $len; ++$i)
            {
                if ($i > 0) $charregex .= '|';
                $charregex .= preg_quote(mb_substr($chars, $i, 1), '/');
            }
            $charregex .= ')';

            return preg_replace("/($charregex+$)/us", '', $str);
        }
    }

    private static function mb_ltrim($str, $chars = null)
    {
        if (is_null($chars))
        {
            return ltrim($str);
        }
         else
         {
            $charregex = '(';
            $len = mb_strlen($chars);
            for ($i = 0; $i < $len; ++$i)
            {
                if ($i > 0) $charregex .= '|';
                $charregex .= preg_quote(mb_substr($chars, $i, 1), '/');
            }
            $charregex .= ')';

            return preg_replace("/(^$charregex+)/us", '', $str);
        }
    }

    private static function dblcompare($a, $b)
    {
        $a = (double)$a;
        $b = (double)$b;

        if (is_nan($a) && is_nan($b))
            return 0;
        if (is_nan($a))
            return -1;
        if (is_nan($b))
            return 1;

        if (($a - $b) > ( (abs($a) < abs($b) ? abs($b) : abs($a)) * self::EPSILON))
            return 1;
        if (($b - $a) > ( (abs($a) < abs($b) ? abs($b) : abs($a)) * self::EPSILON))
            return -1;
        return 0;
    }

    private static function fmod_sql1($x, $y)
    {
        return $x - (floor(($x/$y)+0.0000000001) * $y);
    }

    private static function fmod_sql($x, $y)
    {
        $a = $x / $y;

        if ($a > 0)
            $a = floor($a);
            else
            $a = ceil($a);

        return $x - ($a * $y);
    }
}




class ExprDateTime
{
    public $values = null;

    public function parse($value, $format = null)
    {
        if (is_a($value, '\Flexio\Base\ExprDateTime'))
        {
            $this->values = $value->values;
            return true;
        }

        $value = trim(''.$value);

        // if date is empty, return null
        if ($value == '')
            return false;

        if (isset($format))
        {
            $format = str_replace(['YYYY', 'MM', 'DD', 'HH24', 'HH12', 'HH', 'MI', 'SS'],
                                  ['Y'   , 'm',  'd',  'H',    'h',    'h',  'i',  's'],
                                  $format);
            $arr = date_parse_from_format($format, $value);
        }
         else
        {
            $arr = date_parse($value);
        }

        if (!isset($arr['year']) || !isset($arr['month']) || !isset($arr['day']))
            return false;

        $this->values = [ 'year' => $arr['year'], 'month' => $arr['month'], 'day' => $arr['day'] ];

        if (($arr['hour']??false) !== false && ($arr['minute']??false) !== false && ($arr['second']??false) !== false)
        {
            $this->values['hour'] = $arr['hour'];
            $this->values['minute'] = $arr['minute'];
            $this->values['second'] = $arr['second'];
        }

        return true;
    }

    public function getUnixTime()
    {
        if ($this->hasTimePart())
        {
            return mktime($this->values['hour'], $this->values['minute'], $this->values['second'],
                          $this->values['month'], $this->values['day'], $this->values['year']);
        }
         else
        {
            return mktime(0,0,0, $this->values['month'], $this->values['day'], $this->values['year']);
        }
    }

    public function setToCurrentDate()
    {
        $dt = getdate();
        $this->values = [ 'year' => $dt['year'], 'month' => $dt['mon'], 'day' => $dt['mday'] ];
    }

    public function setToCurrentDateTime()
    {
        $dt = getdate();
        $this->values = [ 'year' => $dt['year'], 'month' => $dt['mon'], 'day' => $dt['mday'],
                          'hour' => $dt['hours'], 'minute' => $dt['minutes'], 'second' => $dt['seconds'] ];
    }

    public function hasTimePart()
    {
        return (isset($this->values['hour']) && $this->values['hour'] !== false);
    }

    public function truncateTime()
    {
        // makes a date out of a date time
        unset($this->values['hour']);
        unset($this->values['minute']);
        unset($this->values['second']);
    }

    public function toString()
    {
        return sprintf("%04d-%02d-%02d", $this->values['year'], $this->values['month'], $this->values['day']);
    }

    public function isNull()
    {
        if (is_null($this->values))
            return true;
        return !isset($this->values['year']);
    }

    public function getYear()     { return $this->values['year']; }
    public function getMonth()    { return $this->values['month']; }
    public function getDay()      { return $this->values['day']; }
    public function getHour()     { return $this->values['hour']; }
    public function getMinute()   { return $this->values['minute']; }
    public function getSecond()   { return $this->values['second']; }

    public function setYear($v)   { $this->values['year'] = $v; }
    public function setMonth($v)  { $this->values['month'] = $v; }
    public function setDay($v)    { $this->values['day'] = $v; }

    public function makeDateTime()
    {
        if (!isset($this->values['hour']))
            $this->values['hour'] = 0;
        if (!isset($this->values['minute']))
            $this->values['minute'] = 0;
        if (!isset($this->values['second']))
            $this->values['second'] = 0;
    }

    public function setHour($v)   { $this->values['hour'] = $v;   $this->makeDateTime(); }
    public function setMinute($v) { $this->values['minute'] = $v; $this->makeDateTime(); }
    public function setSecond($v) { $this->values['second'] = $v; $this->makeDateTime(); }
}
