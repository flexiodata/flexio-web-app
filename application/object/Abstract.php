<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-10-29
 *
 * @package flexio
 * @subpackage Object
 */


namespace Flexio\Object;


interface IObject
{
    public static function create($properties = null);
    public static function load($identifier);
    public function copy();

    public function delete();
    public function set($properties);
    public function get();

    public function getEid();
    public function getType();

    public function setStatus($status);
    public function getStatus();

    public function setOwner($user_eid);
    public function getOwner();

    public function setCreatedBy($user_eid);
    public function getCreatedBy();

    public function allows($user_eid, $action_type);
    public function setRights($rights);

    public function addComment($comment_eid);
    public function getComments();
}
