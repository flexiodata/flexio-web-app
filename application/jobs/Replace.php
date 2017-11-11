<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams
 * Created:  2016-01-19
 *
 * @package flexio
 * @subpackage Jobs
 */


declare(strict_types=1);
namespace Flexio\Jobs;

/*
// EXAMPLE:
{
    "type": "flexio.replace",
    "params": {
        "columns": [],
        "find": "",
        "replace": "",
        "location": "any",
        "match_case": false
    }
}
*/

class Replace extends \Flexio\Jobs\Base
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
            default:
                $outstream = $instream;
                return;

            case \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE:
                $this->getOutput($instream, $outstream);
                return;
        }
    }

    private function getOutput(\Flexio\Object\IStream &$instream, \Flexio\Object\IStream &$outstream)
    {
        $column_expression_map = $this->getColumnExpressionMap($instream);
        if ($column_expression_map === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        // if there aren't any operations, simply create an output stream
        // pointing to the origina content
        if (count($column_expression_map) === 0)
        {
            $outstream = $instream;
            return;
        }

        // create the output with the replaced values
        $outstream->set($instream->get());
        $outstream->setPath(\Flexio\Base\Util::generateHandle());
        $streamreader = $instream->getReader();
        $streamwriter = $outstream->getWriter();

        while (true)
        {
            $input_row = $streamreader->readRow();
            if ($input_row === false)
                break;

            $output_row = array();
            foreach ($input_row as $name => $value)
            {
                // if we have nothing to evaluate, copy the value
                if (!isset($column_expression_map[$name]))
                {
                    $output_row[$name] = $value;
                    continue;
                }

                // we have something to replace; execute the expression
                $retval = null;
                $expression_evaluator = $column_expression_map[$name]['expreval'];
                $expression_evaluator->execute($input_row, $retval);
                $output_row[$name] = $retval;
            }

            // write the output row
            $streamwriter->write($output_row);
        }

        $streamwriter->close();
        $outstream->setSize($streamwriter->getBytesWritten());
    }

    private function getColumnExpressionMap(\Flexio\Object\IStream &$instream)
    {
        // returns an array mapping column names to an expression
        // object that can be used for performing the replace

        // properties
        $job_definition = $this->getProperties();
        $params = $job_definition['params'];

        // determine the list of columns to perform replace on
        $specified_column_names = $params['columns'];
        $columns = $instream->getStructure()->enum($specified_column_names);
        if (count($columns) == 0)
            return array(); // no columns to perform operation on; not a failure, but fall through to no replace

        $column_expression_map = array();
        foreach ($columns as $column)
        {
            // build the replace expression for the given column
            $qname = \Flexio\Base\DbUtil::quoteIdentifierIfNecessary($column['name']);
            $qfind = preg_quote($params['find'],'/');
            $qreplace = \Flexio\Base\ExprUtil::quote($params['replace']);

            $location = $params['location'] ?? 'any';
            if ($location == 'any') {}
            else if ($location == 'leading')
                $qfind = '^' . $qfind;
            else if ($location == 'trailing')
                $qfind = $qfind . '$';
            else if ($location == 'leading_trailing')
                $qfind = '(^' . $qfind .')|(' . $qfind . '$)';
            else if ($location == 'whole')
                $qfind = '^' . $qfind . '$';
            $qfind = \Flexio\Base\ExprUtil::quote($qfind);

            $flags = 'gi';
            if (isset($params['match_case']) && $params['match_case'])
                $flags = 'g';

            $exprtext = "regexp_replace($qname,$qfind,$qreplace,'$flags')";

            // map the column to the expression
            $expreval = new \Flexio\Base\ExprEvaluate;
            $parse_result = $expreval->prepare($exprtext, $instream->getStructure()->enum());
            if ($parse_result === false)
                return false; // trouble building the expression

            $column_expression_map[$column['name']] = array(
                'exprtext' => $exprtext,
                'expreval' => $expreval
            );
        }

        return $column_expression_map;
    }

    // job definition info
    const SCHEMA = <<<EOD
    {
        "type": "object",
        "required": ["type","params"],
        "properties": {
            "type": {
                "type": "string",
                "enum": ["flexio.replace"]
            },
            "params": {
                "type": "object",
                "required": ["columns"],
                "properties": {
                }
            }
        }
    }
EOD;
}
