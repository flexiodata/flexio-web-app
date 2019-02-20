<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie LLC. All rights reserved.
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
// DESCRIPTION:
{
    "op": "merge"
    // TODO: fill out
}

// VALIDATOR:
$validator = \Flexio\Base\Validator::create();
if (($validator->check($params, array(
        'op'         => array('required' => true,  'enum' => ['merge'])
    ))->hasErrors()) === true)
    throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);
*/

class Merge extends \Flexio\Jobs\Base
{
    public function run(\Flexio\IFace\IProcess $process) : void
    {
        //parent::run($process);

/*
        $table_merge_mode = true;
        $input = $process->getStreams();

        $merge_mode = 'table';
        foreach ($input as $stream)
        {
            if ($stream->getMimeType() === \Flexio\Base\ContentType::FLEXIO_TABLE)
                continue;

            $merge_mode = 'stream';
            break;
        }
*/
        $params = $this->getJobParameters();
        $paths = $params['files'] ?? [];

        if (count($paths) == 0)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX, "Missing/empty 'files' array");

        $vfs = new \Flexio\Services\Vfs($process->getOwner());
        $vfs->setProcess($process);

        $streams = [];
        foreach($paths as $path)
        {
            try
            {
                $stream = $vfs->open($path);
                if (!$stream)
                {
                    throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
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



        $merge_mode = 'table';

        foreach ($streams as $stream)
        {
            $mime_type = $stream->getMimeType();
            if ($merge_mode == 'table' && $mime_type == \Flexio\Base\ContentType::JSON)
                $merge_mode = 'json';

            if ($mime_type != \Flexio\Base\ContentType::JSON && $mime_type != \Flexio\Base\ContentType::FLEXIO_TABLE)
            {
                $merge_mode = 'stream';
                break;
            }
        }

        switch ($merge_mode)
        {
            default:
            case 'stream':
                $this->mergeStreams($streams, $output);
                return;

            case 'json':
                $this->mergeJson($streams, $output);
                return;

            case 'table':
                $this->mergeTables($streams, $output);
                return;
        }
    }

    private function mergeStreams(array $streams, \Flexio\Iface\IStream $outstream) : void
    {
        // set the default output mime type; for now, use text, but should be
        // based on content
        $outstream_properties = [
            'mime_type' => \Flexio\Base\ContentType::TEXT
        ];

        $outstream->set($outstream_properties);

        // write to the output
        $streamwriter = $outstream->getWriter();
        foreach ($streams as $stream)
        {
            $streamreader = $stream->getReader();
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

    private function mergeJson(array $streams, \Flexio\Iface\IStream $outstream) : void
    {
        // set the default output mime type; for now, use text, but should be
        // based on content
        $outstream_properties = [
            'mime_type' => \Flexio\Base\ContentType::JSON
        ];

        $outstream->set($outstream_properties);

        $result = null;

        // write to the output
        $streamwriter = $outstream->getWriter();
        foreach ($streams as $stream)
        {
            $json = null;

            $content_type = $stream->getMimeType();
            if ($content_type == \Flexio\Base\ContentType::FLEXIO_TABLE)
            {
                $json = [];
                $reader = $stream->getReader();
                while (($row = $reader->readRow()) !== false)
                    $json[] = $row;
            }
             else if ($content_type == \Flexio\Base\ContentType::JSON)
            {
                $data = '';
                $reader = $stream->getReader();
                while (($chunk = $reader->read(16384)) !== false)
                    $data .= $chunk;

                $json = @json_decode($data);
            }

            if (is_null($json))
            {
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::GENERAL, "JSON decoding error");
            }

            if ($result === null)
            {
                $result = $json;
            }
             else
            {
                if (is_object($result) && is_object($json))
                {
                    $result = (object)array_merge((array)$result, (array)$json);
                }
                 else if (is_array($result) || is_array($json))
                {
                    if (is_object($result))
                        $result = (array)$result;
                    if (is_object($json))
                        $json = (array)$json;
                    if (!is_array($result))
                        $json = [ $result ];
                    if (!is_array($json))
                        $json = [ $json ];
                    $result = array_merge($result, $json);
                }
                 else
                {
                    $result .= $json;
                }
            }
        }

        if ($result !== null)
        {
            $outstream->getWriter()->write(json_encode($result, JSON_UNESCAPED_SLASHES));
            $outstream->setSize($streamwriter->getBytesWritten());
        }
    }

    private function mergeTables(array $streams, \Flexio\Iface\IStream $outstream) : void
    {
        // create a merged structure and a row template for insertion
        // (the bulk insert, which the row inserter uses, requires the fields
        // to match the structure based on offset)
        $outstream_structure = $this->determineStructure($streams);
        $props = [
            'mime_type' => \Flexio\Base\ContentType::FLEXIO_TABLE,
            'structure' => $outstream_structure
        ];
        $outstream->set($props);

        // write to the output
        $streamwriter = $outstream->getWriter();

        $row_template = array();
        foreach ($outstream_structure as $s)
            $row_template[$s['name']] = null;

        // insert the rows from each of the streams
        foreach ($streams as $stream)
        {
            $streamreader = $stream->getReader();
            while (($row = $streamreader->readRow()) !== false)
            {
                $row = \Flexio\Base\Util::mapArray($row_template, $row);
                $streamwriter->write($row);
            }
        }
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
