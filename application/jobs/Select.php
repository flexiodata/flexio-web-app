<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie LLC. All rights reserved.
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
// DESCRIPTION:
{
    "op": "select",  // string, required
    "columns": [     // array, required; array of strings of the columns to select
    ]
}

// VALIDATOR:
$validator = \Flexio\Base\Validator::create();
if (($validator->check($params, array(
        'op'         => array('required' => true,  'enum' => ['select'])
    ))->hasErrors()) === true)
    throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);
*/

class Select extends \Flexio\Jobs\Base
{
    public function run(\Flexio\IFace\IProcess $process) : void
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

    private function processJson(\Flexio\IFace\IStream $instream, \Flexio\IFace\IStream $outstream) : void
    {
        // get the selected columns
        $params = $this->getJobParameters();
        $columns = $params['columns'] ?? null;
        if (!is_array($columns))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

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
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::EXECUTE_FAILED, "The input JSON must be a valid JSON array");
        }

        $payload = json_encode($output, JSON_UNESCAPED_SLASHES);
        $outstream->setMimeType(\Flexio\Base\ContentType::JSON);
        $streamwriter = $outstream->getWriter();
        $streamwriter->write($payload);
        $outstream->setSize($streamwriter->getBytesWritten());
    }

    private function processTable(\Flexio\IFace\IStream $instream, \Flexio\IFace\IStream $outstream) : void
    {
        // get the selected columns
        $params = $this->getJobParameters();
        $columns = $params['columns'] ?? null;
        if (!is_array($columns))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $output_structure = $instream->getStructure()->enum($columns);
        if (count($output_structure) == 0)
        {
            // this was option 1. Create empty output structure
            //$output_structure[] = [ "name" => "no_columns", "type" => "text" ];

            // this is option 2. Strict, don't allow zero-column tables
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED, "Zero-column tables are not allowed");
        }

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
