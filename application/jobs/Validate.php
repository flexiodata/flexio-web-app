<?php
/**
 *
 * Copyright (c) 2018, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2018-04-06
 *
 * @package flexio
 * @subpackage Jobs
 */


declare(strict_types=1);
namespace Flexio\Jobs;

/*
// EXAMPLE:
{
    "op": "validate",
    "path": ""
    "validator": {
        "type": "object",
        "properties": {
            "mime_type": {
                "type": "string",
                "enum": ["image/png", "image/jpeg", "image/gif", "image/tiff"]
            }
        }
    }
}
*/


class Validate extends \Flexio\Jobs\Base
{
    public function run(\Flexio\IFace\IProcess $process)
    {
        parent::run($process);

        $outstream = $process->getStdout();
        $job_params = $this->getJobParameters();
        $validator = (object)$job_params['validator'] ?? null;

        if (is_null($validator))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::MISSING_PARAMETER, "Missing parameter 'validator'");

        // make sure the validator is a valid validator schema
        if (\Flexio\Base\ValidatorSchema::checkSchema($validator)->hasErrors())
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER, "Invalid validator format");

        $instream = $process->getStdin();
        $info = $instream->get();

        $info_to_validate = array(
            'name' => $info['name'] ?? '',
            'path' => $info['path'] ?? '',
            'size' => $info['size'] ?? null,
            'modified' => $info['modified'] ?? '',
            'mime_type' => $info['content_type'] ?? 'application/octet-stream'
        );

        if (\Flexio\Base\ValidatorSchema::check($info_to_validate, $validator)->hasErrors())
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER, "Invalid file");
    }
}

