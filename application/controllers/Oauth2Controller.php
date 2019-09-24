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

            // STEP 2: prepare the params to pass to the remote service, including
            // a state parameter the stores the connection eid and the redirect url
            $state = array(
                'eid' => $connection->getEid()
            );

            $connection_properties = $connection->get();
            $connection_type = $connection_properties['connection_type'] ?? '';
            $connection_info = array(
                'state' => base64_encode(json_encode($state)),
                'redirect' => self::getCallbackUrl()
            );

            // STEP 3: get the remote service authorization url to redirect to authenticate
            $service = \Flexio\Services\Factory::create($connection_type, $connection_info);
            if (!($service instanceof \Flexio\IFace\IOAuthConnection))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

            $service_oauth_url = $service->getAuthorizationUri();
            if (strlen($service_oauth_url) === 0)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

            $this->_redirect($service_oauth_url);
        }
        catch (\Flexio\Base\Exception $e)
        {
            die("Authentication to this service is presently not available.");
        }
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

            // STEP 2: if an error parameter is passed back by the remote
            // service, then set the connection status to an error
            if (isset($params['error']) || !isset($params['code']))
            {
                // set the error code on the connection
                $properties = array();
                $properties['connection_status'] = \Model::CONNECTION_STATUS_ERROR;
                $connection->set($properties);
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
            }

            // STEP 3: get the access token from the code and save it in the
            // connection info
            $connection_properties = $connection->get();
            $connection_type = $connection_properties['connection_type'] ?? '';
            $connection_info = array(
                'code' => $params['code'],
                'redirect' => self::getCallbackUrl()
            );

            // sanity check: the service should be an oauth type service
            $service = \Flexio\Services\Factory::create($connection_type, $connection_info);
            if (!($service instanceof \Flexio\IFace\IOAuthConnection))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

            // if the service isn't authenticated, something went wrong
            if ($service->authenticated() === false)
            {
                $properties = array();
                $properties['connection_status'] = \Model::CONNECTION_STATUS_ERROR;
                $connection->set($properties);
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::CONNECTION_FAILED);
            }

            // DEBUG:
            // $connection_info = $service->get();
            // file_put_contents('/tmp/tokens.txt', "Tokens :" . json_encode($connection_info)."\n", FILE_APPEND);

            // save the connection info from the service
            $properties = array();
            $properties['connection_status'] = \Model::CONNECTION_STATUS_AVAILABLE;
            $properties['connection_info'] = $service->get();
            $connection->set($properties);
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

    private static function getCallbackUrl() : string
    {
        return (IS_SECURE() ? 'https':'http') . '://' . $_SERVER['HTTP_HOST'] . '/oauth2/callback';
    }
}
