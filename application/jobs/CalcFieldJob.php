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


namespace Flexio\Jobs;


require_once __DIR__ . DIRECTORY_SEPARATOR . 'Base.php';

class CalcFieldJob extends \Flexio\Jobs\Base
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
                case \ContentType::MIME_TYPE_FLEXIO_TABLE:
                    $this->createOutput($instream);
                    break;
            }
        }
    }

    private function createOutput($instream)
    {
        // get the job properties
        $job_definition = $this->getProperties();
        $name = $job_definition['params']['name'];
        $type = isset_or($job_definition['params']['type'],'character');
        $width = isset_or($job_definition['params']['width'],null);
        $scale = isset_or($job_definition['params']['decimals'],null);
        $expression = isset_or($job_definition['params']['expression'],null);

        if (isset($width) && !is_integer($width))
            $width = (int)$width;
        if (isset($scale) && !is_integer($scale))
            $scale = (int)$scale;

        // make sure we have a valid expression
        $expreval = new \ExprEvaluate;
        $input_structure = $instream->getStructure()->enum();
        $success = $expreval->prepare($expression, $input_structure);

        if ($success === false)
            return $this->fail(\Model::ERROR_INVALID_PARAMETER, _(''), __FILE__, __LINE__);

        // create the output
        $outstream = $instream->copy()->setPath(\Util::generateHandle());
        $this->getOutput()->push($outstream);

        $output_structure = $outstream->getStructure();
        $added_field = $output_structure->push(array(
            'name' => $name,
            'type' => $type,
            'width' => $width,
            'scale' => $scale
        ));
        if ($added_field === false)
            return $this->fail(\Model::ERROR_INVALID_PARAMETER, _(''), __FILE__, __LINE__);

        $name = $added_field['name']; // get the name of the field that was added (in case it was adjusted for duplicate, for example)
        $outstream->setStructure($output_structure);

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
            $row[$name] = $retval;
            $streamwriter->write($row);
        }

        $streamwriter->close();
        $outstream->setSize($streamwriter->getBytesWritten());
    }


    // job definition info
    const MIME_TYPE = 'flexio.calc';
    const TEMPLATE = <<<EOD
    {
        "type": "flexio.calc",
        "params": {
            "name": "",
            "type": "",
            "decimals": "",
            "expression": ""
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
                "enum": ["flexio.calc"]
            },
            "params": {
                "type": "object"
            }
        }
    }
EOD;
}
