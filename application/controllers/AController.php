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

namespace Flexio\Controllers;

class AController extends \FxControllerAction
{
    /*
        NOTE: DO NOT REMOVE THESE -- THEY ARE HERE FOR TRANSLATION PURPOSES

        // Date Range Picker (in public folder)

        _('All Dates')
        _('Today')
        _('Last 7 Days')
        _('Month to Date')
        _('Year to Date')
        _('Previous Month')
        _('Previous Year')
        _('Specific Date')
        _('Date Range')
        _('Start Date')
        _('End Date')
        _('Next')
        _('Prev')
        _('Set Date Range')

        _('January')
        _('February')
        _('March')
        _('April')
        _('May')
        _('June')
        _('July')
        _('August')
        _('September')
        _('October')
        _('November')
        _('December')
    */

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
        if (isset_or($params['service'], '') == 'dropbox')
        {
            if (strlen(''.isset_or($GLOBALS['g_config']->dropbox_client_id, '')) == 0 ||
                strlen(''.isset_or($GLOBALS['g_config']->dropbox_client_secret, '')) == 0)
            {
                die('This function is presently not available.');
            }
        }

        if (isset_or($params['service'], '') == 'googledrive' || isset_or($params['service'], '') == 'googlesheets')
        {
            if (strlen(''.isset_or($GLOBALS['g_config']->googledrive_client_id, '')) == 0 ||
                strlen(''.isset_or($GLOBALS['g_config']->googledrive_client_secret, '')) == 0)
            {
                die('This function is presently not available.');
            }
        }

        $auth_params['redirect'] = (IS_SECURE() ? 'https':'http') . '://' . $_SERVER['HTTP_HOST'] . '/a/connectionauthcallback';

        $eid = isset_or($params['eid'], false);
        $connection = \Flexio\Object\Connection::load($eid);
        if ($connection === false)
        {
            die('The specified connection is unavailable');
        }

        $result = $connection->authenticate($auth_params);

        // if authenticating returns a string, it's a redirect url
        if ($result !== true && $result !== false)
        {
            fxdebug('redirect URL is: '. $result);
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
        if (isset($auth_params['state']))
        {
            $state = json_decode(base64_decode($auth_params['state']),true);
            $eid = isset_or($state['eid'], false);
            $connection = \Flexio\Object\Connection::load($eid);
        }
        else
        {
            $eid = isset_or($params['eid'], false);
            $connection = \Flexio\Object\Connection::load($eid);
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
            $project_eid = $params['object_eid'];

        $project_url = '/app/project' . (isset($project_eid) ? "?eid=$project_eid" : '');

        if (\System::isLoggedIn() && isset($email) && $email === \System::getCurrentUserName())
        {
            // CASE 1: user is logged in, and user is the same as the one
            // with the link; try to access the shared project (TODO:
            // need to add encryption to incoming URL to avoid email
            // spoofing)
            $redirect = $project_url;
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

            $target_user = \Flexio\Object\User::load($email);
            if ($target_user !== false)
                $target_user_info = $target_user->get();

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

                    // TODO: remove the beta invite code when it's no longer necessary
                    $beta_invite_code = 'invite_code=e0miwu7qkv89h3rlrnst25e3jdxbbrpn';
                    $redirect = "/app/signup?$beta_invite_code&ref=share_auth&email=".urlencode($email)."&user_eid=$target_user_eid&code=$target_user_verified_code&redirect=".urlencode($project_url);
                    $this->_redirect($redirect);
                    return;
                }
                 else
                {
                    // CASE 4: user isn't logged in and they have an account
                    // that's also verified; take them to the sign in page
                    $redirect = '/app/signin?ref=share_auth&redirect='.urlencode($project_url);
                    $this->_redirect($redirect);
                    return;
                }
            }
        }
    }
}
