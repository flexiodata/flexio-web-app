<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2017-10-06
 *
 * @package flexio
 * @subpackage Jobs
 */


declare(strict_types=1);
namespace Flexio\Jobs;

/*
// EXAMPLE:
{
    "type": "flexio.list",
    "params": {
        "path": ""
    }
}
*/

class List1 extends \Flexio\Jobs\Base
{
    public function run(\Flexio\Object\Context &$context)
    {
        parent::run($context);

        // process stdin
        $stdin = $context->getStdin();
        $stdout = $context->getStdout();

        $job_definition = $this->getProperties();
        $path = $job_definition['params']['path'] ?? null;

        if (is_null($path))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::MISSING_PARAMETER, "Missing parameter 'path'");

        $streamwriter = $stdout->getWriter();

        $vfs = new \Flexio\Services\Vfs();
        $files = $vfs->list($path);

        $results = [];
        foreach ($files as $f)
        {
            $entry = array(
                'name' => $f['name'],
                'size' => $f['size'],
                'modified' => $f['modified'],
                'type' => $f['type']
            );

            if (isset($f['.connection_eid']))
                $entry['.connection_eid'] = $f['.connection_eid'];

            if (isset($f['.connection_type']))
                $entry['.connection_type'] = $f['.connection_type'];

            $results[] = $entry;
        }

        $stdout->setMimeType(\Flexio\Base\ContentType::MIME_TYPE_JSON);
        $streamwriter->write(json_encode($results));
    }
}
