<?php
/**
 *
 * Copyright (c) 2008-2011, Gold Prairie LLC. All rights reserved.
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


class Oauth2Controller extends \Flexio\System\FxControllerAction
{
    public function init() : void
    {
        parent::init();
    }

    public function connectAction() : void
    {
        // TODO: route this request through the UI?

        $this->renderRaw();
        $params = $this->getRequest()->getParams();

        $auth_params = array();
        $auth_params['redirect'] = self::getCallbackUrl();

        if (isset($params['service']))
            $auth_params['service'] = $params['service'];

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

        if (($params['service'] ?? '') == 'googlecloudstorage')
        {
            if (strlen(''.($GLOBALS['g_config']->googlecloudstorage_client_id ?? '')) == 0 ||
                strlen(''.($GLOBALS['g_config']->googlecloudstorage_client_secret ?? '')) == 0)
            {
                die('This function is presently not available.');
            }
        }

        if (($params['service'] ?? '') == 'gmail')
        {
            if (strlen(''.($GLOBALS['g_config']->gmail_client_id ?? '')) == 0 ||
                strlen(''.($GLOBALS['g_config']->gmail_client_secret ?? '')) == 0)
            {
                die('This function is presently not available.');
            }
        }


        $eid = $params['eid'] ?? false;

        $connection = false;
        try
        {
            $connection = \Flexio\Object\Connection::load($eid);
            if ($connection->getStatus() === \Model::STATUS_DELETED)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
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

    public function callbackAction() : void
    {
        $params = $this->getRequest()->getParams();

        $auth_params = array();
        $auth_params['redirect'] = self::getCallbackUrl();

        if (isset($params['code']))
            $auth_params['code'] = $params['code'];
        if (isset($params['state']))
            $auth_params['state'] = $params['state'];

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
                    throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
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
            $result = $connection->authenticateCallback($auth_params);

        // render this page, so it can close the popup
        // and do the callback to the parent/app window
        $this->setViewTitle(_('Connection Authorization - Flex.io'));
        $this->renderPublic();
        $this->render();
    }

    private static function getCallbackUrl() : string
    {
        return (IS_SECURE() ? 'https':'http') . '://' . $_SERVER['HTTP_HOST'] . '/oauth2/callback';
    }
}
