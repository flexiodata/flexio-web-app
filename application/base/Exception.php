<?php
/**
 *
 * Copyright (c) 2017, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2017-03-20
 *
 * @package flexio
 * @subpackage Base
 */


namespace Flexio\Base;


class Exception extends \Exception
{

    public function __construct($code, $message = '')
    {
        if (isset($GLOBALS['g_config']->debug_error_log))
        {
            ob_start();
            debug_print_backtrace();
            $data = ob_get_clean();

            $excp = '';
            if ($message)
                $excp = ' Exception message: ' . $message;

            file_put_contents($GLOBALS['g_config']->debug_error_log, "Error '$message' set!$excp\n$data\n\n", FILE_APPEND);
        }

        $local_error = array('code' => $code, 'message' => $message);
        $local_message = json_encode($local_error);

        parent::__construct($local_message);
    }
}
