<?php
/**
 *
 * Copyright (c) 2011, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams, Aaron L. Williams, David Z. Williams
 * Created:  2011-05-12
 *
 * @package flexio
 * @subpackage Api
 */


namespace Flexio\Api;


if (!isset($GLOBALS['humannameparser_included']))
{
    $GLOBALS['humannameparser_included'] = true;
    set_include_path(get_include_path() . PATH_SEPARATOR . (dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'library' . DIRECTORY_SEPARATOR . 'humannameparser'));
}
require_once 'humannameparser_init.php';


class User
{
    public static function create($params, $request)
    {
        if (($params = $request->getValidator()->check($params, array(
                'user_name'             => array('type' => 'string',  'required' => true),
                'email'                 => array('type' => 'string',  'required' => true),
                'password'              => array('type' => 'string',  'required' => true),
                'description'           => array('type' => 'string',  'required' => false),
                'full_name'             => array('type' => 'string',  'required' => false),
                'first_name'            => array('type' => 'string',  'required' => false),
                'last_name'             => array('type' => 'string',  'required' => false),
                'phone'                 => array('type' => 'string',  'required' => false),
                'location_city'         => array('type' => 'string',  'required' => false),
                'location_state'        => array('type' => 'string',  'required' => false),
                'location_country'      => array('type' => 'string',  'required' => false),
                'company_name'          => array('type' => 'string',  'required' => false),
                'company_url'           => array('type' => 'string',  'required' => false),
                'locale_language'       => array('type' => 'string',  'required' => false),
                'locale_decimal'        => array('type' => 'string',  'required' => false),
                'locale_thousands'      => array('type' => 'string',  'required' => false),
                'locale_dateformat'     => array('type' => 'string',  'required' => false),
                'timezone'              => array('type' => 'string',  'required' => false),
                'verify_code'           => array('type' => 'string',  'required' => false),
                'config'                => array('type' => 'string',  'required' => false),
                'send_email'            => array('type' => 'boolean', 'required' => false, 'default' => true),
                'create_sample_project' => array('type' => 'boolean', 'required' => false, 'default' => true),
                'require_verification'  => array('type' => 'boolean', 'required' => false, 'dfeault' => false)
            ))) === false)
            return $request->getValidator()->fail();


        // if a full name is specified, try to parse it
        $first_name_parsed = '';
        $last_name_parsed = '';
/*
// TODO: currently, the name parser is throwing an Exception; need to fix

        if (isset($params['full_name']))
        {
            try
            {
                $parser = new HumanNameParser_Parser($params['full_name']);
                $first_name_parsed = $parser->getFirst();
                $last_name_parsed = $parser->getLast();
            }
             catch (\Exception $e)
            {
                // unable to parse the name; treat the set the first name as whatever was entered
                $first_name_parsed = $params['full_name'];
            }
        }
*/

        // required fields
        $user_name = $params['user_name'];
        $email = $params['email'];
        $password = $params['password'];

        // make sure the user_name is valid syntactically; note: don't check for existence here
        if (!\Flexio\Base\Identifier::isValid($user_name))
            return $request->getValidator()->fail(Api::ERROR_INVALID_PARAMETER, _('This username is invalid.  Please try another.'));

        $description = isset_or($params['description'], '');
        $full_name = isset_or($params['full_name'], '');  // TODO: should we combine first/last name if full_name isn't set?
        $first_name = isset_or($params['first_name'], $first_name_parsed);
        $last_name = isset_or($params['last_name'], $last_name_parsed);
        $phone = isset_or($params['phone'], '');
        $location_city = isset_or($params['location_city'], '');
        $location_state = isset_or($params['location_state'], '');
        $location_country = isset_or($params['location_country'], '');
        $company_name = isset_or($params['company_name'], '');
        $company_url = isset_or($params['company_url'], '');
        $locale_language = isset_or($params['locale_language'], 'en_US');
        $locale_decimal = isset_or($params['locale_decimal'], '.');
        $locale_thousands = isset_or($params['locale_thousands'], ',');
        $locale_dateformat = isset_or($params['locale_dateformat'], 'm/d/Y');
        $timezone = isset_or($params['timezone'], 'UTC');
        $config = isset_or($params['config'], '{}');
        $require_verification = isset_or($params['require_verification'], false);

        $verify_code = '';
        if ($require_verification === true)
            $verify_code = isset($params['verify_code']) ? $params['verify_code'] : \Flexio\Base\Util::generateHandle(); // code to verify user's email address

        // use email to check if account already exists; if it exists
        // based on the email, see if the user is pending (invitations
        // use email)

        $user = \Flexio\Object\User::load($email);
        $new_user_info = array(
                            'user_name' => $user_name,
                            'description' => $description,
                            'full_name' => $full_name,
                            'first_name' => $first_name,
                            'last_name' => $last_name,
                            'email' => $email,
                            'phone' => $phone,
                            'location_city' => $location_city,
                            'location_state' => $location_state,
                            'location_country' => $location_country,
                            'company_name' => $company_name,
                            'company_url' => $company_url,
                            'locale_language' => $locale_language,
                            'locale_decimal' => $locale_decimal,
                            'locale_thousands' => $locale_thousands,
                            'locale_dateformat' => $locale_dateformat,
                            'timezone' => $timezone,
                            'password' => $password,
                            'verify_code' => $verify_code,
                            'config' => $config,
                            'eid_status' => ($require_verification === true ? \Model::STATUS_PENDING : \Model::STATUS_AVAILABLE)
                         );

        if ($require_verification === false)
        {
            // without verification, the user should never already exist; fail if they do
            if ($user !== false)
                return $request->getValidator()->fail(Api::ERROR_CREATE_FAILED, _('This email address is already taken.  Please try another.'));
        }
         else
        {
            if ($user !== false)
            {
                // user already exists; if we're trying to activate a user that's
                // not in a pending state, flag an error
                if ($user->getStatus() != \Model::STATUS_PENDING)
                    return $request->getValidator()->fail(Api::ERROR_CREATE_FAILED, _('This email address is already taken.  Please try another.'));

                // check if the verification code is set and matches the verfication code we
                // have on record
                $verify_code = $user->getVerifyCode();
                if (strlen($verify_code) > 0 && isset($params['verify_code']) && $params['verify_code'] === $verify_code)
                {
                    // user already exists, and has the correct verification code;
                    // set the rest of the information, and activate the user

                    // invited users already exist, but need to have the rest of their
                    // info set; don't allow last minute changes to the username or email
                    unset($new_user_info['user_name']);
                    unset($new_user_info['email']);

                    // link was clicked in notification email and verify code checks out;
                    // so automatically promote user to verified/active status
                    $new_user_info['verify_code'] = '';
                    $new_user_info['eid_status'] = \Model::STATUS_AVAILABLE;

                    $result = $user->set($new_user_info);
                    if ($result === false)
                        $request->getValidator()->fail(Api::ERROR_WRITE_FAILED, _('Operation failed'));

                    // we're done; other parts of the account will have already been created
                    return $user->get();
                }
                else
                {
                    // user exists, but the user isn't yet verified and either the
                    // verification code doesn't exist or the verification code that's
                    // been provided doesn't match; the user could be trying to create an
                    // account after having a project shared with them and having not
                    // verified; or a user could be trying to create an account when they
                    // hadn't yet completed the verification proc_get_status previously;
                    // in either case, we need to create a new verification code and let
                    // the user verify

                    $new_verify_code = \Flexio\Base\Util::generateHandle();
                    $new_user_info['verify_code'] = $new_verify_code;

                    $result = $user->set($new_user_info);
                    if ($result === false)
                        $request->getValidator()->fail(Api::ERROR_WRITE_FAILED, _('Operation failed'));

                    // if appropriate, send an email
                    if ($params['send_email'] === true)
                    {
                        $message_type = \Flexio\Object\Message::TYPE_EMAIL_WELCOME;
                        $email_params = array('email' => $email, 'verify_code' => $new_verify_code);
                        $message = \Flexio\Object\Message::create($message_type, $email_params);
                        $message->send();
                    }

                    // return the user info
                    return $user->get();
                }
            }
        }

        // user doesn't exist; create the user
        $user = \Flexio\Object\User::create($new_user_info);
        if ($user === false)
            return $request->getValidator()->fail(Api::ERROR_CREATE_FAILED, _('Account creation not available at this time'));

        // set the owner and creator
        $user_eid = $user->getEid();
        $user->setOwner($user_eid);
        $user->setCreatedBy($user_eid);

        // if appropriate, send an email
        if ($params['send_email'] === true)
        {
            $message_type = \Flexio\Object\Message::TYPE_EMAIL_WELCOME;
            $email_params = array('email' => $email, 'verify_code' => $verify_code);
            $message = \Flexio\Object\Message::create($message_type, $email_params);
            $message->send();
        }

        // if appropriate, create a default project
        if ($params['create_sample_project'] === true)
            self::createSampleProject($user_eid);

        // return the user info
        return $user->get();
    }

