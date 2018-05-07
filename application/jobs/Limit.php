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

/*
// EXAMPLE:
{
    "op": "limit",
    "rows": 10
}
*/

class Limit extends \Flexio\Jobs\Base
{
    public function run(\Flexio\IFace\IProcess $process)
    {
        parent::run($process);

        // stdin/stdout
        $instream = $process->getStdin();
        $outstream = $process->getStdout();
        $this->processStream($instream, $outstream);
    }

    private function processStream(\Flexio\IFace\IStream &$instream, \Flexio\IFace\IStream &$outstream)
    {
        $mime_type = $instream->getMimeType();
        switch ($mime_type)
        {
            // unhandled input
            default:
                $outstream->copyFrom($instream);
                return;

            // table input
            case \Flexio\Base\ContentType::FLEXIO_TABLE:
                $this->getOutput($instream, $outstream);
                return;

            // stream/text/csv input
            case \Flexio\Base\ContentType::STREAM:
            case \Flexio\Base\ContentType::TEXT:
            case \Flexio\Base\ContentType::CSV:
                $this->getOutput($instream, $outstream);
                return;
        }
    }

    private function getOutput(\Flexio\IFace\IStream &$instream, \Flexio\IFace\IStream &$outstream)
    {
        // input/output
        $outstream->set($instream->get());
        $outstream->setPath(\Flexio\Base\Util::generateHandle());

        // get the number of rows to return
        $params = $this->getJobParameters();
        $rows = intval((['value'] ?? 0));
        $rows_to_output = ($rows > 0 ? $rows : 0);

        // create the reader/writer
        $streamreader = $instream->getReader();
        $streamwriter = $outstream->getWriter();

        // read the specified number of input rows and write them out
        for ($rown = 0; $rown < $rows_to_output; ++$rown)
        {
            $row = $streamreader->readRow();
            if ($row === false)
                break;

            $result = $streamwriter->write($row);
            if ($result === false)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);
        }

        $streamwriter->close();
        $outstream->setSize($streamwriter->getBytesWritten());
    }
}
