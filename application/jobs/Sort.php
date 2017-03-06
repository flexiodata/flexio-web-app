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


namespace Flexio\Jobs;


require_once __DIR__ . DIRECTORY_SEPARATOR . 'Base.php';

class Sort extends \Flexio\Jobs\Base
{
    public function run()
    {
        $input = $this->getInput()->enum();
        foreach ($input as $instream)
        {
            $mime_type = $instream->getMimeType();
            switch ($mime_type)
            {
                // unhandled input
                default:
                    $this->getOutput()->push($instream->copy());
                    break;

                // table input
                case \Flexio\System\ContentType::MIME_TYPE_FLEXIO_TABLE:
                    $this->createOutputFromTable($instream);
                    break;
            }
        }
    }

    private function createOutputFromTable($instream)
    {
        // input/output
        $outstream = $instream->copy()->setPath(\Flexio\System\Util::generateHandle());
        $this->getOutput()->push($outstream);

        // create the output
        $job_statement = self::prepareOutput($this->getProperties(), $instream, $outstream);
        if ($job_statement === false)
            return $this->fail(\Model::ERROR_INVALID_PARAMETER, _(''), __FILE__, __LINE__);

        $streamwriter = \Flexio\Object\StreamWriter::create($outstream);
        if ($streamwriter === false)
            return $this->fail(\Model::ERROR_CREATE_FAILED, _(''), __FILE__, __LINE__);

        if ($outstream->getService()->exec($job_statement) === false)
            return $this->fail(\Model::ERROR_WRITE_FAILED, _(''), __FILE__, __LINE__);
    }

    private static function prepareOutput($job_definition, $instream, &$outstream)
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

                $s = \Flexio\Services\ExprTranslatorPostgres::translate($part['expression'], $input_columns);
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
