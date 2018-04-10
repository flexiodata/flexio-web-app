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
namespace Flexio\Api;


class Request
{
    private $action = false;
    private $action_type = \Flexio\Api\Action::TYPE_UNDEFINED;
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
    private $response_code = '200';
    private $response_created = null; // no timestamp available by default
    private $response_params = array();

    private $header_params = array();
    private $url_params = array();
    private $query_params = array();
    private $post_params = array();


    public static function create() : \Flexio\Api\Request
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

    public function track(string $action_type = null) : \Flexio\Object\Action
    {
        // if the action has already been set, change the state
        if ($this->action !== false)
        {
            // note: don't reset the action type if an action_type is specified
            // but an action has already been created
            $params = $this->getActionParams();
            $this->action->set($params);
            return $this->action;
        }

        if (!isset($action_type))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        // the action hasn't been set yet; create a new action
        $this->action_type = $action_type;
        $params = $this->getActionParams();
        $action = \Flexio\Object\Action::create($params);
        $this->action = $action;
        return $action;
    }

    public function getAction()
    {
        return $this->action;
    }

    public function getActionType() : string
    {
        return $this->action_type;
    }

    public function setIpAddress(string $request_ip_address) : \Flexio\Api\Request
    {
        $this->request_ip_address = $request_ip_address;
        return $this;
    }

    public function getIpAddress() : string
    {
        return $this->request_ip_address;
    }

    public function setToken(string $request_token) : \Flexio\Api\Request
    {
        $this->request_token = $request_token;
        return $this;
    }

    public function getToken() : string
    {
        return $this->request_token;
    }

    public function setMethod(string $request_method) : \Flexio\Api\Request
    {
        $this->request_method = $request_method;
        return $this;
    }

    public function getMethod() : string
    {
        return $this->request_method;
    }

    public function setUrl(string $request_url) : \Flexio\Api\Request
    {
        $this->request_url = $request_url;
        return $this;
    }

    public function getUrl() : string
    {
        return $this->request_url;
    }

    public function setRequestingUser(string $request_created_by) : \Flexio\Api\Request
    {
        // TODO: rename function to follow convention
        $this->request_created_by = $request_created_by;
        return $this;
    }

    public function getRequestingUser() : string
    {
        // TODO: rename function to follow convention
        return $this->request_created_by;
    }

    public function setRequestCreated(string $request_created) : \Flexio\Api\Request
    {
        $this->request_created = $request_created;
        return $this;
    }

    public function getRequestCreated() : string
    {
        return $this->request_created;
    }

    public function setRequestParams(array $params) : \Flexio\Api\Request
    {
        // note: the request params may be all or a sanitized subset of the
        // parameters sent with the request (e.g., parameters such as credential
        // info may not be included)
        $this->request_params = $params;
        return $this;
    }

    public function getRequestParams() : array
    {
        return $this->request_params;
    }

    public function setOwnerFromUrl(string $request_object_owner_eid) : \Flexio\Api\Request
    {
        // TODO: rename function to follow convention
        $this->request_object_owner_eid = $request_object_owner_eid;
        return $this;
    }

    public function getOwnerFromUrl() : string
    {
        // TODO: rename function to follow convention
        return $this->request_object_owner_eid;
    }

    public function setObjectFromUrl(string $request_object_eid) : \Flexio\Api\Request
    {
        // TODO: rename function to follow convention
        $this->request_object_eid = $request_object_eid;
        return $this;
    }

    public function getObjectFromUrl() : string
    {
        // TODO: rename function to follow convention
        return $this->request_object_eid;
    }

    public function setObjectEidType(string $request_object_eid_type) : \Flexio\Api\Request
    {
        $this->request_object_eid_type = $request_object_eid_type;
        return $this;
    }

    public function getObjectEidType() : string
    {
        return $this->request_object_eid_type;
    }

    public function setResponseCode(string $response_code) : \Flexio\Api\Request
    {
        $this->response_code = $response_code;
        return $this;
    }

    public function getResponseCode() : string
    {
        return $this->response_code;
    }

    public function setResponseCreated(string $response_created) : \Flexio\Api\Request
    {
        $this->response_created = $response_created;
        return $this;
    }

    public function getResponseCreated() : string
    {
        return $this->response_created;
    }

    public function setResponseParams(array $response_params) : \Flexio\Api\Request
    {
        $this->response_params = $response_params;
        return $this;
    }

    public function getResponseParams() : array
    {
        return $this->response_params;
    }

    public function setHeaderParams(array $header_params) : \Flexio\Api\Request
    {
        $this->header_params = $header_params;
        return $this;
    }

    public function getHeaderParams() : array
    {
        return $this->header_params;
    }

    public function setUrlParams(array $url_params) : \Flexio\Api\Request
    {
        $this->url_params = $url_params;
        return $this;
    }

    public function getUrlParams() : array
    {
        return $this->url_params;
    }

    public function setQueryParams(array $query_params) : \Flexio\Api\Request
    {
        $this->query_params = $query_params;
        return $this;
    }

    public function getQueryParams() : array
    {
        return $this->query_params;
    }

    public function setPostParams(array $post_params) : \Flexio\Api\Request
    {
        $this->post_params = $post_params;
        return $this;
    }

    public function getPostParams() : array
    {
        return $this->post_params;
    }
}


