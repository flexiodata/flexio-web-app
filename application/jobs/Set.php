<?php
/**
 *
 * Copyright (c) 2017, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams
 * Created:  2017-12-14
 *
 * @package flexio
 * @subpackage Jobs
 */


declare(strict_types=1);
namespace Flexio\Jobs;

/*
// EXAMPLE:
{
    "op": "set",
    "params": {
        "var": "myvar",
        "value": "new_value"
    }
}
*/

class Set extends \Flexio\Jobs\Base
{
    public function run(\Flexio\IFace\IProcess $process)
    {
        parent::run($process);

        // get the duration
        $job_params = $this->getJobParameters();
        $var = $job_params['var'];
        $value = $job_params['value'];

        if (isset($value['op']) && isset($value['params']))
        {
            // right side of set is pipe code

            $pipecode = $value;
            $valstream = \Flexio\Base\Stream::create();

            $params = $process->getParams();

            $subprocess = \Flexio\Jobs\Process::create();
            $subprocess->setOwner($process->getOwner());
            $subprocess->setParams($params);
            $subprocess->setStdin($process->getStdin());
            $subprocess->setStdout($valstream);
            $subprocess->execute($pipecode);

            $params[$var] = $valstream;
            $process->setParams($params);
        }
         else
        {
            $is_json = false;
            if (is_array($value) || is_object($value))
            {
                $value = json_encode($value);
                $is_json = true;
            }

            $outstream = $process->getStdout();
            $outstream->getWriter()->write($value);

            $param_stream = \Flexio\Base\Stream::create();
            $param_stream->getWriter()->write($value);

            if ($is_json)
            {
                $outstream->setMimeType(\Flexio\Base\ContentType::JSON);
                $param_stream->setMimeType(\Flexio\Base\ContentType::JSON);
            }

            $params = $process->getParams();
            $params[$var] = $param_stream;
            $process->setParams($params);
        }
    }
}
