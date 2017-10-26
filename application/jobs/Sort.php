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


class Sort extends \Flexio\Jobs\Base
{
    public function run(\Flexio\Object\Context &$context)
    {
        // TODO: implementation dependent on SQL operations on the service;
        // with memory streams, we can no longer rely on this
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::DEPRECATED);

        // process stdin
        $stdin = $context->getStdin();
        $context->setStdout($this->processStream($stdin));

        // process stream array
        $input = $context->getStreams();
        $context->clearStreams();

        foreach ($input as $instream)
        {
            $outstream = $this->processStream($instream);
            $context->addStream($outstream);
        }
    }

    private function processStream(\Flexio\Object\IStream $instream) : \Flexio\Object\IStream
    {
        $mime_type = $instream->getMimeType();
        switch ($mime_type)
        {
            default:
                return $instream;

            case \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE:
                return $this->getOutput($instream);
        }
    }

    private function getOutput(\Flexio\Object\IStream $instream) : \Flexio\Object\IStream
    {
        // input/output
        $outstream = $instream->copy()->setPath(\Flexio\Base\Util::generateHandle());

        // create the output
        $job_statement = self::prepareOutput($this->getProperties(), $instream, $outstream);
        if ($job_statement === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $streamwriter = $outstream->getWriter();
        if ($outstream->getService()->exec($job_statement) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);

        return $outstream;
    }

    private static function prepareOutput(array $job_definition, \Flexio\Object\IStream $instream, \Flexio\Object\IStream &$outstream)
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

    // job definition info
    const MIME_TYPE = 'flexio.sort';
    const TEMPLATE = <<<EOD
    {
        "type": "flexio.sort",
        "params": {
            "order": [{
                "expression": "",
                "direction": ""
            }]
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
                "enum": ["flexio.sort"]
            },
            "params": {
                "type": "object",
                "required": ["order"],
                "properties": {
                    "order": {
                        "type": "array",
                        "minItems": 1,
                        "items": {
                            "type": "object",
                            "required": ["expression"],
                            "properties": {
                                "expression": {
                                    "type": "string",
                                    "minLength": 1
                                },
                                "direction": {
                                    "type": "string",
                                    "enum": ["asc","desc"]
                                }
                            }
                        }
                    }
                }
            }
        }
    }
EOD;
}
