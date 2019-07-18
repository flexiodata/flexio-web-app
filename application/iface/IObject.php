<?php
/**
 *
 * Copyright (c) 2017, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2017-12-11
 *
 * @package flexio
 * @subpackage IFace
 */


declare(strict_types=1);
namespace Flexio\IFace;


interface IObject
{
    public static function create(array $properties = null);
    public static function load(string $eid);

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
}

