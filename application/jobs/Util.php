<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-06-10
 *
 * @package flexio
 * @subpackage Jobs
 */


declare(strict_types=1);
namespace Flexio\Jobs;


class Util
{
    public static function replaceParameterTokens($process, array &$task) : void
    {
        // note: $process can be either a process or an array
        self::replaceParameterTokensRecurse($process, $task);
    }

    public static function addEids(array $task) : array
    {
        // adds an eid to a task; note: eids are no longer added to
        // tasks, but keep the function here along with the associated
        // tests in case it's needed in the future

        if (isset($task['op']))
        {
            if (!isset($task['eid']))
                $task['eid'] = \Flexio\Base\Eid::generate();
        }

        foreach ($task as $key => &$value)
        {
            if (!is_array($value))
                continue;

            $value = self::addEids($value);
        }

        return $task;
    }

    public static function fixEmptyParams(array $task) : array
    {
        // TODO: temporary fix for making sure empty params are stored as
        // objects; this happens because we're decoding JSON into an associative
        // array in the ApiController; should look for a more comprehensive solution
        // since this can affect other parameters in other API payloads

        if (isset($task['params']) && is_array($task['params']) && count($task['params']) == 0)
            $task['params'] = (object)array();

        foreach ($task as $key => &$value)
        {
            if (!is_array($value))
                continue;

            $value = self::fixEmptyParams($value);
        }

        return $task;
    }

    public static function flattenParams(array $task) : array
    {
        // helper function for iterating through a task and moving all key
        // values from the params node to the top-level; used currently to
        // convert tasks coming in through the old format into the new format
        // until the tasks are migrated to the new format in the SDK, etc

        if (isset($task['params']))
        {
            if (is_array($task['params']))
            {
                foreach ($task['params'] as $key => $value)
                {
                    $task[$key] = $value;
                }
            }
        }

        if (is_array($task))
        {
            foreach ($task as $key => &$value)
            {
                if (!is_array($value))
                    continue;

                $value = self::flattenParams($value);
            }
        }

        return $task;
    }

    private static function replaceParameterTokensRecurse($process, &$value) : void
    {
        if (is_array($value))
        {
            // don't replace subsequences
            if (isset($value['op']) && $value['op'] == 'sequence')
                return;

            foreach ($value as $k => &$v)
            {
                self::replaceParameterTokensRecurse($process, $v);
            }
        }
         else
        {
            if (is_string($value))
            {
                $re = '/\$\{.*?}/';

                preg_match_all($re, $value, $matches, PREG_OFFSET_CAPTURE, 0);

                if (isset($matches[0]))
                {
                    $differential = 0; // keep track of the offsets when we replace due to the difference of the token lengths vs value length

                    foreach ($matches[0] as $match)
                    {
                        $token = $match[0];
                        $token_len = strlen($token);
                        $offset = $match[1];

                        $varname = substr($token, 2, -1);  // turn '${myvar}' into 'myvar'

                        $stream = self::getParameterStream($process, $varname);
                        $replacement = '';

                        if ($stream !== null)
                        {
                            $streamreader = $stream->getReader();
                            while (($chunk = $streamreader->read()) !== false)
                                $replacement .= $chunk;
                        }

                        // use true/false text for boolean value replacements in a string
                        if ($replacement === true)
                            $replacement = 'true';
                        if ($replacement === false)
                            $replacement = 'false';

                        // TODO: need to handle replacements of non-string variable types
                        if (!is_string($replacement))
                            continue;

                        $value = substr_replace($value, $replacement, $offset + $differential, $token_len);
                        $differential += (strlen($replacement) - $token_len);
                    }
                }
            }
        }
    }

    private static function getParameterStream($process, string $varname, array $info = null) : ?\Flexio\Base\Stream
    {
        if ($info === null)
        {
            $info = [];
            if (is_array($process))
            {
                $variables = $process;
                $files = [];
            }
            else
            {
                $variables = $process->getParams();
                $files = $process->getFiles();
            }
        }


        $dot = strpos($varname, '[');
        if ($dot !== false)
        {
            // replace var[1] with var.1
            $varname = preg_replace("/(\\[)(\\d+)(\\])/", '.$2', $varname);

            // replace var['abc'] with var.abc;   var["abc"] with var.abc
            $varname = preg_replace("/(\\[[\"'])([^\"']+)([\"']\\])/", '.$2', $varname);
        }

        $parts = [];
        $dot = strpos($varname, '.');
        if ($dot !== false)
        {
            if (isset($variables[$varname]))
            {
                return $variables[$varname];
            }

            $parts = explode('.', $varname);

            $varname = array_shift($parts);
        }



        $stream = null;

        if ($varname == 'stdin' || $varname == 'input')
        {
            $stream = $process->getStdin();
        }
        else if ($varname == 'uniqid')
        {
            $stream = sha1(uniqid(\Flexio\Base\Util::generateRandomString(20), true));
        }
        else if ($varname == 'files')
        {
            $stream = [];
            foreach ($files as $k => $file)
            {
                $name = $file->getName();
                $ext = pathinfo($name, PATHINFO_EXTENSION);
                $size = $file->getSize();
                $content_type = $file->getMimeType();
                $stream[$k] = [ 'name' => $name, 'extension' => $ext, 'size' => $size, 'content_type' => $content_type ];
            }
        }
        else if (isset($variables[$varname]))
        {
            $stream = $variables[$varname];
        }




        if ($stream === null)
        {
            return null;
        }



        if (count($parts) == 0)
        {
            return self::ensureStream($stream);
        }


        if (is_string($stream))
        {
            $data = @json_decode($stream);
        }
         else
        {
            $stream = self::ensureStream($stream);
            $data = '';
            $streamreader = $stream->getReader();
            while (($chunk = $streamreader->read()) !== false)
                $data .= $chunk;
            $data = @json_decode($data, true);
        }


        while (count($parts) > 0)
        {
            $name = array_shift($parts);
            if (is_string($name) && is_numeric($name))
            {
                $name = (int)$name;
            }
            if (!isset($data[$name]))
                return null;
            $data = $data[$name];
        }

        if (is_null($data))
            return null;

        if (is_array($data) || is_object($data))
        {
            $data = json_encode($data, JSON_UNESCAPED_SLASHES);
            return self::ensureStream($data, \Flexio\Base\ContentType::JSON);
        }


        return self::ensureStream($data);
    }

    private static function ensureStream($stream, string $content_type = null) : \Flexio\Base\Stream
    {
        if ($stream instanceof \Flexio\Base\Stream)
            return $stream;

        if (is_object($stream) || is_array($stream))
        {
            $data = json_encode($stream, JSON_UNESCAPED_SLASHES);
            if ($content_type === null)
            {
                $content_type = \Flexio\Base\ContentType::JSON;
            }
        }
         else
        {
            if ($stream === true)
                $data = 'true';
            else if ($stream === false)
                $data = 'false';
            else
                $data = (string)$stream;
        }

        $res = \Flexio\Base\Stream::create();
        if ($content_type !== null)
        {
            $res->setMimeType($content_type);
        }

        $res->buffer = $data;     // shortcut to speed it up -- can also use getWriter()->write((string)$v)
        return $res;
    }
}