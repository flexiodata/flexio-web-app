<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-08-13
 *
 * @package flexio
 * @subpackage Jobs
 */


namespace Flexio\Jobs;


class Copy extends \Flexio\Jobs\Base
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
                case \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE:
                    $this->createOutputFromTable($instream);
                    break;
            }
        }
    }

    private function createOutputFromTable($instream)
    {
        // input/output
        $outstream = $instream->copy()->setPath(\Flexio\Base\Util::generateHandle());
        $this->getOutput()->push($outstream);

        // create the output
        $job_statement = self::prepareOutput($this->getProperties(), $instream, $outstream);
        if ($job_statement === false)
            return $this->fail(\Flexio\Base\Error::INVALID_PARAMETER, _(''), __FILE__, __LINE__);

        $streamwriter = \Flexio\Object\StreamWriter::create($outstream);
        if ($streamwriter === false)
            return $this->fail(\Flexio\Base\Error::CREATE_FAILED, _(''), __FILE__, __LINE__);

        if ($outstream->getService()->exec($job_statement) === false)
            return $this->fail(\Flexio\Base\Error::WRITE_FAILED, _(''), __FILE__, __LINE__);
    }

    private static function prepareOutput($job_definition, $instream, $outstream)
    {
        // properties
        $input_structure = $instream->getStructure();
        $input_columns = $input_structure->enum();

        $input_path = $instream->getPath();
        $output_path = $outstream->getPath();

        // get the output columns from the job definition
        $output_columns = array();
        if (isset($job_definition['params']['columns']))
            $output_columns = self::getOutputStructureFromColumnParams($job_definition, $input_columns);
              else if (isset($job_definition['params']['actions']))
            $output_columns = self::getOutputStructureFromActionParams($job_definition, $input_columns);
              else
            $output_columns = $input_columns;

        // additional columns may have been added, so make sure the store_name is set,
        // duplicate fieldnames are accounted for, etc
        $structure = \Flexio\Object\Structure::create($output_columns);
        if ($structure === false)
            return false;

        $output_columns = $structure->enum();

        // build the output expression
        $columns = '';
        $exprs = '';

        foreach ($output_columns as &$col)
        {
            if (strlen($columns) > 0)
                $columns .= ',';

            // build up the expression using the internal storage name
            $qcolumn_name = \Flexio\Base\DbUtil::quoteIdentifierIfNecessary($col['store_name']);

            $columns .= $qcolumn_name;
            if (strlen($exprs) > 0)
                $exprs .= ',';

            if (isset($col['expression']))
            {
                $pgsqlexpr = \Flexio\Base\ExprTranslatorPostgres::translate($col['expression'], $input_columns);
                if ($pgsqlexpr === false)
                    return false; // couldn't translate the expression
                $exprs .= $pgsqlexpr . ' AS ' . $qcolumn_name;
            }
                else
            {
                $exprs .= $qcolumn_name;
            }

            // don't pass on formulas used to create a new column
            unset($col['expression']);
        }

        $outstream->setStructure($output_columns);
        $sql = "INSERT INTO $output_path ($columns) SELECT $exprs FROM $input_path";
        return $sql;
    }

    private static function getOutputStructureFromColumnParams($job_definition, $input_columns)
    {
        if (!isset($job_definition['params']['columns']))
            return false;

        // index the columns
        $indexed = [];
        foreach ($input_columns as $column)
        {
            if (isset($column['name']))
                $indexed[strtolower($column['name'])] = $column;
        }

        $output_columns = [];
        foreach ($job_definition['params']['columns'] as $column)
        {
            if (isset($column['name']))
            {
                $output_columns[] = $column;
            }
                else if (is_string($column))
            {
                // locate the column
                $column = strtolower($column);
                if (!isset($indexed[$column]))
                    return false; // column not found

                $output_columns[] = $indexed[$column];
            }
        }

        return $output_columns;
    }

    private static function getOutputStructureFromActionParams($job_definition, $input_columns)
    {
        if (!isset($job_definition['params']['actions']))
            return false;

        $output_columns = $input_columns;

        foreach ($job_definition['params']['actions'] as $action)
        {
            if (!isset($action['action']))
                return false; // missing parameter

            if ($action['action'] == 'drop')
            {
                if (!isset($action['name']))
                    return false; // missing parameter

                // find the column in $output_columns and remove it
                foreach ($output_columns as $k => $col)
                {
                    if (0 == strcasecmp($col['name'], $action['name']))
                    {
                        unset($output_columns[$k]);
                        break;
                    }
                }
            }

            if ($action['action'] == 'create')
            {
                if (!isset($action['params']['name']))
                    return false; // missing parameter

                if (isset($indexed[strtolower($action['params']['name'])]))
                    return false; // invalid parameter

                $field = $action['params']['name'];

                $col = array(
                    "name" => $action['params']['name'],
                    "type" => $action['params']['type']
                );

                if (isset($action['params']['width']))
                    $col['width'] = (int)$action['params']['width'];
                if (isset($action['params']['scale']))
                    $col['scale'] = (int)$action['params']['scale'];
                if (isset($action['params']['expression']))
                    $col['expression'] = $action['params']['expression'];

                $output_columns[] = $col;
            }

            if ($action['action'] == 'alter')
            {
                if (!isset($action['name']))
                    return false; // missing parameter

                $field = $action['name'];

                // find the column in $output_columns and remove it
                $idx = -1;
                foreach ($output_columns as $k => $col)
                {
                    if (0 == strcasecmp($col['name'], $field))
                    {
                        $idx = $k;
                        break;
                    }
                }
                if ($idx == -1)
                {
                    // column not found
                    return false;
                }


                // start out by passing through the column as it is
                $output_columns[$k]['expression'] = \Flexio\Base\DbUtil::quoteIdentifierIfNecessary($field);
                $old_type = $output_columns[$k]['type'];

                if (isset($action['params']['type']) && $old_type != $action['params']['type'])
                {
                    $new_type = $action['params']['type'];

                    $expr = $output_columns[$k]['expression'];
                    $width = $output_columns[$k]['width'];
                    $scale = $output_columns[$k]['scale'];


                    // adjust size when converting from non-character types to character types
                    if ($new_type == 'character' || $new_type == 'widecharacter')
                    {
                        if (!isset($action['params']['width']))
                        {
                            if ($old_type == 'date')
                                $action['params']['width'] = 10;
                            if ($old_type == 'datetime')
                                $action['params']['width'] = 20;
                        }
                    }

                    if ($new_type == 'numeric')
                    {
                        // when converting to numeric, set a max of width 18
                        if (!isset($action['params']['width']))
                            $width = min($width, 18);
                    }

                    // if caller is modifying width/scale at the same time, do it here
                    if (isset($action['params']['width']))
                    {
                        // only allow width changes for character and numeric
                        if ($new_type != 'character' && $new_type != 'widecharacter' && $new_type != 'numeric')
                            return false; // invalid parameter

                        $width = $action['params']['width'];
                    }
                    if (isset($action['params']['scale']))
                    {
                        // only allow scale changes for numeric
                        if ($new_type != 'numeric')
                            return false; // invalid parameter

                        $scale = $action['params']['scale'];
                    }

                    // check bounds
                    if ($width < 1)
                        return false; // invalid parameter
                    if ($width > 65535)
                        return false; // invalid parameter

                    $output_columns[$k]['type'] = $new_type;
                    $output_columns[$k]['width'] = $width;
                    $output_columns[$k]['scale'] = $scale;

                    if (isset($action['params']['expression']))
                    {
                        $output_columns[$k]['expression'] = $action['params']['expression'];
                    }
                        else
                    {
                        $output_columns[$k]['expression'] = \Flexio\Base\ExprUtil::getCastExpression($expr, $old_type, $new_type, $width, $scale);
                    }

                    continue;
                }

                if (isset($action['params']['width']))
                {
                    $type = $output_columns[$k]['type'];
                    $expr = $output_columns[$k]['expression'];
                    $scale = $output_columns[$k]['scale'];
                    $old_width = $output_columns[$k]['width'];
                    $new_width = $action['params']['width'];

                    if ($new_width < 1)
                        return false; // invalid parameter
                    if ($new_width > 65535)
                        return false; // invalid parameter

                    if ($type == 'character' || $type == 'widecharacter')
                        $expr = "cast($expr,character($new_width))";
                    else if ($type == 'numeric')
                        $expr = "cast($expr,numeric($new_width,$scale))";
                    else
                        return false; // invalid parameter

                    $output_columns[$k]['width'] = $new_width;
                    $output_columns[$k]['expression'] = $expr;
                }

                if (isset($action['params']['scale']))
                {
                    $type = $output_columns[$k]['type'];
                    $expr = $output_columns[$k]['expression'];
                    $width = $output_columns[$k]['width'];
                    $new_scale = $action['params']['scale'];

                    if ($new_scale < 0)
                        return false; // invalid parameter
                    if ($new_scale > 15)
                        return false; // invalid parameter

                    if ($type == 'numeric')
                        $expr = "cast($expr,numeric($width,$new_scale))";
                    else
                        return false; // invalid parameter

                    $output_columns[$k]['scale'] = $new_scale;
                    $output_columns[$k]['expression'] = $expr;
                }

                if (isset($action['params']['expression']))
                {
                    $output_columns[$k]['expression'] = $action['params']['expression'];
                }
            }
        }

        return $output_columns;
    }

    // job definition info
    const MIME_TYPE = 'flexio.copy';
    const TEMPLATE = <<<EOD
    {
        "type": "flexio.copy",
        "params": {
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
                "enum": ["flexio.copy"]
            },
            "params": {
                "type": "object"
            }
        }
    }
EOD;
}
