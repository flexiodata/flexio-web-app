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


declare(strict_types=1);
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

    public static function create() : \Flexio\Api\Request
    {
        return (new static);
    }

    public function setValidator(\Flexio\Base\Validator $validator)
    {
        $this->validator = $validator;
        return $this;
    }

    public function getValidator() : \Flexio\Base\Validator
    {
        return $this->validator;
    }

    public function setRequestingUser(string $user) : \Flexio\Api\Request
    {
        $this->requesting_user = $user;
        return $this;
    }

    public function getRequestingUser() : string
    {
        return $this->requesting_user;
    }

    public function setApiVersion(int $version) : \Flexio\Api\Request
    {
        $this->api_version = $version;
        return $this;
    }

    public function getApiVersion() : int
    {
        return $this->api_version;
    }
}
