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


require_once __DIR__ . DIRECTORY_SEPARATOR . 'Abstract.php';

class Base implements \Flexio\IFace\IJob
{
    // properties for derived classes; these the job parameters
    protected $properties;

    public function __construct()
    {
        $this->properties = array();
    }

    public static function create(array $properties = null) : \Flexio\Jobs\Base
    {
        $object = new static();

        // if properties are specified, set them
        if (isset($properties))
            $object->properties = $properties;

        return $object;
    }

    public function getProperties() : array
    {
        return $this->properties;
    }

    public function run(\Flexio\Jobs\IProcess $process)
    {
        $this->replaceParameterTokens($process);
    }

    public function replaceParameterTokens($process) : \Flexio\Jobs\Base
    {
        $this->replaceParameterTokensRecurse($process, $this->properties);
        return $this;
    }

    private function replaceParameterTokensRecurse($process, &$value)
    {
        // normally, $process is an object that exposes the \Flexio\Jobs\IProcess interface; however, for the
        // convenience of the test suite, a key/value array may be passed instead

        // $value is the array or value that we will replace tokens on

        if (is_array($process))
            $variables = $process;
             else
            $variables = $process->getParams();

        if (is_array($value))
        {
            foreach ($value as $k => &$v)
            {
                $this->replaceParameterTokensRecurse($process, $v);
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
                        $replacement = '';

                        if ($varname == 'stdin')
                        {
                            $replacement = '';
                            $stream = $process->getStdin();
                            $streamreader = $stream->getReader();
                            while (($chunk = $streamreader->read()) !== false)
                                $replacement .= $chunk;
                        }
                         else if (isset($variables[$varname]))
                        {
                            if ($variables[$varname] instanceof \Flexio\Base\Stream)
                            {
                                $replacement = '';
                                $streamreader = $variables[$varname]->getReader();
                                while (($chunk = $streamreader->read()) !== false)
                                    $replacement .= $chunk;
                            }
                             else
                            {
                                $replacement = (string)$variables[$varname];
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
}
