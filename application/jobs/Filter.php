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

/*
// EXAMPLE:
{
    "op": "filter",
    "params": {
    }
}
*/

class Filter extends \Flexio\Jobs\Base
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
            default:
                $outstream->copyFrom($instream);
                return;

            case \Flexio\Base\ContentType::JSON:
                $this->getOutputFromJson($instream, $outstream);
                return;

            case \Flexio\Base\ContentType::FLEXIO_TABLE:
                $this->getOutputFromTable($instream, $outstream);
                return;
        }
    }

    private function getOutputFromJson(\Flexio\IFace\IStream &$instream, \Flexio\IFace\IStream &$outstream)
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





        $payload = '';
        $reader = $instream->getReader();
        while (($data = $reader->read(32768)) !== false)
        {
            $payload .= $data;
        }

        $data = @json_decode($payload);
        unset($payload);

        if (!is_array($data))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_FORMAT, "The input JSON must be a valid JSON array");

        // scan through the whole array and generate a structure from it
        $input_structure = [];
        foreach ($data as $row)
        {
            $row = (array)$row;
            foreach ($row as $key => $val)
            {
                if (is_string($val))
                    $input_structure[] = [ 'name' => $key, 'type' => 'text' ];
                     else
                    $input_structure[] = [ 'name' => $key, 'type' => 'numeric' ];
            }
            break;
        }

        // make sure we have a valid expression
        $expreval = new \Flexio\Base\ExprEvaluate;
        $success = $expreval->prepare($filter_expression, $input_structure);

        if ($success === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        
        $results = [];
        foreach ($data as $row)
        {
            $row = (array)$row;

            $retval = null;
            $success = $expreval->execute($row, $retval);

            // if the expression evaluates true, write the row
            if ($retval === true)
                $results[] = $row;
        }

        $payload = json_encode($results);
        $streamwriter = $outstream->getWriter();
        $streamwriter->write($payload);
        $outstream->setSize($streamwriter->getBytesWritten());
    }

    private function getOutputFromTable(\Flexio\IFace\IStream &$instream, \Flexio\IFace\IStream &$outstream)
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

        // if we don't have a filter expression, then there's no output; we're done
        if ($filter_expression === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::MISSING_PARAMETER, "Missing where parameter");

        $outstream->setStructure($input_structure);

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
    }
}
