<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-08-01
 *
 * @package flexio
 * @subpackage Jobs
 */


declare(strict_types=1);
namespace Flexio\Jobs;


class Duplicate extends \Flexio\Jobs\Base
{
    public function run()
    {
        $this->getOutput()->setEnv($this->getInput()->getEnv());
        $input = $this->getInput()->getStreams();

        foreach ($input as $instream)
        {
            $mime_type = $instream->getMimeType();
            switch ($mime_type)
            {
                // unhandled input; TODO: should handle for other types of content
                default:
                    $this->getOutput()->addStream($instream->copy());
                    break;

                // table input
                case \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE:
                    $this->createOutputFromTable($instream);
                    break;
            }
        }
    }

    private function createOutputFromTable(\Flexio\Object\Stream $instream)
    {
        // input/output
        $outstream = $instream->copy()->setPath(\Flexio\Base\Util::generateHandle());
        $this->getOutput()->addStream($outstream);

        // create the output
        $job_statement = self::prepareOutput($this->getProperties(), $instream, $outstream);
        if ($job_statement === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $streamwriter = \Flexio\Object\StreamWriter::create($outstream);

        if ($outstream->getService()->exec($job_statement) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);
    }

    private static function prepareOutput(array $job_definition, \Flexio\Object\Stream $instream, \Flexio\Object\Stream &$outstream)
    {
        $input_path = $instream->getPath();
        $output_path = $outstream->getPath();

        $input_structure = $instream->getStructure();
        $input_columns = $input_structure->enum();

        $specified_column_names = $job_definition['params']['columns'];
        $duplicate_columns = $input_structure->enum($specified_column_names);

        if (count($duplicate_columns) == 0)
            return false;

        // build up the output column string
        $output_columns_str = '';
        foreach ($input_columns as $col)
        {
            if (strlen($output_columns_str) > 0)
                $output_columns_str .= ',';

            $output_columns_str .= $col['store_name'];
        }

        // build up the duplicate column string
        $duplicate_columns_str = '';
        foreach ($duplicate_columns as $col)
        {
            if (strlen($duplicate_columns_str) > 0)
                $duplicate_columns_str .= ',';

            $duplicate_columns_str .= $col['store_name'];
        }

        $sql = "
            insert into $output_path ($output_columns_str)
             select $output_columns_str from (
                select $output_columns_str,
                count(*) over (partition by $duplicate_columns_str order by $duplicate_columns_str) as xdrowcnt
                from $input_path
            ) duplicate_rows
            where
            duplicate_rows.xdrowcnt > 1
        ";

        return $sql;
    }

    // job definition info
    const MIME_TYPE = 'flexio.duplicate';
    const TEMPLATE = <<<EOD
    {
        "type": "flexio.duplicate",
        "params": {
            "columns": [ "*" ]
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
                "enum": ["flexio.duplicate"]
            },
            "params": {
                "type": "object"
            }
        }
    }
EOD;
}
