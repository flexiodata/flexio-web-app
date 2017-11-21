<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams
 * Created:  2015-08-26
 *
 * @package flexio
 * @subpackage Jobs
 */


declare(strict_types=1);
namespace Flexio\Jobs;

/*
// EXAMPLE:
    {
        "type": "flexio.create",
        "params": {
            "name": "test",
            "mime_type": "text/csv",
            "content": ""
        }
    }
*/

class Create extends \Flexio\Jobs\Base
{
    public function run(\Flexio\Jobs\IProcess $process)
    {
        parent::run($process);

        // create job adds new streams; don't clear existing streams
        $job_definition = $this->getProperties();

        $outstream = null;
        $mime_type = $job_definition['params']['mime_type'] ?? \Flexio\Base\ContentType::MIME_TYPE_STREAM;
        switch ($mime_type)
        {
            default:
            case \Flexio\Base\ContentType::MIME_TYPE_STREAM:
            case \Flexio\Base\ContentType::MIME_TYPE_TXT:
            case \Flexio\Base\ContentType::MIME_TYPE_CSV:
            case \Flexio\Base\ContentType::MIME_TYPE_JSON:
                $outstream = $this->createFileStream();
                break;

            case \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE:
                $outstream = $this->createTableStream();
                break;
        }

        if (!isset($outstream))
            return;

        // if no name is specified in the create job, send the content to stdout;
        // otherwise, add it onto the list of files
        if (!isset($job_definition['params']['name']))
            $process->setBuffer($outstream);
             else
            $process->addStream($outstream);
    }

    private function createFileStream() : \Flexio\Base\IStream
    {
        $job_definition = $this->getProperties();
        $name = $job_definition['params']['name'] ?? _('New File');
        $mime_type = ($job_definition['params']['mime_type'] ?? \Flexio\Base\ContentType::MIME_TYPE_STREAM);

        // get the content and decode it
        $content = '';
        if (isset($job_definition['params']['content']))
        {
            $content = $job_definition['params']['content'];
            if (!is_string($content))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

            $content = base64_decode($content);
        }

        // create the output stream
        $outstream_properties = array(
            'name' => $name,
            'mime_type' => $mime_type
        );
        $outstream = \Flexio\Base\StreamMemory::create($outstream_properties);
        $streamwriter = $outstream->getWriter();

        // write the content
        $streamwriter->write($content);
        $streamwriter->close();
        $outstream->setSize($streamwriter->getBytesWritten());
        return $outstream;
    }

    private function createTableStream() : \Flexio\Base\IStream
    {
        $job_definition = $this->getProperties();
        $name = $job_definition['params']['name'] ?? _('New Table');
        $mime_type = $job_definition['params']['mime_type'] ?? \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE;
        $structure = $job_definition['params']['columns'] ?? '[]';
        $outstream_properties = array(
            'name' => $name,
            'mime_type' => \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE,
            'structure' => $structure
        );

        $outstream = \Flexio\Base\StreamMemory::create($outstream_properties);
        $streamwriter = $outstream->getWriter();

        if (isset($job_definition['params']['content']))
        {
            $rows = $job_definition['params']['content'];
            if (!is_array($rows))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

            foreach ($rows as $row)
            {
                // if the row array is non-associative, then insert them
                // based on the index of the field compared to the structure
                if (\Flexio\Base\Util::isAssociativeArray($row) === false)
                {
                    $idx = 0;
                    $row_with_keys = array();
                    $output_structure = $outstream->getStructure()->enum();
                    foreach($output_structure as $col)
                    {
                        if (array_key_exists($idx, $row))
                            $row_with_keys[$col['name']] = $row[$idx];
                        $idx++;
                    }
                    $row = $row_with_keys;
                }

                $result = $streamwriter->write($row);
                if ($result === false)
                    throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);
            }
        }

        $result = $streamwriter->close();
        if ($result === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);

        $outstream->setSize($streamwriter->getBytesWritten());
        return $outstream;
    }
}
