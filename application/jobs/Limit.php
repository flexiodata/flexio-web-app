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
        $this->getOutput()->setEnv($this->getInput()->getEnv());
        $input = $this->getInput()->getStreams();

        foreach ($input as $instream)
        {
            $outstream = false;
            $mime_type = $instream->getMimeType();

            switch ($mime_type)
            {
                // unhandled input
                default:
                    $outstream = $instream->copy();
                    break;

                // table input
                case \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE:
                    $outstream = $this->createOutput($instream);
                    break;

                // stream/text/csv input
                case \Flexio\Base\ContentType::MIME_TYPE_STREAM:
                case \Flexio\Base\ContentType::MIME_TYPE_TXT:
                case \Flexio\Base\ContentType::MIME_TYPE_CSV:
                    $outstream = $this->createOutput($instream);
                    break;
            }

            $this->getOutput()->addStream($outstream);
        }
    }

    private function createOutput(\Flexio\Object\Stream $instream) : \Flexio\Object\Stream
    {
        // input/output
        $outstream = $instream->copy()->setPath(\Flexio\Base\Util::generateHandle());

        // get the number of rows to return
        $job_definition = $this->getProperties();
        $rows = intval(($job_definition['params']['value'] ?? 0));
        $rows_to_output = ($rows > 0 ? $rows : 0);

        // create the reader/writer
        $streamreader = \Flexio\Object\StreamReader::create($instream);
        $streamwriter = \Flexio\Object\StreamWriter::create($outstream);

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
        return $outstream;
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
