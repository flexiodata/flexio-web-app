<?php
/**
 *
 * Copyright (c) 2009-2011, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams
 * Created:  2012-11-27
 *
 * @package flexio
 * @subpackage System
 */


declare(strict_types=1);
namespace Flexio\System;


class Translate
{
    // use this instead of calling setlocale() by yourself

    public static function setLocale(string $locale) : void
    {
        $suffix = 'utf8';
        if (strtoupper(substr(PHP_OS, 0, 6)) == "DARWIN")
            $suffix = 'UTF-8';

        putenv('LC_ALL=' . $locale . '.' . $suffix);
        setlocale(LC_ALL, $locale . '.' . $suffix);

        putenv('LC_NUMERIC=C');
        setlocale(LC_NUMERIC, 'C');

        if (strtoupper(substr(PHP_OS, 0, 3)) == "WIN")
        {
            switch (substr($locale, 0, 2))
            {
                default:
                case 'en': setlocale(LC_TIME, 'C'); break;
                case 'de': setlocale(LC_TIME, 'German_Germany'); break;
                case 'fr': setlocale(LC_TIME, 'French_France');  break;
                case 'es': setlocale(LC_TIME, 'Spanish_Spain');  break;
                case 'zh': setlocale(LC_TIME, 'Chinese_China');  break;
            }
        }

        bindtextdomain("messages", dirname(__DIR__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'i18n');
        textdomain("messages");
    }

    public static function translateText(string $str) : string
    {
        while (true)
        {
            $start_loc = strpos($str, '_T'.'("');
            if ($start_loc === false)
                break;
            $end_loc = strpos($str, '")', $start_loc);
            if ($end_loc === false)
                break;

            $chunk = substr($str, $start_loc + 4, $end_loc - $start_loc - 4);

            $chunk = str_replace("\\'", "'", $chunk);

            $chunk = gettext($chunk);
            $chunk = str_replace("'", "\\'", $chunk);
            $chunk = str_replace("\n", "\\n", $chunk);
            $chunk = "'" . $chunk . "'";

            $str = substr_replace($str, $chunk, $start_loc, $end_loc - $start_loc + 2);
        }

        while (true)
        {
            $start_loc = strpos($str, '_T'."('");
            if ($start_loc === false)
                break;
            $end_loc = strpos($str, "')", $start_loc);
            if ($end_loc === false)
                break;

            $chunk = substr($str, $start_loc + 4, $end_loc - $start_loc - 4);
            $chunk = str_replace("\\'", "'", $chunk);

            $chunk = gettext($chunk);

            $chunk = "'" . str_replace("'", "\\'", $chunk) . "'";

            $str = substr_replace($str, $chunk, $start_loc, $end_loc - $start_loc + 2);
        }

        return $str;
    }
}
