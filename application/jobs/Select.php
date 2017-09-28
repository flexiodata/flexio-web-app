<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams, Benjamin I. Williams
 * Created:  2015-12-03
 *
 * @package flexio
 * @subpackage Jobs
 */


declare(strict_types=1);
namespace Flexio\Jobs;


class Select extends \Flexio\Jobs\Base
{
    public function run()
    {
        $this->getOutput()->setEnv($this->getInput()->getEnv());
        $input = $this->getInput()->getStreams();

        foreach ($input as $instream)
        {
            $this->createOutput($instream);
        }
    }

    private function createOutput(\Flexio\Object\Stream $instream)
    {
        $job_definition = $this->getProperties();
        $mime_type = $instream->getMimeType();

        // if we have an output file filter, see if the filename
        // matches any of the filters; if not, we're done
        if (isset($job_definition['params']['files']))
        {
            $files = $job_definition['params']['files'];
            if (!is_array($files))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

            $filematches = false;
            $filename = $instream->getName();
            foreach ($files as $pattern)
            {
                if (\Flexio\Base\File::matchPath($filename, $pattern, true) === false)
                    continue;

                $filematches = true;
                break;
            }

            // file doesn't match any of the paths; we're done
            if ($filematches === false)
                return;
        }

        $outstream = false;
        $mime_type = $instream->getMimeType();

        switch ($mime_type)
        {
            // if we don't have a table, we only care about selecting the file,
            // so we're done
            default:
                $outstream = $instream->copy();
                break;

            // if we have a table input, perform additional column selection
            case \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE:
                $outstream = $this->createOutputFromTable($instream);
                break;
        }

        $this->getOutput()->addStream($outstream);
    }

    private function createOutputFromTable(\Flexio\Object\Stream $instream) : \Flexio\Object\Stream
    {
        // input/output
        $outstream = $instream->copy(); // copy everything, including the original path (since we're only selecting fields)

        // get the selected columns
        $job_definition = $this->getProperties();
        $params = $job_definition['params'];
        if (!isset($params['columns']) || !is_array($params['columns']))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::MISSING_PARAMETER);

        $output_structure = $instream->getStructure()->enum($params['columns']);
        $outstream->setStructure($output_structure);
        return $outstream;
    }


    // job definition info
    const MIME_TYPE = 'flexio.select';
    const TEMPLATE = <<<EOD
    {
        "type": "flexio.select",
        "params": {
            "files" : [
            ],
            "columns": [
            ]
        }
    }
EOD;
    const SCHEMA = <<<EOD
    {
        "type": "object",
        "required": ["type","params"],
        "properties": {
            "type": {
                "type": "string",
                "enum": ["flexio.select"]
            },
            "params": {
                "type": "object",
                "properties": {
                    "files": {
                        "type": "array",
                        "items": {
                            "type": "string"
                        }
                    },
                    "columns": {
                        "type": "array",
                        "items": {
                            "type": "string"
                        }
                    }
                }
            }
        }
    }
EOD;
}
