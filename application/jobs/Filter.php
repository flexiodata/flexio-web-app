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


declare(strict_types=1);
namespace Flexio\Jobs;


class Filter extends \Flexio\Jobs\Base
{
    public function run(\Flexio\Object\Context &$context)
    {
        // process stdin
        $stdin = $context->getStdin();
        $context->setStdout($this->processStream($stdin));

        // process stream array
        $input = $context->getStreams();
        $context->clearStreams();

        foreach ($input as $instream)
        {
            $outstream = $this->processStream($instream);
            $context->addStream($outstream);
        }
    }

    private function processStream(\Flexio\Object\IStream $instream) : \Flexio\Object\IStream
    {
        $mime_type = $instream->getMimeType();
        switch ($mime_type)
        {
            default:
                return $instream;

            case \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE:
                return $this->getOutput($instream);
        }
    }

    private function getOutput(\Flexio\Object\IStream $instream) : \Flexio\Object\IStream
    {
        // get the job properties
        $job_definition = $this->getProperties();
        $exclude = $job_definition['params']['exclude'] ?? false;

        if (isset($job_definition['params']['where']))
            $filter_expression = $job_definition['params']['where'];
        else if (isset($job_definition['params']['on']))
            $filter_expression = $job_definition['params']['on'];
        else
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::MISSING_PARAMETER);

        if ($exclude)
        {
            $filter_expression = "not ($filter_expression)";
        }

        // make sure we have a valid expression
        $expreval = new \Flexio\Base\ExprEvaluate;
        $input_structure = $instream->getStructure()->enum();
        $success = $expreval->prepare($filter_expression, $input_structure);

        if ($success === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        // create the output
        $outstream = $instream->copy()->setPath(\Flexio\Base\Util::generateHandle());

        // if we don't have a filter expression, then there's no output; we're done
        if ($filter_expression === false)
            return $outstream;

        // write to the output
        $streamreader = $instream->getReader();
        $streamwriter = $outstream->getWriter();

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
        return $outstream;
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
