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


declare(strict_types=1);
namespace Flexio\Object;


interface IObject
{
    public static function create(array $properties = null);
    public static function load(string $identifier);
    public function copy();

    public function delete();
    public function set(array $properties);
    public function get();

    public function getEid();
    public function getType();

    public function setStatus(string $status);
    public function getStatus();

    public function setOwner(string $user_eid);
    public function getOwner();

    public function allows(string $user_eid, string $action);
    public function setRights(\Flexio\Object\Acl $acl);
    public function getRights();
}
