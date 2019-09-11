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
        // note: this function begins to the oauth process

        $this->renderRaw();
        $params = $this->getRequest()->getParams();
        $connection_eid = $params['eid'] ?? '';

        try
        {
            // STEP 1: get the connection info to determine the connection type
            $connection = \Flexio\Object\Connection::load($connection_eid);
            if ($connection->getStatus() === \Model::STATUS_DELETED)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

            $connection_info = $connection->get();
            $connection_type = $connection_info['connection_type'];

            // STEP 2: prepare the params to pass to the remote service, including
            // a state parameter the stores the connection eid and the redirect url
            $state = array(
                'eid' => $connection->getEid()
            );

            $auth_params = array();
            $auth_params['state'] = base64_encode(json_encode($state));
            $auth_params['redirect'] = self::getCallbackUrl();

            // STEP 3: get the service class, pass the auth params and get the remote
            // service authorization url to redirect to in order to authenticate
            $service_class = self::getServiceClass($connection_type);
            if (strlen($service_class) === 0)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

            // TODO: right now, the service creation function for each of the services
            // can return multiple types; however, for this phase of the authentication,
            // the response should always be a string; should split out this phase of
            // the authentication into a separate function call for each service; for now,
            // because this function enforces a return type, downstream exception handling
            // will catch the error if the wrong type is returned
            $service_oauth_url = $service_class::create($auth_params);
            //fxdebug('redirect URL is: '. $service_oauth_url);

            if (is_null($service_oauth_url))
            {
                $properties = array();
                $properties['connection_status'] = \Model::CONNECTION_STATUS_ERROR;
                $connection->set($properties);
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::CONNECTION_FAILED);
            }

            // STEP 4: redirect to the remote service
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
        // note: this function completes the oauth process

        $params = $this->getRequest()->getParams();

        // state should always be set since we pass it; if it isn't, we don't know
        // the connection info, so we're done
        if (!isset($params['state']))
        {
            // render this page, so it can close the popup and do the callback
            // to the parent/app window
            $this->setViewTitle(_('Connection Authorization - Flex.io'));
            $this->renderPublic();
            $this->render();
            return;
        }

        try
        {
            // STEP 1: get the connection eid from the state and load it
            $state = json_decode(base64_decode($params['state']),true);
            $eid = $state['eid'] ?? '';
            $connection = \Flexio\Object\Connection::load($eid);
            if ($connection->getStatus() === \Model::STATUS_DELETED)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

            // STEP 2: if an error parameter is passed back by the remote service,
            // then set the connection status to an error
            if (isset($params['error']))
            {
                // set the error code on the connection
                $properties = array();
                $properties['connection_status'] = \Model::CONNECTION_STATUS_ERROR;
                $connection->set($properties);
            }

            // STEP 3: if the code parameter is passed back by the remote service,
            // then get the access token from the code and save it in the connection
            // info
            if (isset($params['code']))
            {
                $auth_params = array();
                $auth_params['code'] = $params['code'];
                $auth_params['redirect'] = self::getCallbackUrl();

                // get the connection properties
                $connection_info = $connection->get();
                $connection_type = $connection_info['connection_type'] ?? '';

                $service_class = self::getServiceClass($connection_type);
                if (strlen($service_class) === 0)
                    throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

                // TODO: right now, the service creation function for each of the services
                // can return multiple types; however, for this phase of the authentication,
                // the response should always be a servcie class; should split out this phase of
                // the authentication into a separate function call for each service
                $service = $service_class::create($auth_params);

                // if the service creation response is null, something went wrong
                if (is_null($service))
                {
                    $properties = array();
                    $properties['connection_status'] = \Model::CONNECTION_STATUS_ERROR;
                    $connection->set($properties);
                    throw new \Flexio\Base\Exception(\Flexio\Base\Error::CONNECTION_FAILED);
                }

                // we're authenticated; get the token
                $tokens = $service->getTokens();

                // DEBUG:
                // file_put_contents('/tmp/tokens.txt', "Tokens :" . json_encode($tokens)."\n", FILE_APPEND);

                $properties = array();
                $properties['connection_status'] = \Model::CONNECTION_STATUS_AVAILABLE;
                $properties['connection_info']['access_token'] = $tokens['access_token'];
                $properties['connection_info']['refresh_token'] = $tokens['refresh_token'];

                if (isset($tokens['expires']))
                    $properties['connection_info']['expires'] = $tokens['expires'];

                if ($connection_type == 'gmail')
                    $properties['connection_info']['email'] = $service_object->retrieveEmailAddress();

                $connection->set($properties);
            }
        }
        catch (\Flexio\Base\Exception $e)
        {
            // fall through
        }

        // render this page, so it can close the popup and do the callback
        // to the parent/app window
        $this->setViewTitle(_('Connection Authorization - Flex.io'));
        $this->renderPublic();
        $this->render();
    }

    private static function getServiceClass(string $service) : string
    {
        if ($service === \Flexio\Services\Factory::TYPE_DROPBOX)
        {
            if (strlen(''.($GLOBALS['g_config']->dropbox_client_id ?? '')) > 0 &&
                strlen(''.($GLOBALS['g_config']->dropbox_client_secret ?? '')) > 0)
            {
                return '\Flexio\Services\Dropbox';
            }
        }

        if ($service === \Flexio\Services\Factory::TYPE_BOX)
        {
            if (strlen(''.($GLOBALS['g_config']->box_client_id ?? '')) > 0 &&
                strlen(''.($GLOBALS['g_config']->box_client_secret ?? '')) > 0)
            {
                return '\Flexio\Services\Box';
            }
        }

        if ($service === \Flexio\Services\Factory::TYPE_GITHUB)
        {
            if (strlen(''.($GLOBALS['g_config']->github_client_id ?? '')) > 0 &&
                strlen(''.($GLOBALS['g_config']->github_client_secret ?? '')) > 0)
            {
                return '\Flexio\Services\GitHub';
            }
        }

        if ($service === \Flexio\Services\Factory::TYPE_GOOGLEDRIVE)
        {
            // note: google drive and google sheets use the same config items
            if (strlen(''.($GLOBALS['g_config']->googledrive_client_id ?? '')) > 0 &&
                strlen(''.($GLOBALS['g_config']->googledrive_client_secret ?? '')) > 0)
            {
                return '\Flexio\Services\GoogleDrive';
            }
        }

        if ($service === \Flexio\Services\Factory::TYPE_GOOGLESHEETS)
        {
            // note: google drive and google sheets use the same config items
            if (strlen(''.($GLOBALS['g_config']->googledrive_client_id ?? '')) > 0 &&
                strlen(''.($GLOBALS['g_config']->googledrive_client_secret ?? '')) > 0)
            {
                return '\Flexio\Services\GoogleSheets';
            }
        }

        if ($service === \Flexio\Services\Factory::TYPE_GOOGLECLOUDSTORAGE)
        {
            if (strlen(''.($GLOBALS['g_config']->googlecloudstorage_client_id ?? '')) > 0 &&
                strlen(''.($GLOBALS['g_config']->googlecloudstorage_client_secret ?? '')) > 0)
            {
                return '\Flexio\Services\GoogleCloudStorage';
            }
        }

        if ($service === \Flexio\Services\Factory::TYPE_LINKEDIN)
        {
            if (strlen(''.($GLOBALS['g_config']->linkedin_client_id ?? '')) > 0 &&
                strlen(''.($GLOBALS['g_config']->linkedin_client_secret ?? '')) > 0)
            {
                return '\Flexio\Services\LinkedIn';
            }
        }

        if ($service === \Flexio\Services\Factory::TYPE_GMAIL)
        {
            if (strlen(''.($GLOBALS['g_config']->gmail_client_id ?? '')) > 0 &&
                strlen(''.($GLOBALS['g_config']->gmail_client_secret ?? '')) > 0)
            {
                return '\Flexio\Services\Gmail';
            }
        }

        return '';
    }

    private static function getCallbackUrl() : string
    {
        return (IS_SECURE() ? 'https':'http') . '://' . $_SERVER['HTTP_HOST'] . '/oauth2/callback';
    }
}
