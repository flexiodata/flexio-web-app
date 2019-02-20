<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams
 * Created:  2016-03-21
 *
 * @package flexio
 * @subpackage Base
 */


declare(strict_types=1);
namespace Flexio\Base;


class ExprUtil
{
    public static function quote($value)
    {
        // always add quotes; escape any embedded quotes
        $value = (string)$value;

        if (false !== strpos($value, "'"))
            $value = str_replace("'", "''", $value);

        return "'" . $value . "'";
    }

    public static function quoteIfNeeded($value)
    {
        // only add quotes if we have a string that isn't
        // equivalent to a numeric or boolean value

        if (is_bool($value))
            return $value;

        if (is_numeric($value))
            return $value;

        if (is_string($value))
        {
            if (false !== strpos($value, "'"))
                $value = str_replace("'", "''", $value);

            return "'" . $value . "'";
        }

        return null;
    }

    public static function getCastExpression($name, $old_type, $new_type, $new_width = null, $new_scale = null)
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
                    $expr = "cast($expr,character)";
                     else
                    $expr = "cast($expr,character($width))";

                break;

            case 'numeric':
                if ($new_width == -1 || is_null($new_width))
                {
                    $expr = "cast($expr,numeric)";
                }
                 else
                {
                     if ($new_scale == -1 || is_null($new_scale))
                        $expr = "cast($expr,numeric($new_width))";
                         else
                        $expr = "cast($expr,numeric($new_width,$new_scale))";
                }
                break;

            case 'float':
            case 'double':
                $expr = "cast($expr,double)";
                break;

            case 'integer':
                $expr = "cast($expr,integer)";
                break;

            case 'date':
                $expr = "cast($expr,date)";
                break;

            case 'timestamp':
            case 'datetime':
                $expr = "cast($expr,datetime)";
                break;

            case 'boolean':
                $expr = "cast($expr,boolean)";
                break;

            default:
                // unknown/unallowed type
                return null;
        }

        return $expr;
    }
};

