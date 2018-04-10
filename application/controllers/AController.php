<?php
/**
 *
 * Copyright (c) 2008-2011, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   David Z. Williams
 * Created:  2008-12-30
 *
 * @package flexio
 * @subpackage Controller
 */


declare(strict_types=1);
namespace Flexio\Controllers;


class AController extends \Flexio\System\FxControllerAction
{
    public function init()
    {
        parent::init();
    }

    public function connectionauthAction()
    {
        // TODO: route this request through the UI?

        $this->renderRaw();
        $params = $this->getRequest()->getParams();
        $auth_params = array();

        if (isset($params['service']))
            $auth_params['service'] = $params['service'];
        if (isset($params['code']))
            $auth_params['code'] = $params['code'];
        if (isset($params['state']))
            $auth_params['state'] = $params['state'];

        // check for empty
        if (($params['service'] ?? '') == 'dropbox')
        {
            if (strlen(''.($GLOBALS['g_config']->dropbox_client_id ?? '')) == 0 ||
                strlen(''.($GLOBALS['g_config']->dropbox_client_secret ?? '')) == 0)
            {
                die('This function is presently not available.');
            }
        }

        if (($params['service'] ?? '') == 'box')
        {
            if (strlen(''.($GLOBALS['g_config']->box_client_id ?? '')) == 0 ||
                strlen(''.($GLOBALS['g_config']->box_client_secret ?? '')) == 0)
            {
                die('This function is presently not available.');
            }
        }

        if (($params['service'] ?? '') == 'github')
        {
            if (strlen(''.($GLOBALS['g_config']->github_client_id ?? '')) == 0 ||
                strlen(''.($GLOBALS['g_config']->github_client_secret ?? '')) == 0)
            {
                die('This function is presently not available.');
            }
        }

        if (($params['service'] ?? '') == 'googledrive' || ($params['service'] ?? '') == 'googlesheets')
        {
            if (strlen(''.($GLOBALS['g_config']->googledrive_client_id ?? '')) == 0 ||
                strlen(''.($GLOBALS['g_config']->googledrive_client_secret ?? '')) == 0)
            {
                die('This function is presently not available.');
            }
        }

        $auth_params['redirect'] = (IS_SECURE() ? 'https':'http') . '://' . $_SERVER['HTTP_HOST'] . '/a/connectionauthcallback';

        $eid = $params['eid'] ?? false;

        $connection = false;
        try
        {
            $connection = \Flexio\Object\Connection::load($eid);
            if ($connection->getStatus() === \Model::STATUS_DELETED)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);
        }
        catch (\Flexio\Base\Exception $e)
        {
            die('The specified connection is unavailable');
        }

        $result = $connection->authenticate($auth_params);