    public static function set($params, $request)
    {
        if (($params = $request->getValidator()->check($params, array(
                'eid'               => array('type' => 'identifier', 'required' => true),
                'user_name'         => array('type' => 'string', 'required' => false),
                'password'          => array('type' => 'string', 'required' => false),
                'description'       => array('type' => 'string', 'required' => false),
                'full_name'         => array('type' => 'string', 'required' => false),
                'first_name'        => array('type' => 'string', 'required' => false),
                'last_name'         => array('type' => 'string', 'required' => false),
                'email'             => array('type' => 'string', 'required' => false),
                'phone'             => array('type' => 'string', 'required' => false),
                'location_city'     => array('type' => 'string', 'required' => false),
                'location_state'    => array('type' => 'string', 'required' => false),
                'location_country'  => array('type' => 'string', 'required' => false),
                'company_name'      => array('type' => 'string', 'required' => false),
                'company_url'       => array('type' => 'string', 'required' => false),
                'locale_language'   => array('type' => 'string', 'required' => false),
                'locale_decimal'    => array('type' => 'string', 'required' => false),
                'locale_thousands'  => array('type' => 'string', 'required' => false),
                'locale_dateformat' => array('type' => 'string', 'required' => false),
                'timezone'          => array('type' => 'string', 'required' => false),
                'config'            => array('type' => 'string', 'required' => false)
            ))) === false)
            return $request->getValidator()->fail();

        // note: password parameter is allowed in this call; it's used in some
        // type of workflow (such as when sharing with a user that doesn't exist)

        $user_identifier = $params['eid'];
        $requesting_user_eid = $request->getRequestingUser();

        // load the user
        $user = \Flexio\Object\User::load($user_identifier);
        if ($user === false)
            return $request->getValidator()->fail(Api::ERROR_NO_OBJECT);

        // check the rights, but only if the object isn't pending;
        // TODO: proper approach is to always check rights; right now, \Flexio\Api\User::set()
        // is used to set user info for unverified accounts that were created from an invitation
        // the rest of the info needs to be set to complete the process before the user is able
        // to log in; probably best to pass the verification code and use the \Flexio\Api\User::create()
        // function to complete the process rather than this, since this approach could be used to
        // set info for unverified users
        if ($user->getStatus() !== \Model::STATUS_PENDING)
        {
            if ($user->allows($requesting_user_eid, \Flexio\Object\Rights::ACTION_WRITE) === false)
                return $request->getValidator()->fail(Api::ERROR_INSUFFICIENT_RIGHTS);
        }

/*
// TODO: currently, the name parser is throwing an Exception; need to fix
        // try to fill in any unspecified first/last name from any specified full name
        if (isset($params['full_name']))
        {
            try
            {
                $parser = new HumanNameParser_Parser($params['full_name']);
                $first_name_parsed = $parser->getFirst();
                $last_name_parsed = $parser->getLast();

                if (!isset($params['first_name']))
                    $params['first_name'] = $first_name_parsed;
                if (!isset($params['last_name']))
                    $params['last_name'] = $last_name_parsed;
            }
             catch (\Exception $e)
            {
                // unable to parse the name; treat the set the first name as whatever was entered
                $first_name_parsed = $params['full_name'];

                if (!isset($params['first_name']))
                    $params['first_name'] = $first_name_parsed;
            }
        }
*/

        $user->set($params);
        return $user->get();
    }

