<?php
/**
 *
 * Copyright (c) 2017, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2017-07-19
 *
 * @package flexio
 * @subpackage Jobs
 */


declare(strict_types=1);
namespace Flexio\Jobs;

/*
// DESCRIPTION:
{
    "op": "settype",
    "name": "",
    "type": "",
    "decimals": "",
    "expression": ""
    // TODO: fill out
}

// VALIDATOR:
$validator = \Flexio\Base\Validator::create();
if (($validator->check($params, array(
        'op'         => array('required' => true,  'enum' => ['settype'])
    ))->hasErrors()) === true)
    throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);
*/

class SetType extends \Flexio\Jobs\Base
{
    public function run(\Flexio\IFace\IProcess $process) : void
    {
        parent::run($process);

        // stdin/stdout
        $instream = $process->getStdin();
        $outstream = $process->getStdout();
        $this->processStream($instream, $outstream);
    }

    private function processStream(\Flexio\IFace\IStream &$instream, \Flexio\IFace\IStream &$outstream) : void
    {
        $mime_type = $instream->getMimeType();
        switch ($mime_type)
        {
            default:
                $outstream->copyFrom($instream);
                return;

            case \Flexio\Base\ContentType::FLEXIO_TABLE:
                $this->getOutput($instream, $outstream);
                return;
        }
    }

    private function getOutput(\Flexio\IFace\IStream &$instream, \Flexio\IFace\IStream &$outstream) : void
    {
        // get the job properties
        $job_params = $this->getJobParameters();
        $columns = $job_params['columns'];
        $type = $job_params['type'] ?? 'character';
        $width = $job_params['width'] ?? null;
        $scale = $job_params['decimals'] ?? null;

        if (!isset($columns) || !is_array($columns))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);
        if (isset($width) && !is_integer($width))
            $width = (int)$width;
        if (isset($scale) && !is_integer($scale))
            $scale = (int)$scale;

        // create the new output structure
        $outstream->set($instream->get());
        $outstream->setPath(\Flexio\Base\Util::generateHandle());
        $columns = $instream->getStructure()->getNames($columns); // get columns satisfying wildcards
        $column_keys = array_flip($columns);
        $output_structure = \Flexio\Base\Structure::create();
        $changed_columns = array();

        $input_structure_enum = $instream->getStructure()->enum();
        foreach ($input_structure_enum as $i)
        {
            $output_column = $i;
            if (array_key_exists($i['name'], $column_keys) === true)
            {
                $output_column['type'] = $type;
                if (isset($width))
                    $output_column['width'] = $width;
                if (isset($scale))
                    $output_column['scale'] = $scale;

                $changed_columns[] = $output_column;
            }

            $output_structure->push($output_column);
        }
        $outstream->setStructure($output_structure);

        // write to the output
        $streamreader = $instream->getReader();
        $streamwriter = $outstream->getWriter();

        while (true)
        {
            $row = $streamreader->readRow();
            if ($row === false)
                break;

            $row = self::convertRowValues($row, $changed_columns);
            $streamwriter->write($row);
        }

        $streamwriter->close();
        $outstream->setSize($streamwriter->getBytesWritten());
    }

    private static function convertRowValues(array $row, array $changed_columns) : array
    {
        $output_row = array();

        foreach ($row as $name => $value)
        {
            foreach ($changed_columns as $c)
            {
                if ($c['name'] === $name)
                {
                    $value = self::convertValue($value, $c);
                    break;
                }
            }

            $output_row[$name] = $value;
        }

        return $output_row;
    }

    private static function convertValue($value, $changed_column) // TODO: add return type
    {
        $new_type = $changed_column['type'];
        $new_scale = $changed_column['scale'];

        switch ($new_type)
        {
            default:
                return $value;

            case 'text':
            case 'character':
            case 'widecharacter':
                return strval($value);

            case 'numeric':
            case 'double':
                $value = floatval($value);
                $value = round($value, $new_scale);
                return floatval($value);

            case 'integer':
                $value = floatval($value);
                $value = round($value, 0);
                return intval($value);

            case 'date':
                $value = strval($value);
                $value = strtotime($value);
                if ($value === false)
                    return null;
                return date('Y-m-d', $value);

            case 'datetime':
                $value = strval($value);
                $value = strtotime($value);
                if ($value === false)
                    return null;
                return date('Y-m-d H:i:s.u', $value);

            case 'boolean':
                $value = strval($value);
                $value = strtolower(trim($value));
                if ($value === 'true' || $value === 't' || (is_numeric($value) && intval($value) != 0))
                    return true;
                     else
                    return false;
        }
    }
}
