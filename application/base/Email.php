<?php
/**
 *
 * Copyright (c) 2018, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2018-05-25
 *
 * @package flexio
 * @subpackage Base
 */


declare(strict_types=1);
namespace Flexio\Base;


class Email
{
    public static function isValid(string $email) : bool
    {
        // checks if an email address is valid
        return \Flexio\Base\Util::isValidEmail($email);
    }
}
