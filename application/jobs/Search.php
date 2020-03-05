<?php
/**
 *
 * Copyright (c) 2019, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2019-08-08
 *
 * @package flexio
 * @subpackage Jobs
 */


declare(strict_types=1);
namespace Flexio\Jobs;

/*
// DESCRIPTION:
{
    "op": "search",  // string, required
    "index": "",     // string, required (index to query; for internal cache this is the eid of the pipe the cache is associated with)
    "query": "",     // string, required
    "columns: ""     // string, optional
}

// VALIDATOR:
$validator = \Flexio\Base\Validator::create();
if (($validator->check($params, array(
        'op'         => array('required' => true,  'enum' => ['search']),
        'index'      => array('required' => true,  'type' => 'string')
    ))->hasErrors()) === true)
    throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);
*/

class Search extends \Flexio\Jobs\Base
{
    public function run(\Flexio\IFace\IProcess $process) : void
    {
        // get the input index
        $job_params = $this->getJobParameters();
        $index = $job_params['index'] ?? false;
        $structure = $job_params['structure'] ?? false;
        if ($index === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX, "Missing parameter 'index'");

        // get the available columns from the structure, if it's supplied; used for
        // wildcard column selection
        $available_columns = array();
        if ($structure !== false)
        {
            $structure = \Flexio\Base\Structure::create($structure);
            $available_columns = $structure->getNames();
        }

        // TODO: experimental: parameters passed in as stdin
        $instream = $process->getStdin();
        $streamreader = $instream->getReader();

        $data = '';
        while (($chunk = $streamreader->read()) !== false)
            $data .= $chunk;

        $columns_to_return = array();
        $rows_to_return = '';
        self::getSearchParams($data, $available_columns, $columns_to_return, $rows_to_return);

        // connect to elasticsearch
        $elasticsearch_connection_info = \Flexio\System\System::getSearchCacheConfig();
        if ($elasticsearch_connection_info['type'] !== 'elasticsearch')
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE, "Search not available");
        $elasticsearch = \Flexio\Services\ElasticSearch::create($elasticsearch_connection_info);


        //echo($rows_to_return);
        //die;

        // query the index
        $result = $elasticsearch->query($index, $rows_to_return);

        // write the output of the search query to stdout
        $outstream = $process->getStdout();
        $streamwriter = $outstream->getWriter();

        // start the output
        $streamwriter->write("[");

        // write out each row
        $first = true;
        foreach ($result as $r)
        {
            $row_values = [];
            foreach ($columns_to_return as $c)
            {
                $row_values[] = $r[$c] ?? '';
            }

            if ($first !== true)
                $streamwriter->write(',');

            $streamwriter->write(json_encode($row_values));
            $first = false;
        }

        // end the output
        $streamwriter->write(']');

        // set the content type
        $outstream->setMimeType(\Flexio\Base\ContentType::JSON);
    }

    private static function getSearchParams(string $search_params, array $available_columns, array &$search_columns, string &$search_rows) : void
    {
        // EXPERIMENTAL: query params passed in as a json string of array values
        // first parameter: desired return columns or "*" for all columns
        // second parameter: query string/array to limit the results
        // examples:
        //  - ["*", "col1=a&col2=b"]
        //  - [["col1","col2"], ["col1","a"],["col2","b"]]

        // default to all columns/rows
        $search_columns = $available_columns;
        $search_rows = '{"query": {"match_all": {}}}';

        // get the input
        $search_params = @json_decode($search_params, true);
        if (!is_array($search_params))
            return;

        // get the column selection params
        if (count($search_params) > 0)
        {
            $columns_to_return = [];
            $columns_requested = $search_params[0];
            $columns_requested = \Flexio\Base\Util::coerceToArray($columns_requested);

            // replace wildcard columns with all available columns
            foreach ($columns_requested as $c)
            {
                if ($c !== '*')
                {
                    $columns_to_return[] = $c;
                }
                 else
                {
                    foreach ($available_columns as $ac)
                    {
                        $columns_to_return[] = $ac;
                    }
                }
            }

            $search_columns = $columns_to_return;
        }

        // get the row selection params
        if (count($search_params) > 1)
        {
            $query_parameters = self::coerceToQueryParams($search_params[1], $available_columns);

            // example:
            // '{"query": {"bool": "must": {"match": {"first_name": "John"}}}}';
            $match_expression = array();
            foreach ($query_parameters as $key => $value)
            {
                // for now, straight key/value copy
                $match_expression[] = ['match' => [$key => $value]];
            }

            $match_expression = json_encode($match_expression,JSON_UNESCAPED_SLASHES);
            $search_rows = '{"query": {"bool": {"must": '.$match_expression. '}}}';
        }
    }

    private static function coerceToQueryParams($value, array $available_columns) : array
    {
        if (is_string($value))
        {
            // if we have a string, treat it as a URL query string
            // examples:\
            //  - col1=a
            //  - col1=a&col2=b
            $query_parameters = array();
            parse_str($value, $query_parameters);
            return $query_parameters;
        }

        if (is_array($value))
        {
            // if we have an array, treat it is a combination of column names and associated
            // filter values on those columns in either vertical or horizontal format:
            // examples:
            //  - [["col1","col2"],["a","b"]] // horizontal column format where values are under columns
            //  - [["col1","a"],["col2","b"]] // vertical column format where values are to the right of the columns

            // first, determine if we're in the horizontal or vertical mode by seeing which has
            // values that match columns names; in even of tie, give precedence to vertical
            // column format to handle basic filter where value would be next to column title: ["col1", "a"]

            $table = array();
            try
            {
                $table = \Flexio\Base\Table::create($value);

                // get the first row and the first column
                $possible_horizontal_columns = $table->getRow(0);
                $possible_vertical_columns = $table->getCol(0);

                // convert columns to lowercase string for match
                $possible_horizontal_columns = array_map('strval', $possible_horizontal_columns);
                $possible_horizontal_columns = array_map('strtolower', $possible_horizontal_columns);
                $possible_vertical_columns = array_map('strval', $possible_vertical_columns);
                $possible_vertical_columns = array_map('strtolower', $possible_vertical_columns);

                // use the better orientation for determining the query values;
                // give precedence to vertical column layout
                $count_matching_columns_horizontal = count(array_intersect($available_columns, $possible_horizontal_columns));
                $count_matching_columns_vertical = count(array_intersect($available_columns, $possible_vertical_columns));

                if ($count_matching_columns_horizontal > $count_matching_columns_vertical)
                {
                    $query_parameters = array();
                    if ($table->getRowCount() > 1)
                    {
                        $col_count = $table->getColCount();
                        for ($idx = 0; $idx < $col_count; $idx++)
                        {
                            $column = $table->getCol($idx);
                            $query_param_name = $column[0];
                            $query_param_value = $column[1]; // TODO: for now, only use the first item
                            $query_parameters[$query_param_name] = $query_param_value;
                        }
                    }
                    return $query_parameters;
                }
                else
                {
                    $query_parameters = array();
                    if ($table->getColCount() > 1)
                    {
                        $row_count = $table->getRowCount();
                        for ($idx = 0; $idx < $row_count; $idx++)
                        {
                            $row = $table->getRow($idx);
                            $query_param_name = $row[0];
                            $query_param_value = $row[1]; // TODO: for now, only use the first item
                            $query_parameters[$query_param_name] = $query_param_value;
                        }
                    }
                    return $query_parameters;
                }
            }
            catch (\Flexio\Base\Exception $e)
            {
                // fall through
            }
        }

        // some other format
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);
    }
}

