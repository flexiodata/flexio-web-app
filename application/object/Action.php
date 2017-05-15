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
    const TYPE_UNDEFINED        = '';                        // undefined
    const TYPE_READ             = 'read';             // ability to read the properties of an object
    const TYPE_WRITE            = 'write';            // ability to write the properties of an object
    const TYPE_DELETE           = 'delete';           // ability to delete an object
    const TYPE_EXECUTE          = 'execute';          // ability to run a process object
    const TYPE_READ_RIGHTS      = 'read.rights';      // ability to see rights
    const TYPE_WRITE_RIGHTS     = 'write.rights';     // ability to change rights


    private $objects;

    public function __construct()
    {
        $this->initialize();
    }

    public static function isValidType(string $action) : bool
    {
        switch ($action)
        {
            default:
            case self::TYPE_UNDEFINED:
                return false;

            case self::TYPE_READ:
            case self::TYPE_WRITE:
            case self::TYPE_DELETE:
            case self::TYPE_EXECUTE:
            case self::TYPE_READ_RIGHTS:
            case self::TYPE_WRITE_RIGHTS:
                return true;
        }
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
