<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-04-14
 *
 * @package flexio
 * @subpackage Jobs
 */


declare(strict_types=1);
namespace Flexio\Jobs;


class List1 extends \Flexio\Jobs\Base
{
    public function run(\Flexio\Object\Context &$context)
    {
        // process stdin
        $stdin = $context->getStdin();
        $stdout = $stdin->copy()->setPath(\Flexio\Base\Util::generateHandle());
        $context->setStdout($stdout);

        $job_definition = $this->getProperties();
        $path = $job_definition['params']['path'] ?? null;

        if (is_null($path))
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::MISSING_PARAMETER, "Missing parameter 'path'");
        }


        $streamwriter = \Flexio\Object\StreamWriter::create($stdout);

        
        $vfs = new \Flexio\Services\Vfs();

        $files = $vfs->listObjects($path);

        $results = [];
        foreach ($files as $f)
        {
            $entry = array(
                'name' => $f['name'],
                'size' => $f['size'],
                'modified' => $f['modified'],
                'type' => $f['type']
            );

            if (isset($f['.connection_type']))
            {
                $entry['.connection_type'] = $f['.connection_type'];
            }

            $results[] = $entry;
        }

        $stdout->setMimeType(\Flexio\Base\ContentType::MIME_TYPE_JSON);
        $streamwriter->write(json_encode($results));
    }


    // job definition info
    const MIME_TYPE = 'flexio.list';
    const TEMPLATE = <<<EOD
    {
        "type": "flexio.list",
        "params": {
            "path": ""
        }
    }
EOD;
    // direction is "asc" or "desc"
    const SCHEMA = <<<EOD
    {
        "type": "object",
        "required": ["type","params"],
        "properties": {
            "type": {
                "type": "string",
                "enum": ["flexio.list"]
            },
            "params": {
                "type": "object",
                "required": ["value"],
                "properties": {

                }
            }
        }
    }
EOD;
}
