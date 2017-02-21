<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams
 * Created:  2016-05-25
 *
 * @package flexio
 * @subpackage Jobs
 */


namespace Flexio\Jobs;


require_once __DIR__ . DIRECTORY_SEPARATOR . 'Base.php';

class RenameFileJob extends \Flexio\Jobs\Base
{
    public function run()
    {
        $input = $this->getInput()->enum();
        foreach ($input as $instream)
        {
            $mime_type = $instream->getMimeType();
            switch ($mime_type)
            {
                // allow renames for all inputs
                default:
                    $this->createOutputFromInput($instream);
                    break;
            }
        }
    }

    public function createOutputFromInput($instream)
    {
        // input/output
        $outstream = $instream->copy()->setPath(\Util::generateHandle());
        $this->getOutput()->push($outstream);

        // properties
        $job_definition = $this->getProperties();

        $append_timestamp = false;
        if (isset($job_definition['params']['append_timestamp']))
            $append_timestamp = toBoolean($job_definition['params']['append_timestamp']);

        $instream_info = $instream->get();
        $outstream_info = $instream_info;

        $filename = isset_or($outstream_info['name'], false);
        if ($filename !== false && $append_timestamp === true)
        {
            // TODO: generalize wildcard replacement; for now, just add a datestamp
            $timestamp = \System::getTimestamp();
            $file_timestamp = \Util::formatDate($timestamp);
            $file_timestamp = preg_replace('/[^A-Za-z0-9]/', '', $file_timestamp);

            // rename the file if we can get the filename parts
            $filename_base = \Util::getFilename($filename);
            $filename_ext = \Util::getFileExtension($filename);

            $new_filename = (strlen($filename_base) > 0 ? $filename_base . "_" : '') . $file_timestamp . (strlen($filename_ext) > 0 ? ".$filename_ext" : '');
            $outstream_info['name'] = $new_filename;
        }

        $outstream->set($outstream_info);
    }

    // job definition info
    const MIME_TYPE = 'flexio.rename-file';
    const TEMPLATE = <<<EOD
    {
        "type": "flexio.rename-file",
        "params": {
            "append_timestamp": true
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
                "enum": ["flexio.rename-file"]
            },
            "params": {
                "type": "object"
            }
        }
    }
EOD;
}
