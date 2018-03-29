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
namespace Flexio\Api2;


class Request
{
    private $method;
    private $url;
    private $url_params;
    private $query_params;
    private $post_params;
    private $requesting_user;
    private $url_owner_eid;
    private $url_object_eid;
    private $action;

    public function __construct()
    {
        $this->initialize();
    }

    public static function create() : \Flexio\Api2\Request
    {
        return (new static);
    }

    public function setMethod(string $method)
    {
        $this->method = $method;
    }

    public function getMethod() : string
    {
        return $this->method;
    }

    public function setUrl(string $url)
    {
        $this->url = $url;
    }

    public function getUrl(string $url) : string
    {
        return $this->url;
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

    public function setOwnerFromUrl(string $owner_eid)
    {
        $this->url_owner_eid = $owner_eid;
    }

    public function getOwnerFromUrl() : string
    {
        return $this->url_owner_eid;
    }

    public function setObjectFromUrl(string $object_eid)
    {
        $this->url_object_eid = $object_eid;
    }

    public function getObjectFromUrl() : string
    {
        return $this->url_object_eid;
    }

    public function setAction(\Flexio\Object\Action $action)
    {
        $this->action = $action;
    }

    public function getAction()
    {
        return $this->action;
    }

    private function initialize()
    {
        $this->method = '';
        $this->url = '';
        $this->url_params = array();
        $this->query_params = array();
        $this->post_params = array();
        $this->requesting_user = '';
        $this->url_owner_eid = '';
        $this->url_object_eid = '';
        $this->action = false;
    }
}
