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
            $query_parameters = \Flexio\Base\Util::coerceToQueryParams($search_params[1], $available_columns);

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
}

