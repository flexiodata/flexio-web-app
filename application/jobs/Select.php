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
    public function run(\Flexio\Object\Context &$context)
    {
        // process stdin
        $stdin = $context->getStdin();
        $stdout = $context->getStdout();
        $this->processStream($stdin, $stdout);

        // process stream array
        $input = $context->getStreams();
        $context->clearStreams();

        foreach ($input as $instream)
        {
            $outstream = \Flexio\Object\StreamMemory::create();
            $this->processStream($instream, $outstream);
            $context->addStream($outstream);
        }
    }

    private function processStream(\Flexio\Object\IStream $instream, \Flexio\Object\IStream $outstream)
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
            {
                $outstream = $instream;
                return;
            }
        }

        $mime_type = $instream->getMimeType();
        switch ($mime_type)
        {
            // if we don't have a table, we only care about selecting the file,
            // so we're done
            default:
                $outstream = $instream;
                return;

            // if we have a table input, perform additional column selection
            case \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE:
                $this->getOutput($instream, $outstream);
                return;
        }
    }

    private function getOutput(\Flexio\Object\IStream $instream, \Flexio\Object\IStream $outstream)
    {
        // input/output
        $outstream->set($instream->get());
        $outstream->setPath(\Flexio\Base\Util::generateHandle());

        // get the selected columns
        $job_definition = $this->getProperties();
        $params = $job_definition['params'];
        if (!isset($params['columns']) || !is_array($params['columns']))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::MISSING_PARAMETER);

        $output_structure = $instream->getStructure()->enum($params['columns']);
        $outstream->setStructure($output_structure);

        // copy the data with the new structure
        $streamreader = $instream->getReader();
        $streamwriter = $outstream->getWriter();

        while (true)
        {
            $row = $streamreader->readRow();
            if ($row === false)
                break;

            $streamwriter->write($row);
        }

        $streamwriter->close();
        $outstream->setSize($streamwriter->getBytesWritten());
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
