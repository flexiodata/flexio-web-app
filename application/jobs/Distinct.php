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


class Distinct extends \Flexio\Jobs\Base
{
    public function run(\Flexio\Object\Context &$context)
    {
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

        $streamwriter = \Flexio\Object\StreamWriter::create($outstream);
        if ($outstream->getService()->exec($job_statement) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);

        return $outstream;
    }

    private static function prepareOutput(array $job_definition, \Flexio\Object\IStream $instream, \Flexio\Object\IStream $outstream)
    {
        // get the input/output paths
        $input_path = $instream->getPath();
        $output_path = $outstream->getPath();

        // get the list of output columns
        $selected_columns_list = $job_definition['params']['columns'] ?? false;
        if (!is_array($selected_columns_list))
            $selected_columns_list = array(\Flexio\Base\Structure::WILDCARD_ALL);

        // get the list of columns to use for determining distinctness
        $distinct_columns_list = $job_definition['params']['distinct'] ?? false;
        if (!is_array($distinct_columns_list))
            $distinct_columns_list = array(\Flexio\Base\Structure::WILDCARD_ALL);

        // convert the specified fields and wildcards into the list of columns
        $selected_columns = $instream->getStructure()->enum($selected_columns_list);
        $distinct_columns = $instream->getStructure()->enum($distinct_columns_list);

        // make sure we have at least one output column; save the output structure
        if (count($selected_columns) === 0)
            return false;
        $outstream->setStructure($selected_columns);

        // build the statement
        $selected_columns_str = '';
        foreach ($selected_columns as $col)
        {
            if (strlen($selected_columns_str) > 0)
                $selected_columns_str .= ',';

            $selected_columns_str .= $col['store_name']; // use the fieldname used in the storage table
        }

        $distinct_columns_str = '';
        foreach ($distinct_columns as $col)
        {
            if (strlen($distinct_columns_str) > 0)
                $distinct_columns_str .= ',';

            $distinct_columns_str .= $col['store_name']; // use the fieldname used in the storage table
        }

        if (strlen($distinct_columns_str) > 0)
            $distinct_columns_str = "ON ($distinct_columns_str)";

        $sql = "INSERT INTO $output_path ($selected_columns_str) SELECT DISTINCT $distinct_columns_str $selected_columns_str FROM $input_path";
        return $sql;
    }

    // job definition info
    const MIME_TYPE = 'flexio.distinct';
    const TEMPLATE = <<<EOD
    {
        "type": "flexio.distinct",
        "params": {
            "distinct": [ "email_field"],
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
                "enum": ["flexio.distinct"]
            },
            "params": {
                "type": "object"
            }
        }
    }
EOD;
}