    public static function get($params, $request)
    {
        if (($params = $request->getValidator()->check($params, array(
                'eid' => array('type' => 'identifier', 'required' => true)
            ))) === false)
            return $request->getValidator()->fail();

        $user_identifier = $params['eid'];
        $requesting_user_eid = $request->getRequestingUser();

        // load the object
        $user = \Flexio\Object\User::load($user_identifier);
        if ($user === false)
            return $request->getValidator()->fail(Api::ERROR_NO_OBJECT);

        // check the rights on the object
        if ($user->allows($requesting_user_eid, \Flexio\Object\Rights::ACTION_READ) === false)
            return $request->getValidator()->fail(Api::ERROR_INSUFFICIENT_RIGHTS);

        return $user->get();
    }

    public static function about($params, $request)
    {
        // returns the information for the currently logged-in user or an empty eid
        // if the user isn't logged in

        $requesting_user_eid = $request->getRequestingUser();

        $user = \Flexio\Object\User::load($requesting_user_eid);
        if ($user === false)
        {
            $properties = array();
            $properties['eid'] = '';
            $properties['eid_type'] = \Model::TYPE_USER;
            return $properties;
        }

        return $user->get();
    }

    public static function changepassword($params, $request)
    {
        if (($params = $request->getValidator()->check($params, array(
                'eid'               => array('type' => 'identifier', 'required' => true),
                'old_password'      => array('type' => 'string', 'required' => true),
                'new_password'      => array('type' => 'string', 'required' => true)
            ))) === false)
            return $request->getValidator()->fail();

        $user_identifier = $params['eid'];
        $requesting_user_eid = $request->getRequestingUser();
        $old_password = $params['old_password'];
        $new_password = $params['new_password'];

        // load the object
        $user = \Flexio\Object\User::load($user_identifier);
        if ($user === false)
            return $request->getValidator()->fail(Api::ERROR_NO_OBJECT);

        // check the rights on the object
        if ($user->allows($requesting_user_eid, \Flexio\Object\Rights::ACTION_WRITE) === false)
            return $request->getValidator()->fail(Api::ERROR_INSUFFICIENT_RIGHTS);

        if ($user->checkPassword($old_password) === false)
            return $request->getValidator()->fail(Api::ERROR_INVALID_PARAMETER, _('The current password entered was incorrect'));

        $new_params = array(
            'password' => $new_password
        );
        $user->set($new_params);
        return $user->get();
    }

