<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-08-16
 *
 * @package flexio
 * @subpackage Jobs
 */


declare(strict_types=1);
namespace Flexio\Jobs;

/*
// EXAMPLE:
{
    "type": "flexio.calc",
    "params": {
        "name": "",
        "type": "",
        "decimals": "",
        "expression": ""
    }
}
*/

class CalcField extends \Flexio\Jobs\Base
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
        // get the job properties
        $job_definition = $this->getProperties();
        $name = $job_definition['params']['name'];
        $type = $job_definition['params']['type'] ?? 'character';
        $width = $job_definition['params']['width'] ?? null;
        $scale = $job_definition['params']['decimals'] ?? null;
        $expression = $job_definition['params']['expression'] ?? null;

        if (isset($width) && !is_integer($width))
            $width = (int)$width;
        if (isset($scale) && !is_integer($scale))
            $scale = (int)$scale;

        // make sure we have a valid expression
        $expreval = new \Flexio\Base\ExprEvaluate;
        $input_structure = $instream->getStructure()->enum();
        $success = $expreval->prepare($expression, $input_structure);

        if ($success === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        // create the output
        $outstream->set($instream->get());
        $outstream->setPath(\Flexio\Base\Util::generateHandle());
        $output_structure = $outstream->getStructure();
        $added_field = $output_structure->push(array(
            'name' => $name,
            'type' => $type,
            'width' => $width,
            'scale' => $scale
        ));
        if ($added_field === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $name = $added_field['name']; // get the name of the field that was added (in case it was adjusted for duplicate, for example)
        $outstream->setStructure($output_structure);

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
            $row[$name] = $retval;
            $streamwriter->write($row);
        }

        $streamwriter->close();
        $outstream->setSize($streamwriter->getBytesWritten());
    }


    // job definition info
    const SCHEMA = <<<EOD
    {
        "type": "object",
        "required": ["type","params"],
        "properties": {
            "type": {
                "type": "string",
                "enum": ["flexio.calc"]
            },
            "params": {
                "type": "object"
            }
        }
    }
EOD;
}
