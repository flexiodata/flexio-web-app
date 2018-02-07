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

/*
// EXAMPLE:
{
    "op": "select",
    "params": {
        "files" : [
        ],
        "columns": [
        ]
    }
}
*/

class Select extends \Flexio\Jobs\Base
{
    public function run(\Flexio\IFace\IProcess $process)
    {
        parent::run($process);

        // stdin/stdout
        $instream = $process->getStdin();
        $outstream = $process->getStdout();

        $content_type = $instream->getMimeType();
        switch ($content_type)
        {
            default:
                $outstream->copyFrom($instream);
                return;

            case \Flexio\Base\ContentType::JSON:
                $this->processJson($instream, $outstream);
                return;
            
            // if we have a table input, perform additional column selection
            case \Flexio\Base\ContentType::FLEXIO_TABLE:
                $this->processTable($instream, $outstream);
                return;
        }
    }

    private function processJson(\Flexio\IFace\IStream $instream, \Flexio\IFace\IStream $outstream)
    {
        // get the selected columns
        $job_definition = $this->getProperties();
        $columns = $job_definition['params']['columns'] ?? null;
        if (!is_array($columns))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::MISSING_PARAMETER);

        // get the input's payload
        $payload = '';
        $reader = $instream->getReader();
        while (($data = $reader->read(32768)) !== false)
        {
            $payload .= $data;
        }

        $data = @json_decode($payload);
        unset($payload);


        if (is_object($data))
        {
            $output = (object)\Flexio\Base\Util::filterArray((array)$data, $columns);
        }
        else if (is_array($data))
        {
            $output = [];
            foreach ($data as $row)
            {
                if (is_object($row))
                {
                    $row = (object)\Flexio\Base\Util::filterArray((array)$row, $columns);
                }

                $output[] = $row;
            }
        }
        else
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_FORMAT, "The input JSON must be a valid JSON array");
        }

        $payload = json_encode($output);
        $outstream->setMimeType(\Flexio\Base\ContentType::JSON);
        $streamwriter = $outstream->getWriter();
        $streamwriter->write($payload);
        $outstream->setSize($streamwriter->getBytesWritten());
    }

    private function processTable(\Flexio\IFace\IStream $instream, \Flexio\IFace\IStream $outstream)
    {
        // get the selected columns
        $job_definition = $this->getProperties();
        $columns = $job_definition['params']['columns'] ?? null;
        if (!is_array($columns))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::MISSING_PARAMETER);

        $output_structure = $instream->getStructure()->enum($columns);


        $outstream->set(['structure' => $output_structure,
                         'mime_type' => \Flexio\Base\ContentType::FLEXIO_TABLE]);

        // copy the data with the new structure
        $streamreader = $instream->getReader();
        $streamwriter = $outstream->getWriter();

        while (true)
        {
            $row = $streamreader->readRow();
            if ($row === false)
                break;

            $row = \Flexio\Base\Util::filterArray($row, $columns);
            $streamwriter->write($row);
        }

        $streamwriter->close();
    }
}
