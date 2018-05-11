<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-06-17
 *
 * @package flexio
 * @subpackage Base
 */


declare(strict_types=1);
namespace Flexio\Base;


class Identifier
{
    public static function generate() : string
    {
        return \Flexio\Base\Util::generateHandle();
    }

    public static function isValid($identifier, &$message = '', $prefix = 'An identifier') : bool
    {
        // identifiers (e.g. usernames, or other 'named handles') must
        // follow the following rules:
        //     1. be a lowercase string
        //     2. must not be an eid
        //     3. have a length between 3 and 80 chars
        //     4. start with a letter
        //     5. not be one of a list of various reserved words

        // initialize the message parameter
        $message = '';

        // make sure identifier is a lowercase string
        if (!is_string($identifier))
            return false;
        if ($identifier !== strtolower($identifier))
        {
            $message = _("$prefix must be lowercase");
            return false;
        }

        if (\Flexio\Base\Eid::isValid($identifier))
        {
            $message = _("$prefix must not be an eid");
            return false;
        }

        // make sure identifier is correct length
        $identifier_minlength = 3;
        $identifier_maxlength = 80;
        $identifier_length = strlen($identifier);
        if ($identifier_length < $identifier_minlength || $identifier_length > $identifier_maxlength)
        {
            $message = _("$prefix must be between 3 and 80 characters");
            return false;
        }

        // make sure identifier starts with a letter; the rest of the
        // identifier must be made up of a letter, number, underscore
        // or hyphen
        if (!preg_match('/^[a-z][a-z0-9_-]{2,79}$/', $identifier))
        {
            $message = _("$prefix may only contain letters, numbers, hyphens and underscores");
            return false;
        }

        if (\Flexio\Base\Keyword::exists($identifier))
        {
            $message = _("$prefix must not be a reserved or illegal word");
            return false;
        }

        // valid identifier
        return true;
    }
}
