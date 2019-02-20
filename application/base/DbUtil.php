<?php
/**
 *
 * Copyright (c) 2009-2012, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams; David Z. Williams
 * Created:  2012-03-27
 *
 * @package flexio
 * @subpackage Base
 */


declare(strict_types=1);
namespace Flexio\Base;


class DbUtil
{
    public const TYPE_TEXT          = 'text';
    public const TYPE_CHARACTER     = 'character';
    public const TYPE_WIDECHARACTER = 'widecharacter';
    public const TYPE_NUMERIC       = 'numeric';
    public const TYPE_DOUBLE        = 'double';
    public const TYPE_INTEGER       = 'integer';
    public const TYPE_DATE          = 'date';
    public const TYPE_DATETIME      = 'datetime';
    public const TYPE_BOOLEAN       = 'boolean';

    public static function formatSql($sql)
    {
        // remove all excess white space
        $sql = str_replace("\n", "", $sql);
        $sql = str_replace("\t", "", $sql);

        $count = 0;
        while (true)
        {
            $sql = str_replace("  ", " ", $sql, $count);
            if ($count == 0)
                break;
        }

        // add \n's for readability before and/or after keywords
        $sql = str_ireplace("select ",     "\nselect\n    ",   $sql);
        $sql = str_ireplace("from ",       "\nfrom\n    ",     $sql);
        $sql = str_ireplace("into ",       "\ninto\n    ",     $sql);
        $sql = str_ireplace("where ",      "\nwhere\n    ",    $sql);
        $sql = str_ireplace("inner join ", "\ninner join ",    $sql);
        $sql = str_ireplace("left join ",  "\nleft join ",     $sql);
        $sql = str_ireplace("group by ",   "\ngroup by\n    ", $sql);
        $sql = str_ireplace("having ",     "\nhaving\n    ",   $sql);
        $sql = str_ireplace("order by ",   "\norder by\n    ", $sql);
        $sql = str_ireplace("limit ",      "\nlimit ",         $sql);
        $sql = str_ireplace("set ",        "\nset ",           $sql);
        $sql = str_ireplace(", ",          ",\n    ",          $sql);

        return $sql;
    }

    public static function parseSqlScript($script_path)
    {
        $commands = array();

        $f = fopen($script_path, "rb");
        if (!$f)
            return null;

        $total_length = filesize($script_path);
        $contents = fread($f, $total_length);
        fclose($f);

        $delimiter = ";";
        $offset = 0;
        $done = false;

        while (!$done)
        {
            if ($offset >= $total_length)
                break;

            // find the first delimiter

            $pos1 = strpos($contents, $delimiter . "\r\n", $offset);
            $pos2 = strpos($contents, $delimiter . "\n", $offset);
            $pos = null;

            if ($pos1 === false && $pos2 === false)
            {
                $pos = strlen($contents);
                $done = true;
            }
             else
            {
                if ($pos1 === false) $pos1 = PHP_INT_MAX;
                if ($pos2 === false) $pos2 = PHP_INT_MAX;
                $pos = min($pos1,$pos2);
            }

            while ($contents[$offset] == "\r" ||
                   $contents[$offset] == "\n" ||
                   $contents[$offset] == "\t" ||
                   $contents[$offset] == " ")
            {
                $offset++;
                if ($offset >= $total_length)
                    break;
            }

            $part = trim(substr($contents, $offset, $pos-$offset));

            if (strncasecmp($part, "delimiter ", 10) == 0)
            {
                $delimiter = "";
                $off = 10;
                while ($off < strlen($part) && $part[$off] != "\r" && $part[$off] != "\n")
                {
                    $delimiter .= $part[$off++];
                }

                $offset += $off;
                $done = false;
                continue;
            }

            if (strlen($part) > 0)
                $commands[] = $part;

            if ($done)
                break;

            $offset = $pos;

            $offset += strlen($delimiter);
        }

        return $commands;
    }

    public static function quoteIdentifierIfNecessary($str)
    {
        if (!is_string($str))
            return null;

        $str = str_replace('?', '', $str);

        if (false === strpbrk($str, "\"'-/\\!@#$%^&*() \t"))
            return $str;
             else
            return ('"' . $str . '"');
    }

