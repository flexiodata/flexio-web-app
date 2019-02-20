<?php
/**
 *
 * Copyright (c) 2009-2011, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   David Z. Williams
 * Created:  2009-05-22
 *
 * @package flexio
 * @subpackage Controller
 */


declare(strict_types=1);
namespace Flexio\Controllers;


class ApiController extends \Flexio\System\FxControllerAction
{
    public function init() : void
    {
        parent::init();
        $this->renderRaw();
    }

    public function indexAction() : void
    {
        $request = $this->getRequest();
        \Flexio\Api\Api::request($request);
    }
}
