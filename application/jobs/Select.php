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
            // if we don't have a table, we only care about selecting the file,
            // so we're done
            default:
                $outstream->copyFrom($instream);
                return;

            // if we have a table input, perform additional column selection
            case \Flexio\Base\ContentType::FLEXIO_TABLE:
                $this->processTable($instream, $outstream);
                return;
        }
        
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
