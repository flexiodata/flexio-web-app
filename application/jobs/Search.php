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

        // search is an internal function for pulling info from the
        // elasticsearch index; don't allow parameter replacement to
        // save a little bit of time
        //\Flexio\Jobs\Util::replaceParameterTokens($process, $task);

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
            $structure = \Flexio\Base\Structure::create($structure);
            $available_columns = $structure->getNames();
        }

        // get the query parameters; these are passed in as stdin;
        // TODO: should we do it a different way?
        $instream = $process->getStdin();
        $streamreader = $instream->getReader();

        $query_parameters = '';
        while (($chunk = $streamreader->read()) !== false)
            $query_parameters .= $chunk;

        $columns_to_return = array();
        $rows_to_return = '';
        $additional_output_config = array();
        self::getSearchParams($query_parameters, $available_columns, $columns_to_return, $rows_to_return, $additional_output_config);

        $return_headers = false;
        if (isset($additional_output_config['headers']) && $additional_output_config['headers'] === true)
            $return_headers = true;

        $limit_row_count = false;
        if (isset($additional_output_config['limit']))
            $limit_row_count = $additional_output_config['limit'];

        // connect to elasticsearch
        $elasticsearch_connection_info = \Flexio\System\System::getSearchCacheConfig();
        if ($elasticsearch_connection_info['type'] !== 'elasticsearch')
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE, "Search not available");
        $elasticsearch = \Flexio\Services\ElasticSearch::create($elasticsearch_connection_info);

        // query the index
        $result = $elasticsearch->query($index, $rows_to_return);

        // write the output of the search query to stdout
        $outstream = $process->getStdout();
        $streamwriter = $outstream->getWriter();

        // start the output
        $streamwriter->write("[");
        $first = true;

        if ($return_headers == true)
        {
            $streamwriter->write(json_encode($columns_to_return));
            $first = false;
        }

        // write out each row
        $idx = 0;
        foreach ($result as $r)
        {
            if ($limit_row_count !== false && $idx >= $limit_row_count)
                break;

            if ($first !== true)
                $streamwriter->write(',');

            $row_values = [];
            foreach ($columns_to_return as $c)
            {
                $row_values[] = $r[$c] ?? '';
            }

            $streamwriter->write(json_encode($row_values));
            $idx++;
            $first = false;
        }

        // end the output
        $streamwriter->write(']');

        // set the content type
        $outstream->setMimeType(\Flexio\Base\ContentType::JSON);
    }

    private function getJobParameters() : array
    {
        return $this->properties;
    }

    private static function getSearchParams(string $search_params, array $available_columns, array &$search_columns, string &$search_rows, array &$config) : void
    {
        // EXPERIMENTAL: query params passed in as a json string of array values
        // first parameter: desired return columns or "*" for all columns
        // second parameter: query string/array to limit the results; when in string mode, uses lucene query syntax
        // third parameter; additional configuration (header=true/false, limit=max rows to return)
        // examples:
        //  - ["*", "_exists_:title"]
        //  - ["*", "author:brown", "headers=true&limit=0"]
        //  - [["col1","col2"], ["col1","a"],["col2","b"]]

        // default to all columns/rows
        $search_columns = $available_columns;
        $search_rows = '{"query": {"match_all": {}}}';

        // default configuration
        $config = array();
        $config['headers'] = false;
        $config['limit'] = false;

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
                if ($c !== '*' && $c !== '')
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
            $query_param = $search_params[1];

            // if the search parameter is a string, pass it through as a lucene query string,
            // unless it's empty, in which case, return all items
            if (!isset($query_param))
            {
                // fall through; equivalent to empty; don't do anything
            }
            else if (is_string($query_param))
            {
                if (strlen(trim($query_param)) > 0)
                    $search_rows = json_encode(["query" => ["query_string" => ["query" => $query_param]]], JSON_UNESCAPED_SLASHES);
                     else { /* don't do anything */ }
            }
            else
            {
                $query_parameters = \Flexio\Base\Util::coerceToQueryParams($search_params[1], $available_columns);

                // example:
                // '{"query": {"bool": "must": {"match": {"first_name": "John"}}}}';
                $match_expression = array();
                foreach ($query_parameters as $key => $value)
                {
                    // for now, straight key/value copy

                    if (count($value) == 0)
                        continue;
                    $value = $value[0];

                    $match_expression[] = ['match' => [$key => $value]];
                }

                $match_expression = json_encode($match_expression,JSON_UNESCAPED_SLASHES);
                $search_rows = '{"query": {"bool": {"must": '.$match_expression. '}}}';
            }
        }

        // get the configuration
        if (count($search_params) > 2)
        {
            $query_param = $search_params[2];

            if (is_string($query_param))
            {
                $config = array();
                $config['headers'] = false;
                $config['limit'] = false;

                $config_parameters = array();
                parse_str($query_param, $config_parameters);

                $config['headers'] = false;
                if (isset($config_parameters['headers']) && toBoolean($config_parameters['headers']) === true)
                    $config['headers'] = true;

                $config['limit'] = false;
                if (isset($config_parameters['limit']) && intval($config_parameters['limit']) >= 0)
                $config['limit'] = intval($config_parameters['limit']);
            }
        }
    }
}

