<?php
/**
 *
 * Copyright (c) 2009-2011, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   David Z. Williams
 * Created:  2009-04-16
 *
 * @package flexio
 * @subpackage Controller
 */


declare(strict_types=1);
namespace Flexio\Controllers;


class PhantomController extends \Flexio\System\FxControllerAction
{
    public function init() : void
    {
        $this->renderRaw();
        parent::init();
    }

    public static function renderWorkspaceThumbnail(array $params) : string
    {
        // get phantomjs executable path
        $exe = \Flexio\System\System::getBinaryPath('phantomjs');

        // get phantomjs script path
        $js_script_path = \Flexio\System\System::getBaseDirectory() . DIRECTORY_SEPARATOR . 'scripts' . DIRECTORY_SEPARATOR . 'phantomjs' . DIRECTORY_SEPARATOR . 'render-workspace-thumbnail.js';

        // run phantomjs
        $params_file = self::createParamsFile($params);

        // set ssl-protocol to 'any' because, right now, phantomjs defaults to sslv3, which is deprecated
        $exec_str = "$exe --ignore-ssl-errors=true --ssl-protocol=any \"$js_script_path\" \"$params_file\"";

        // for debugging
        $fp = popen($exec_str, "r");
        $output = '';
        while (!feof($fp))
        {
            $buffer = fgets($fp);
            if (strlen($buffer) > 0) $output .= $buffer;
        }
        $retcode = pclose($fp);

        @unlink($params_file);

        return $output;
    }

    private static function createParamsFile(array $params) : string
    {
        // get the domain name and session id
        $domain = $_SERVER['SERVER_NAME'];
        $sess_id = session_id();

        // include domain and session id in the params file
        $args = $params;
        $args['domain'] = $domain;
        $args['session_id'] = $sess_id;

        if (isset($params['url']))
        {
            $url = self::createFullUri($params['url']);
            $args['url'] = urlencode($url);
        }

        $params_file = \Flexio\Base\File::getTempFilename('json');
        file_put_contents($params_file, json_encode($args));
        chmod($params_file, 0600);

        return $params_file;
    }

    private static function createFullUri(string $url) : string
    {
        // get the domain name
        $domain = $_SERVER['SERVER_NAME'];

        if (strpos($url, 'http://') === false && strpos($url, 'https://') === false)
        {
            // build up a full url string to pass to phantomjs
            $url = trim($url, " /");
            $url = "https://$domain/$url";
        }

        return $url;
    }
}
