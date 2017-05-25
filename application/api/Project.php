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
    public static function create(array $params, string $requesting_user_eid = null) : array
    {
        $validator = \Flexio\Base\Validator::create();
        if (($params = $validator->check($params, array(
                'eid_status'   => array('type' => 'string', 'required' => false),
                'name'         => array('type' => 'string', 'required' => false),
                'description'  => array('type' => 'string', 'required' => false)
            ))->getParams()) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        // right now, only allow a project to be created by an actual user since
        // we have no other way of knowing who the intended owner is (until we allow
        // projects to be created from a user api endpoint)
        $user = \Flexio\Object\User::load($requesting_user_eid);
        if ($user === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED);

        // create the object
        $project = \Flexio\Object\Project::create($params);
        $project->setOwner($requesting_user_eid);
        $project->setCreatedBy($requesting_user_eid);

        // return the project
        return $project->get();
    }

    public static function delete(array $params, string $requesting_user_eid = null) : bool
    {
        $validator = \Flexio\Base\Validator::create();
        if (($params = $validator->check($params, array(
                'eid' => array('type' => 'identifier', 'required' => true)
            ))->getParams()) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $project_identifier = $params['eid'];

        // load the object
        $project = \Flexio\Object\Project::load($project_identifier);
        if ($project === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // check the rights on the object
        if ($project->allows(\Flexio\Object\Action::TYPE_DELETE, $requesting_user_eid) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        $project->delete();
        return true;
    }

    public static function set(array $params, string $requesting_user_eid = null) : array
    {
        $validator = \Flexio\Base\Validator::create();
        if (($params = $validator->check($params, array(
                'eid'          => array('type' => 'identifier', 'required' => true),
                'eid_status'   => array('type' => 'string', 'required' => false),
                'name'         => array('type' => 'string', 'required' => false),
                'description'  => array('type' => 'string', 'required' => false)
            ))->getParams()) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $project_identifier = $params['eid'];

        // load the object
        $project = \Flexio\Object\Project::load($project_identifier);
        if ($project === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // check the rights on the object
        if ($project->allows(\Flexio\Object\Action::TYPE_WRITE, $requesting_user_eid) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // set the project params
        $project->set($params);
        return $project->get();
    }

    public static function get(array $params, string $requesting_user_eid = null) : array
    {
        $validator = \Flexio\Base\Validator::create();
        if (($params = $validator->check($params, array(
                'eid' => array('type' => 'identifier', 'required' => true)
            ))->getParams()) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $project_identifier = $params['eid'];

        // load the object
        $project = \Flexio\Object\Project::load($project_identifier);
        if ($project === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // check the rights on the object
        if ($project->allows(\Flexio\Object\Action::TYPE_READ, $requesting_user_eid) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // fill out the properties with additional info
        $properties = $project->get();
        if ($properties === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

        return $properties;
    }

    public static function listall(array $params, string $requesting_user_eid = null) : array
    {
        // load the object
        $user = \Flexio\Object\User::load($requesting_user_eid);
        if ($user === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // get the projects
        $result = array();
        $projects = $user->getProjects();
        foreach ($projects as $p)
        {
            if ($p->allows(\Flexio\Object\Action::TYPE_READ, $requesting_user_eid) === false)
                continue;

            $result[] = $p->get();
        }

        return $result;
    }

    public static function pipes(array $params, string $requesting_user_eid = null) : array
    {
        // eid is the project
        $validator = \Flexio\Base\Validator::create();
        if (($params = $validator->check($params, array(
                'eid' => array('type' => 'identifier', 'required' => true),
                'pipe_eid' => array('type' => 'identifier', 'array' => true, 'required' => false)
            ))->getParams()) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $project_identifier = $params['eid'];
        $filter_list = $params['pipe_eid'] ?? null;

        // load the project
        $project = \Flexio\Object\Project::load($project_identifier);
        if ($project === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        return self::getMembersByType($project, \Model::TYPE_PIPE, $requesting_user_eid, $filter_list);
    }

    public static function connections(array $params, string $requesting_user_eid = null) : array
    {
        // eid is the project
        $validator = \Flexio\Base\Validator::create();
        if (($params = $validator->check($params, array(
                'eid' => array('type' => 'identifier', 'required' => true),
                'connection_eid' => array('type' => 'identifier', 'array' => true, 'required' => false)
            ))->getParams()) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $project_identifier = $params['eid'];
        $filter_list = $params['connection_eid'] ?? null;

        // load the project
        $project = \Flexio\Object\Project::load($project_identifier);
        if ($project === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        return self::getMembersByType($project, \Model::TYPE_CONNECTION, $requesting_user_eid, $filter_list);
    }

    private static function getMembersByType(\Flexio\Object\Project $project, string $type, string $requesting_user_eid = null, array $filter_list = null) : array
    {
        // get the member objects
        $check_object_list = false;
        if (isset($filter_list))
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

            // check the rights on the object; only show objects for
            // which we have rights
            if ($o->allows(\Flexio\Object\Action::TYPE_READ, $requesting_user_eid) === false)
                continue;

            $object_properties = $o->get();
            if ($object_properties === false)
                continue;

            $result[] = $object_properties;
        }

        return $result;
    }
}
