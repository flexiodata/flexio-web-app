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
// DESCRIPTION:
{
    "op": "filter", // string, required
    "where": ""     // string, required
}

// VALIDATOR:
$validator = \Flexio\Base\Validator::create();
if (($validator->check($params, array(
        'op'         => array(required' => true,  'enum' => ['filter']),
        'where'      => array(required' => true,  'type' => 'string')
    ))->hasErrors()) === true)
    throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);
*/

class Filter extends \Flexio\Jobs\Base
{
    public function run(\Flexio\IFace\IProcess $process) : void
    {
        parent::run($process);

        // stdin/stdout
        $instream = $process->getStdin();
        $outstream = $process->getStdout();
        $this->processStream($instream, $outstream);
    }

    private function processStream(\Flexio\IFace\IStream &$instream, \Flexio\IFace\IStream &$outstream) : void
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

    private function getOutputFromJson(\Flexio\IFace\IStream &$instream, \Flexio\IFace\IStream &$outstream) : void
    {
        // get the job properties
        $params = $this->getJobParameters();
        $exclude = $params['exclude'] ?? false;

        if (isset($params['where']))
            $filter_expression = $params['where'];
        else if (isset($params['on']))
            $filter_expression = $params['on'];
        else
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

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
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::EXECUTE_FAILED, "The input JSON must be a valid JSON array");

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
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);


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

        $outstream->setMimeType(\Flexio\Base\ContentType::JSON);
        $streamwriter = $outstream->getWriter();
        $streamwriter->write($payload);
        $outstream->setSize($streamwriter->getBytesWritten());
    }

    private function getOutputFromTable(\Flexio\IFace\IStream &$instream, \Flexio\IFace\IStream &$outstream) : void
    {
        // get the job properties
        $params = $this->getJobParameters();
        $exclude = $params['exclude'] ?? false;

        if (isset($params['where']))
            $filter_expression = $params['where'];
        else if (isset($params['on']))
            $filter_expression = $params['on'];
        else
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        if ($exclude)
        {
            $filter_expression = "not ($filter_expression)";
        }

        // make sure we have a valid expression
        $expreval = new \Flexio\Base\ExprEvaluate;
        $input_structure = $instream->getStructure()->enum();
        $success = $expreval->prepare($filter_expression, $input_structure);

        if ($success === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        // if we don't have a filter expression, then there's no output; we're done
        if ($filter_expression === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX, "Missing where parameter");

        $outstream->set(['mime_type' => \Flexio\Base\ContentType::FLEXIO_TABLE,
                         'structure' => $input_structure ]);

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