    public static function getCompatibleType($type1, $type2)
    {
        // gets a field type that can represent both input field types
        switch ($type1)
        {
            case self::TYPE_TEXT:
            {
                if ($type2 === self::TYPE_TEXT)
                    return self::TYPE_TEXT;

                if ($type2 === self::TYPE_CHARACTER)
                    return self::TYPE_TEXT;

                if ($type2 === self::TYPE_WIDECHARACTER)
                    return self::TYPE_TEXT;

                if ($type2 === self::TYPE_NUMERIC)
                    return self::TYPE_TEXT;

                if ($type2 === self::TYPE_DOUBLE)
                    return self::TYPE_TEXT;

                if ($type2 === self::TYPE_INTEGER)
                    return self::TYPE_TEXT;

                if ($type2 === self::TYPE_DATE)
                    return self::TYPE_TEXT;

                if ($type2 === self::TYPE_DATETIME)
                    return self::TYPE_TEXT;

                if ($type2 === self::TYPE_BOOLEAN)
                    return self::TYPE_TEXT;
            }
            break;

            case self::TYPE_CHARACTER:
            {
                if ($type2 === self::TYPE_TEXT)
                    return self::TYPE_TEXT;

                if ($type2 === self::TYPE_CHARACTER)
                    return self::TYPE_CHARACTER;

                if ($type2 === self::TYPE_WIDECHARACTER)
                    return self::TYPE_WIDECHARACTER;

                if ($type2 === self::TYPE_NUMERIC)
                    return self::TYPE_CHARACTER;

                if ($type2 === self::TYPE_DOUBLE)
                    return self::TYPE_CHARACTER;

                if ($type2 === self::TYPE_INTEGER)
                    return self::TYPE_CHARACTER;

                if ($type2 === self::TYPE_DATE)
                    return self::TYPE_CHARACTER;

                if ($type2 === self::TYPE_DATETIME)
                    return self::TYPE_CHARACTER;

                if ($type2 === self::TYPE_BOOLEAN)
                    return self::TYPE_CHARACTER;
            }
            break;

            case self::TYPE_WIDECHARACTER:
            {
                if ($type2 === self::TYPE_TEXT)
                    return self::TYPE_TEXT;

                if ($type2 === self::TYPE_CHARACTER)
                    return self::TYPE_WIDECHARACTER;

                if ($type2 === self::TYPE_WIDECHARACTER)
                    return self::TYPE_WIDECHARACTER;

                if ($type2 === self::TYPE_NUMERIC)
                    return self::TYPE_WIDECHARACTER;

                if ($type2 === self::TYPE_DOUBLE)
                    return self::TYPE_WIDECHARACTER;

                if ($type2 === self::TYPE_INTEGER)
                    return self::TYPE_WIDECHARACTER;

                if ($type2 === self::TYPE_DATE)
                    return self::TYPE_WIDECHARACTER;

                if ($type2 === self::TYPE_DATETIME)
                    return self::TYPE_WIDECHARACTER;

                if ($type2 === self::TYPE_BOOLEAN)
                    return self::TYPE_WIDECHARACTER;
            }
            break;

            case self::TYPE_NUMERIC:
            {
                if ($type2 === self::TYPE_TEXT)
                    return self::TYPE_TEXT;

                if ($type2 === self::TYPE_CHARACTER)
                    return self::TYPE_CHARACTER;

                if ($type2 === self::TYPE_WIDECHARACTER)
                    return self::TYPE_WIDECHARACTER;

                if ($type2 === self::TYPE_NUMERIC)
                    return self::TYPE_NUMERIC;

                if ($type2 === self::TYPE_DOUBLE)
                    return self::TYPE_NUMERIC;

                if ($type2 === self::TYPE_INTEGER)
                    return self::TYPE_NUMERIC;

                if ($type2 === self::TYPE_DATE)
                    return self::TYPE_CHARACTER;

                if ($type2 === self::TYPE_DATETIME)
                    return self::TYPE_CHARACTER;

                if ($type2 === self::TYPE_BOOLEAN)
                    return self::TYPE_CHARACTER;
            }
            break;

            case self::TYPE_DOUBLE:
            {
                if ($type2 === self::TYPE_TEXT)
                    return self::TYPE_TEXT;

                if ($type2 === self::TYPE_CHARACTER)
                    return self::TYPE_CHARACTER;

                if ($type2 === self::TYPE_WIDECHARACTER)
                    return self::TYPE_WIDECHARACTER;

                if ($type2 === self::TYPE_NUMERIC)
                    return self::TYPE_NUMERIC;

                if ($type2 === self::TYPE_DOUBLE)
                    return self::TYPE_DOUBLE;

                if ($type2 === self::TYPE_INTEGER)
                    return self::TYPE_DOUBLE;

                if ($type2 === self::TYPE_DATE)
                    return self::TYPE_CHARACTER;

                if ($type2 === self::TYPE_DATETIME)
                    return self::TYPE_CHARACTER;

                if ($type2 === self::TYPE_BOOLEAN)
                    return self::TYPE_CHARACTER;
            }
            break;

            case self::TYPE_INTEGER:
            {
                if ($type2 === self::TYPE_TEXT)
                    return self::TYPE_TEXT;

                if ($type2 === self::TYPE_CHARACTER)
                    return self::TYPE_CHARACTER;

                if ($type2 === self::TYPE_WIDECHARACTER)
                    return self::TYPE_WIDECHARACTER;

                if ($type2 === self::TYPE_NUMERIC)
                    return self::TYPE_NUMERIC;

                if ($type2 === self::TYPE_DOUBLE)
                    return self::TYPE_DOUBLE;

                if ($type2 === self::TYPE_INTEGER)
                    return self::TYPE_INTEGER;

                if ($type2 === self::TYPE_DATE)
                    return self::TYPE_CHARACTER;

                if ($type2 === self::TYPE_DATETIME)
                    return self::TYPE_CHARACTER;

                if ($type2 === self::TYPE_BOOLEAN)
                    return self::TYPE_CHARACTER;
            }
            break;

            case self::TYPE_DATE:
            {
                if ($type2 === self::TYPE_TEXT)
                    return self::TYPE_TEXT;

                if ($type2 === self::TYPE_CHARACTER)
                    return self::TYPE_CHARACTER;

                if ($type2 === self::TYPE_WIDECHARACTER)
                    return self::TYPE_WIDECHARACTER;

                if ($type2 === self::TYPE_NUMERIC)
                    return self::TYPE_CHARACTER;

                if ($type2 === self::TYPE_DOUBLE)
                    return self::TYPE_CHARACTER;

                if ($type2 === self::TYPE_INTEGER)
                    return self::TYPE_CHARACTER;

                if ($type2 === self::TYPE_DATE)
                    return self::TYPE_DATE;

                if ($type2 === self::TYPE_DATETIME)
                    return self::TYPE_DATETIME;

                if ($type2 === self::TYPE_BOOLEAN)
                    return self::TYPE_CHARACTER;
            }
            break;

            case self::TYPE_DATETIME:
            {
                if ($type2 === self::TYPE_TEXT)
                    return self::TYPE_TEXT;

                if ($type2 === self::TYPE_CHARACTER)
                    return self::TYPE_CHARACTER;

                if ($type2 === self::TYPE_WIDECHARACTER)
                    return self::TYPE_WIDECHARACTER;

                if ($type2 === self::TYPE_NUMERIC)
                    return self::TYPE_CHARACTER;

                if ($type2 === self::TYPE_DOUBLE)
                    return self::TYPE_CHARACTER;

                if ($type2 === self::TYPE_INTEGER)
                    return self::TYPE_CHARACTER;

                if ($type2 === self::TYPE_DATE)
                    return self::TYPE_DATETIME;

                if ($type2 === self::TYPE_DATETIME)
                    return self::TYPE_DATETIME;

                if ($type2 === self::TYPE_BOOLEAN)
                    return self::TYPE_CHARACTER;
            }
            break;

            case self::TYPE_BOOLEAN:
            {
                if ($type2 === self::TYPE_TEXT)
                    return self::TYPE_TEXT;

                if ($type2 === self::TYPE_CHARACTER)
                    return self::TYPE_CHARACTER;

                if ($type2 === self::TYPE_WIDECHARACTER)
                    return self::TYPE_WIDECHARACTER;

                if ($type2 === self::TYPE_NUMERIC)
                    return self::TYPE_CHARACTER;

                if ($type2 === self::TYPE_DOUBLE)
                    return self::TYPE_CHARACTER;

                if ($type2 === self::TYPE_INTEGER)
                    return self::TYPE_CHARACTER;

                if ($type2 === self::TYPE_DATE)
                    return self::TYPE_CHARACTER;

                if ($type2 === self::TYPE_DATETIME)
                    return self::TYPE_CHARACTER;

                if ($type2 === self::TYPE_BOOLEAN)
                    return self::TYPE_BOOLEAN;
            }
            break;
        }

        // default
        return self::TYPE_TEXT;
    }
}
