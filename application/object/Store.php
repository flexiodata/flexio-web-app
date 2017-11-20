<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-11-16
 *
 * @package flexio
 * @subpackage Object
 */


declare(strict_types=1);
namespace Flexio\Object;


class Store
{
    public static function load(string $identifier, string $eid_type = null)
    {
        if (!isset($eid_type))
            $eid_type = self::getModel()->getTypeByIdentifier($identifier);

        switch ($eid_type)
        {
            default:
                return false; // unknown eid type

            case \Model::TYPE_OBJECT:
                return \Flexio\Object\Object::load($identifier);

            case \Model::TYPE_COMMENT:
                return \Flexio\Object\Comment::load($identifier);

            case \Model::TYPE_CONNECTION:
                return \Flexio\Object\Connection::load($identifier);

            case \Model::TYPE_PIPE:
                return \Flexio\Object\Pipe::load($identifier);

            case \Model::TYPE_PROCESS:
                return \Flexio\Object\Process::load($identifier);

            case \Model::TYPE_PROJECT:
                return \Flexio\Object\Project::load($identifier);

            case \Model::TYPE_STREAM:
                return \Flexio\Object\Stream::load($identifier);

            case \Model::TYPE_USER:
                return \Flexio\Object\User::load($identifier);

            case \Model::TYPE_TOKEN:
                return \Flexio\Object\Token::load($identifier);
        }
    }

    public static function getModel() : \Model
    {
        return \Flexio\System\System::getModel();
    }
}