    public static function activate($params, $request)
    {
        if (($params = $request->getValidator()->check($params, array(
                'email'     => array('type' => 'string', 'required' => true),
                'verify_code'      => array('type' => 'string', 'required' => true)
            ))) === false)
            return $request->getValidator()->fail();

        $email = $params['email'];
        $code = $params['verify_code'];

        $user = \Flexio\Object\User::load($email);
        if ($user === false)
            return $request->getValidator()->fail(Api::ERROR_NO_OBJECT, _('This user is unavailable'));

        if ($user->getStatus() != \Model::STATUS_PENDING)
            return $request->getValidator()->fail(Api::ERROR_WRITE_FAILED, _('This user is already activated'));

        if ($this->getVerifyCode() != $code)
            return $request->getValidator()->fail(Api::ERROR_WRITE_FAILED, _('The activation credentials do not match'));

        if ($user->set(array('eid_status' => \Model::STATUS_AVAILABLE, 'verify_code' => '')) === false)
            return $request->getValidator()->fail(Api::ERROR_WRITE_FAILED, _('Could not activate the user at this time'));

        return true;
    }

    public static function resendverify($params, $request)
    {
        if (($params = $request->getValidator()->check($params, array(
                'email'     => array('type' => 'string', 'required' => true)
            ))) === false)
            return $request->getValidator()->fail();

        $email = $params['email'];

        $user = \Flexio\Object\User::load($email);
        if ($user === false)
            return $request->getValidator()->fail(Api::ERROR_NO_OBJECT, _('This user is unavailable'));

        // TODO: if the verify code is a set, but blank, should we regenerate
        // the verification code?

        $verify_code = $this->getVerifyCode();
        if (!isset($verify_code))
            return $request->getValidator()->fail(Api::ERROR_MISSING_PARAMETER, _('Missing verification code'));

        $message_type = \Flexio\Object\Message::TYPE_EMAIL_WELCOME;
        $email_params = array('email' => $email, 'verify_code' => $verify_code);
        $message = \Flexio\Object\Message::create($message_type, $email_params);
        $message->send();

        return true;
    }

