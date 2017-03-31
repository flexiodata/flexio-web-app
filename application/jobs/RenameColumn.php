<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams
 * Created:  2016-01-21
 *
 * @package flexio
 * @subpackage Jobs
 */


declare(strict_types=1);
namespace Flexio\Jobs;


class RenameColumn extends \Flexio\Jobs\Base
{
    public function run()
    {
        $input = $this->getInput()->enum();
        foreach ($input as $instream)
        {
            $this->createOutputFromTable($instream);
        }
    }

    private function createOutputFromTable(\Flexio\Object\Stream $instream)
    {
        // input/output
        $outstream = $instream->copy(); // copy everything, including the original path (since we're only changing field names)
        $this->getOutput()->push($outstream);

        // properties
        $input_structure = $instream->getStructure();
        $input_columns = $input_structure->enum();

        // get the columns to rename
        $job_definition = $this->getProperties();
        $params = $job_definition['params'];
        if (!isset($params['columns']) || !is_array($params['columns']))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::MISSING_PARAMETER);

        $indexed = [];
        $job_columns = $params['columns'];
        foreach ($job_columns as $column)
        {
            if (!isset($column['name']) || !isset($column['new_name']))
                continue;

            $indexed[strtolower($column['name'])] = $column['new_name'];
        }

        // create a new structure with the renamed columns
        $output_columns = [];
        foreach ($input_columns as $c)
        {
            $name = strtolower($c['name']);
            if (isset($indexed[$name]))
                $c['name'] = $indexed[$name];

            $output_columns[] = $c;
        }

        // update the structure
        $outstream->setStructure($output_columns);
    }

    // job definition info
    const MIME_TYPE = 'flexio.rename';
    const TEMPLATE = <<<EOD
    {
        "type": "flexio.rename",
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
                "enum": ["flexio.rename"]
            },
            "params": {
                "type": "object"
            }
        }
    }
EOD;
}
