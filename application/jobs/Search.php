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
        'index'      => array('required' => true,  'type' => 'string'),
        'query'      => array('required' => true,  'type' => 'string'),
        'columns'    => array('required' => false,  'type' => 'string')
    ))->hasErrors()) === true)
    throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);
*/

class Search extends \Flexio\Jobs\Base
{
    public function run(\Flexio\IFace\IProcess $process) : void
    {
        $job_params = $this->getJobParameters();
        $index = $job_params['index'] ?? null;
        $query = $job_params['query'] ?? null;

        if (is_null($index))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX, "Missing parameter 'index'");
        if (is_null($query))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX, "Missing parameter 'query'");

        // connect to elasticsearch
        $elasticsearch_connection_info = array(
            'host'     => $GLOBALS['g_config']->experimental_cache_host ?? '',
            'port'     => $GLOBALS['g_config']->experimental_cache_port ?? '',
            'username' => $GLOBALS['g_config']->experimental_cache_username ?? '',
            'password' => $GLOBALS['g_config']->experimental_cache_password ?? ''
        );
        $elasticsearch = \Flexio\Services\ElasticSearch::create($elasticsearch_connection_info);

        // TODO: for now, return all results
        $query = <<<EOT
{
    "query": {
        "match_all": {}
    }
}
EOT;

        // query the index
        $query = json_decode($query, true);
        $result = $elasticsearch->query($index, $query);

        // write the output of the search query to stdout
        $outstream = $process->getStdout();
        $streamwriter = $outstream->getWriter();

        // start the output
        $streamwriter->write("[");

        // TODO: write out column info and select columns if those options are set

        // write out each row
        $first = true;
        foreach ($result as $r)
        {
            $row_values = array_values($r);

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

