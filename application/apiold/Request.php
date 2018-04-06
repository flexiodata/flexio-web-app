<?php
/**
 *
 * Copyright (c) 2017, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2017-11-02
 *
 * @package flexio
 * @subpackage Api
 */


declare(strict_types=1);
namespace Flexio\Api1;


class Request
{
    private $method;
    private $url_params;
    private $query_params;
    private $post_params;
    private $requesting_user;

    public function __construct()
    {
        $this->initialize();
    }

    public static function create() : \Flexio\Api1\Request
    {
        return (new static);
    }

    public function clone() : \Flexio\Api1\Request
    {
        // create a new object and set the properties
        $new_request = static::create();
        $new_request->setMethod($this->getMethod());
        $new_request->setUrlParams($this->getUrlParams());
        $new_request->setQueryParams($this->getQueryParams());
        $new_request->setPostParams($this->getPostParams());
        $new_request->setRequestingUser($this->getRequestingUser());
        return $new_request;
    }

    public function setMethod(string $method)
    {
        $this->method = $method;
    }

    public function getMethod() : string
    {
        return $this->method;
    }

    public function setUrlParams(array $params)
    {
        $this->url_params = $params;
    }

    public function getUrlParams() : array
    {
        return $this->url_params;
    }

    public function setQueryParams(array $params)
    {
        $this->query_params = $params;
    }

    public function getQueryParams() : array
    {
        return $this->query_params;
    }

    public function setPostParams(array $params)
    {
        $this->post_params = $params;
    }

    public function getPostParams() : array
    {
        return $this->post_params;
    }

    public function setRequestingUser(string $param)
    {
        $this->requesting_user = $param;
    }

    public function getRequestingUser() : string
    {
        return $this->requesting_user;
    }

    private function initialize()
    {
        $this->method = '';
        $this->url_params = array();
        $this->query_params = array();
        $this->post_params = array();
        $this->requesting_user = '';
    }
}
