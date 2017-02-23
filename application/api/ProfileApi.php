<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams, Aaron L. Williams, David Z. Williams
 * Created:  2016-12-08
 *
 * @package flexio
 * @subpackage Api
 */


namespace Flexio\Api;


class ProfileApi
{
    public static function getProfilePictureUrl($user_eid)
    {
        if (!\Flexio\System\System::getModel()->registry->entryExists($user_eid, 'profile.picture'))
            return false;

        return '/api/v1/users/' . $user_eid . '/profilepicture';
    }

    public static function getProfileBackgroundUrl($user_eid)
    {
        if (!\Flexio\System\System::getModel()->registry->entryExists($user_eid, 'profile.background'))
            return false;

        return '/api/v1/users/' . $user_eid . '/profilebackground';
    }
}
