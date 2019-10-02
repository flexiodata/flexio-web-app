<?php
/**
 *
 * Copyright (c) 2019, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2019-09-20
 *
 * @package flexio
 * @subpackage IFace
 */


declare(strict_types=1);
namespace Flexio\IFace;


interface IOAuthConnection
{
    public function getAuthorizationUri() : string;
    public function getTokens() : array;
    public function getUserInfo() : array;
}

