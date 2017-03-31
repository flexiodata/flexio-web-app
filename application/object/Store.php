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
    public static function create(string $eid_type, array $properties = null) : \Flexio\Object\Base
    {
        switch ($eid_type)
        {
            default:
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED);

            case \Model::TYPE_OBJECT:
                return \Flexio\Object\Object::create($properties);

            case \Model::TYPE_COMMENT:
                return \Flexio\Object\Comment::create($properties);

            case \Model::TYPE_CONNECTION:
                return \Flexio\Object\Connection::create($properties);

            case \Model::TYPE_PIPE:
                return \Flexio\Object\Pipe::create($properties);

            case \Model::TYPE_PROCESS:
                return \Flexio\Object\Process::create($properties);

            case \Model::TYPE_PROJECT:
                return \Flexio\Object\Project::create($properties);

            case \Model::TYPE_STREAM:
                return \Flexio\Object\Stream::create($properties);

            case \Model::TYPE_USER:
                return \Flexio\Object\User::create($properties);

            case \Model::TYPE_TOKEN:
                return \Flexio\Object\Token::create($properties);
        }
    }

    public static function load(string $identifier)
    {
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

    public static function getModel()
    {
        return \Flexio\System\System::getModel();
    }
}
