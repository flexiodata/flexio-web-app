<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-04-15
 *
 * @package flexio
 * @subpackage Jobs
 */


require_once __DIR__ . DIRECTORY_SEPARATOR . 'Base.php';

class MergeJob extends Base
{
    public function run()
    {
        // TODO: merge content by compatible mime_type; pass on files
        // that aren't handled

        // iterate over the input streams and find out if all of the streams
        // are tables; if they are, merge them as tables; otherwise, merge
        // them as text

        $table_merge_mode = true;
        $input = $this->getInput()->enum();

        foreach ($input as $instream)
        {
            if ($instream->getMimeType() === ContentType::MIME_TYPE_FLEXIO_TABLE)
                continue;

            $table_merge_mode = false;
            break;
        }

        if ($table_merge_mode === false)
            return $this->mergeStreams();

        if ($table_merge_mode === true)
            return $this->mergeTables();

        return $this->fail(\Model::ERROR_GENERAL, _(''), __FILE__, __LINE__);
    }

    private function mergeStreams()
    {
        // set the default output mime type; for now, use text, but should be
        // based on content
        $outstream_properties = array(
            'name' => 'merged',
            'mime_type' => ContentType::MIME_TYPE_TXT
        );
        $outstream = \Flexio\Object\Stream::create($outstream_properties);
        $this->getOutput()->push($outstream);

        // write to the output
        $streamwriter = \Flexio\Object\StreamWriter::create($outstream);
        if ($streamwriter === false)
            return $this->fail(\Model::ERROR_CREATE_FAILED, _(''), __FILE__, __LINE__);

        $input = $this->getInput()->enum();
        foreach ($input as $instream)
        {
            $instream->read(function ($data) use (&$streamwriter) {
                $streamwriter->write($data);
            });
        }

        $streamwriter->close();
        $outstream->setSize($streamwriter->getBytesWritten());
    }

    private function mergeTables()
    {
        // create a merged structure and a row template for insertion
        // (the bulk insert, which the row inserter uses, requires the fields
        // to match the structure based on offset)
        $outstream_structure = $this->determineStructure();
        $outstream_properties = array(
            'name' => 'merged',
            'mime_type' => ContentType::MIME_TYPE_FLEXIO_TABLE,
            'structure' => $outstream_structure
        );
        $outstream = \Flexio\Object\Stream::create($outstream_properties);
        $this->getOutput()->push($outstream);

        // write to the output
        $streamwriter = \Flexio\Object\StreamWriter::create($outstream);
        if ($streamwriter === false)
            return $this->fail(\Model::ERROR_CREATE_FAILED, _(''), __FILE__, __LINE__);

        $row_template = array();
        foreach ($outstream_structure as $s)
            $row_template[$s['name']] = null;

        // insert the rows from each of the streams
        $input = $this->getInput()->enum();
        foreach ($input as $instream)
        {
            $instream->read(function ($data) use (&$streamwriter, $row_template) {
                $row = Util::mapArray($row_template, $data);
                $streamwriter->write($row);
            });
        }

        $streamwriter->close();
        $outstream->setSize($streamwriter->getBytesWritten());
    }

    private function determineStructure()
    {
        // this function finds out a "superset" structure from a list
        // of inputs that can be safely appended to
        $structures = array();
        $input = $this->getInput()->enum();

        foreach ($input as $instream)
            $structures[] = $instream->getStructure();

        $merged_structure = \Flexio\Object\Structure::union($structures);
        return $merged_structure->enum();
    }


    // job definition info
    const MIME_TYPE = 'flexio.merge';
    const TEMPLATE = <<<EOD
    {
        "type": "flexio.merge",
        "params": {
            "matching_filenames": true,
            "matching_columnames": true
        }
    }
EOD;
    // direction is "asc" or "desc"
    const SCHEMA = <<<EOD
    {
        "type": "object",
        "required": ["type","params"],
        "properties": {
            "type": {
                "type": "string",
                "enum": ["flexio.merge"]
            },
            "params": {
                "type": "object"
            }
        }
    }
EOD;
}
