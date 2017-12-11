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


declare(strict_types=1);
namespace Flexio\Jobs;

/*
EXAMPLE:
{
    "type": "flexio.merge",
    "params": {
        "matching_filenames": true,
        "matching_columnames": true
    }
}
*/

class Merge extends \Flexio\Jobs\Base
{
    public function run(\Flexio\IFace\IProcess $process)
    {
        // TODO: logic for this job is perfectly good, but we're removing the
        // notion right now of separate input streams in the process, so these
        // functions will likely be removed from the process interface; however,
        // we're looking to bring process for multiple streams back another way,
        // probably using named parameters
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::DEPRECATED);


        parent::run($process);

        // note: no need to process the stdin buffer, since it's a single stream and the merged
        // result is the same as the original

        // process stream array

        // TODO: merge content by compatible mime_type; pass on files
        // that aren't handled

        // iterate over the input streams and find out if all of the streams
        // are tables; if they are, merge them as tables; otherwise, merge
        // them as text

        $table_merge_mode = true;
        $input = $process->getStreams();

        $merge_mode = 'table';
        foreach ($input as $instream)
        {
            if ($instream->getMimeType() === \Flexio\Base\ContentType::FLEXIO_TABLE)
                continue;

            $merge_mode = 'stream';
            break;
        }

        switch ($merge_mode)
        {
            default:
            case 'stream':
                return $this->mergeStreams($process);

            case 'table':
                return $this->mergeTables($process);
        }
    }

    private function mergeStreams(\Flexio\IFace\IProcess $process)
    {
        $input = $process->getStreams();
        $process->clearStreams();

        // set the default output mime type; for now, use text, but should be
        // based on content
        $outstream_properties = array(
            'name' => 'merged',
            'mime_type' => \Flexio\Base\ContentType::TEXT
        );
        $outstream = \Flexio\Base\Stream::create($outstream_properties);

        // write to the output
        $streamwriter = $outstream->getWriter();
        foreach ($input as $instream)
        {
            $streamreader = $instream->getReader();
            while (true)
            {
                $data = $streamreader->read();
                if ($data === false)
                    break;

                $streamwriter->write($data);
            }
        }

        $streamwriter->close();
        $outstream->setSize($streamwriter->getBytesWritten());
        $process->addStream($outstream);
    }

    private function mergeTables(\Flexio\IFace\IProcess $process)
    {
        $input = $process->getStreams();
        $process->clearStreams();

        // create a merged structure and a row template for insertion
        // (the bulk insert, which the row inserter uses, requires the fields
        // to match the structure based on offset)
        $outstream_structure = $this->determineStructure($input);
        $outstream_properties = array(
            'name' => 'merged',
            'mime_type' => \Flexio\Base\ContentType::FLEXIO_TABLE,
            'structure' => $outstream_structure
        );
        $outstream = \Flexio\Base\Stream::create($outstream_properties);

        // write to the output
        $streamwriter = $outstream->getWriter();

        $row_template = array();
        foreach ($outstream_structure as $s)
            $row_template[$s['name']] = null;

        // insert the rows from each of the streams
        foreach ($input as $instream)
        {
            $streamreader = $instream->getReader();
            while (true)
            {
                $row = $streamreader->readRow();
                if ($row === false)
                    break;
                $row = \Flexio\Base\Util::mapArray($row_template, $row);
                $streamwriter->write($row);
            }
        }

        $streamwriter->close();
        $outstream->setSize($streamwriter->getBytesWritten());
        $process->addStream($outstream);
    }

    private function determineStructure(array $streams) : array
    {
        // this function finds out a "superset" structure from a list
        // of streams that can be safely appended to
        $structures = array();
        foreach ($streams as $s)
            $structures[] = $s->getStructure();

        $merged_structure = \Flexio\Base\Structure::union($structures);
        return $merged_structure->enum();
    }
}
