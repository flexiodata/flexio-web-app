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


namespace Flexio\Jobs;


require_once __DIR__ . DIRECTORY_SEPARATOR . 'Base.php';

class SearchJob extends \Flexio\Jobs\Base
{
    public function run()
    {
        // TODO: right now, search works like a filter, so only run it on tables
        // and pass everything through; however search should be stream search
        // where the only streams that get passed on are streams that have the
        // items in question; when we switch to this mode, don't pass on unhandled
        // output

        $input = $this->getInput()->enum();
        foreach ($input as $instream)
        {
            $mime_type = $instream->getMimeType();
            switch ($mime_type)
            {
                // unhandled input; TODO: see note above
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
        // note: the SearchJob used to be implement the functionality using
        // the FilterJob, the old implementation of which is now below until
        // the SearchJob is reworked; this function takes the SearchJob JSON
        // definition and constructs the JSON necessary for the old FilterJob

        // get the columns and search criteria
        $columns = $job_definition['params']['columns'];
        $search_criteria = isset_or($job_definition['params']['search'], '');

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

    private static function createFilterStatement($job_definition, $instream, &$outstream)
    {
        // if the condition operator exists, it will be either "and" or "or"
        if (!isset($job_definition['params']['condition']['items']) || !is_array($job_definition['params']['condition']['items']) || count($job_definition['params']['condition']['items']) == 0)
            return false;

        $logical = isset_or($job_definition['params']['condition']['operator'], 'and');
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
                    $expr_translator = new \Flexio\Services\ExprTranslatorPostgres;
                    $expr_translator->setStructure($input_columns);
                    if ($expr_translator->parse($expr) === false)
                        return false;

                    $type = $expr_translator->getTypeAsString();

                    $columns[] = [ 'name' => $expr, 'type' => $type ];
                }
            }

            $operator = $item['operator'];
            $value = $item['right'];

            $date_format = isset_or($item['date_format'], 'MM/DD/YYYY');
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

        $where = \Flexio\Services\ExprTranslatorPostgres::translate($where, $input_columns);
        $sql = "INSERT INTO $output_path ($columns) SELECT $columns FROM $input_path WHERE " . $where;
        return $sql;
    }

    private static function assembleConditionPart($column, $operator, $value, $date_format)
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
                    $part .= "to_char($left, " . \Flexio\Services\ExprUtil::quote($date_format) . ")";
                else if ($is_datetime)
                    $part .= "to_char($left, " . \Flexio\Services\ExprUtil::quote($date_format) . ")";
                else
                    $part .= "cast($left,text)";

                /*
                $value = str_replace('%',"\\%",$value);
                $value = str_replace('_',"\\_",$value);
                $qvalue = \Flexio\Services\ExprUtil::quote($value);
                $qvalue = substr_replace($qvalue, '%', 1, 0);
                $qvalue = substr_replace($qvalue, '%', -1, 0);

                if ($operator == 'ncontains')
                    $part .= " not like $qvalue";
                     else
                    $part .= " like $qvalue";
                */

                $part .= ',' . \Flexio\Services\ExprUtil::quote($value) . ')';

                break;

            case 'ncontainsword':
            case 'containsword':
            case 'nicontainsword':
            case 'icontainsword':

                if ($is_character)
                    $part .= $left;
                else if ($is_date)
                    $part .= "to_char($left, " . \Flexio\Services\ExprUtil::quote($date_format) . ")";
                else if ($is_datetime)
                    $part .= "to_char($left, " . \Flexio\Services\ExprUtil::quote($date_format) . ")";
                else
                    $part .= "cast($left,text)";

                $regex = preg_quote($value);
                $regex = "^$regex(\s|$$)|\s$regex\s|\s$regex$$";

                if ($operator == 'nicontainsword' || $operator == 'icontainsword')
                {
                    $part = "$part ~* " . \Flexio\Services\ExprUtil::quote($regex);
                }
                 else
                {
                    $part = "$part ~ " . \Flexio\Services\ExprUtil::quote($regex);
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
                    $qvalue = \Flexio\Services\ExprUtil::quote($value);
                }
                else if ($is_numeric)
                {
                    $qvalue = floatval($value);
                }
                else if ($is_date)
                {
                    $qvalue = \Flexio\Services\ExprUtil::quote($value);
                    $qfmt = \Flexio\Services\ExprUtil::quote($date_format);
                    $qvalue = "to_date($qvalue,$qfmt)";
                }

                $part .= " $oper $qvalue";
                break;
        }

        $part .= ')';

        return $part;
    }


    // job definition info
    const MIME_TYPE = 'flexio.search';
    const TEMPLATE = <<<EOD
    {
        "type": "flexio.search",
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
                "enum": ["flexio.search"]
            },
            "params": {
                "type": "object"
            }
        }
    }
EOD;
}
