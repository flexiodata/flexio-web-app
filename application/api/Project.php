<?php
/**
 *
 * Copyright (c) 2013, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2013-10-30
 *
 * @package flexio
 * @subpackage Api
 */


declare(strict_types=1);
namespace Flexio\Api;


class Project
{
    public static function create($params, $request)
    {
        if (($params = $request->getValidator()->check($params, array(
                'eid_status'   => array('type' => 'string', 'required' => false),
                'name'         => array('type' => 'string', 'required' => false),
                'description'  => array('type' => 'string', 'required' => false)
            ))) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $requesting_user_eid = $request->getRequestingUser();

        // right now, only allow a project to be created by an actual user since
        // we have no other way of knowing who the intended owner is (until we allow
        // projects to be created from a user api endpoint)
        $user = \Flexio\Object\User::load($requesting_user_eid);
        if ($user === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED);

        // create the object
        $project = \Flexio\Object\Project::create($params);
        if ($project === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED);

        // set the owner
        $project->setOwner($requesting_user_eid);
        $project->setCreatedBy($requesting_user_eid);

        // return the project
        return $project->get();
    }

    public static function delete($params, $request)
    {
        if (($params = $request->getValidator()->check($params, array(
                'eid' => array('type' => 'identifier', 'required' => true)
            ))) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $project_identifier = $params['eid'];
        $requesting_user_eid = $request->getRequestingUser();

        // load the object
        $project = \Flexio\Object\Project::load($project_identifier);
        if ($project === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // check the rights on the object
        if ($project->allows($requesting_user_eid, \Flexio\Object\Rights::ACTION_DELETE) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        $project->delete();
        return true;
    }

    public static function set($params, $request)
    {
        if (($params = $request->getValidator()->check($params, array(
                'eid'          => array('type' => 'identifier', 'required' => true),
                'eid_status'   => array('type' => 'string', 'required' => false),
                'name'         => array('type' => 'string', 'required' => false),
                'description'  => array('type' => 'string', 'required' => false)
            ))) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $project_identifier = $params['eid'];
        $requesting_user_eid = $request->getRequestingUser();

        // load the object
        $project = \Flexio\Object\Project::load($project_identifier);
        if ($project === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // check the rights on the object
        if ($project->allows($requesting_user_eid, \Flexio\Object\Rights::ACTION_WRITE) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // set the project params
        $project->set($params);
        return $project->get();
    }

    public static function get($params, $request)
    {
        if (($params = $request->getValidator()->check($params, array(
                'eid' => array('type' => 'identifier', 'required' => true)
            ))) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $project_identifier = $params['eid'];
        $requesting_user_eid = $request->getRequestingUser();

        // load the object
        $project = \Flexio\Object\Project::load($project_identifier);
        if ($project === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // check the rights on the object
        if ($project->allows($requesting_user_eid, \Flexio\Object\Rights::ACTION_READ) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // fill out the properties with additional info
        $properties = $project->get();
        if ($properties === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

        return $properties;
    }

    public static function listall($params, $request)
    {
        // get the projects for the requesting user
        $requesting_user_eid = $request->getRequestingUser();

        // load the object
        $user = \Flexio\Object\User::load($requesting_user_eid);
        if ($user === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // check the rights on the object
        if ($user->allows($requesting_user_eid, \Flexio\Object\Rights::ACTION_READ) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // get the projects
        $result = array();
        $projects = $user->getProjects();
        foreach ($projects as $p)
        {
            if ($p->allows($requesting_user_eid, \Flexio\Object\Rights::ACTION_READ) === false)
                continue;

            $result[] = $p->get();
        }

        return $result;
    }

    public static function pipes($params, $request)
    {
        // eid is the project
        if (($params = $request->getValidator()->check($params, array(
                'eid' => array('type' => 'identifier', 'required' => true),
                'pipe_eid' => array('type' => 'identifier', 'array' => true, 'required' => false)
            ))) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $project_identifier = $params['eid'];
        $filter_list = $params['pipe_eid'] ?? false;
        $requesting_user_eid = $request->getRequestingUser();

        // load the project
        $project = \Flexio\Object\Project::load($project_identifier);
        if ($project === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // check rights for the project
        if ($project->allows($requesting_user_eid, \Flexio\Object\Rights::ACTION_READ) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        return self::getMembersByType($project, \Model::TYPE_PIPE, $filter_list);
    }

    public static function connections($params, $request)
    {
        // eid is the project
        if (($params = $request->getValidator()->check($params, array(
                'eid' => array('type' => 'identifier', 'required' => true),
                'connection_eid' => array('type' => 'identifier', 'array' => true, 'required' => false)
            ))) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $project_identifier = $params['eid'];
        $filter_list = $params['connection_eid'] ?? false;
        $requesting_user_eid = $request->getRequestingUser();

        // load the project
        $project = \Flexio\Object\Project::load($project_identifier);
        if ($project === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // check rights for the project
        if ($project->allows($requesting_user_eid, \Flexio\Object\Rights::ACTION_READ) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        return self::getMembersByType($project, \Model::TYPE_CONNECTION, $filter_list);
    }

    public static function trashed($params, $request)
    {
        // eid is the parent container
        if (($params = $request->getValidator()->check($params, array(
                'eid' => array('type' => 'identifier', 'required' => true)
            ))) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        // see if the user has rights to this project
        $project_identifier = $params['eid'];
        $requesting_user_eid = $request->getRequestingUser();

        // load the object
        $project = \Flexio\Object\Project::load($project_identifier);
        if ($project === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // check the rights on the object
        if ($project->allows($requesting_user_eid, \Flexio\Object\Rights::ACTION_READ) === false)
           throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // get the trashed items
        $result = array();
        $objects = $project->getMembers();

        foreach ($objects as $o)
        {
            // only allow pipes in the list for now
            $object_type = $o->getType();
            if ($object_type !== \Model::TYPE_PIPE)
                continue;

            // only show items in the trash
            if ($o->getStatus() !== \Model::STATUS_TRASH)
                continue;

            $result[] = $o->get();

        }

        return $result;
    }

    public static function addTrash($params, $request)
    {
        // eid is the project; currently, not checked
        if (($params = $request->getValidator()->check($params, array(
                'eid' => array('type' => 'identifier', 'required' => true),
                'items' => array('type' => 'object', 'required' => true)
            ))) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $requesting_user_eid = $request->getRequestingUser();

        $objects = self::filterEidItems($params['items']);
        if ($objects === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        // check the rights for each object; make sure all of them have
        // write privileges (TODO: explicitly check the parent project rather
        // than relying on the fact that projects govern permissions?)
        foreach ($objects as $eid)
        {
            // load the object
            $object = \Flexio\Object\Store::load($eid);
            if ($object === false)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

            // check the rights on the object
            if ($object->allows($requesting_user_eid, \Flexio\Object\Rights::ACTION_DELETE) === false)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);
        }

        // loop through the objects and try to add them to the trash
        foreach ($objects as $eid)
        {
            $obj = \Flexio\Object\Store::load($eid);
            if ($obj === false)
                continue;

            if ($obj->getStatus() !== \Model::STATUS_AVAILABLE)
                continue;

            // if the item is a connection, delete it straight away;
            // if it's another object, send it to the trash so it can
            // can purged later or recovered
            if ($obj->getType() === \Model::TYPE_CONNECTION)
                $obj->setStatus(\Model::STATUS_DELETED);
                 else
                $obj->setStatus(\Model::STATUS_TRASH);
        }

        return true;
    }

    public static function unTrash($params, $request)
    {
        if (($params = $request->getValidator()->check($params, array(
                'eid' => array('type' => 'identifier', 'required' => true),
                'items' => array('type' => 'object', 'required' => true)
            ))) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $requesting_user_eid = $request->getRequestingUser();

        $project_identifier = $params['eid'];
        $objects = self::filterEidItems($params['items']);
        if ($objects === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        // check the rights for each object; make sure all of them have
        // write privileges (TODO: explicitly check the parent project rather
        // than relying on the fact that projects govern permissions?)
        foreach ($objects as $eid)
        {
            // load the object
            $object = \Flexio\Object\Store::load($eid);
            if ($object === false)
               throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

            // check the rights on the object
            if ($object->allows($requesting_user_eid, \Flexio\Object\Rights::ACTION_DELETE) === false)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);
        }

        // iterate through the members and restore the trashed objects in the list
        $project = \Flexio\Object\Project::load($project_identifier);
        if ($project === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        $object_eids = array_flip($objects);
        $project_members = $project->getMembers();

        foreach ($project_members as $member)
        {
            if ($member->getStatus() !== \Model::STATUS_TRASH)
                continue;

            $member_eid = $member->getEid();
            if (!array_key_exists($member_eid, $object_eids))
                continue;

            $member->setStatus(\Model::STATUS_AVAILABLE);
        }

        return true;
    }

    public static function clearTrash($params, $request)
    {
        if (($params = $request->getValidator()->check($params, array(
                'eid' => array('type' => 'identifier', 'required' => true),
                'items' => array('type' => 'object', 'required' => true)
            ))) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $requesting_user_eid = $request->getRequestingUser();

        $project_identifier = $params['eid'];
        $objects = self::filterEidItems($params['items']);
        if ($objects === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        // check the rights for each object; make sure all of them have
        // write privileges (TODO: explicitly check the parent project rather
        // than relying on the fact that projects govern permissions?)
        foreach ($objects as $eid)
        {
            // load the object
            $object = \Flexio\Object\Store::load($eid);
            if ($object === false)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

            // check the rights on the object
            if ($object->allows($requesting_user_eid, \Flexio\Object\Rights::ACTION_DELETE) === false)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);
        }

        // iterate through the members and clear the trashed objects in the list
        $project = \Flexio\Object\Project::load($project_identifier);
        if ($project === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        $object_eids = array_flip($objects);
        $project_members = $project->getMembers();

        foreach ($project_members as $member)
        {
            if ($member->getStatus() !== \Model::STATUS_TRASH)
                continue;

            $member_eid = $member->getEid();
            if (!array_key_exists($member_eid, $object_eids))
                continue;

            $member->delete();
        }

        return true;
    }

    private static function getMembersByType($project, $type, $filter_list)
    {
        // get the member objects
        $check_object_list = false;
        if (is_array($filter_list))
            $check_object_list = true;

        $object_eids = array();
        if ($check_object_list)
            $object_eids = array_flip($filter_list);

        $objects = $project->getMembers();

        $result = array();
        foreach ($objects as $o)
        {
            $o_eid = $o->getEid();

            if ($o->getType() !== $type)
                continue;

            if ($o->getStatus() !== \Model::STATUS_AVAILABLE)
                continue;

            // if an object filter list is specified, check if the
            // object is part of that list; if so, get the info for
            // it; otherwise move on
            if ($check_object_list)
            {
                if (!isset($object_eids[$o_eid]))
                    continue;
            }

            $object_properties = $o->get();
            if ($object_properties === false)
                continue;

            $result[] = $object_properties;
        }

        return $result;
    }

    private static function filterEidItems($items)
    {
        if (!is_array($items))
            return false;

        $filtered_items = array();
        foreach ($items as $i)
        {
            // if we don't have a string, fail
            if (!is_string($i))
                return false;

            // if we have a string, but it isn't an eid, just ignore it
            if (!\Flexio\Base\Eid::isValid($i))
                continue;

            $filtered_items[] = $i;
        }

        return $filtered_items;
    }
}
