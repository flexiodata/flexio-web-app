<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
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


class Base implements \Flexio\IFace\IJob
{
    // properties for derived classes; these the job parameters
    protected $properties;

    public function __construct()
    {
        $this->properties = array();
    }

    public static function create(array $task) : \Flexio\Jobs\Base
    {
        $object = new static();
        $object->properties = $task;
        return $object;
    }

    public function getProperties() : array
    {
        return $this->properties;
    }

    public function validate() : array
    {
        $errors = array();
        return $errors;
    }

    public function run(\Flexio\IFace\IProcess $process)
    {
        $this->replaceParameterTokens($process);
    }

    public function replaceParameterTokens($process) : \Flexio\Jobs\Base
    {
        $info = [];

        // normally, $process is an object that exposes the \Flexio\IFace\IProcess interface; however, for the
        // convenience of the test suite, a key/value array may be passed instead.
        // $value is the array or value that we will replace tokens on

        if (is_array($process))
        {
            $info['variables'] = $process;
            $info['files'] = [];
        }
        else
        {
            $info['variables'] = $process->getParams();
            $info['files'] = $process->getFiles();
        }


        $this->replaceParameterTokensRecurse($info, $process, $this->properties);
        return $this;
    }

    private function replaceParameterTokensRecurse(&$info, $process, &$value)
    {
        if (is_array($value))
        {
            // don't replace subsequences
            if (isset($value['op']) && $value['op'] == 'sequence')
                return;

            foreach ($value as $k => &$v)
            {
                $this->replaceParameterTokensRecurse($info, $process, $v);
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
                        $suffix = '';
                        $replacement = '';

                        $period = strpos($varname, '.');
                        if ($period !== false)
                        {
                            $suffix = substr($varname, $period+1);
                            $varname = substr($varname, 0, $period);
                        }

                        if ($varname == 'stdin' || $varname == 'input')
                        {
                            $stream = $process->getStdin();
                            $streamreader = $stream->getReader();
                            while (($chunk = $streamreader->read()) !== false)
                                $replacement .= $chunk;

                        }
                        else if ($varname == 'uniqid')
                        {
                            $replacement = sha1(uniqid(\Flexio\Base\Util::generateRandomString(20), true));
                        }
                        else if ($varname == 'files')
                        {
                            $parts = explode('.', $suffix);

                            if (count($parts) >= 1 && isset($info['files'][$part[0]]))
                            {
                                $file = $info['files'][$part[0]];
                                if (($parts[1] ?? '') == 'name')
                                {
                                    if ($file instanceof \Flexio\Base\Stream)
                                        $replacement = $file->getName();
                                }
                            }
                        }
                        else
                        {
                            $var = $info['variables'][$varname] ?? null;
                            if ($var !== null)
                            {
                                if ($var instanceof \Flexio\Base\Stream)
                                {
                                    $streamreader = $var->getReader();
                                    while (($chunk = $streamreader->read()) !== false)
                                        $replacement .= $chunk;
                                }
                                else
                                {
                                    if (is_array($var))
                                    {
                                        $replacement = json_encode($var);
                                    }
                                    else
                                    {
                                        $replacement = (string)$var;
                                    }
                                }
                            }
                        }

                        if (strlen($suffix) > 0)
                        {
                            $data = json_decode($replacement, true);
                            $replacement = '';
                            if (isset($data[$suffix]))
                            {
                                $replacement = (string)$data[$suffix];
                            }
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

    public static function addEids(array $task) : array
    {
        // if a task eid isn't set, then add one

        // TODO: eventually, we may want to check if eid is unique and store it in the
        // database; however, for now, task eids are only used internally for identifying
        // tasks within a job for purposes of correlating preview info with the task, so
        // there's no need to do any checks and eids are sufficiently unique to not worry
        // about duplicate eids within a single task

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
}