        // if authenticating returns a string, it's a redirect url
        if ($result !== true && $result !== false)
        {
            //fxdebug('redirect URL is: '. $result);
            $this->_redirect($result);
        }
         else
        {
            die("Authentication to this service is presently not available.");
        }
    }

    public function connectionauthcallbackAction()
    {
        $params = $this->getRequest()->getParams();

        $auth_params = array();

        if (isset($params['service']))
            $auth_params['service'] = $params['service'];
        if (isset($params['code']))
            $auth_params['code'] = $params['code'];
        if (isset($params['state']))
            $auth_params['state'] = $params['state'];

        $auth_params['redirect'] = (IS_SECURE() ? 'https':'http') . '://' . $_SERVER['HTTP_HOST'] . '/a/connectionauthcallback';

        // if the state is set, get the eid and load the connection; otherwise
        // try to get the eid from the raw params
        $connection = false;
        try
        {
            if (isset($auth_params['state']))
            {
                $state = json_decode(base64_decode($auth_params['state']),true);
                $eid = $state['eid'] ?? false;
                $connection = \Flexio\Object\Connection::load($eid);
                if ($connection->getStatus() === \Model::STATUS_DELETED)
                    throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);
            }
            else
            {
                $eid = $params['eid'] ?? false;
                $connection = \Flexio\Object\Connection::load($eid);
                if ($connection->getStatus() === \Model::STATUS_DELETED)
                    throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);
            }
        }
        catch (\Flexio\Base\Exception $e)
        {
        }

        if (isset($params['error']))
        {
            // set the error code on the connection
            if ($connection !== false)
            {
                $properties = array();
                $properties['connection_status'] = \Model::CONNECTION_STATUS_ERROR;
                if ($connection !== false)
                    $connection->set($properties);
            }
        }

        $result = false;
        if ($connection !== false)
            $result = $connection->authenticate($auth_params);

        /*
        // TODO: remove this? this should have already been taken care of
        //       by the 'error' check above...
        if ($result === false)
        {
            die("The authorization unfortunately did not succeed.");
        }
        */

        // render this page, so it can close the popup
        // and do the callback to the parent/app window
        $this->setViewTitle(_('Connection Authorization - Flex.io'));
        $this->renderPublic();
        $this->render();
    }

    public function shareauthAction()
    {
        $this->renderRaw();
        $params = $this->getRequest()->getParams();

        $email = null;
        $verify_code = null;
        $eid = null;

        if (isset($params['email']))
            $email = $params['email'];
        if (isset($params['verify_code']))
            $verify_code = $params['verify_code'];
        if (isset($params['object_eid']))
            $object_eid = $params['object_eid'];

        $url = isset($object_eid) ? "/app/pipes/$object_eid" : '/app/home';

        $current_user_email = '';
        try
        {
            $current_user_eid = \Flexio\System\System::getCurrentUserEid();
            $current_user_info = \Flexio\Object\User::load($current_user_eid)->get();
            $current_user_email = $current_user_info['email'];
        }
        catch (\Flexio\Base\Exception $e)
        {
        }

        if (\Flexio\System\System::isLoggedIn() && isset($email) && $email === $current_user_email)
        {
            // CASE 1: user is logged in, and user is the same as the one
            // with the link; try to access the shared pipe (TODO:
            // need to add encryption to incoming URL to avoid email
            // spoofing)
            $redirect = $url;
            $this->_redirect($redirect);
            return;
        }
         else
        {
            // from here on out, we'll need an email address
            if (!isset($email))
            {
                // we don't have an email address, so nothing we can do
                $redirect = '/app/signin?ref=share_auth';
                $this->_redirect($redirect);
                return;
            }

            // get the user we're sharing with
            $target_user_info = false;
            $target_user = false;

            try
            {
                $user_eid = \Flexio\Object\User::getEidFromEmail($email);
                $target_user = \Flexio\Object\User::load($user_eid);
                if ($target_user->getStatus() === \Model::STATUS_DELETED)
                    throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);
                $target_user_info = $target_user->get();
            }
            catch (\Flexio\Base\Exception $e)
            {
            }

            if ($target_user_info === false)
            {
                // CASE 2: user isn't logged in and they don't have an account;
                // note: this is a situation where something has gone wrong because
                // when items are shared, the account should be created; send
                // them to the login page
                $redirect = '/app/signin?ref=share_auth';
                $this->_redirect($redirect);
                return;
            }
             else
            {
                $target_user_eid_status = $target_user_info['eid_status'];
                if ($target_user_eid_status !== \Model::STATUS_AVAILABLE)
                {
                    // CASE 3: user isn't logged in and they have an account, but
                    // it's not verified; TODO: verify the account
                    $target_user_verified_code = $target_user_info['verify_code'];
                    if (!isset($verify_code) || $target_user_verified_code != $verify_code)
                    {
                        // verification codes don't match
                        $redirect = '/app/signin?ref=share_auth';
                        $this->_redirect($redirect);
                        return;
                    }

                    $redirect = "/app/signup?ref=share_auth&email=".urlencode($email)."&user_eid=$target_user_eid&verify_code=$target_user_verified_code&redirect=".urlencode($url);
                    $this->_redirect($redirect);
                    return;
                }
                 else
                {
                    // CASE 4: user isn't logged in and they have an account
                    // that's also verified; take them to the sign in page
                    $redirect = '/app/signin?ref=share_auth&redirect='.urlencode($url);
                    $this->_redirect($redirect);
                    return;
                }
            }
        }
    }
}
