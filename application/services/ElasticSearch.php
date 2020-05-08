<?php
/**
 *
 * Copyright (c) 2017, Flex Research LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2017-06-23
 *
 * @package flexio
 * @subpackage Services
 */


declare(strict_types=1);
namespace Flexio\Services;


class ElasticSearch implements \Flexio\IFace\IConnection,
                               \Flexio\IFace\IFileSystem
{
    // number of rows returned in each page while scrolling
    private const READ_PAGE_SIZE = 10000;

    // min/max number of rows that will be sent to to ES to index in one request;
    // actual number is a range between these based on row size
    private const MIN_WRITE_PAGE_SIZE = 100;
    private const MAX_WRITE_PAGE_SIZE = 10000;

    // maximum number of items that will be returned from a search without using scrolling;
    // note: here for reference; defaults to 10k, and setting it higher can result in bad performance
    private const MAX_INDEX_RESULT_WINDOW = 10000;

    // maximum number of rows that can be written in the write loop; primarily to avoid hanging loops
    private const MAX_INDEX_ROW_WRITE_LIMIT = 1000000;

    // connection info
    private $host = '';
    private $port = '';
    private $username = '';
    private $password = '';

    public static function create(array $params = null) : \Flexio\Services\ElasticSearch
    {
        if (isset($params['port']))
            $params['port'] = (string)$params['port'];

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'host'     => array('type' => 'string', 'required' => true),
                'port'     => array('type' => 'string', 'required' => true),
                'username' => array('type' => 'string', 'required' => true),
                'password' => array('type' => 'string', 'required' => true)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $validated_params = $validator->getParams();
        $host = $validated_params['host'];
        $port = intval($validated_params['port']);
        $username = $validated_params['username'];
        $password = $validated_params['password'];

        // AWS elasticsearch services uses IAM for access control; this is a mode
        // which is either on or off.  When on, the requests must be signed with
        // the Signature V4 algorithm; when off, HTTP basic authentication is used
        // HTTP basic authentication is not supported on AWS
        $use_aws_iam = (($params['type'] ?? '') == 'elasticsearch-aws');

        $service = new self;
        $connected = $service->initialize($host, $port, $username, $password, $use_aws_iam);
        if ($connected === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_SERVICE);

        return $service;
    }

    ////////////////////////////////////////////////////////////
    // IConnection interface
    ////////////////////////////////////////////////////////////

    public function connect() : bool
    {
        $use_aws_iam = $this->use_aws_iam;
        $host = $this->host;
        $port = $this->port;
        $username = $this->username;
        $password = $this->password;

        if ($this->initialize($host, $port, $username, $password, $use_aws_iam) === false)
            return false;

        return true;
    }

    public function disconnect() : void
    {
        $this->password = '';
    }

    public function authenticated() : bool
    {
        return $this->testConnection();
    }

    public function get() : array
    {
        $properties = array(
            'host'     => $this->host,
            'port'     => $this->port,
            'username' => $this->username,
            'password' => $this->password
        );

        return $properties;
    }

    ////////////////////////////////////////////////////////////
    // IFileSystem interface
    ////////////////////////////////////////////////////////////

    public function getFlags() : int
    {
        return 0;
    }

    public function list(string $path = '', array $options = []) : array
    {
        // note: right now, the list is flat; path isn't used
        return $this->listIndexes();
    }

    public function getFileInfo(string $path) : array
    {
        // TODO: implement
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
    }

    public function exists(string $path) : bool
    {
        // TODO: implement
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
    }

    public function createFile(string $path, array $properties = []) : bool
    {
        // TODO: implement
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
    }

    public function createDirectory(string $path, array $properties = []) : bool
    {
        // TODO: implement
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
    }

    public function unlink(string $path) : bool
    {
        $index = self::convertToValid($path);
        $this->deleteIndex($index);
        return true;
    }

    public function open($path) : \Flexio\IFace\IStream
    {
        // TODO: implement
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
    }

    public function read(array $params, callable $callback) // TODO: add return type
    {
        if (!isset($params['path']))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);
        if (!array_key_exists('q', $params))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

        $path = $params['path'];
        $query = $params['q'];
        if (!is_string($path))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

        $limit = $params['limit'] ?? PHP_INT_MAX;
        if (!is_integer($limit))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

        // clamp limit
        if ($limit < 0)
            $limit = 0;
        if ($limit > PHP_INT_MAX)
            $limit = PHP_INT_MAX;

        // no results to return
        if ($limit == 0)
            return;

        // elasticsearch limits results returned in a basic search to a
        // maximum result size for performance; if matching results are
        // less than the maximum result size, we can get all of them with
        // a performant query; otherwise, we have to use a scrolling query
        // which is much more expensive; in following logic, to avoid
        // using the more expensive scrolling query if we don't have a known
        // limit, assume queries will return a small result size, which will
        // either give us the result or tell us if we need to do a more
        // expensive query, and do so in both cases quickly

        // initial search
        $result = $this->search($path, $query, $limit);
        $total = $result['hits']['total']['value'] ?? false;
        $total_relation = $result['hits']['total']['relation'] ?? false;

        if ($total_relation === 'eq' && $total <= self::MAX_INDEX_RESULT_WINDOW)
        {
            // total results are available in this query, so return
            // the results and we're done

            $rows = $result['hits']['hits'] ?? false;
            if ($rows === false || count($rows) === 0)
                return;

            foreach ($rows as $r)
            {
                $content = $r['_source'];
                $callback($content);
            }
        }
         else
        {
echo('bad');
die;

            // we have more results than can be returned with the normal query, so
            // redo the query with a scrolling query and return the results

            $idx = 0;
            $scroll_id = null;
            while (true)
            {
                // get the page of results
                $page = $this->searchScroll($path, $query, $scroll_id);
                $rows = $page['hits']['hits'] ?? false;

                // if we don't have any rows, we're done; make sure to cleanup search scroll
                if ($rows === false || count($rows) === 0)
                {
                    $this->deleteSearchScroll($scroll_id);
                    return;
                }

                // get the rows; if the limit is exceeded, we're done
                foreach ($rows as $r)
                {
                    if ($idx >= $limit)
                    {
                        $this->deleteSearchScroll($scroll_id);
                        return;
                    }

                    $content = $r['_source'];
                    $callback($content);
                    $idx++;
                }

                $scroll_id = $page['_scroll_id'];
            }
        }
    }

    public function write(array $params, callable $callback) // TODO: add return type
    {
        if (!isset($params['path']))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);

        // get the index name (path)
        $index = $params['path'];
        $index = self::convertToValid($index);

        // output the rows
        $write_page_size = 0;
        $rows_to_write = array();

        $total_row_count = 0;
        while (true)
        {
            $row = $callback();
            if ($row === false)
                break;

            if ($write_page_size === 0)
                $write_page_size = self::getWritePageSize($row);

            $rows_to_write[] = $row;
            $total_row_count++;

            if ($total_row_count >= self::MAX_INDEX_ROW_WRITE_LIMIT)
                break;

            if (count($rows_to_write) <= $write_page_size)
                continue;

            $this->writeRows($index, $rows_to_write);
            $rows_to_write = array();

            // small delay
            usleep(100000); // sleep 1000ms
        }

        // write out whatever is left over
        if (count($rows_to_write) > 0)
            $this->writeRows($index, $rows_to_write);
    }

    ////////////////////////////////////////////////////////////
    // additional functions
    ////////////////////////////////////////////////////////////

    public function listIndexes() : array
    {
        // see here:
        // https://www.elastic.co/guide/en/elasticsearch/reference/current/indices-stats.html

        $url = $this->getHostUrlString() . '/_stats';
        $request = new \GuzzleHttp\Psr7\Request('GET', $url);
        $response = $this->sendWithCredentials($request);

        $httpcode = $response->getStatusCode();
        $result = (string)$response->getBody();
        if ($httpcode < 200 || $httpcode > 299)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

        $result = json_decode($result, true);

        $indexes = [];

        if (isset($result['indices']))
        {
            $indices = $result['indices'];
            foreach ($indices as $index_name => $index_info)
            {
                // only show indices that aren't hidden
                //if (substr($index_name, 0, 1) === '.')
                //    continue;

                // TODO: include other information from the stats
                $indexes[] = array('name' => $index_name,
                                   'path' => '/' . $index_name,
                                   'size' => null,
                                   'modified' => null,
                                   'hash' => '', // TODO: available?
                                   'type' => 'FILE');
            }
        }

        return $indexes;
    }

    public function deleteIndex(string $index) : void
    {
        try
        {
            $url_query_params = array(
                'ignore_unavailable' => 'true' // don't throw an exception if index doesn't exist
            );
            $url_query_str = http_build_query($url_query_params);

            $url = $this->getHostUrlString() . '/' . urlencode($index) . '?' . $url_query_str;
            $request = new \GuzzleHttp\Psr7\Request('DELETE', $url);
            $response = $this->sendWithCredentials($request);

            $httpcode = $response->getStatusCode();
            $result = (string)$response->getBody();
            if ($httpcode < 200 || $httpcode > 299)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::DELETE_FAILED);

            $result = json_decode($result,true);

            if (!is_array($result))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::DELETE_FAILED);
            if (isset($result['error']) && $result['error'] === true)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);
            if (isset($result['errors']) && $result['errors'] === true)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);
        }
        catch (\Exception $e)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::DELETE_FAILED);
        }
    }

    public function createIndex(string $index, array $structure = null) : void
    {
        // see here for more info about index creation and settings:
        // https://www.elastic.co/guide/en/elasticsearch/reference/current/indices-create-index.html
        // https://www.elastic.co/guide/en/elasticsearch/reference/current/index-modules.html

        // for now, disable mapping; rely instead on default mapping
        // that elasticsearch creates from json
        $mapping_enabled = false;

        try
        {
            // configure index
            $index_configuration = array();
            $index_configuration['settings'] = [
                'max_result_window' => self::MAX_INDEX_RESULT_WINDOW,
                "number_of_shards" => 1, // * see note 1, note 2
                "number_of_replicas" => 1
            ];
            // * note 1: number of shards is important because a cluster limits the amount
            //           of shards overall based on number of nodes, and if this limit is
            //           exceeded, then index creation fails; default is 1000 shards per node
            //           this can also be changed, but it can affect performance; replicas count
            //           towards this limit, but closed indexes do not; for example, with one
            //           node, and one-shard-per-index and one-replica-per-index, we could have
            //           500 open indexes per node; see:
            //           https://www.elastic.co/guide/en/elasticsearch/reference/master/misc-cluster.html
            // * note 2: normal default is 5, but our typical index size is small (recommended target size
            //           of 1 shard per 30GB); see:
            //           https://www.elastic.co/blog/how-many-shards-should-i-have-in-my-elasticsearch-cluster
            //           https://aws.amazon.com/blogs/database/get-started-with-amazon-elasticsearch-service-how-many-shards-do-i-need/

            if ($mapping_enabled)
            {
                $index_mapping_info = self::getMappingFromStructure($structure);
                $index_configuration['mappings'] = $index_mapping_info['mappings'];
            }

            $url = $this->getHostUrlString() . '/' . urlencode($index);
            $request = new \GuzzleHttp\Psr7\Request('PUT', $url, ['Content-Type' => 'application/json'], json_encode($index_configuration));
            $response = $this->sendWithCredentials($request);

            $httpcode = $response->getStatusCode();
            $result = (string)$response->getBody();

            if ($httpcode < 200 || $httpcode > 299)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED);

            $result = json_decode($result,true);

            if (!is_array($result))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED);
            if (isset($result['error']) && $result['error'] === true)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);
            if (isset($result['errors']) && $result['errors'] === true)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);
        }
        catch (\Exception $e)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED);
        }
    }

    public function writeRows(string $index, array $rows, bool $refresh_immediately = false) : void
    {
        // additional optimizations:
        // https://aws.amazon.com/premiumsupport/knowledge-center/elasticsearch-indexing-performance/

        // common api options; see here for more information about filtering results
        // https://www.elastic.co/guide/en/elasticsearch/reference/current/common-options.html

        try
        {
            // create the post buffer for the bulk api endpoint
            $index_write_string = '';
            foreach ($rows as $r)
            {
                $index_write_string .= '{"index": {"_index": "' . $index . '"}}';
                $index_write_string .= "\n";
                $index_write_string .= json_encode($r, 0); // encode json without returns (each row must be on one line)
                $index_write_string .= "\n";
            }
            $index_write_string .= "\n"; // payload must end with newline

            // write the content; note: set the filter path to pull back basic header info;
            // a lot of info is returned back for each of the bulk request row items and this
            // can add up to signficant network traffic when inserting 100ks rows; per AWS
            // recommendation
            $url_query_params = array(
                'refresh' => $refresh_immediately ? 'true' : 'false',
                'filter_path' => 'took,errors'
            );
            $url_query_str = http_build_query($url_query_params);

            $url = $this->getHostUrlString() . '/_bulk?' . $url_query_str;
            $request = new \GuzzleHttp\Psr7\Request('POST', $url, ['Content-Type' => 'application/x-ndjson'], $index_write_string); // use ndjson for bulk operations
            $response = $this->sendWithCredentials($request);

            $httpcode = $response->getStatusCode();
            $result = (string)$response->getBody();

            if ($httpcode < 200 || $httpcode > 299)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);

            $result = json_decode($result,true);

            if (!is_array($result))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);
            if (isset($result['error']) && $result['error'] === true)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);
            if (isset($result['errors']) && $result['errors'] === true)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);
        }
        catch (\Exception $e)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);
        }
    }

    public function search(string $index, array $search_query = null, int $limit = self::MAX_INDEX_RESULT_WINDOW) : array
    {
        // perform a regular query without scrolling; used for search requests
        // less than the max result window

        // clamp limit to max result window size
        if ($limit > self::MAX_INDEX_RESULT_WINDOW)
            $limit = self::MAX_INDEX_RESULT_WINDOW;

        try
        {
            $request = false;

            // if the search query is null, return all rows
            if (!isset($search_query))
                $search_query = array('query' => array('match_all' => new \stdClass()));

            // perform regular search query
            $url_query_params = array();
            $url_query_params['from'] = 0;
            $url_query_params['size'] = $limit;
            $url_query_params['filter_path'] = '_scroll_id,took,timed_out,hits.total,hits.hits._source'; // default response includes a lot of metadata; only get what we need to save network traffic
            $url_query_str = http_build_query($url_query_params);
            $url = $this->getHostUrlString() . '/' . urlencode($index) . '/_search?' . $url_query_str;
            $search_str = json_encode($search_query);
            $request = new \GuzzleHttp\Psr7\Request('POST', $url, ['Content-Type' => 'application/json'], $search_str);

            if ($request === false)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

            // send the request
            $response = $this->sendWithCredentials($request);

            $httpcode = $response->getStatusCode();
            $result = (string)$response->getBody();

            if ($httpcode < 200 || $httpcode > 299)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

            $result = json_decode($result,true);

            if (!is_array($result))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);
            if (isset($result['error']) && $result['error'] === true)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);
            if (isset($result['errors']) && $result['errors'] === true)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

            return $result;
        }
        catch (\Exception $e)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);
        }
    }

    public function searchScroll(string $index, array $search_query = null, string $scroll_id = null) : array
    {
        try
        {
            $request = false;

            // if the search query is null, return all rows
            if (!isset($search_query))
                $search_query = array('query' => array('match_all' => new \stdClass()));

            // if we don't have a scroll_id, it's the start of the scrolling
            // if we have a scroll_id, it's part of an ongoing query

            // see here for more info about scrolling:
            // https://www.elastic.co/guide/en/elasticsearch/reference/current/search-request-body.html#request-body-search-scroll

            if (!isset($scroll_id))
            {
                // perform initial query for a scroll query; scrolling will be finished
                // when there are no hit results

                // with scrolling, use doc sort order for speed per note in documentation
                $search_query['sort'] = ['_doc'];
                $url_query_params = array();
                $url_query_params['from'] = 0;
                $url_query_params['size'] = self::READ_PAGE_SIZE; // here, size is the paging size; subsequent scrolling calls will returns additional results and overall limit is controlled by page calls
                $url_query_params['scroll'] = '1m'; // set timeout to 1 minute
                $url_query_params['filter_path'] = '_scroll_id,took,hits.hits._source'; // default response includes a lot of metadata; only get what we need to save network traffic
                $url_query_str = http_build_query($url_query_params);
                $url = $this->getHostUrlString() . '/' . urlencode($index) . '/_search?' . $url_query_str;
                $search_str = json_encode($search_query);
                $request = new \GuzzleHttp\Psr7\Request('POST', $url, ['Content-Type' => 'application/json'], $search_str);
            }
             else
            {
                // perform additional query for scroll query; scrolling will be finished
                // when there are no hit results

                $search_query = array(
                    'scroll_id' => $scroll_id
                );

                $url_query_params = array();
                $url_query_params['scroll'] = '1m'; // use scrolling since we want to return all results
                $url_query_params['filter_path'] = '_scroll_id,took,hits.hits._source'; // default response includes a lot of metadata; only get what we need to save network traffic
                $url_query_str = http_build_query($url_query_params);
                $url = $this->getHostUrlString() . '/_search/scroll?' . $url_query_str;
                $request = new \GuzzleHttp\Psr7\Request('POST', $url, ['Content-Type' => 'application/json'], json_encode($search_query));
            }

            if ($request === false)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

            // send the request
            $response = $this->sendWithCredentials($request);

            $httpcode = $response->getStatusCode();
            $result = (string)$response->getBody();

            if ($httpcode < 200 || $httpcode > 299)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

            $result = json_decode($result,true);

            if (!is_array($result))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);
            if (isset($result['error']) && $result['error'] === true)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);
            if (isset($result['errors']) && $result['errors'] === true)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

            return $result;
        }
        catch (\Exception $e)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);
        }
    }

    public function deleteSearchScroll(string $scroll_id = null)
    {
        if (!isset($scroll_id) || strlen($scroll_id) === 0)
            return;

        try
        {
            $url = $this->getHostUrlString() . '/_search/scroll/' . urlencode($scroll_id);
            $request = new \GuzzleHttp\Psr7\Request('DELETE', $url);
            $response = $this->sendWithCredentials($request);
        }
        catch (\Exception $e)
        {
            // don't fail; search scroll will automatically delete
        }
    }

    public function getClusterStats() : array
    {
        // additional information is available about hte cluster here; TODO return?
        // cluster shard limit: https://www.elastic.co/guide/en/elasticsearch/reference/master/misc-cluster.html
        // GET /_cluster/settings?include_defaults=true

        try
        {
            $url = $this->getHostUrlString() . '/_cluster/stats';
            $request = new \GuzzleHttp\Psr7\Request('GET', $url);
            $response = $this->sendWithCredentials($request);

            $httpcode = $response->getStatusCode();
            $result = (string)$response->getBody();
            if ($httpcode < 200 || $httpcode > 299)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_DATABASE);

            $result = json_decode($result,true);
            return $result;
        }
        catch (\Exception $e)
        {
            return array();
        }
    }

    public function getIndicesStats() : array
    {
        // see here:
        // https://www.elastic.co/guide/en/elasticsearch/reference/current/indices-stats.html

        // also available:
        // https://www.elastic.co/guide/en/elasticsearch/reference/current/cat-indices.html
        // GET /_cat/indices?format=json
        // GET /_cat/shards

        try
        {
            $url = $this->getHostUrlString() . '/_stats';
            $request = new \GuzzleHttp\Psr7\Request('GET', $url);
            $response = $this->sendWithCredentials($request);

            $httpcode = $response->getStatusCode();
            $result = (string)$response->getBody();
            if ($httpcode < 200 || $httpcode > 299)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_DATABASE);

            $result = json_decode($result,true);
            return $result;
        }
        catch (\Exception $e)
        {
            return array();
        }
    }

    public function info() : array
    {
        // TODO: parallels testConnection function; factor?
        try
        {
            $url = $this->getHostUrlString();
            $request = new \GuzzleHttp\Psr7\Request('GET', $url);
            $response = $this->sendWithCredentials($request);

            $httpcode = $response->getStatusCode();
            $result = (string)$response->getBody();
            if ($httpcode < 200 || $httpcode > 299)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_DATABASE);

            $result = json_decode($result,true);

            if (!is_array($result))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_DATABASE);
            if (!isset($result['version']))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_DATABASE);

            $version = $result['version'];
            if (!isset($version['number']))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_DATABASE);

            return $version;
        }
        catch (\Exception $e)
        {
            return array();
        }
    }

    private function initialize(string $host, int $port, string $username, string $password, bool $use_aws_iam = false) : bool
    {
        $this->use_aws_iam = $use_aws_iam;
        $this->host = $host;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;
        return true;
    }

    private function testConnection() : bool
    {
        try
        {
            $url = $this->getHostUrlString();
            $request = new \GuzzleHttp\Psr7\Request('GET', $url);
            $response = $this->sendWithCredentials($request);

            $httpcode = $response->getStatusCode();
            $result = (string)$response->getBody();
            if ($httpcode < 200 || $httpcode > 299)
                return false;

            $result = json_decode($result,true);

            if (!is_array($result))
                return false;
            if (!isset($result['version']))
                return false;

            $version = $result['version'];
            if (!isset($version['number']))
                return false;

            // TODO: do we want/need to require a minimum version?
            return true;
        }
        catch (\Exception $e)
        {
            return false;
        }
    }

    private function getHostUrlString() : string
    {
        if ((string)$this->port == "80")
        {
            return 'http://' . $this->host;
        }
        else if (($this->use_aws_iam ?? false) || (string)$this->port == "443")
        {
            return 'https://' . $this->host;
        }
        else
        {
            return 'http://' . $this->host . ':' . (string)$this->port;
        }
    }

    private function getBasicAuthString() : string
    {
        return base64_encode($this->username . ':' . $this->password);
    }

    private static function getMappingFromStructure(array $structure = null) : array
    {
        // see here for info indexes, types, and mappings:
        // https://www.elastic.co/guide/en/elasticsearch/reference/current/mapping.html

        // example:
        /*
        PUT <index>
        {
            "mappings": {
                "properties": {
                    "<field1>": {
                        "type": text|integer|double|date|boolean
                        <extra info>
                    },
                    "<field2>": {
                        "type": text|integer|double|date|boolean
                        <extra info>
                    }
                }
            }
        }
        */

        if (!isset($structure))
            return array();

        // create a mapping from the structure
        $properties = array();
        foreach ($structure as $field)
        {
            $fieldname = $field['name'];
            $fieldtype = $field['type'];
            $properties[$fieldname] = self::getMappingInfoFromStructureType($fieldtype);
        }

        $index_mapping_info = array();
        //$index_mapping_info['mappings']['_all'] = array('enabled' => false);
        $index_mapping_info['mappings']['properties'] = $properties;

        return $index_mapping_info;
    }

    private static function getMappingInfoFromStructureType(string $structure_type) : array
    {
        switch ($structure_type)
        {
            default:
            case \Flexio\Base\Structure::TYPE_STRING:
            case \Flexio\Base\Structure::TYPE_TEXT:
            case \Flexio\Base\Structure::TYPE_CHARACTER:
            case \Flexio\Base\Structure::TYPE_WIDECHARACTER:
                $info = array(
                    'type' => 'text',
                    'index' => true,
                    'store' => false
                );
                return $info;

            case \Flexio\Base\Structure::TYPE_NUMBER:
            case \Flexio\Base\Structure::TYPE_NUMERIC:
            case \Flexio\Base\Structure::TYPE_DOUBLE:
                $info = array(
                    'type' => 'double',
                    'coerce' => true,
                    'doc_values' => true,
                    'ignore_malformed' => false,
                    'index' => true,
                    'store' => false
                );
                return $info;

            case \Flexio\Base\Structure::TYPE_INTEGER:
                $info = array(
                    'type' => 'integer',
                    'coerce' => true,
                    'doc_values' => true,
                    'ignore_malformed' => false,
                    'index' => true,
                    'store' => false
                );
                return $info;

            case \Flexio\Base\Structure::TYPE_DATE:
            case \Flexio\Base\Structure::TYPE_DATETIME:
                $info = array(
                    'type' => 'date',
                    'doc_values' => true,
                    'format' => 'strict_date_optional_time||epoch_millis',
                    'ignore_malformed' => false,
                    'index' => true,
                    'store' => false
                );
                return $info;

            case \Flexio\Base\Structure::TYPE_BOOLEAN:
                $info = array(
                    'type' => 'boolean',
                    'doc_values' => true,
                    'index' => true,
                    'store' => false
                );
                return $info;
        }
    }

    private static function convertToValid(string $name) : string
    {
        $name = ltrim($name,'/');
        return strtolower($name);
    }


    private function sendWithCredentials(\GuzzleHttp\Psr7\Request $request)
    {
        $client = new \GuzzleHttp\Client();
        $options = [];

        if (($this->use_aws_iam ?? false))
        {
            $credentials = new \Aws\Credentials\Credentials($this->username, $this->password);
            $signer = new \Aws\Signature\SignatureV4('es', 'us-east-1');
            $request = $signer->signRequest($request, $credentials);
        }
        else
        {
            $options = [ 'auth' => [ $this->username, $this->password ] ];
        }

        return $client->send($request, $options);
    }

    private static function getWritePageSize(array $row) : int
    {
        // calculate how many records to write at for approximate
        // 5MB payload using row record as rough estimate
        $sample = json_encode($row,0);
        if ($sample === false)
            return self::MAX_WRITE_PAGE_SIZE;

        $sample_size = strlen($sample);
        if ($sample_size === 0)
            return self::MAX_WRITE_PAGE_SIZE;

        $write_page_size = (int)(5000000/$sample_size);

        // clamp the number with a range
        if ($write_page_size > self::MAX_WRITE_PAGE_SIZE)
            $write_page_size = self::MAX_WRITE_PAGE_SIZE;
        if ($write_page_size <= self::MIN_WRITE_PAGE_SIZE)
            $write_page_size = self::MIN_WRITE_PAGE_SIZE;

        return $write_page_size;
    }
}
