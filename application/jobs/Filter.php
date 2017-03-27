<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-02-16
 *
 * @package flexio
 * @subpackage Jobs
 */


namespace Flexio\Jobs;


require_once __DIR__ . DIRECTORY_SEPARATOR . 'Base.php';

class Filter extends \Flexio\Jobs\Base
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
            }
        }
    }

    private function createOutput($instream)
    {
        // get the job properties
        $job_definition = $this->getProperties();
        $exclude = $job_definition['params']['exclude'] ?? false;

        if (isset($job_definition['params']['where']))
            $filter_expression = $job_definition['params']['where'];
        else if (isset($job_definition['params']['on']))
            $filter_expression = $job_definition['params']['on'];
        else
            return $this->fail(\Flexio\Base\Error::MISSING_PARAMETER, _('Missing where parameter'), __FILE__, __LINE__);

        if ($exclude)
        {
            $filter_expression = "not ($filter_expression)";
        }

        // make sure we have a valid expression
        $expreval = new \Flexio\Base\ExprEvaluate;
        $input_structure = $instream->getStructure()->enum();
        $success = $expreval->prepare($filter_expression, $input_structure);

        if ($success === false)
            return $this->fail(\Flexio\Base\Error::INVALID_PARAMETER, _(''), __FILE__, __LINE__);

        // create the output
        $outstream = $instream->copy()->setPath(\Flexio\Base\Util::generateHandle());
        $this->getOutput()->push($outstream);

        // if we don't have a filter expression, then there's no output; we're done
        if ($filter_expression === false)
            return;

        // write to the output
        $streamreader = \Flexio\Object\StreamReader::create($instream);
        $streamwriter = \Flexio\Object\StreamWriter::create($outstream);

        while (true)
        {
            $row = $streamreader->readRow();
            if ($row === false)
                break;

            $retval = null;
            $success = $expreval->execute($row, $retval);

            // if the expression evaluates true, write the row
            if ($retval === true)
                $streamwriter->write($row);
        }

        $streamwriter->close();
        $outstream->setSize($streamwriter->getBytesWritten());
    }

    // job definition info
    const MIME_TYPE = 'flexio.filter';
    const TEMPLATE = <<<EOD
    {
        "type": "flexio.filter",
        "params": {
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
                "enum": ["flexio.filter"]
            },
            "params": {
                "type": "object"
            }
        }
    }
EOD;
}
