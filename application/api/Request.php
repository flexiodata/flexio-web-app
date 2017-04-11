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
    private $requesting_user;
    public function __construct()
    {
        $this->requesting_user = \Flexio\Object\User::USER_PUBLIC;
    }

    public static function create() : \Flexio\Api\Request
    {
        return (new static);
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
}
