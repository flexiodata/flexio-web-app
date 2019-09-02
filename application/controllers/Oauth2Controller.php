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
        $connection_eid = $params['eid'] ?? '';

        $auth_params = array();
        $auth_params['redirect'] = self::getCallbackUrl();

        // if the service parameter is set, perform a check upfront if
        // the service is valid; note: the service parameter isn't required
        // since the connection has a connection_type which correspondes
        // to the service; this check is here because the check may be
        // useful for debugging
        if (isset($params['service']))
        {
            if (self::isValidService($params['service']) === false)
                die('This function is presently not available.');
        }

        try
        {
            $connection = \Flexio\Object\Connection::load($connection_eid);
            if ($connection->getStatus() === \Model::STATUS_DELETED)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

            $service_oauth_url = $connection->authenticateInit($auth_params);
            //fxdebug('redirect URL is: '. $service_oauth_url);
            $this->_redirect($service_oauth_url);
        }
        catch (\Flexio\Base\Exception $e)
        {
            die('The specified connection is unavailable');
        }

        die("Authentication to this service is presently not available.");
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
                $eid = $state['eid'] ?? '';
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

    private static function isValidService(string $service) : bool
    {
        if ($service === \Flexio\Services\Factory::TYPE_DROPBOX)
        {
            if (strlen(''.($GLOBALS['g_config']->dropbox_client_id ?? '')) == 0 ||
                strlen(''.($GLOBALS['g_config']->dropbox_client_secret ?? '')) == 0)
            {
                return false;
            }

            return true;
        }

        if ($service === \Flexio\Services\Factory::TYPE_BOX)
        {
            if (strlen(''.($GLOBALS['g_config']->box_client_id ?? '')) == 0 ||
                strlen(''.($GLOBALS['g_config']->box_client_secret ?? '')) == 0)
            {
                return false;
            }

            return true;
        }

        if ($service === \Flexio\Services\Factory::TYPE_GITHUB)
        {
            if (strlen(''.($GLOBALS['g_config']->github_client_id ?? '')) == 0 ||
                strlen(''.($GLOBALS['g_config']->github_client_secret ?? '')) == 0)
            {
                return false;
            }

            return true;
        }

        if ($service === \Flexio\Services\Factory::TYPE_GOOGLEDRIVE)
        {
            // note: google drive and google sheets use the same config items
            if (strlen(''.($GLOBALS['g_config']->googledrive_client_id ?? '')) == 0 ||
                strlen(''.($GLOBALS['g_config']->googledrive_client_secret ?? '')) == 0)
            {
                return false;
            }

            return true;
        }

        if ($service === \Flexio\Services\Factory::TYPE_GOOGLESHEETS)
        {
            // note: google drive and google sheets use the same config items
            if (strlen(''.($GLOBALS['g_config']->googledrive_client_id ?? '')) == 0 ||
                strlen(''.($GLOBALS['g_config']->googledrive_client_secret ?? '')) == 0)
            {
                return false;
            }

            return true;
        }

        if ($service === \Flexio\Services\Factory::TYPE_GOOGLECLOUDSTORAGE)
        {
            if (strlen(''.($GLOBALS['g_config']->googlecloudstorage_client_id ?? '')) == 0 ||
                strlen(''.($GLOBALS['g_config']->googlecloudstorage_client_secret ?? '')) == 0)
            {
                return false;
            }

            return true;
        }

        if ($service === \Flexio\Services\Factory::TYPE_GMAIL)
        {
            if (strlen(''.($GLOBALS['g_config']->gmail_client_id ?? '')) == 0 ||
                strlen(''.($GLOBALS['g_config']->gmail_client_secret ?? '')) == 0)
            {
                return false;
            }

            return true;
        }

        return false;
    }

    private static function getCallbackUrl() : string
    {
        return (IS_SECURE() ? 'https':'http') . '://' . $_SERVER['HTTP_HOST'] . '/oauth2/callback';
    }
}
