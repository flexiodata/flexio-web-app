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


class Group extends \Flexio\Jobs\Base
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

    private function processStream(\Flexio\Object\Stream $instream) : \Flexio\Object\Stream
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

    public function getOutput(\Flexio\Object\Stream $instream) : \Flexio\Object\Stream
    {
        // input/output
        $outstream = $instream->copy()->setPath(\Flexio\Base\Util::generateHandle());

        // create the output
        $job_statement = self::prepareOutput($this->getProperties(), $instream, $outstream);
        if ($job_statement === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        // note: no need to call StreamWriter::create() because job statement creates a table
        if ($outstream->getService()->exec($job_statement) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);

        // update the output structure; the fieldnames are specified by the job (and may
        // be different than the fieldnames used in the internal storage; however, the
        // the field types, widths, and scale are only known by the job; find out these
        // values and merge them with the appropriate names)
        $store_columns = $outstream->getService()->describeTable($outstream->getPath());
        $output_columns = $outstream->getStructure()->enum();
        $output_columns = self::mergeColumnInfo($output_columns, $store_columns);
        $outstream->setStructure($output_columns);
        return $outstream;
    }

    private static function prepareOutput(array $job_definition, \Flexio\Object\Stream $instream, \Flexio\Object\Stream &$outstream)
    {
        // properties
        if (!isset($job_definition['params']['columns']))
            return false;
        if (!is_array($job_definition['params']['columns']) || count($job_definition['params']['columns']) == 0)
            return false;

        $group_parts = isset($job_definition['params']['group']) ? $job_definition['params']['group'] : array();
        $where = $job_definition['params']['where'] ?? '';
        $having = $job_definition['params']['having'] ?? '';
        $detail = $job_definition['params']['detail'] ?? false;

        $input_columns = $instream->getStructure()->enum();

        // build and save the initial output struture
        $output_columns = array();
        $used_field_names = [];

        foreach ($job_definition['params']['columns'] as &$col)
        {
            if (!isset($col['name']) || !isset($col['function']) || !isset($col['expression']))
                return false;

            $fld = $col['name'];

            // add field name to used_field_names; prevent using same field name twice
            if (array_key_exists(strtolower($fld), $used_field_names))
                return false;

            $used_field_names[strtolower($fld)] = true;

            // save part of the structure info (type, width and scale will be populated later)
            $output_column = array(
                'name' => $fld,
                'type' => null,
                'width' => null,
                'scale' => null
            );
            $output_columns[] = $output_column;
        }

        $outstream->setStructure($output_columns);
        $output_columns = $outstream->getStructure()->enum();

        // build the group expression
        $group_expr = '';
        $group_fields = [];

        $input_columns_indexed = [];
        foreach ($input_columns as $column)
            $input_columns_indexed[strtolower($column['name'])] = $column;

        foreach ($group_parts as $part)
        {
            $part = strtolower($part);
            if (!isset($input_columns_indexed[$part]))
                return false;

            // add this field to the group_fields map (used below)
            $group_column_info = $input_columns_indexed[$part];
            $group_fields[$part] = $group_column_info;

            if (strlen($group_expr) > 0)
                $group_expr .= ',';
            $group_expr .=  $group_column_info['store_name']; // build the group expression with the internal store_name
        }

        // build the column expression
        $column_expr = '';

        $output_columns_indexed = [];
        foreach ($output_columns as $column)
            $output_columns_indexed[strtolower($column['name'])] = $column;

        foreach ($job_definition['params']['columns'] as &$col)
        {
            if (strlen($column_expr) > 0)
                $column_expr .= ',';

            // find out the store_name for the specified field
            $specified_fieldname = $col['name'];
            $store_fieldinfo = $output_columns_indexed[$specified_fieldname] ?? false;
            if ($store_fieldinfo === false)
                return false;

            $store_name = $store_fieldinfo['store_name'];
            $func = $col['function'];
            $expr = $col['expression'];

            switch ($col['function'])
            {
                case '':
                case 'first':
                case 'group_by':
                {
                    if (array_key_exists(strtolower($expr), $group_fields))
                    {
                        // we don't need to qualify this field with any aggregate function,
                        // because we are using the field in the GROUP BY clause
                    }
                     else
                    {
                        $expr = "MAX($expr)";
                    }
                }
                break;

                case 'row_count':
                {
                    $expr = "count(*)"; // TODO: should we even have a row_count given the behavior of count?
                }
                break;

                case 'count':
                {
                    // note: count(*) can produce different result than count(<field>);
                    // count(<field>) returns the number of non-null values in a given
                    // field, so that if <field> contains nulls, then the count of the
                    // values in that field will be less than the number of rows in the
                    // table
                    if (strlen($expr) === 0)
                        $expr = "count(*)";
                         else
                        $expr = "count(($expr))";
                }
                break;

                case 'group_id':
                case 'groupid':      $expr = "row_number() over (ORDER BY $group_expr)";    break;
                case 'min':          $expr = "min(($expr))";            break;
                case 'max':          $expr = "max(($expr))";            break;
                case 'sum':          $expr = "sum(($expr))";            break;
                case 'avg':          $expr = "avg(($expr))";            break;
                case 'group_concat': $expr = "string_agg(($expr),',')"; break;
                default:
                    // invalid grouping function
                    return false;
            }

            $qstore_name = \Flexio\Base\DbUtil::quoteIdentifierIfNecessary($store_name);
            $column_expr .= "($expr) AS $qstore_name";
        }


        // build the output statement
        $where = \Flexio\Base\ExprTranslatorPostgres::translate($where, $input_columns);
        $having = \Flexio\Base\ExprTranslatorPostgres::translate($having, $output_columns);
        if ($where === false)
            $where = '';
        if ($having === false)
            $having = '';

        $sql = "CREATE TABLE " . $outstream->getPath() . " AS SELECT ";
        $sql .= " $column_expr ";
        $sql .= " FROM " . $instream->getPath();

        if (strlen($where) > 0)
            $sql .= " WHERE ($where)";

        if (strlen($group_expr) > 0)
            $sql .= " GROUP BY $group_expr";

        if (strlen($having) > 0)
            $sql .= " HAVING ($having)";

        if (strlen($group_expr) > 0)
            $sql .= " ORDER BY ($group_expr)";

        return $sql;
    }

    private static function mergeColumnInfo(array $stream_columns, array $store_columns)
    {
        // take the name and the display name from the stream columns and merge
        // in the width and scale info from the columns in the storage table;
        // the field names in the storage table correspond to the store_name
        // names in the stream columns

        $store_columns_indexed = [];
        foreach ($store_columns as $c)
            $store_columns_indexed[strtolower($c['name'])] = $c;

        $output_columns = array();
        foreach ($stream_columns as $c)
        {
            $lookup_name = strtolower($c['store_name']);

            $type = $store_columns_indexed[$lookup_name]['type'] ?? false;
            $width = $store_columns_indexed[$lookup_name]['width'] ?? false;
            $scale = $store_columns_indexed[$lookup_name]['scale'] ?? false;

            $output_column = array();
            $output_column['name'] = $c['name'];

            if ($type !== false)
                $output_column['type'] = $type;
            if ($width !== false)
                $output_column['width'] = $width;
            if ($scale !== false)
                $output_column['scale'] = $scale;

            $output_columns[] = $output_column;
        }

        return $output_columns;
    }

    // job definition info
    const MIME_TYPE = 'flexio.group';
    const TEMPLATE = <<<EOD
    {
        "type": "flexio.group",
        "params": {
            "group": [
                "vend_no"
            ],
            "columns": [{
                "name": "vend_no",
                "function": "",
                "expression": "vend_no"
            },
            {
                "name": "row_count",
                "function": "count",
                "expression": ""
            },
            {
                "name": "sum_gross_amt",
                "function": "sum",
                "expression": "gross_amt"
            }],
            "where" : "",
            "having" : "",
            "unique" : false,
            "detail" : false
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
                "enum": ["flexio.group"]
            },
            "params": {
                "type": "object",
                "required": ["columns"],
                "properties": {
                    "group": {
                        "type": "array",
                        "items": {
                            "type": "string",
                            "format": "fx.fieldname"
                        }
                    },
                    "columns": {
                        "type": "array",
                        "minItems": 1,
                        "items": {
                            "type": "object",
                            "required": ["name", "function", "expression"],
                            "properties": {
                                "name": {
                                    "type": "string",
                                    "format": "fx.fieldname"
                                },
                                "function": {
                                    "type": "string",
                                    "enum": ["","group_by","first","min","max","sum","avg","row_count","count","group_id","groupid","group_concat"]
                                },
                                "expression": {
                                    "type": "string"
                                }
                            }
                        }
                    },
                    "where": {
                        "type": "string"
                    },
                    "having": {
                        "type": "string"
                    },
                    "unique": {
                        "type": "boolean"
                    },
                    "detail": {
                        "type": "boolean"
                    }
                }
            }
        }
    }
EOD;
}
