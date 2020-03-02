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

        // TODO: experimental: parameters passed in as stdin; first parameter is
        // a delimited list of columns we want to return or "*" for all columns;
        // second parameter is a filter string to limit the results
        $instream = $process->getStdin();
        $streamreader = $instream->getReader();

        $data = '';
        while (($chunk = $streamreader->read()) !== false)
            $data .= $chunk;
        $data = @json_decode($data, true);

        $columns_to_return = $available_columns; // default; return all columns
        $rows_requested = false;
        if (is_array($data))
        {
            if (count($data) > 0)
            {
                $columns_to_return = [];
                $columns_requested = $data[0];
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
            }

            if (count($data) > 1)
            {
                $rows_requested = $data[1];
            }
        }

        // connect to elasticsearch
        $elasticsearch_connection_info = \Flexio\System\System::getSearchCacheConfig();
        if ($elasticsearch_connection_info['type'] !== 'elasticsearch')
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE, "Search not available");
        $elasticsearch = \Flexio\Services\ElasticSearch::create($elasticsearch_connection_info);

        // if no query is specified, return all rows
        $query = '{"query": {"match_all": {}}}';
        if ($rows_requested !== false)
        {
            $query_parameters = array();
            parse_str($rows_requested, $query_parameters);

            // example:
            // '{"query": {"bool": "must": {"match": {"first_name": "John"}}}}';
            $match_expression = array();
            foreach ($query_parameters as $key => $value)
            {
                // for now, straight key/value copy
                $match_expression[] = ['match' => [$key => $value]];
            }

            $match_expression = json_encode($match_expression,JSON_UNESCAPED_SLASHES);
            $query = '{"query": {"bool": {"must": '.$match_expression. '}}}';
        }

        // query the index
        $result = $elasticsearch->query($index, $query);

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
}

