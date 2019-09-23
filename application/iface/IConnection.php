<?php
/**
 *
 * Copyright (c) 2018, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2018-02-23
 *
 * @package flexio
 * @subpackage IFace
 */


declare(strict_types=1);
namespace Flexio\IFace;


interface IConnection
{
    public function connect() : bool;
    public function disconnect() : void;
    public function authenticated() : bool;
    public function get() : array; // returns list of connection properties; general name matches pattern for other objects
}

