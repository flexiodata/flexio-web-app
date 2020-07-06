<?php
/**
 *
 * Copyright (c) 2017, Flex Research LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2017-03-20
 *
 * @package flexio
 * @subpackage Base
 */


declare(strict_types=1);
namespace Flexio\Base;


class Exception extends \Exception
{
    public function __construct($code, $message = null)
    {
        $local_error = array('code' => $code, 'message' => $message);
        $local_message = json_encode($local_error);

        parent::__construct($local_message);
        $this->code = $code;
    }
}
