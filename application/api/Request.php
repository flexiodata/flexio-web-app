<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-05-09
 *
 * @package flexio
 * @subpackage Api
 */


namespace Flexio\Api;


class Request
{
    private $validator;
    private $requesting_user;
    private $api_version;

    public function __construct()
    {
        $this->validator = \Flexio\Base\Validator::getInstance();
        $this->requesting_user = \Flexio\Object\User::USER_PUBLIC;
        $this->api_version = 1;
    }

    public static function create()
    {
        return (new static);
    }

    public function setValidator($validator)
    {
        $this->validator = $validator;
        return $this;
    }

    public function getValidator()
    {
        return $this->validator;
    }

    public function setRequestingUser($user)
    {
        $this->requesting_user = $user;
        return $this;
    }

    public function getRequestingUser()
    {
        return $this->requesting_user;
    }

    public function setApiVersion($version)
    {
        $this->api_version = $version;
        return $this;
    }

    public function getApiVersion()
    {
        return $this->api_version;
    }
}
