<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-02-16
 *
 * @package flexio
 * @subpackage Jobs
 */


declare(strict_types=1);
namespace Flexio\Jobs;

/*
// EXAMPLE:
// direction is 'asc' or 'desc'
{
    "type": "flexio.sort",
    "params": {
        "order": [{
            "expression": "",
            "direction": ""
        }]
    }
}
*/

class Sort extends \Flexio\Jobs\Base
{
    public function run(\Flexio\Jobs\IProcess $process)
    {
        // TODO: implementation dependent on SQL operations on the service;
        // with memory streams, we can no longer rely on this
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::DEPRECATED);

        parent::run($process);

        // stdin/stdout
        $instream = $process->getStdin();
        $outstream = $process->getStdout();
        $this->processStream($instream, $outstream);
    }

    private function processStream(\Flexio\Base\IStream &$instream, \Flexio\Base\IStream &$outstream)
    {
        $mime_type = $instream->getMimeType();
        switch ($mime_type)
        {
            default:
                $outstream->copy($instream);
                return;

            case \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE:
                $this->getOutput($instream, $outstream);
                return;
        }
    }

    private function getOutput(\Flexio\Base\IStream &$instream, \Flexio\Base\IStream &$outstream)
    {
        // input/output
        $outstream->set($instream->get());
        $outstream->setPath(\Flexio\Base\Util::generateHandle());

        // create the output
        $job_statement = self::prepareOutput($this->getProperties(), $instream, $outstream);
        if ($job_statement === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $streamwriter = $outstream->getWriter();
        if ($outstream->getService()->exec($job_statement) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);
    }

    private static function prepareOutput(array $job_definition, \Flexio\Base\IStream $instream, \Flexio\Base\IStream &$outstream)
    {
        if (!isset($job_definition['params']['order']) || !is_array($job_definition['params']['order']))
            return false;

        $input_columns = $instream->getStructure()->enum();
        $input_path = $instream->getPath();
        $output_path = $outstream->getPath();

        $order = '';
        foreach ($job_definition['params']['order'] as $part)
        {
            if (array_key_exists('expression',$part))
            {
                if (!is_string($part['expression']))
                    return false;

                $s = \Flexio\Base\ExprTranslatorPostgres::translate($part['expression'], $input_columns);
                if ($s === false)
                    return false;

                if (array_key_exists('direction',$part))
                {
                if (!is_string($part['direction']))
                    return false;

                    if (0 == strcasecmp($part['direction'], 'desc'))
                        $s .= ' DESC';
                    else if (0 == strcasecmp($part['direction'], 'asc'))
                        {}
                    else
                        return false;
                }

                if (strlen($order) > 0)
                    $order .= ',';
                $order .= $s;
            }
        }

        $columns = '';
        foreach ($input_columns as $col)
        {
            if (strlen($columns) > 0)
                $columns .= ',';

            $columns .= $col['store_name'];
        }

        $sql = "INSERT INTO $output_path ($columns) SELECT $columns FROM $input_path";
        if (strlen($order) > 0)
            $sql .= " ORDER BY $order";

        return $sql;
    }
}
