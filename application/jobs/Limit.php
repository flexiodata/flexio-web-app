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


namespace Flexio\Jobs;


require_once __DIR__ . DIRECTORY_SEPARATOR . 'Base.php';

class Limit extends \Flexio\Jobs\Base
{
    public function run()
    {
        $input = $this->getInput()->enum();
        foreach ($input as $instream)
        {
            $mime_type = $instream->getMimeType();
            switch ($mime_type)
            {
                // unhandled input
                default:
                    $this->getOutput()->push($instream->copy());
                    break;

                // table input
                case \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE:
                    $this->createOutput($instream);
                    break;

                // stream/text/csv input
                case \Flexio\Base\ContentType::MIME_TYPE_STREAM:
                case \Flexio\Base\ContentType::MIME_TYPE_TXT:
                case \Flexio\Base\ContentType::MIME_TYPE_CSV:
                    $this->createOutput($instream);
                    break;
            }
        }
    }

    private function createOutput($instream)
    {
        // input/output
        $outstream = $instream->copy()->setPath(\Flexio\Base\Util::generateHandle());
        $this->getOutput()->push($outstream);

        // get the number of rows to return
        $job_definition = $this->getProperties();
        $rows = intval(isset_or($job_definition['params']['value'],0));
        $rows_to_output = ($rows > 0 ? $rows : 0);

        // create the reader
        $streamreader = \Flexio\Object\StreamReader::create($instream);
        if ($streamreader === false)
            return $this->fail(\Model::ERROR_READ_FAILED, _(''), __FILE__, __LINE__);

        // write to the output
        $streamwriter = \Flexio\Object\StreamWriter::create($outstream);
        if ($streamwriter === false)
            return $this->fail(\Model::ERROR_CREATE_FAILED, _(''), __FILE__, __LINE__);

        // read the specified number of input rows and write them out
        for ($rown = 0; $rown < $rows_to_output; ++$rown)
        {
            $row = $streamreader->readRow();
            if ($row === false)
                break;

            $result = $streamwriter->write($row);
            if ($result === false)
                return $this->fail(\Model::ERROR_WRITE_FAILED, _(''), __FILE__, __LINE__);
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
