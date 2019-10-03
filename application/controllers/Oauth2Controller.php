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
        // note: this function initiates the oauth process

        $this->renderRaw();
        $params = $this->getRequest()->getParams();

        // note: connection_type is needed to create the appropriate oauth service;
        // connection_eid is optional; if a connection_eid is specified, information
        // from the oauth authentication process will be saved to the connection in
        // the callback
        $connection_type = $params['service'] ?? '';
        $connection_eid = $params['eid'] ?? '';

        // TODO: if a connection is specified, make sure it exists and the requesting
        // user has access to it

        try
        {
            // STEP 1: prepare the params to pass to the remote service, including
            // a state parameter the stores the connection eid and the redirect url
            $state = array(
                'connection_type' => $connection_type,
                'connection_eid' => $connection_eid
            );
            $connection_info = array(
                'state' => base64_encode(json_encode($state)),
                'redirect' => self::getCallbackUrl()
            );

            // STEP 2: get the remote service authorization url to redirect to authenticate
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

        // get various possible oauth params
        $params = $this->getRequest()->getParams();
        $state = $params['state'] ?? false;
        $error = $params['error'] ?? false;
        $code = $params['code'] ?? false;

        // state should always be set since we pass it; if it isn't, there's no
        // further information we can act on
        if ($state === false)
        {
            $this->renderPage();
            return;
        }

        // get the information from the state
        $state = json_decode(base64_decode($state),true);
        $connection_type = $state['connection_type'] ?? ''; // required
        $connection_eid = $state['connection_eid'] ?? '';   // optional

        // create an initial set of connection properties to save
        $connection_properties_to_save = array();
        $connection_properties_to_save['connection_status'] = \Model::CONNECTION_STATUS_ERROR;

        try
        {
            // STEP 1: check for any errors
            if ($error === true || $code === false)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::CONNECTION_FAILED);

            // STEP 2: finish the authentication process
            $connection_info = array(
                'code' => $code,
                'redirect' => self::getCallbackUrl()
            );
            $service = \Flexio\Services\Factory::create($connection_type, $connection_info);

            // STEP 3: make sure the service is authenticated
            if (!($service instanceof \Flexio\IFace\IOAuthConnection))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
            if ($service->authenticated() === false)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::CONNECTION_FAILED);

            // STEP 4: set the connection info to save from the service
            $connection_properties_to_save['connection_status'] = \Model::CONNECTION_STATUS_AVAILABLE;
            $connection_properties_to_save['connection_info'] = $service->get();
        }
        catch (\Flexio\Base\Exception $e)
        {
            // fall through
        }

        // if a connection was specified, save the info to the connection
        try
        {
            $connection = \Flexio\Object\Connection::load($connection_eid);
            if ($connection->getStatus() === \Model::STATUS_DELETED)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
            $connection->set($connection_properties_to_save);
        }
        catch (\Flexio\Base\Exception $e)
        {
            // fall through
        }

        $this->view->flexio_oauth_info = json_encode($connection_properties_to_save);
        $this->renderPage();
    }

    private function renderPage()
    {
        // render this page, so it can close the popup and do the callback
        // to the parent/app window
        $this->setViewTitle(_('Connection Authorization - Flex.io'));
        $this->renderPublic();
        $this->render();
    }

    private static function getCallbackUrl() : string
    {
        // examples:
        // https://www.flex.io/oauth2/callback
        // https://test.flex.io/oauth2/callback
        // https://localhost/oauth2/callback

        return (IS_SECURE() ? 'https':'http') . '://' . $_SERVER['HTTP_HOST'] . '/oauth2/callback';
    }
}
