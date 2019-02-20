<?php
/**
 *
 * Copyright (c) 2013, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams
 * Created:  2013-02-19
 *
 * @package flexio
 * @subpackage Base
 */


declare(strict_types=1);
namespace Flexio\Base;


class Gzip
{
    public static function gzip($in, $out = false) : bool
    {
        if (!file_exists($in))
            return false;

        if ($out === false)
            $out = $in . '.gz';

        if (file_exists($out))
        {
            @unlink($out);
            if (file_exists($out))
                return false; // can't overwrite output file
        }

        $inf = fopen($in, "rb");
        if (!$inf)
            return false; // can't open file for input
        $outf = gzopen($out, "wb6");
        if (!$outf)
        {
            fclose($inf);
            return false;
        }

        while (true)
        {
            $buf = fread($inf, 16384);
            $buflen = strlen($buf);
            if ($buflen > 0)
                gzwrite($outf, $buf, $buflen);
            if ($buflen != 16384)
                break;
        }

        gzclose($outf);
        fclose($inf);

        return true;
    }

    public static function gunzip($in, $out = false) : bool
    {
        if (!file_exists($in))
            return false;

        if ($out === false)
        {
            if (strtolower(substr($in, -3)) != '.gz')
                return false;

            $out = substr($in, 0, strlen($in) - 3);
        }

        if (file_exists($out))
        {
            @unlink($out);
            if (file_exists($out))
                return false; // can't overwrite output file
        }

        $inf = gzopen($in, "rb");
        if (!$inf)
            return false; // can't open file for input

        $outf = fopen($out, "wb");
        if (!$outf)
        {
            gzclose($inf);
            return false;
        }

        while (true)
        {
            $buf = gzread($inf, 16384);
            $buflen = strlen($buf);
            if ($buflen > 0)
                fwrite($outf, $buf, $buflen);
            if ($buflen != 16384)
                break;
        }

        fclose($outf);
        gzclose($inf);

        return true;
    }
}