    public static function requestpasswordreset($params, $request)
    {
        if (($params = $request->getValidator()->check($params, array(
                'email'     => array('type' => 'string', 'required' => true)
            ))) === false)
            return $request->getValidator()->fail();

        $email = $params['email'];
        $verify_code = \Flexio\Base\Util::generateHandle();

        $user = \Flexio\Object\User::load($email);
        if ($user === false)
            return $request->getValidator()->fail(Api::ERROR_NO_OBJECT, _('This user is unavailable'));

        if ($user->set(array('verify_code' => $verify_code)) === false)
            return $request->getValidator()->fail(Api::ERROR_WRITE_FAILED, _('Could not send password reset email at this time'));

        $message_type = \Flexio\Object\Message::TYPE_EMAIL_RESET_PASSWORD;
        $email_params = array('email' => $email, 'verify_code' => $verify_code);
        $message = \Flexio\Object\Message::create($message_type, $email_params);
        $message->send();

        return true;
    }

    public static function resetpassword($params, $request)
    {
        if (($params = $request->getValidator()->check($params, array(
                'email'       => array('type' => 'string', 'required' => true),
                'password'    => array('type' => 'string', 'required' => true),
                'verify_code' => array('type' => 'string', 'required' => true)
            ))) === false)
            return $request->getValidator()->fail();

        $email = $params['email'];
        $password = $params['password'];
        $code = $params['verify_code'];

        $user = \Flexio\Object\User::load($email);
        if ($user === false)
            return $request->getValidator()->fail(Api::ERROR_NO_OBJECT, _('This user is unavailable'));

        if ($user->getVerifyCode() !== $code)
            return $request->getValidator()->fail(Api::ERROR_INVALID_PARAMETER, _('The credentials do not match'));

        if ($user->set(array('password' => $password, 'eid_status' => \Model::STATUS_AVAILABLE, 'verify_code' => '')))
            return $request->getValidator()->fail(Api::ERROR_WRITE_FAILED, _('Could not update user at this time'));

        return true;
    }

    public static function createSample($params, $request)
    {
        // note: this is an API endpoint function for debugging; internally,
        // createSampleProject() is used when a user is created so that the owner
        // will be set to the newly created user even though the user and projects
        // have both been created initially by the system

        if (($params = $request->getValidator()->check($params, array(
            ))) === false)
            return $request->getValidator()->fail();

        $user_eid = $request->getRequestingUser();
        $project = self::createSampleProject($user_eid, 'Sample Project');
        return true;
    }

    private static function createSampleProject($user_eid, $name = null, $description = null)
    {
        // TODO: convert over to using API functions?

        $project_params['name'] = isset_or($name, _('Sample Project'));
        $project_params['description'] = isset_or($description, _('Sample project to demonstrate functionality.'));

        $project = \Flexio\Object\Project::create($project_params);
        if ($project === false)
            return false;

        // TODO: no need to check owner since creation of project
        // implies ability to set rights; correct?

        // set the owner
        $project->setOwner($user_eid);
        $project->setCreatedBy($user_eid);

        // create sample pipes
        $demo_dir = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'scripts' . DIRECTORY_SEPARATOR . 'demo' . DIRECTORY_SEPARATOR;
        self::createSamplePipe($user_eid, $project->getEid(), '', '', $demo_dir .'pipe_weather.json');
        self::createSamplePipe($user_eid, $project->getEid(), '', '', $demo_dir .'pipe_rss.json');

        return true;
    }

    private static function createSamplePipe($user_eid, $project_eid, $name, $description, $file_name)
    {
        // STEP 1: read the pipe file and convert it to JSON
        $f = @fopen($file_name, 'rb');
        if (!$f)
            return false;

        $buf = '';
        while (!feof($f))
        {
            $buf .= fread($f, 65535);
        }

        fclose($f);

        $pipe_definition = @json_decode($buf,true);
        if ($pipe_definition === false)
            return false;

        // STEP 2: create a pipe container and add it to the project
        $call_params['name'] = isset_or($pipe_definition['name'], $name);
        $call_params['description'] = isset_or($pipe_definition['description'], $description);
        $call_params['task'] = array();
        if (isset($pipe_definition['task']))
            $call_params['task'] = $pipe_definition['task'];

        $pipe = \Flexio\Object\Pipe::create($call_params);
        if ($pipe === false)
            return false;

        // set the owner
        $pipe->setOwner($user_eid);
        $pipe->setCreatedBy($user_eid);

        // if a parent project is specified, add the object as a member of the project
        $project = \Flexio\Object\Project::load($project_eid);
        if ($project !== false)
            $project->addMember($pipe->getEid());

        return $pipe->getEid();
    }
}
