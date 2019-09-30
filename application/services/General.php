<?php
/**
 *
 * Copyright (c) 2019, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams
 * Created:  2019-09-30
 *
 * @package flexio
 * @subpackage Services
 */


declare(strict_types=1);
namespace Flexio\Services;


class General implements \Flexio\IFace\IConnection
{
    // note: general purpose service for storing arbitrary parameters

    private $params = array();

    public static function create(array $params = null) : \Flexio\Services\Firebase
    {
        $service = new self;
        $service->params = $params;
        return $service;
    }

    ////////////////////////////////////////////////////////////
    // IConnection interface
    ////////////////////////////////////////////////////////////

    public function connect() : bool
    {
        return true;
    }

    public function disconnect() : void
    {
        // reset the params
        $this->params = array();
    }

    public function authenticated() : bool
    {
        return true;
    }

    public function get() : array
    {
        return $this->params;
    }

    ////////////////////////////////////////////////////////////
    // additional functions
    ////////////////////////////////////////////////////////////

    // TODO: add here
}
