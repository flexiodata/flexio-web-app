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
    private $action = false;
    private $action_type = '';
    private $request_type = 'HTTP';
    private $request_ip_address = '';
    private $request_token = '';
    private $request_method = '';
    private $request_url = '';
    private $request_created_by = '';
    private $request_created = null; // no timestamp available by default
    private $request_object_owner_eid = '';
    private $request_object_eid = '';
    private $request_object_type = '';
    private $request_params = array();
    private $response_type = 'HTTP';
    private $response_code = '';
    private $response_created = null; // no timestamp available by default
    private $response_params = array();

    private $url_params = array();
    private $query_params = array();
    private $post_params = array();


    public static function create() : \Flexio\Api2\Request
    {
        return (new static);
    }

    private function getActionParams() : array
    {
        $params = array(
            'action_type'        => $this->action_type,
            'request_type'       => $this->request_type,
            'request_ip'         => $this->request_ip_address,
            'request_token'      => $this->request_token,
            'request_method'     => $this->request_method,
            'request_route'      => $this->request_url,
            'request_created_by' => $this->request_created_by,
            'request_created'    => $this->request_created,
            'target_owned_by'    => $this->request_object_owner_eid,
            'target_eid'         => $this->request_object_eid,
            'target_eid_type'    => $this->request_object_type,
            'request_params'     => $this->request_params,
            'response_type'      => $this->response_type,
            'response_code'      => $this->response_code,
            'response_created'   => $this->response_created,
            'response_params'    => $this->response_params,
            'owned_by'           => $this->request_created_by,
            'created_by'         => $this->request_created_by
        );
        return $params;
    }

    public function createAction(string $action_type) : \Flexio\Object\Action
    {
        $this->action_type = $action_type;
        $params = $this->getActionParams();
        $action = \Flexio\Object\Action::create($params);
        $this->action = $action;
        return $action;
    }

    public function updateAction()
    {
        if ($this->action === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        $action->set($params);
    }

    public function setIpAddress(string $request_ip_address)
    {
        $this->request_ip_address = $request_ip_address;
    }

    public function getIpAddress() : string
    {
        return $this->request_ip_address;
    }

    public function setToken(string $request_token)
    {
        $this->request_token = $request_token;
    }

    public function getToken() : string
    {
        return $this->request_token;
    }

    public function setMethod(string $request_method)
    {
        $this->request_method = $request_method;
    }

    public function getMethod() : string
    {
        return $this->request_method;
    }

    public function setUrl(string $request_url)
    {
        $this->request_url = $request_url;
    }

    public function getUrl() : string
    {
        return $this->request_url;
    }

    public function setRequestingUser(string $request_created_by)
    {
        // TODO: rename function to follow convention
        $this->request_created_by = $request_created_by;
    }

    public function getRequestingUser() : string
    {
        // TODO: rename function to follow convention
        return $this->request_created_by;
    }

    public function setRequestCreated(string $request_created)
    {
        $this->request_created = $request_created;
    }

    public function getRequestCreated() : string
    {
        return $this->request_created;
    }

    public function setOwnerFromUrl(string $request_object_owner_eid)
    {
        // TODO: rename function to follow convention
        $this->request_object_owner_eid = $request_object_owner_eid;
    }

    public function getOwnerFromUrl() : string
    {
        // TODO: rename function to follow convention
        return $this->request_object_owner_eid;
    }

    public function setObjectFromUrl(string $request_object_eid)
    {
        // TODO: rename function to follow convention
        $this->request_object_eid = $request_object_eid;
    }

    public function getObjectFromUrl() : string
    {
        // TODO: rename function to follow convention
        return $this->request_object_eid;
    }

    public function setObjectEidType(string $request_object_eid_type)
    {
        $this->request_object_eid_type = $request_object_eid_type;
    }

    public function getObjectEidType() : string
    {
        $this->request_object_eid_type;
    }

    public function setResponseCode(string $response_code)
    {
        $this->response_code = $response_code;
    }

    public function getResponseCode() : string
    {
        return $this->response_code;
    }

    public function setResponseCreated(string $response_created)
    {
        $this->response_created = $response_created;
    }

    public function getResponseCreated() : string
    {
        return $this->response_created;
    }

    public function setUrlParams(array $url_params)
    {
        $this->url_params = $url_params;
    }

    public function getUrlParams() : array
    {
        return $this->url_params;
    }

    public function setQueryParams(array $query_params)
    {
        $this->query_params = $query_params;
    }

    public function getQueryParams() : array
    {
        return $this->query_params;
    }

    public function setPostParams(array $post_params)
    {
        $this->post_params = $post_params;
    }

    public function getPostParams() : array
    {
        return $this->post_params;
    }
}


