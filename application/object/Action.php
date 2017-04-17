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

    public static function create() : \Flexio\Object\Action
    {
        return (new static);
    }

    public static function record(string $name, string $user_eid, array $params) : bool
    {
        $action_params = array(
            'name' => $name,
            'user_eid' => $user_eid,
            'params' => json_encode($params)
        );
        \Flexio\System\System::getModel()->action->record($action_params);
    }
}
