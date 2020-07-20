<?php
/**
 *
 * Copyright (c) 2019, Flex Research LLC. All rights reserved.
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

class Search implements \Flexio\IFace\IJob
{
    private $properties = array();

    public static function validate(array $task) : array
    {
        $errors = array();
        return $errors;
    }

    public static function run(\Flexio\IFace\IProcess $process, array $task) : void
    {
        unset($task['op']);
        $object = new static();
        $object->properties = $task;
        $object->run_internal($process);
    }

    private function run_internal(\Flexio\IFace\IProcess $process) : void
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
            // we only care about names; ignore types since structure info can come
            // from places like the interface that may not care about official types
            // so much as describing behavior (e.g. descriptive type of: array[string]
            // or something like that)
            $simple_structure = array();
            foreach ($structure as $s)
                $simple_structure[] = array('name' => $s['name'] ?? '');

            $structure = \Flexio\Base\Structure::create($simple_structure);
            $available_columns = $structure->getNames();
        }

        // EXPERIMENTAL: query params passed in via stdin as a json string of array
        // values as follows:
        // first parameter: desired return columns or "*" for all columns
        // second parameter: query string/array to limit the results; when in string mode, uses lucene query syntax
        // third parameter; additional configuration (header=true/false, limit=max rows to return)
        // examples:
        //  - ["*", "_exists_:title"]
        //  - ["*", "author:brown", "headers=true&limit=0"]
        //  - [["col1","col2"], ["col1","a"],["col2","b"]]

        // get the query parameters; these are passed in as stdin;
        // TODO: should we do it a different way?
        $instream = $process->getStdin();
        $streamreader = $instream->getReader();

        $query_parameters = '';
        while (($chunk = $streamreader->read()) !== false)
            $query_parameters .= $chunk;

        $query_parameters = @json_decode($query_parameters, true);
        if (is_null($query_parameters) || (is_string($query_parameters) && strlen($query_parameters) === 0))
            $query_parameters = array(); // treat null or empty string like empty parameters

        if (!is_array($query_parameters))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $columns_to_return = self::getColumns($query_parameters, $available_columns);
        $rows_to_return = self::getQuery($query_parameters);
        $additional_output_config = self::getConfig($query_parameters);

        $return_headers = true; // return headers by default
        if (isset($additional_output_config['headers']))
            $return_headers = $additional_output_config['headers'];

        $limit_row_count = false; // don't limit rows by default

        // limit based on user-passed parameters
        if (isset($additional_output_config['limit']))
            $limit_row_count = $additional_output_config['limit'];

        // if row limit isn't set by the user, set it to any default
        // given by the mount config items
        $process_params = $process->getParams();
        if ($limit_row_count === false && isset($process_params['search_default_result_size']))
        {
            $search_default_result_size_param = $process_params['search_default_result_size'];
            $limit_row_count = intval($search_default_result_size_param);
        }

        // cap limit based on fixed process parameters passed in from
        // mount config items
        $process_params = $process->getParams();
        if (isset($process_params['search_max_result_size']))
        {
            $search_max_result_size_param = $process_params['search_max_result_size'];
            $search_max_result_size = intval($search_max_result_size_param);
            if ($limit_row_count === false || $limit_row_count > $search_max_result_size)
                $limit_row_count = $search_max_result_size;
        }

        // connect to elasticsearch
        $elasticsearch = \Flexio\System\System::getSearchCache();

        // write the output of the search query to stdout
        $outstream = $process->getStdout();
        $outstream->setMimeType(\Flexio\Base\ContentType::JSON);
        $streamwriter = $outstream->getWriter();

        // start the output
        $streamwriter->write("[");
        $first = true;

        if ($return_headers == true)
        {
            $streamwriter->write(json_encode($columns_to_return));
            $first = false;
        }

        // write the output
        $params['path'] = $index;
        $params['q'] = $rows_to_return;

        if ($limit_row_count !== false)
            $params['limit'] = $limit_row_count;

        $elasticsearch->read($params, function($row) use (&$streamwriter, &$columns_to_return, &$first) {
            if ($first !== true)
                $streamwriter->write(',');

            $row_values = [];
            foreach ($columns_to_return as $c)
                $row_values[] = $row[$c] ?? '';

            $streamwriter->write(json_encode($row_values, JSON_UNESCAPED_SLASHES));
            $first = false;
        });

        // if first is true, we haven't written anything, add an output with
        // a two-dimensional array with an empty string; this allows this function
        // to return results that won't throw an error in the add-on; note: for API
        // usage, this may not be the best behavior, so we want do something else
        if ($first === true)
            $streamwriter->write('[""]');

        // end the output
        $streamwriter->write(']');
    }

    private function getJobParameters() : array
    {
        return $this->properties;
    }

    private static function getColumns(array $search_params, array $available_columns) : ?array
    {
        // if we don't have any column params, return all the columns
        if (count($search_params) < 1)
            return $available_columns;

        // get the requested columns
        $columns_requested = $search_params[0];
        $columns_requested = \Flexio\Base\Util::coerceToArray($columns_requested);

        // replace wildcard columns with all available columns
        $columns_to_return = [];
        foreach ($columns_requested as $c)
        {
            if ($c !== '*' && $c !== '')
            {
                // add listed column
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

        return $columns_to_return;
    }

    private static function getQuery(array $search_params) : ?array
    {
        if (count($search_params) < 2)
            return null;

        $query_param = $search_params[1];

        // no search param; return null, which will cause all items to be returned
        if (is_null($query_param))
            return null;

        $query_string = null;

        if (is_bool($query_param))
            $query_string = ($query_param === true ? 'true' : 'false');

        if (is_int($query_param))
            $query_string = strval($query_param);

        if (is_float($query_param))
            $query_string = strval($query_param);

        if (is_string($query_param))
            $query_string = $query_param;

        if (is_array($query_param))
            $query_string = self::buildQuery($query_param);

        // if we weren't able to get a query string, error out
        if (!isset($query_string))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        // if we have an empty string, return null, which will cause all items to be returned
        if (strlen(trim($query_string)) === 0)
            return null;

        // right now, we don't need queries with scores, because we're only interested in
        // whether or not a row matches; using a filter without a score is faster since
        // the a score doesn't need to be calculated and the results are cached
        // $query_with_score = ['query' => ['query_string' => ['query' => $query_string]]];
        $query_without_score = ['query' => ['bool' => ['filter' => ['query_string' => ['query' => $query_string]]]]];
        return $query_without_score;
    }

    private static function getConfig(array $search_params) : ?array
    {
        if (count($search_params) < 3)
            return null;

        $config_param = $search_params[2];

        if (!is_string($config_param))
            return null;

        $parse_arr = array();
        parse_str($config_param, $parse_arr);
        if (!isset($parse_arr))
            return null;

        $result = array();
        if (array_key_exists('headers', $parse_arr))
            $result['headers'] = toBoolean($parse_arr['headers']);
        if (array_key_exists('limit', $parse_arr))
            $result['limit'] = intval($parse_arr['limit']);

        return $result;
    }

    private static function buildQuery(array $arr) : string
    {
        // make sure we have a basic two-dimensional array
        $table = \Flexio\Base\Table::create($arr)->getRange();

        // get the content and build the query; combine rows
        // into AND of values (ignoring empty spaces)
        $query = '';
        foreach ($table as $row)
        {
            $row_query = '';
            foreach ($row as $value)
            {
                // ignore nulls and empty strings
                if (is_null($value) || $value === '')
                    continue;

                // convert boolean to equivalent string
                if (is_bool($value))
                {
                    if ($value === true)
                        $value = 'true';
                         else
                        $value = 'false';
                }

                // convert ints/floats to equivalent string
                if (is_int($value) || is_float($value))
                    $value = strval($value);

                if (strlen($row_query) > 0)
                    $row_query .= ' AND ';

                $row_query .= '(' . $value . ')';
            }

            // ignore empty rows
            if (strlen($row_query) === 0)
                continue;

            if (strlen($query) > 0)
                $query .= ' OR ';

            $query .= '(' . $row_query . ')';
        }

        return $query;
    }
}

