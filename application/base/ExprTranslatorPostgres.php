<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie LLC. All rights reserved.
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


class ExprTranslatorPostgres
{
    public $fields = [];
    public $parser = null;
    public $parse_result = null;


    public static function translate($expr, $structure = null)
    {
        $obj = new self;
        $obj->setStructure($structure);
        if (!$obj->parse($expr))
            return false;
        return $obj->getResult();
    }

    public static function getLastError()
    {
        return null;
    }

    public function setStructure($structure)
    {
        $this->fields = [];
        foreach ($structure as $fld)
        {
            if (isset($fld['name']))
                $this->fields[strtolower($fld['name'])] = $fld;
        }
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

        //$ret = $this->printNode($ret);
        //die($ret);

        return true;
    }

    public function getResult()
    {
        if (!$this->parse_result)
            return false;
        return $this->printNode($this->parse_result);
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

    private function printNode($node)
    {
        $node_type = $node->getNodeType();

        if ($node_type == ExprParser::NODETYPE_VALUE)
        {
            if (is_string($node->val))
            {
                return \Flexio\Base\ExprUtil::quote($node->val);
            }
             else if (is_bool($node->val))
            {
                return $node->val ? 'true' : 'false';
            }
             else if (is_null($node->val))
            {
                return 'null';
            }
             else if (is_float($node->val))
            {
                return $node->text;
            }
             else
            {
                return $node->val;
            }
        }
         else if ($node_type == ExprParser::NODETYPE_OPERATOR)
        {
            $oper = $this->operators[$node->index];

            if (isset($oper['func']))
            {
                return call_user_func([ $this, $oper['func'] ], $oper, $node->params);
            }

            if ($node->unary)
            {
                $param = $this->printNode($node->params[0]);
                if ($param === false)
                    return false;

                // adding this space makes it impossible to use multiple
                // negative unary operators to generate a sql comment
                return $node->name . ' (' . $param . ')';
            }
             else
            {
                $param0 = $this->printNode($node->params[0]);
                $param1 = $this->printNode($node->params[1]);
                if ($param0 === false || $param1 === false)
                    return false;

                return '(' . $param0 . ') '  . $node->name . ' (' . $param1 . ')';
            }
        }
         else if ($node_type == ExprParser::NODETYPE_FUNCTION)
        {
            $funcname = strtolower($node->name);
            if (!isset($this->functions[$funcname]))
                return false; // function doesn't exist

            $func = $this->functions[$funcname];

            if (isset($func['func']))
            {
                return call_user_func([ $this, $func['func'] ], $funcname, $node->params);
            }


            $ret = $funcname . '(';

            $param_count = count($node->params);
            for ($i = 0; $i < count($node->params); ++$i)
            {
                $parami = $this->printNode($node->params[$i]);
                if ($parami === false)
                    return false;
                $ret .= $parami;
                if ($i+1 < $param_count)
                    $ret .= ',';
            }
            $ret .= ')';
            return $ret;
        }
         else if ($node_type == ExprParser::NODETYPE_VARIABLE)
        {
            $key = strtolower($node->name);

            // look up variable to see if it's allowed
            if (isset($this->values[$key]['name']))
            {
                return $this->values[$key]['name'];
            }
             else if (isset($this->fields[$key]['name']))
            {
                // field structures can specify a 'store_name', which is the
                // name that's used to store the data in postgres; if the
                // store_name is set, translate the application field name,
                // given by 'name' to the field name used to store the data,
                // given by 'store_name'; otherwise, use the normal name

                $result = $this->fields[$key]['name'];
                if (isset($this->fields[$key]['store_name']))
                    $result = $this->fields[$key]['store_name'];

                return $result;
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

    private function castValueToDate($value)
    {
        if ($value->getNodeType() == ExprParser::NODETYPE_VALUE && $this->getType($value) == ExprParser::TYPE_STRING)
        {
            $matches = [];
            if (!preg_match('~^(?<year>[0-9]{4})[-./ ](?<month>[0-9]{2})[-./ ](?<day>[0-9]{2})([ ]+(?<hour>[0-9]{2}):(?<minute>[0-9]{2})(:(?<second>[0-9]{2}))?)?$~', $value->val, $matches))
                return 'null::date';

            if ($matches['year'] < 1753 || $matches['month'] == 0 || $matches['day'] == 0)
                return 'null::date';

            if (isset($matches['hour']) && $matches['hour'] > 24)
                return 'null::date';

            if (isset($matches['minute']) && $matches['minute'] > 59)
                return 'null::date';

            if (isset($matches['second']) && $matches['second'] > 59)
                return 'null::date';

            return "'" . $value->val . "'::date";
        }
         else
        {
            return false;
        }
    }

    private function castValueToTimestamp($value)
    {
        if ($value->getNodeType() == ExprParser::NODETYPE_VALUE && $this->getType($value) == ExprParser::TYPE_STRING)
        {
            $matches = [];
            if (!preg_match('~^(?<year>[0-9]{4})[-./ ](?<month>[0-9]{2})[-./ ](?<day>[0-9]{2})([ ]+(?<hour>[0-9]{2}):(?<minute>[0-9]{2})(:(?<second>[0-9]{2}))?)?$~', $value->val, $matches))
                return 'null::timestamp';

            if ($matches['year'] < 1753 || $matches['month'] == 0 || $matches['day'] == 0)
                return 'null::timestamp';

            if (isset($matches['hour']) && $matches['hour'] > 24)
                return 'null::timestamp';

            if (isset($matches['minute']) && $matches['minute'] > 59)
                return 'null::timestamp';

            if (isset($matches['second']) && $matches['second'] > 59)
                return 'null::timestamp';

            return "'" . $value->val . "'::timestamp";
        }
         else
        {
            return false;
        }
    }

    private static function getCastExpression($name, $old_type, $new_type, $new_width = null, $new_scale = null)
    {
        $width = $new_width;
        $scale = $new_scale;

        $expr = $name;

        switch ($new_type)
        {
            case 'text':
            case 'character':
            case 'widecharacter':
                if ($new_width == -1 || is_null($new_width))
                    $cast = 'varchar';
                     else
                    $cast = "varchar($width)";
                if ($old_type == 'date')
                    $expr = "to_char($expr,'YYYY-MM-DD')::$cast";
                else if ($old_type == 'datetime')
                    $expr = "to_char($expr,'YYYY-MM-DD HH24:MI:SSOF')::$cast";
                else
                    $expr = "($expr)::$cast";

                break;

            case 'double':
            case 'real':
            case 'numeric':
                if ($new_type == 'double' || $new_type == 'real')
                {
                    $cast = 'double precision';
                }
                 else if ($new_width == -1 || is_null($new_width))
                {
                    $cast = 'numeric';
                }
                 else
                {
                     if ($new_scale == -1 || is_null($new_scale))
                        $cast = "numeric($new_width)";
                         else
                        $cast = "numeric($new_width,$new_scale)";
                }
                if ($width > 1000) // numerics have a max width of 1000
                    return false;
                if ($old_type == 'text' || $old_type == 'character' || $old_type == 'widecharacter')
                    $expr = "(substring(translate(($expr),',%$# ','') from '^[+-]*(?:[0-9]{1,$width}|[0-9]{1,$width}[.][0-9]*|[0-9]{0,$width}[.][0-9]+)$'))::$cast";
                else if ($old_type == 'numeric' || $old_type == 'double' || $old_type == 'float' || $old_type == 'real' || $old_type == 'integer')
                    $expr = "(CASE WHEN ($expr) > 10^$width THEN NULL ELSE ($expr) END)::$cast";
                else if ($old_type == 'boolean')
                    $expr = "(CASE WHEN ($expr) THEN 1 ELSE 0 END)::$cast";
                else if ($old_type == 'date')
                    $expr = "to_char($expr, 'YYYYMMDD')::numeric";
                else if ($old_type == 'datetime')
                    $expr = "to_char($expr, 'YYYYMMDDHH24MISS')::numeric";
                else
                    return false;

                break;



            case 'integer':
                if ($old_type == 'text' || $old_type == 'character' || $old_type == 'widecharacter')
                    $expr = "round((substring(translate(($expr),',%$# ','') from '^[+-]*(?:[0-9]{1,$width}|[0-9]{1,$width}[.][0-9]*|[0-9]{0,$width}[.][0-9]+)$'))::numeric,0)";
                else if ($old_type == 'numeric' || $old_type == 'double' || $old_type == 'float' || $old_type == 'real' || $old_type == 'integer')
                    $expr = "round(($expr),0)::integer";
                else if ($old_type == 'boolean')
                    $expr = "(CASE WHEN ($expr) THEN 1 ELSE 0 END)::integer";
                else if ($old_type == 'date')
                    $expr = "to_char($expr, 'YYYYMMDD')::numeric";
                else if ($old_type == 'datetime')
                    $expr = "to_char($expr, 'YYYYMMDDHH24MISS')::numeric";
                else
                    return false;
                break;

            case 'date':
                if ($old_type != 'text' && $old_type != 'character' && $old_type != 'widecharacter')
                    $expr = "($expr)::text";
                $expr = "(CASE WHEN ($expr) ~ '^[0-9]{1,2}[-/. ][0-9]{1,2}[-/. ][0-9]{3,4}' THEN to_date(($expr),'MM DD YYYY')".
                        "      WHEN ($expr) ~ '^[0-9]{1,2}[-/. ][0-9]{1,2}[-/. ][0-9]{1,2}' THEN to_date(($expr),'MM DD YY')".
                        "      WHEN ($expr) ~ '^[0-9]{3,4}[-/. ][0-9]{1,2}[-/. ][0-9]{1,2}' THEN to_date(($expr),'YYYY MM DD')".
                        "      WHEN ($expr) ~ '^[0-9]{8}'                                   THEN to_date(($expr),'YYYYMMDD') ELSE NULL END)";
                break;

            case 'timestamp':
            case 'datetime':
                if ($old_type != 'text' && $old_type != 'character' && $old_type != 'widecharacter')
                    $expr = "($expr)::text";
                $expr = "(CASE WHEN ($expr) ~ '^[0-9]{1,2}[-/. ][0-9]{1,2}[-/. ][0-9]{3,4}(?:[T ]?[0-9]{1,2}[:. ][0-9]{1,2}[:. ][0-9]{1,2})?' THEN to_timestamp(($expr),'MM DD YYYY HH24 MI SS')".
                        "      WHEN ($expr) ~ '^[0-9]{1,2}[-/. ][0-9]{1,2}[-/. ][0-9]{1,2}(?:[T ]?[0-9]{1,2}[:. ][0-9]{1,2}[:. ][0-9]{1,2})?' THEN to_timestamp(($expr),'MM DD YY HH24 MI SS')".
                        "      WHEN ($expr) ~ '^[0-9]{3,4}[-/. ][0-9]{1,2}[-/. ][0-9]{1,2}(?:[T ]?[0-9]{1,2}[:. ][0-9]{1,2}[:. ][0-9]{1,2})?' THEN to_timestamp(($expr),'YYYY MM DD HH24 MI SS')".
                        "      WHEN ($expr) ~ '^[0-9]{14}'                                                                                    THEN to_timestamp(($expr),'YYYYMMDDHH24MISS')".
                        "      WHEN ($expr) ~ '^[0-9]{8}(?:[T ]?[0-9]{1,2}[:. ][0-9]{1,2}[:. ][0-9]{1,2})?'                                   THEN to_timestamp(($expr),'YYYYMMDD HH24 MI SS') ELSE NULL END)";
                break;

            case 'boolean':
                if ($old_type == 'text' || $old_type == 'character' || $old_type == 'widecharacter')
                    $expr = "(CASE WHEN ($expr) ~* '^(?:[YTX1]|true|yes|on)$' THEN TRUE".
                            "      WHEN ($expr) ~* '^(?:[NF0]|false|no|off|)$' THEN FALSE ELSE NULL END)";
                else if ($old_type == 'numeric' || $old_type == 'integer' || $old_type == 'double' || $old_type == 'float' || $old_type == 'real')
                    $expr = "(($expr) <> 0)";
                else if ($old_type == 'boolean')
                    $expr = "($expr)";
                else
                    $expr = "(($expr) IS NOT NULL)";
                break;

            default:
                // unknown/unallowed type
                return null;
        }

        return $expr;
    }


    // s = string; i = integer; f = float; n = (any numeric); b = boolean; * = any type

    public $operators = [
        [ 'name' => '-',   'prec' => 9, 'unary' => true,                  'types' => [ 'f(f)', 'i(i)'   ] ],
        [ 'name' => '+',   'prec' => 9, 'unary' => true,                  'types' => [ 'f(f)', 'i(i)'   ] ],
        [ 'name' => 'not', 'prec' => 9, 'unary' => true,  'assoc' => 'R', 'types' => [ 'b(b)' ], 'func' => 'oper_not' ],
        [ 'name' => '!',   'prec' => 9, 'unary' => true,  'assoc' => 'R', 'types' => [ 'b(b)' ], 'func' => 'oper_not' ],
        [ 'name' => '*',   'prec' => 8, 'unary' => false, 'assoc' => 'L', 'types' => [ 'f(ff)', 'i(ii)', 'f(fi)', 'f(if)' ] ],
        [ 'name' => '/',   'prec' => 8, 'unary' => false, 'assoc' => 'L', 'types' => [ 'f(ff)', 'i(ii)', 'f(fi)', 'f(if)' ], 'func' => 'oper_div' ],
        [ 'name' => '%',   'prec' => 8, 'unary' => false, 'assoc' => 'L', 'types' => [ 'f(ff)', 'i(ii)', 'f(fi)', 'f(if)' ], 'func' => 'oper_mod' ],
        [ 'name' => '-',   'prec' => 7, 'unary' => false, 'assoc' => 'L', 'types' => [ 'f(ff)', 'i(ii)', 'f(fi)', 'f(if)' ] ],
        [ 'name' => '+',   'prec' => 7, 'unary' => false, 'assoc' => 'L', 'types' => [ 'f(ff)', 'i(ii)', 'f(fi)', 'f(if)' ] ],
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
        [ 'name' => 'and', 'prec' => 4, 'unary' => false, 'assoc' => 'L', 'types' => [ 'b(bb)' ] ],
        [ 'name' => 'or',  'prec' => 3, 'unary' => false, 'assoc' => 'L', 'types' => [ 'b(bb)' ] ]
    ];

    public $functions = [
        'abs'          => [ 'types' => [ 'f(n)', 'f(s)', 'f(N)' ] ],
        'acos'         => [ 'types' => [ 'f(n)', 'f(s)', 'f(N)' ], 'func' => 'func_acos' ],
        'ascii'        => [ 'types' => [ 'i(s)', 'i(n)', 'i(b)' ], 'i(N)', 'func' => 'func_ascii' ],
        'asin'         => [ 'types' => [ 'f(n)', 'f(s)', 'f(N)' ], 'func' => 'func_asin' ],
        'atan'         => [ 'types' => [ 'f(n)', 'f(s)', 'f(N)' ] ],
        'ceiling'      => [ 'types' => [ 'f(n)', 'f(s)', 'f(N)' ] ],
        'cast'         => [ 'types' => [ 's(*s)', 'f(*f)', 'i(*i)', 'd(*d)', 't(*t)', 'b(*b)' ], 'func' => 'func_cast' ],
        'concat'       => [ 'types' => [ 's(*[*...])' ], 'func' => 'func_concat' ],
        'contains'     => [ 'types' => [ 'b(ss)', 'b(ns)', 'b(bs)', 'b(Ns)' ], 'func' => 'func_contains' ],
        'cos'          => [ 'types' => [ 'f(n)', 'f(s)', 'f(N)' ] ],
        'current_date' => [ 'types' => [ 'd()' ], 'func' => 'func_current_date' ],
        'day'          => [ 'types' => [ 'f(d)', 'f(t)', 'f(s)', 'f(N)' ], 'func' => 'func_day' ],
        'exp'          => [ 'types' => [ 'f(n)', 'f(s)', 'f(N)' ] ],
        'floor'        => [ 'types' => [ 'f(n)', 'f(s)', 'f(N)' ] ],
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
        'now'          => [ 'types' => [ 't()' ] ],
        'pi'           => [ 'types' => [ 'f()' ] ],
        'pow'          => [ 'types' => [ 'f(nn)', 'f(ns)', 'f(nN)', 'f(sn)', 'f(ss)', 'f(sN)', 'f(Nn)', 'f(Ns)', 'f(NN)' ], 'func' => 'func_pow' ],
        'regexp_replace'=>[ 'types' => [ 's(sss)', 's(ssss)', 's(nss)', 's(nsss)', 's(bss)', 's(bsss)', 's(Nss)', 's(Nsss)' ], 'func' => 'func_regexp_replace' ],
        'replace'      => [ 'types' => [ 's(sss)', 's(nss)', 's(bss)', 's(Nss)' ], 'func' => 'func_replace' ],
        'reverse'      => [ 'types' => [ 's(s)', 's(n)', 's(b)', 's(N)' ], 'func' => 'func_reverse' ],
        'right'        => [ 'types' => [ 's(si)', 's(ni)', 's(bi)', 's(Ni)' ], 'func' => 'func_right' ],
        'round'        => [ 'types' => [ 'f(n)', 'f(s)', 'f(N)', 'f(ni)', 'f(si)', 'f(Ni)' ] ],
        'rpad'         => [ 'types' => [ 's(si)', 's(sis)', 's(ni)', 's(nis)', 's(bi)', 's(bis)', 's(Ni)', 's(Nis)' ], 'func' => 'func_rpad' ],
        'rtrim'        => [ 'types' => [ 's(s)', 's(ss)', 's(n)', 's(ns)', 's(b)', 's(bs)', 's(N)', 's(Ns)' ], 'func' => 'func_rtrim' ],
        'second'       => [ 'types' => [ 'f(d)', 'f(t)', 'f(N)', 'f(s)' ], 'func' => 'func_second' ],
        'sign'         => [ 'types' => [ 'f(n)', 'f(s)', 'f(N)' ] ],
        'sin'          => [ 'types' => [ 'f(n)', 'f(s)', 'f(N)' ] ],
        'strpart'      => [ 'types' => [ 's(*si)' ], 'func' => 'func_strpart' ],
        'strpos'       => [ 'types' => [ 'i(ss)', 'i(ns)', 'i(bs)', 'i(Ns)' ], 'func' => 'func_strpos' ],
        'substr'       => [ 'types' => [ 's(si[i])', 's(ni[i])', 's(bi[i])', 's(Ni[i])' ], 'func' => 'func_substr' ],
        'tan'          => [ 'types' => [ 'f(n)', 'f(s)', 'f(N)' ] ],
        'to_char'      => [ 'types' => [ 's(s)', 's(n)', 's(d)', 's(t)', 's(b)', 's(N)', 's(ns)', 's(ds)', 's(ts)', 's(Ns)' ], 'func' => 'func_to_char' ],
        'to_date'      => [ 'types' => [ 'd(s[s])', 'd(n[s])', 'd(d[s])', 'd(b[s])', 'd(N[s])' ], 'func' => 'func_to_date' ],
        'to_datetime'  => [ 'types' => [ 't(s[s])', 't(n[s])', 't(d[s])', 't(b[s])', 't(N[s])' ], 'func' => 'func_to_timestamp' ], // alias for to_timestamp
        'to_timestamp' => [ 'types' => [ 't(s[s])', 't(n[s])', 't(d[s])', 't(b[s])', 't(N[s])' ], 'func' => 'func_to_timestamp' ],
        'to_number'    => [ 'types' => [ 'f(s[s])', 'f(n[s])', 'f(d[s])', 'f(b[s])', 'f(N[s])' ], 'func' => 'func_to_number' ],
        'trim'         => [ 'types' => [ 's(s)', 's(ss)', 's(n)', 's(ns)', 's(b)', 's(bs)', 's(N)', 's(Ns)' ], 'func' => 'func_trim' ],
        'trunc'        => [ 'types' => [ 'f(n)', 'f(s)', 'f(N)' ], 'func' => 'func_generic_numfunc' ],
        'upper'        => [ 'types' => [ 's(s)', 's(n)', 's(b)', 's(N)' ], 'func' => 'func_upper' ],
        'year'         => [ 'types' => [ 'f(d)', 'f(t)', 'f(N)', 'f(s)' ], 'func' => 'func_year' ],

        // used by cast() -- for example cast(fld, numeric(10,2))
        'text'         => [ 'types' => [ 's(i)' ] ],
        'character'    => [ 'types' => [ 's(i)' ] ],
        'numeric'      => [ 'types' => [ 'f(i)', 'f(ii)' ] ]
    ];


    public $values = [
        'current_date' => [ 'type' => 'd', 'name' => 'current_date' ],
        'pi'           => [ 'type' => 'f', 'name' => 'pi()' ],

        'text'         => [ 'type' => 's', 'name' => "'text'" ],
        'character'    => [ 'type' => 's', 'name' => "'character'" ],
        'numeric'      => [ 'type' => 'f', 'name' => "'numeric'" ],
        'double'       => [ 'type' => 'f', 'name' => "'double'" ],
        'integer'      => [ 'type' => 'i', 'name' => "'integer'" ],
        'boolean'      => [ 'type' => 'b', 'name' => "'boolean'" ],
        'date'         => [ 'type' => 'd', 'name' => "'date'" ],
        'datetime'     => [ 'type' => 't', 'name' => "'datetime'" ]
    ];



    public function oper_compare($oper, $params)
    {
        $param0 = $this->printNode($params[0]);
        $param1 = $this->printNode($params[1]);

        if ($param0 === false || $param1 === false)
            return false;

        $type0 = $this->getType($params[0]);
        $type1 = $this->getType($params[1]);

        if ($type0 == ExprParser::TYPE_DATE && $type1 == ExprParser::TYPE_STRING)
        {
            $casted_value = $this->castValueToDate($params[1]);
            if ($casted_value === false)
                return false;
            return '(' . $param0 . ')' . $oper['name'] . $casted_value;
        }
         else if ($type0 == ExprParser::TYPE_DATETIME && $type1 == ExprParser::TYPE_STRING)
        {
            $casted_value = $this->castValueToTimestamp($params[1]);
            if ($casted_value === false)
                return false;
            return '(' . $param0 . ')' . $oper['name'] . $casted_value;
        }

        return '(' . $param0 . ')' . $oper['name'] . '(' . $param1 . ')';
    }

    public function oper_regex($oper, $params)
    {
        $param0 = $this->printNode($params[0]);
        $param1 = $this->printNode($params[1]);

        // build up the expression
        if ($this->getType($params[0]) == ExprParser::TYPE_STRING && $this->getType($params[1]) == ExprParser::TYPE_STRING)
        {
            return '(' . $param0 . ')' . $oper['name'] . '(' . $param1 . ')';
        }

        return false;
    }

    public function oper_div($oper, $params)
    {
        if ($this->getType($params[0]) == ExprParser::TYPE_INTEGER && $this->getType($params[1]) == ExprParser::TYPE_INTEGER)
        {
            // prevent integer math
            return '(' . $this->printNode($params[0]) . ')::float/nullif(' . $this->printNode($params[1]) . ',0)::float';
        }

        return '(' . $this->printNode($params[0]) . ')/nullif(' . $this->printNode($params[1]) . ',0.0)';
    }

    public function oper_mod($oper, $params)
    {
        return '(' . $this->printNode($params[0]) . ')%nullif(' . $this->printNode($params[1]) . ',0)';
    }

    public function oper_not($oper, $params)
    {
        return 'not (' . $this->printNode($params[0]) . ')';
    }

    public function func_acos($func, $params)
    {
        $param = $this->printNode($params[0]);

        return "(CASE WHEN ($param) < -1.0 or ($param) > 1.0 THEN 'NaN'::float ELSE acos($param) END)";
    }

    public function func_ascii($func, $params)
    {
        $res = 'ascii(';

        if ($this->getType($params[0]) != ExprParser::TYPE_STRING)
            $res .= '(' . $this->printNode($params[0]) . ')::text';
             else
            $res .= $this->printNode($params[0]);

        return $res . ')';
    }

    public function func_asin($func, $params)
    {
        $param = $this->printNode($params[0]);

        return "(CASE WHEN ($param) < -1.0 or ($param) > 1.0 THEN 'NaN'::float ELSE asin($param) END)";
    }

    public function func_cast($func, $params)
    {
        $param = $this->printNode($params[0]);
        if ($param === false)
            return false;
        $old_type = $this->getType($params[0]);

        if ($old_type == ExprParser::TYPE_NULL)
            return 'null';

        $type = null;
        $width = null;
        $scale = null;

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

        if ($params[1]->getNodeType() == ExprParser::NODETYPE_VARIABLE)
        {
            switch ($params[1]->name)
            {
                case 'character':
                case 'text':
                    $type = 'text';
                    break;
                case 'numeric':
                case 'double':
                case 'integer':
                case 'date':
                case 'datetime':
                case 'boolean':
                    $type = $params[1]->name;
                    break;
                default:
                    return false; // unknown type
            }
        }

        switch ($old_type)
        {
            case ExprParser::TYPE_STRING:    $old_type = 'text'; break;
            case ExprParser::TYPE_INTEGER:   $old_type = 'integer'; break;
            case ExprParser::TYPE_FLOAT:     $old_type = 'float'; break;
            case ExprParser::TYPE_BOOLEAN:   $old_type = 'boolean'; break;
            case ExprParser::TYPE_DATE:      $old_type = 'date'; break;
            case ExprParser::TYPE_DATETIME:  $old_type = 'datetime'; break;
            default: return false;
        }


        return self::getCastExpression($param, $old_type, $type, $width, $scale);
    }

    public function func_concat($func, $params)
    {
        $res = '';

        $first = true;
        foreach ($params as $p)
        {
            if ($first === false)
                $res .= ' || ';

            $res .= '((' . $this->printNode($p) . ')::text)';

            $first = false;
        }

        return $res;
    }

    public function func_contains($func, $params)
    {
        $res = '(strpos(';

        if ($this->getType($params[0]) != ExprParser::TYPE_STRING)
            $res .= '(' . $this->printNode($params[0]) . ')::text';
             else
            $res .= $this->printNode($params[0]);

        $res .= ',' . $this->printNode($params[1]);

        return $res . ')<>0)';
    }

    public function func_current_date($func, $params)
    {
        return 'current_date';
    }

    public function func_day($func, $params)
    {
        if ($params[0]->getNodeType() == ExprParser::NODETYPE_VALUE && $this->getType($params[0]) == ExprParser::TYPE_STRING)
            $param = $this->castValueToTimestamp($params[0]);
        else if ($this->getType($params[0]) == ExprParser::TYPE_NULL)
            $param = 'null::timestamp';
        else
            $param = $this->printNode($params[0]);

        return '(extract(day from (' . $param . ')))';
    }

    public function func_hour($func, $params)
    {
        if ($params[0]->getNodeType() == ExprParser::NODETYPE_VALUE && $this->getType($params[0]) == ExprParser::TYPE_STRING)
            $param = $this->castValueToTimestamp($params[0]);
        else if ($this->getType($params[0]) == ExprParser::TYPE_NULL)
            $param = 'null::timestamp';
        else
            $param = $this->printNode($params[0]);

        return '(extract(hour from (' . $param . ')))';
    }

    public function func_iif($func, $params)
    {
        return '(CASE WHEN (' . $this->printNode($params[0]) . ') THEN (' . $this->printNode($params[1]) . ') ELSE ('.  $this->printNode($params[2]) . ') END)';
    }

    public function func_initcap($func, $params)
    {
        $res = 'initcap(';

        if ($this->getType($params[0]) != ExprParser::TYPE_STRING)
            $res .= '(' . $this->printNode($params[0]) . ')::text';
             else
            $res .= $this->printNode($params[0]);

        return $res . ')';
    }

    public function func_isnull($func, $params)
    {
        return '(' . $this->printNode($params[0]) . ' IS NULL)';
    }

    public function func_iskindof($func, $params)
    {
        // get the regex to use when checking for the kind
        $match_type = $this->printNode($params[1]);
        $match_regex = '';

        switch ($match_type)
        {
            case "'email'":
                $match_regex = "^[A-Za-z0-9._%-]+@[A-Za-z0-9.-]+[.][A-Za-z]+$";
                break;

            case "'english'":
                $match_regex = "^[\\x00-\\x7F]*$";
                break;
        }

        // no match expression
        if (strlen($match_regex) === 0)
            return '(false)';

        // return the match expression
        $res = '(';

        if ($this->getType($params[0]) != ExprParser::TYPE_STRING)
            $res .= '(' . $this->printNode($params[0]) . ')::text';
             else
            $res .= $this->printNode($params[0]);

        $res .= ' ~* ' . \Flexio\Base\ExprUtil::quote($match_regex);
        return $res . ')';
    }

    public function func_left($func, $params)
    {
        $res = 'left(';

        if ($this->getType($params[0]) != ExprParser::TYPE_STRING)
            $res .= '(' . $this->printNode($params[0]) . ')::text';
             else
            $res .= $this->printNode($params[0]);

        $res .= ',' . $this->printNode($params[1]);

        return $res . ')';
    }

    public function func_length($func, $params)
    {
        $res = 'length(';

        if ($this->getType($params[0]) != ExprParser::TYPE_STRING)
            $res .= '(' . $this->printNode($params[0]) . ')::text';
             else
            $res .= $this->printNode($params[0]);

        return $res . ')';
    }

    public function func_ln($func, $params)
    {
        $param = $this->printNode($params[0]);
        if ($this->getType($params[0]) == ExprParser::TYPE_STRING)
        {
            $param = "(CASE WHEN ($param) ~ '^[+-]{0,1}(?:[0-9]+|[0-9]+[.][0-9]*|[0-9]+[.][0-9]+)$' THEN trim($param)::numeric ELSE NULL END)";
        }

        return "(CASE WHEN ($param) = 0 THEN '-Infinity'::float WHEN ($param) < 0 THEN 'NaN'::float ELSE ln($param) END)";
    }

    public function func_log($func, $params)
    {
        $param = $this->printNode($params[0]);
        if ($this->getType($params[0]) == ExprParser::TYPE_STRING)
        {
            $param = "(CASE WHEN ($param) ~ '^[+-]{0,1}(?:[0-9]+|[0-9]+[.][0-9]*|[0-9]+[.][0-9]+)$' THEN trim($param)::numeric ELSE NULL END)";
        }

        return "(CASE WHEN ($param) = 0 THEN '-Infinity'::float WHEN ($param) < 0 THEN 'NaN'::float ELSE log($param) END)";
    }

    public function func_lower($func, $params)
    {
        $res = 'lower(';

        if ($this->getType($params[0]) != ExprParser::TYPE_STRING)
            $res .= '(' . $this->printNode($params[0]) . ')::text';
             else
            $res .= $this->printNode($params[0]);

        return $res . ')';
    }

    public function func_lpad($func, $params)
    {
        $res = 'lpad(';

        if ($this->getType($params[0]) != ExprParser::TYPE_STRING)
            $res .= '(' . $this->printNode($params[0]) . ')::text';
             else
            $res .= $this->printNode($params[0]);

        $res .= ',' . $this->printNode($params[1]);

        if (count($params) > 2)
            $res .= ',' . $this->printNode($params[2]);

        return $res . ')';
    }

    public function func_ltrim($func, $params)
    {
        $res = 'ltrim(';

        if ($this->getType($params[0]) != ExprParser::TYPE_STRING)
            $res .= '(' . $this->printNode($params[0]) . ')::text';
             else
            $res .= $this->printNode($params[0]);

        if (count($params) > 1)
            $res .= ',' . $this->printNode($params[1]);

        return $res . ')';
    }

    public function func_md5($func, $params)
    {
        $res = 'md5(';

        if ($this->getType($params[0]) != ExprParser::TYPE_STRING)
            $res .= '(' . $this->printNode($params[0]) . ')::text';
             else
            $res .= $this->printNode($params[0]);

        return $res . ')';
    }

    public function func_minute($func, $params)
    {
        if ($params[0]->getNodeType() == ExprParser::NODETYPE_VALUE && $this->getType($params[0]) == ExprParser::TYPE_STRING)
            $param = $this->castValueToTimestamp($params[0]);
        else if ($this->getType($params[0]) == ExprParser::TYPE_NULL)
            $param = 'null::timestamp';
        else
            $param = $this->printNode($params[0]);

        return '(extract(minute from (' . $param . ')))';
    }

    public function func_mod($func, $params)
    {
        return '(' . $this->printNode($params[0]) . ')%nullif(' . $this->printNode($params[1]) . ',0)';
    }

    public function func_month($func, $params)
    {
        if ($params[0]->getNodeType() == ExprParser::NODETYPE_VALUE && $this->getType($params[0]) == ExprParser::TYPE_STRING)
            $param = $this->castValueToTimestamp($params[0]);
        else if ($this->getType($params[0]) == ExprParser::TYPE_NULL)
            $param = 'null::timestamp';
        else
            $param = $this->printNode($params[0]);

        return '(extract(month from (' . $param . ')))';
    }

    public function func_pow($func, $params)
    {
        $param0 = $this->printNode($params[0]);
        $param1 = $this->printNode($params[1]);

        return "(CASE WHEN ($param0) < 0 and round($param1,0) <> ($param1) THEN 'NaN'::float ELSE pow($param0,$param1) END)";
    }

    public function func_regexp_replace($func, $params)
    {
        $res = 'regexp_replace(';

        if ($this->getType($params[0]) != ExprParser::TYPE_STRING)
            $res .= '(' . $this->printNode($params[0]) . ')::text';
                else
            $res .= $this->printNode($params[0]);

        $res .= ',' . $this->printNode($params[1]);
        $res .= ',' . $this->printNode($params[2]);
        if (count($params) > 3) $res .= ',' . $this->printNode($params[3]);

        return $res . ')';
    }

    public function func_replace($func, $params)
    {
        $res = 'replace(';

        if ($this->getType($params[0]) != ExprParser::TYPE_STRING)
            $res .= '(' . $this->printNode($params[0]) . ')::text';
             else
            $res .= $this->printNode($params[0]);

        $res .= ',' . $this->printNode($params[1]);
        $res .= ',' . $this->printNode($params[2]);

        return $res . ')';
    }

    public function func_reverse($func, $params)
    {
        $res = 'reverse(';

        if ($this->getType($params[0]) != ExprParser::TYPE_STRING)
            $res .= '(' . $this->printNode($params[0]) . ')::text';
             else
            $res .= $this->printNode($params[0]);

        return $res . ')';
    }

    public function func_right($func, $params)
    {
        $res = 'right(';

        if ($this->getType($params[0]) != ExprParser::TYPE_STRING)
            $res .= '(' . $this->printNode($params[0]) . ')::text';
             else
            $res .= $this->printNode($params[0]);

        $res .= ',' . $this->printNode($params[1]);

        return $res . ')';
    }

    public function func_rpad($func, $params)
    {
        $res = 'rpad(';

        if ($this->getType($params[0]) != ExprParser::TYPE_STRING)
            $res .= '(' . $this->printNode($params[0]) . ')::text';
             else
            $res .= $this->printNode($params[0]);

        $res .= ',' . $this->printNode($params[1]);

        if (count($params) > 2)
            $res .= ',' . $this->printNode($params[2]);

        return $res . ')';
    }

    public function func_rtrim($func, $params)
    {
        $res = 'rtrim(';

        if ($this->getType($params[0]) != ExprParser::TYPE_STRING)
            $res .= '(' . $this->printNode($params[0]) . ')::text';
             else
            $res .= $this->printNode($params[0]);

        if (count($params) > 1)
            $res .= ',' . $this->printNode($params[1]);

        return $res . ')';
    }

    public function func_second($func, $params)
    {
        if ($params[0]->getNodeType() == ExprParser::NODETYPE_VALUE && $this->getType($params[0]) == ExprParser::TYPE_STRING)
            $param = $this->castValueToTimestamp($params[0]);
        else if ($this->getType($params[0]) == ExprParser::TYPE_NULL)
            $param = 'null::timestamp';
        else
            $param = $this->printNode($params[0]);

        return '(extract(second from (' . $param . ')))';
    }

    public function func_strpart($func, $params)
    {
        $res = 'split_part(';

        if ($this->getType($params[0]) != ExprParser::TYPE_STRING)
            $res .= '(' . $this->printNode($params[0]) . ')::text';
             else
            $res .= $this->printNode($params[0]);

        $res .= ',' . $this->printNode($params[1]);
        $res .= ', (CASE WHEN (' . $this->printNode($params[2]) . ') < 1 THEN 999999 ELSE (' . $this->printNode($params[2]) . ') END)';

        return $res . ')';
    }

    public function func_strpos($func, $params)
    {
        $res = 'strpos(';

        if ($this->getType($params[0]) != ExprParser::TYPE_STRING)
            $res .= '(' . $this->printNode($params[0]) . ')::text';
             else
            $res .= $this->printNode($params[0]);

        $res .= ',' . $this->printNode($params[1]);

        return $res . ')';
    }

    public function func_substr($func, $params)
    {
        $res = 'substr(';

        if ($this->getType($params[0]) != ExprParser::TYPE_STRING)
            $res .= '(' . $this->printNode($params[0]) . ')::text';
             else
            $res .= $this->printNode($params[0]);

        $res .= ',' . $this->printNode($params[1]);

        if (count($params) >= 3)
            $res .= ',' . 'GREATEST(' . $this->printNode($params[2]) . ',0)';

        return $res . ')';
    }

    public function func_to_char($func, $params)
    {
        if ($this->getType($params[0]) == ExprParser::TYPE_NULL)
            return "null::text";

        if (count($params) == 1)
        {
            $param0 = $this->printNode($params[0]);
            return "($param0)::text";
        }
         else if (count($params) == 2)
        {
            $param0 = $this->printNode($params[0]);
            $param1 = $this->printNode($params[1]);
            return "to_char($param0,$param1)";
        }
         else
        {
            return false;
        }
    }

    public function func_to_number($func, $params)
    {
        if ($this->getType($params[0]) == ExprParser::TYPE_NULL)
            return "null::numeric";

        if (count($params) == 1)
        {
            // to_number() with one parameter is the same as cast(fld, numeric)
            $v = new ExprVariable;
            $v->name = 'numeric';
            $params[] = $v;
            return $this->func_cast($func, $params);
        }
         else if (count($params) == 2)
        {
            $param0 = $this->printNode($params[0]);
            $param1 = $this->printNode($params[1]);
            return "to_number($param0,$param1)";
        }
         else
        {
            return false;
        }
    }

    public function func_to_date($func, $params)
    {
        if ($this->getType($params[0]) == ExprParser::TYPE_NULL)
            return "null::date";

        if (count($params) == 1)
        {
            // to_date() with one parameter is the same as cast(fld, date)
            $v = new ExprVariable;
            $v->name = 'date';
            $params[] = $v;
            return $this->func_cast($func, $params);
        }
         else if (count($params) == 2)
        {
            $param0 = $this->printNode($params[0]);
            $param1 = $this->printNode($params[1]);
            return "to_date($param0,$param1)";
        }
         else
        {
            return false;
        }
    }

    public function func_to_timestamp($func, $params)
    {
        if ($this->getType($params[0]) == ExprParser::TYPE_NULL)
            return "null::timestamp";

        if (count($params) == 1)
        {
            // to_timestamp() with one parameter is the same as cast(fld, datetime)
            $v = new ExprVariable;
            $v->name = 'datetime';
            $params[] = $v;
            return $this->func_cast($func, $params);
        }
         else if (count($params) == 2)
        {
            $param0 = $this->printNode($params[0]);
            $param1 = $this->printNode($params[1]);
            return "to_timestamp($param0,$param1)";
        }
         else
        {
            return false;
        }
    }



    public function func_trim($func, $params)
    {
        $res = 'trim(';

        if ($this->getType($params[0]) != ExprParser::TYPE_STRING)
            $res .= '(' . $this->printNode($params[0]) . ')::text';
             else
            $res .= $this->printNode($params[0]);

        if (count($params) > 1)
            $res .= ',' . $this->printNode($params[1]);

        return $res . ')';
    }

    public function func_upper($func, $params)
    {
        $res = 'upper(';

        if ($this->getType($params[0]) != ExprParser::TYPE_STRING)
            $res .= '(' . $this->printNode($params[0]) . ')::text';
             else
            $res .= $this->printNode($params[0]);

        return $res . ')';
    }

    public function func_year($func, $params)
    {
        if ($params[0]->getNodeType() == ExprParser::NODETYPE_VALUE && $this->getType($params[0]) == ExprParser::TYPE_STRING)
            $param = $this->castValueToTimestamp($params[0]);
        else if ($this->getType($params[0]) == ExprParser::TYPE_NULL)
            $param = 'null::timestamp';
        else
            $param = $this->printNode($params[0]);

        return '(extract(year from (' . $param . ')))';
    }


    // this is a generic function that can be used for functions that
    // take a numeric parameter, but also a character fields that
    // should be coerced into a number.   Ex: trunc(9.9); trunc("9.9");
    public function func_generic_numfunc($func, $params)
    {
        if ($this->getType($params[0]) == ExprParser::TYPE_STRING)
        {
            $param = $this->printNode($params[0]);
            return "(CASE WHEN ($param) ~ '^[+-]{0,1}(?:[0-9]+|[0-9]+[.][0-9]*|[0-9]+[.][0-9]+)$' THEN trunc(trim($param)::numeric) ELSE NULL END)";
        }
         else if ($this->getType($params[0]) == ExprParser::TYPE_NULL)
        {
            return 'null::numeric';
        }
         else
        {
            $param = $this->printNode($params[0]);
            return "$func($param)";
        }
    }
}
