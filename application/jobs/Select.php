<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams, Benjamin I. Williams
 * Created:  2015-12-03
 *
 * @package flexio
 * @subpackage Jobs
 */


namespace Flexio\Jobs;


class Select extends \Flexio\Jobs\Base
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
                    $this->createOutputFromTable($instream);
                    break;
            }
        }
    }

    private function createOutputFromTable($instream)
    {
        // input/output
        $outstream = $instream->copy(); // copy everything, including the original path (since we're only selecting fields)
        $this->getOutput()->push($outstream);

        // get the selected columns
        $job_definition = $this->getProperties();
        $params = $job_definition['params'];
        if (!isset($params['columns']) || !is_array($params['columns']))
            return $this->fail(\Flexio\Base\Error::MISSING_PARAMETER, _(''), __FILE__, __LINE__);

        $output_structure = $instream->getStructure()->enum($params['columns']);
        $outstream->setStructure($output_structure);
    }


    // job definition info
    const MIME_TYPE = 'flexio.select';
    const TEMPLATE = <<<EOD
    {
        "type": "flexio.select",
        "params": {
            "columns": [
                "",
                ""
            ]
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
                "enum": ["flexio.select"]
            },
            "params": {
                "type": "object",
                "required": ["columns"],
                "properties": {
                    "columns": {
                        "type": "array",
                        "minItems": 1,
                        "items": {
                            "type": "string"
                        }
                    }
                }
            }
        }
    }
EOD;
}
