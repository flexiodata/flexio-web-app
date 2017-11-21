<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-01-16
 *
 * @package flexio
 * @subpackage Jobs
 */


declare(strict_types=1);
namespace Flexio\Jobs;

/*
// EXAMPLE:
{
    "type": "flexio.search",
    "params": {
    }
}
*/

class Search extends \Flexio\Jobs\Base
{
    public function run(\Flexio\Jobs\IProcess $process)
    {
        // TODO: implementation dependent on SQL operations on the service;
        // with memory streams, we can no longer rely on this
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::DEPRECATED);

        parent::run($process);

        // process buffer
        $instream = $process->getBuffer();
        $outstream = \Flexio\Base\StreamMemory::create();
        $this->processStream($instream, $outstream);
        $process->setBuffer($outstream);

        // process stream array
        $input = $process->getStreams();
        $process->clearStreams();

        foreach ($input as $instream)
        {
            $outstream = \Flexio\Base\StreamMemory::create();
            $this->processStream($instream, $outstream);
            $process->addStream($outstream);
        }
    }

    private function processStream(\Flexio\Base\IStream &$instream, Flexio\Object\IStream &$outstream)
    {
        // TODO: right now, search works like a filter, so only run it on tables
        // and pass everything through; however search should be stream search
        // where the only streams that get passed on are streams that have the
        // items in question; when we switch to this mode, don't pass on unhandled
        // output

        $mime_type = $instream->getMimeType();
        switch ($mime_type)
        {
            default:
                $outstream = $instream;
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
        // note: \Flexio\Jobs\Search used to be implement the functionality using
        // \Flexio\Jobs\Filter, the old implementation of which is now below until
        // \Flexio\Jobs\Search is reworked; this function takes \Flexio\Jobs\Search
        // JSON definition and constructs the JSON necessary for the old
        // \Flexio\Jobs\Filter job

        // get the columns and search criteria
        $columns = $job_definition['params']['columns'];
        $search_criteria = $job_definition['params']['search'] ?? '';

        // build a filter expression from the search criteria
        if (is_string($columns))
            $columns = explode(',', $columns);
        if (is_string($search_criteria))
        {
            if (strlen($search_criteria) == 0)
                $search_criteria = [];
                 else
                $search_criteria = [ $search_criteria ];
        }

        $items = [];

        if (count($columns) == 1 && $columns[0] == '*')
            $columns = $instream->getStructure()->getNames();

        foreach ($columns as $column)
        {
            foreach ($search_criteria as $crit)
            {
                $items[] = array(
                    'left' => [ $column ],
                    'operator' => 'icontainsword',
                    'right' => $crit
                );
            }
        }

        $filter_job_definition = '
        {
            "params": {
                "condition": {
                  "operator": "or",
                  "items": '.json_encode($items).'
                }
            }
        }
        ';
        $filter_job_definition = json_decode($filter_job_definition, true);


        return self::createFilterStatement($filter_job_definition, $instream, $outstream);
    }

    private static function createFilterStatement(array $job_definition, \Flexio\Base\IStream $instream, \Flexio\Base\IStream &$outstream)
    {
        // if the condition operator exists, it will be either "and" or "or"
        if (!isset($job_definition['params']['condition']['items']) || !is_array($job_definition['params']['condition']['items']) || count($job_definition['params']['condition']['items']) == 0)
            return false;

        $logical = $job_definition['params']['condition']['operator'] ?? 'and';
        $conditions = $job_definition['params']['condition']['items'];

        // properties
        $input_path = $instream->getPath();
        $output_path = $outstream->getPath();

        $input_structure = $instream->getStructure();
        $input_columns = $input_structure->enum();

        $output_structure = $outstream->getStructure();
        $output_columns = $output_structure->enum();

        // iterate through the conditions
        $where = '';
        foreach ($conditions as $item)
        {
            // determine the columns to use in the condition
            $specified_column_names = $item['left'];
            $columns = $input_structure->enum($specified_column_names);

            if (count($columns) === 0)
            {
                // perhaps it's an expression
                $exprs = $item['left'];
                if (is_array($exprs))
                    $exprs = $exprs;
                else if (is_string($exprs))
                    $exprs = [ $exprs ];
                else
                    return false;

                if (count($exprs) == 0)
                    return false;

                $columns = [];
                foreach ($exprs as $expr)
                {
                    $expr_translator = new \Flexio\Base\ExprTranslatorPostgres;
                    $expr_translator->setStructure($input_columns);
                    if ($expr_translator->parse($expr) === false)
                        return false;

                    $type = $expr_translator->getTypeAsString();

                    $columns[] = [ 'name' => $expr, 'type' => $type ];
                }
            }

            $operator = $item['operator'];
            $value = $item['right'];

            $date_format = $item['date_format'] ?? 'MM/DD/YYYY';
            $date_format = str_replace('YYYY','YY', $date_format);
            $date_format = str_replace('YY','YYYY', $date_format);  // allows user to specify YY or YYYY in date values


            $filter_part = '';

            $first = true;
            foreach ($columns as $column)
            {
                if ($first === false)
                    $filter_part .= ' or ';

                $filter_part .= '(';

                if (is_array($value))
                {
                    $first_value = true;
                    foreach ($value as $part)
                    {
                        if ($first_value === false)
                            $filter_part .= ' or ';
                        $filter_part .= '(';
                        $filter_part .= self::assembleConditionPart($column, $operator, $part, $date_format);
                        $filter_part .= ')';
                        $first_value = false;
                    }
                }
                 else
                {
                    $filter_part .= self::assembleConditionPart($column, $operator, $value, $date_format);
                }

                $filter_part .= ')';

                $first = false;
            }

            if (strlen($where) > 0)
                $where .= " $logical ";

            $where .= $filter_part;
        }

        if (strlen($where) == 0)
            return false;


        // build the datastore insert statement
        $columns = '';
        foreach ($output_columns as $col)
        {
            if (strlen($columns) > 0)
                $columns .= ',';

            $columns .= $col['store_name'];
        }

        $where = \Flexio\Base\ExprTranslatorPostgres::translate($where, $input_columns);
        $sql = "INSERT INTO $output_path ($columns) SELECT $columns FROM $input_path WHERE " . $where;
        return $sql;
    }

    private static function assembleConditionPart(array $column, string $operator, $value, string $date_format) // TODO: add parameter type
    {
        $finfo = $column;

        $left = $finfo['name'];
        $is_numeric = ($finfo['type'] == 'numeric' || $finfo['type'] == 'integer' || $finfo['type'] == 'float' || $finfo['type'] == 'double') ? true:false;
        $is_date = ($finfo['type'] == 'date') ? true:false;
        $is_datetime = ($finfo['type'] == 'datetime') ? true:false;
        $is_boolean = ($finfo['type'] == 'boolean') ? true:false;
        $is_character = (!$is_numeric && !$is_datetime && !$is_date && !$is_boolean);

        $part = '(';

        switch ($operator)
        {
            case 'ncontains':
            case 'contains':

                $part .= 'contains(';

                if ($is_character)
                    $part .= $left;
                else if ($is_date)
                    $part .= "to_char($left, " . \Flexio\Base\ExprUtil::quote($date_format) . ")";
                else if ($is_datetime)
                    $part .= "to_char($left, " . \Flexio\Base\ExprUtil::quote($date_format) . ")";
                else
                    $part .= "cast($left,text)";

                /*
                $value = str_replace('%',"\\%",$value);
                $value = str_replace('_',"\\_",$value);
                $qvalue = \Flexio\Base\ExprUtil::quote($value);
                $qvalue = substr_replace($qvalue, '%', 1, 0);
                $qvalue = substr_replace($qvalue, '%', -1, 0);

                if ($operator == 'ncontains')
                    $part .= " not like $qvalue";
                     else
                    $part .= " like $qvalue";
                */

                $part .= ',' . \Flexio\Base\ExprUtil::quote($value) . ')';

                break;

            case 'ncontainsword':
            case 'containsword':
            case 'nicontainsword':
            case 'icontainsword':

                if ($is_character)
                    $part .= $left;
                else if ($is_date)
                    $part .= "to_char($left, " . \Flexio\Base\ExprUtil::quote($date_format) . ")";
                else if ($is_datetime)
                    $part .= "to_char($left, " . \Flexio\Base\ExprUtil::quote($date_format) . ")";
                else
                    $part .= "cast($left,text)";

                $regex = preg_quote($value);
                $regex = "^$regex(\s|$$)|\s$regex\s|\s$regex$$";

                if ($operator == 'nicontainsword' || $operator == 'icontainsword')
                {
                    $part = "$part ~* " . \Flexio\Base\ExprUtil::quote($regex);
                }
                 else
                {
                    $part = "$part ~ " . \Flexio\Base\ExprUtil::quote($regex);
                }


                break;

            case 'eq':
            case 'neq':
            case 'gt':
            case 'gte':
            case 'lt':
            case 'lte':

                $part .= $left;

                    if ($operator == 'eq')  $oper = '=';
                else if ($operator == 'neq') $oper = '<>';
                else if ($operator == 'gt')  $oper = '>';
                else if ($operator == 'gte') $oper = '>=';
                else if ($operator == 'lt')  $oper = '<';
                else if ($operator == 'lte') $oper = '<=';
                else
                    return false; // invalid operator

                if ($is_character)
                {
                    $qvalue = \Flexio\Base\ExprUtil::quote($value);
                }
                else if ($is_numeric)
                {
                    $qvalue = floatval($value);
                }
                else if ($is_date)
                {
                    $qvalue = \Flexio\Base\ExprUtil::quote($value);
                    $qfmt = \Flexio\Base\ExprUtil::quote($date_format);
                    $qvalue = "to_date($qvalue,$qfmt)";
                }

                $part .= " $oper $qvalue";
                break;
        }

        $part .= ')';

        return $part;
    }
}
