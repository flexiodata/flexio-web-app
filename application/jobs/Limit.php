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


class Limit extends \Flexio\Jobs\Base
{
    public function run(\Flexio\Object\Context &$context)
    {
        parent::run($context);

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

    private function processStream(\Flexio\Object\IStream &$instream, \Flexio\Object\IStream &$outstream)
    {
        $mime_type = $instream->getMimeType();
        switch ($mime_type)
        {
            // unhandled input
            default:
                $outstream = $instream;
                return;

            // table input
            case \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE:
                $this->getOutput($instream, $outstream);
                return;

            // stream/text/csv input
            case \Flexio\Base\ContentType::MIME_TYPE_STREAM:
            case \Flexio\Base\ContentType::MIME_TYPE_TXT:
            case \Flexio\Base\ContentType::MIME_TYPE_CSV:
                $this->getOutput($instream, $outstream);
                return;
        }
    }

    private function getOutput(\Flexio\Object\IStream &$instream, \Flexio\Object\IStream &$outstream)
    {
        // input/output
        $outstream->set($instream->get());
        $outstream->setPath(\Flexio\Base\Util::generateHandle());

        // get the number of rows to return
        $job_definition = $this->getProperties();
        $rows = intval(($job_definition['params']['value'] ?? 0));
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


    // job definition info
    const MIME_TYPE = 'flexio.limit';
    const TEMPLATE = <<<EOD
    {
        "type": "flexio.limit",
        "params": {
            "rows": 10
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
                "enum": ["flexio.limit"]
            },
            "params": {
                "type": "object",
                "required": ["value"],
                "properties": {
                    "rows": {
                        "type": "integer",
                        "minimum": 1
                    }
                }
            }
        }
    }
EOD;
}
