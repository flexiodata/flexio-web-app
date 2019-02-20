<?php
/*!
 *
 * Copyright (c) 2009-2011, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams
 * Created:  2009-10-09
 *
 */


// stub is a file included by other processing scripts.  The purpose of stub
// is to process the command line parameters to set up a valid operating
// environment for GP in which scripts can access the appropriate databases


include_once __DIR__ . '/../application/bootstrap.php';


global $g_context;
global $g_context_param;


if (!isset($g_context))
    $g_context = array();
$g_context_param = "";


if (isset($argv))
{
    foreach ($argv as $arg)
    {
        global $g_params;
        global $g_context_param;
        global $g_store;

        if (substr($arg,0,10) == "--context=")
        {
            // parameters are passed to the program as a base64-encoded
            // php-serialized object.  This code restores the parameters
            // object from the passed string

            $g_context_param = trim(substr($arg,10));
            $context_decoded = base64_decode($g_context_param);
            $g_context = unserialize($context_decoded);

            if (isset($g_context['identity']) && strlen($g_context['identity']) > 0)
            {
                \Flexio\System\System::unserializeIdentity($g_context['identity']);
            }

            if (isset($g_context['lang']))
            {
                $params = array('locale_language' => $g_context['lang']);
                \Flexio\System\System::setLocaleSettings($params);
            }
        }


        if (substr($arg,0,2) == "--")
        {
            $arginfo = explode('=',substr($arg,2));
            if (count($arginfo) == 2)
            {
                if (!isset($g_context['params']))
                    $g_context['params'] = array();
                $g_context['params'][$arginfo[0]] = $arginfo[1];
            }
        }
    }
}


function getAllParams()
{
    global $g_context;
    if (isset($g_context['params']))
        return $g_context['params'];
    return null;
}

function getParam($p)
{
    global $g_context;
    if (isset($g_context['params']) && isset($g_context['params'][$p]))
        return $g_context['params'][$p];
    return null;
}
