<?php
/**
 *
 * Copyright (c) 2012-2013, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams; Benjamin I. Williams
 * Created:  2013-05-20
 *
 * @package flexio
 * @subpackage Api
 */


declare(strict_types=1);
namespace Flexio\Api;


class Comment
{
    public static function create(array $params, string $requesting_user_eid = null) : array
    {
        $validator = \Flexio\Base\Validator::create();
        if (($params = $validator->check($params, array(
                'parent_eid' => array('type' => 'identifier', 'required' => true),
                'comment'    => array('type' => 'string', 'required' => false)
            ))->getParams()) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $parent_identifier = $params['parent_eid'];

        // load the parent
        $parent = \Flexio\Object\Store::load($parent_identifier);
        if ($parent === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // check the rights on the parent
        if ($parent->allows($requesting_user_eid, \Flexio\Object\Rights::ACTION_WRITE) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // create the comment
        $comment = \Flexio\Object\Comment::create($params);
        $comment->setOwner($requesting_user_eid);
        $comment->setCreatedBy($requesting_user_eid);

        // attach the comment to the parent
        $parent->addComment($comment->getEid());

        // get the comment properties
        return $comment->get();
    }

    public static function delete(array $params, string $requesting_user_eid = null) : bool
    {
        $validator = \Flexio\Base\Validator::create();
        if (($params = $validator->check($params, array(
                'eid' => array('type' => 'identifier', 'required' => true)
            ))->getParams()) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $comment_identifier = $params['eid'];

        // load the object
        $comment = \Flexio\Object\Comment::load($comment_identifier);
        if ($comment === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // check the rights on the object
        if ($comment->allows($requesting_user_eid, \Flexio\Object\Rights::ACTION_DELETE) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // delete the object
        $comment->delete();
        return true;
    }

    public static function set(array $params, string $requesting_user_eid = null) : array
    {
        $validator = \Flexio\Base\Validator::create();
        if (($params = $validator->check($params, array(
                'eid'     => array('type' => 'identifier', 'required' => true),
                'comment' => array('type' => 'string', 'required' => false)
            ))->getParams()) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $comment_identifier = $params['eid'];

        // load the object
        $comment = \Flexio\Object\Comment::load($comment_identifier);
        if ($comment === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // check the rights on the object
        if ($comment->allows($requesting_user_eid, \Flexio\Object\Rights::ACTION_WRITE) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // update the object
        $comment->set($params);
        return $comment->get();
    }

    public static function get(array $params, string $requesting_user_eid = null) : array
    {
        $validator = \Flexio\Base\Validator::create();
        if (($params = $validator->check($params, array(
                'eid' => array('type' => 'identifier', 'required' => true)
            ))->getParams()) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $comment_identifier = $params['eid'];

        // load the object
        $comment = \Flexio\Object\Comment::load($comment_identifier);
        if ($comment === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // check the rights on the object
        if ($comment->allows($requesting_user_eid, \Flexio\Object\Rights::ACTION_READ) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // get the object
        return $comment->get();
    }
}
