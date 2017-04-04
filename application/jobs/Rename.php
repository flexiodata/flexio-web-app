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


class Rename extends \Flexio\Jobs\Base
{
    public function run()
    {
        $input = $this->getInput()->enum();
        foreach ($input as $instream)
        {
            $this->createOutput($instream);
        }
    }

    private function createOutput(\Flexio\Object\Stream $instream)
    {
        $this->createOutputWithRenamedColumns($instream);
    }

    private function createOutputWithRenamedStreams(\Flexio\Object\Stream $instream)
    {
        // input/output
        $outstream = $instream->copy()->setPath(\Flexio\Base\Util::generateHandle());
        $this->getOutput()->push($outstream);

        // properties
        $job_definition = $this->getProperties();

        $append_timestamp = false;
        if (isset($job_definition['params']['append_timestamp']))
            $append_timestamp = toBoolean($job_definition['params']['append_timestamp']);

        $instream_info = $instream->get();
        $outstream_info = $instream_info;

        $filename = $outstream_info['name'] ?? false;
        if ($filename !== false && $append_timestamp === true)
        {
            // TODO: generalize wildcard replacement; for now, just add a datestamp
            $timestamp = \Flexio\System\System::getTimestamp();
            $file_timestamp = \Flexio\Base\Util::formatDate($timestamp);
            $file_timestamp = preg_replace('/[^A-Za-z0-9]/', '', $file_timestamp);

            // rename the file if we can get the filename parts
            $filename_base = \Flexio\Base\Util::getFilename($filename);
            $filename_ext = \Flexio\Base\Util::getFileExtension($filename);

            $new_filename = (strlen($filename_base) > 0 ? $filename_base . "_" : '') . $file_timestamp . (strlen($filename_ext) > 0 ? ".$filename_ext" : '');
            $outstream_info['name'] = $new_filename;
        }

        $outstream->set($outstream_info);
    }

    private function createOutputWithRenamedColumns(\Flexio\Object\Stream $instream)
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
