<?php
/**
 *
 * Copyright (c) 2017, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2017-04-17
 *
 * @package flexio
 * @subpackage Object
 */


declare(strict_types=1);
namespace Flexio\Object;


class Action
{
    private $objects;

    public function __construct()
    {
        $this->initialize();
    }

    public static function create(string $requesting_user, array $params) : \Flexio\Object\Action
    {
        return (new static);
    }
}

