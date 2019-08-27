<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie LLC. All rights reserved.
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
        //     4. must start with a letter and may only contain letters, numbers, hyphens and underscores
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
            $message = _("$prefix must start with a letter and may only contain letters, numbers, hyphens and underscores");
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

    public static function makeValid(string $name) : string
    {
        // identifiers (e.g. usernames, or other 'named handles') must
        // follow the following rules:
        //     1. be a lowercase string
        //     2. must not be an eid
        //     3. have a length between 3 and 80 chars
        //     4. must start with a letter and may only contain letters, numbers, hyphens and underscores
        //     5. not be one of a list of various reserved words

        // start with the name
        $updated_name = $name;

        // make sure the string is lowercase and trimmed of leading/trailing spaces
        $updated_name = trim(strtolower($updated_name));

        // if we have an eid, prefix an id string and we're done
        if (\Flexio\Base\Eid::isValid($updated_name))
            return 'id-' . $updated_name;

        // if we have a reserved word, prefix an id string and we're done
        if (\Flexio\Base\Keyword::exists($updated_name))
            return 'id-' . $updated_name;

        // replace any illegal characters with a hyphen
        $updated_name = preg_replace('/[^a-z0-9_-]/', '-', $updated_name);

        // make sure the string doesn't start with a number
        if (!preg_match('/^[a-z]/', $updated_name))
            $updated_name = 'id-' . $updated_name;

        // make sure strings are long enough
        if (strlen($updated_name) < 3)
            return 'id-' . $updated_name;

        // return the string, trimmed to the correct size
        return substr($updated_name,0,80);
    }
}
