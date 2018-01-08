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
    "op": "merge",
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
        //parent::run($process);

/*
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
*/
        $job_definition = $this->getProperties();
        $paths = $job_definition['params']['files'] ?? [];

        if (count($paths) == 0)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER, "Missing/empty 'files' array");

        $vfs = new \Flexio\Services\Vfs();
        $vfs->setProcess($process);

        $streams = [];
        foreach($paths as $path)
        {
            try
            {
                $stream = $vfs->open($path);
                if (!$stream)
                {
                    throw new \Flexio\Base\Exception(\Flexio\Base\Error::NOT_FOUND);
                }

                $streams[] = $stream;
            }
            catch (\Exception $e)
            {
                $stream = \Flexio\Base\Stream::create();
                $streamwriter = $stream->getWriter();
                $vfs->read($path, function($data) use (&$streamwriter) {
                    $streamwriter->write($data);
                });

                $streams[] = $stream;
            }
        }

        $output = $process->getStdout();



        $merge_mode = 'stream';
        
        switch ($merge_mode)
        {
            default:
            case 'stream':
                return $this->mergeStreams($streams, $output);

            case 'json':
                return $this->mergeJson($streams, $output);

            case 'table':
                return $this->mergeTables($streams, $output);
        }
    }

    private function mergeStreams(array $streams, \Flexio\Iface\IStream $outstream)
    {
        // set the default output mime type; for now, use text, but should be
        // based on content
        $outstream_properties = [
            'mime_type' => \Flexio\Base\ContentType::TEXT
        ];
        
        $outstream->set($outstream_properties);

        // write to the output
        $streamwriter = $outstream->getWriter();
        foreach ($streams as $instream)
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

        $outstream->setSize($streamwriter->getBytesWritten());
    }

    private function mergeJson(\Flexio\IFace\IProcess $process)
    {
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
