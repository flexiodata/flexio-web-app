<?php
/**
 *
 * Copyright (c) 2017, Gold Prairie LLC. All rights reserved.
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
        if (isset($GLOBALS['g_config']->debug_error_log))
        {
            // specifying DEBUG_BACKTRACE_IGNORE_ARGS avoids debug_print_backtrace()
            // throwing an exception itself (for instance, when a closure such as
            // service::read() is in the backtrace)

            ob_start();
            debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
            $data = ob_get_clean();

            $excp = '';
            if (is_string($message))
                $excp = ' Exception message: ' . $message;

            file_put_contents($GLOBALS['g_config']->debug_error_log, "Error '$message' set!$excp\n$data\n\n", FILE_APPEND);
        }

        $local_error = array('code' => $code, 'message' => $message);
        $local_message = json_encode($local_error);

        parent::__construct($local_message);

        $this->code = $code;
    }
}
