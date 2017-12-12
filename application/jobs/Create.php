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
            "content_type": "text/csv",
            "content": ""
        }
    }
*/

class Create extends \Flexio\Jobs\Base
{
    public function run(\Flexio\IFace\IProcess $process)
    {
        parent::run($process);

        // create job adds new streams; don't clear existing streams
        $job_definition = $this->getProperties();
        $params = $job_definition['params'] ?? [];


        // TODO: factor

        if (isset($params['path']))
        {
            $columns = $params['columns'] ?? [];

            $vfs = new \Flexio\Services\Vfs();
            $vfs->setProcess($process);

            $create_params = [];
            if (is_array($columns) && count($columns) > 0)
            {
                $create_params['structure'] = $columns;
            }

            $vfs->createFile($params['path'], $create_params);
        }
         else
        {
            $outstream = $process->getStdout();
            $content_type = $params['content_type'] ?? \Flexio\Base\ContentType::STREAM;

            if (isset($params['columns']) && is_array($params['columns']) && count($params['columns']))
                $content_type = \Flexio\Base\ContentType::FLEXIO_TABLE;
            
            switch ($content_type)
            {
                default:
                case \Flexio\Base\ContentType::STREAM:
                case \Flexio\Base\ContentType::TEXT:
                case \Flexio\Base\ContentType::CSV:
                case \Flexio\Base\ContentType::JSON:
                    $this->createFile($outstream);
                    break;
    
                case \Flexio\Base\ContentType::FLEXIO_TABLE:
                    $this->createTable($outstream);
                    break;
            }
        }

    }

    private function createFile(\Flexio\IFace\IStream &$outstream)
    {
        $job_definition = $this->getProperties();
        $name = $job_definition['params']['name'] ?? _('New File');
        $content_type = ($job_definition['params']['content_type'] ?? \Flexio\Base\ContentType::STREAM);

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
            'content_type' => $content_type
        );
        $outstream->set($outstream_properties);
        $streamwriter = $outstream->getWriter();

        // write the content
        $streamwriter->write($content);
        $streamwriter->close();
        $outstream->setSize($streamwriter->getBytesWritten());
    }

    private function createTable(\Flexio\IFace\IStream &$outstream)
    {
        $job_definition = $this->getProperties();
        $name = $job_definition['params']['name'] ?? _('New Table');
        $content_type = $job_definition['params']['content_type'] ?? \Flexio\Base\ContentType::FLEXIO_TABLE;
        $structure = $job_definition['params']['columns'] ?? '[]';
        $outstream_properties = array(
            'name' => $name,
            'mime_type' => \Flexio\Base\ContentType::FLEXIO_TABLE,
            'structure' => $structure
        );

        $outstream->set($outstream_properties);
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
    }
}
