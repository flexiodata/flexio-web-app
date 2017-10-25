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

class Base implements \Flexio\Jobs\IJob
{
    // properties for derived classes; these the job parameters
    protected $properties;

    public function __construct()
    {
        $properties = array();
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

    public function run(\Flexio\Object\Context &$context)
    {
        $this->replaceParameterTokens($context);
    }

    public function replaceParameterTokens($context)
    {
        $this->replaceParameterTokensRecurse($context, $this->properties);
    }

    private function replaceParameterTokensRecurse($context, &$value)
    {
        $variables = $context->getEnv();

        if (is_array($value))
        {
            foreach ($value as $k => &$v)
            {
                $this->replaceParameterTokensRecurse($context, $v);
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

                        if (isset($variables[$varname]))
                        {
                            $replacement = $variables[$varname];
                        }

                        $value = substr_replace($value, $replacement, $offset + $differential, $token_len);
                        $differential += (strlen($replacement) - $token_len);
                    }
                }
            }
        }
    }
}
