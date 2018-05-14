<?php
/**
 *
 * Copyright (c) 2010-2011, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams; David Williams
 * Created:  2010-06-03
 *
 * @package flexio
 * @subpackage System
 */


declare(strict_types=1);
namespace Flexio\System;


class FlexioPlugin extends \Flexio\System\FrameworkPlugin
{
    public function preDispatch() : void
    {
        global $g_config;

        \Flexio\System\System::setupSessionAuth();

        $request = $this->getRequest();
        $controller_name = $request->getControllerName();
        $action_name = $request->getActionName();

        // TODO: necessary to convert to lowercase?
        $controller_name = strtolower($controller_name);

        switch ($controller_name)
        {
            // default controller
            default:
            {
                $request->setControllerName('default');
                $request->setActionName('index');

                $view = FlexioPlugin::getView();
                $this->view->render_raw = false;
                $this->view->echo_content = false; // php view fragments are no longer rendered by default

                return;
            }

            // api controllers
            case 'v1':
            {
                $request->setControllerName('v1');
                $request->setActionName('index');

                return;
            }

            case 'api':
            {
                $request->setControllerName('api');
                $request->setActionName('index');

                return;
            }

            case 'app':
            {
                $request->setControllerName('default');
                $request->setActionName('index');

                $view = FlexioPlugin::getView();
                $this->view->render_raw = false;
                $this->view->echo_content = false; // php view fragments are no longer rendered by default

                return;
            }

            // standard controllers
            case 'a':
            case 'install':
            case 'phantom':
            case 'test':
            case 'util':
            {
                if (IS_API_ONLY())
                {
                    header('HTTP/1.0 404 Not Found');
                    exit(0);
                }

                $view = FlexioPlugin::getView();
                $this->view->render_raw = false;
                $this->view->echo_content = false; // php view fragments are no longer rendered by default

                return;
            }
        }
    }

    public function dispatchLoopShutdown() : void
    {
        $view = FlexioPlugin::getView();
        $response = $this->getResponse();

        // set the browser window (or tab) title
        if (isset($view->title))
            $view->headTitle($view->title);
             else
            $view->headTitle('Flex.io');

        // translate the view files (using the <i18n> tags)
        $i18n = new Flexio_View_Filter_Translate();
        $view->content = $i18n->filter($response->getBody());

        // only render the view fragment
        if (isset($view->render_body_only) && $view->render_body_only)
        {
            $response->setBody($view->content);
            return;
        }

        // render public pages (the only pages left that use this are
        // in AController: the share auth and  connection auth callback pages
        if (isset($view->render_public) && $view->render_public)
        {
            $body = $view->render('layout-php.phtml');
            $body = $i18n->filter($body);
            $response->setBody($body);
            return;
        }

        $body = $view->render('layout.phtml');
        $body = $i18n->filter($body);
        $response->setBody($body);
    }
}


class Flexio_View_Filter_Translate
{
    /**
     * Starting delimiter for translation snippets in view
     *
     */
    const I18N_DELIMITER_START = '<i18n';

    /**
     * Ending delimiter for translation snippets in view
     *
     */
    const I18N_DELIMITER_END = '</i18n>';

    /**
     * Filter the value for i18n tags and translate
     *
     * @param string $value
     * @return string
     */
    public function filter(string $value) : string
    {
        $delim_start_len = strlen(Flexio_View_Filter_Translate::I18N_DELIMITER_START);
        $delim_end_len = strlen(Flexio_View_Filter_Translate::I18N_DELIMITER_END);

        $offset = 0;
        while (($start_pos = strpos($value, Flexio_View_Filter_Translate::I18N_DELIMITER_START, $offset)) !== false)
        {
            if (($close_tag = \Flexio\Base\Util::zlstrpos($value, '>', $start_pos)) === false)
                throw new \Exception("Open tag was not terminated after position [$offset]!");

            if (($end_pos = strpos($value, Flexio_View_Filter_Translate::I18N_DELIMITER_END, $close_tag)) === false)
                throw new \Exception("No ending tag after position [$offset] found!");


            $offset = $close_tag + 1;

            $translate = substr($value, $offset, $end_pos - $offset);
            $translate = _($translate);

            $params = array();
            if ($close_tag > $start_pos + $delim_start_len)
            {
                $params = Flexio_View_Filter_Translate::parseTagParams(substr($value, $start_pos + $delim_start_len, $close_tag - ($start_pos + $delim_start_len)));
                foreach ($params as $k => $v)
                    $translate = str_replace('%'.$k, $v, $translate);
            }

            $offset = $end_pos + $delim_end_len;

            if (isset($params['sq']))  // escape single quotes
                $translate = str_replace("'", "\\'", $translate);

            if (isset($params['dq']))  // escape double quotes
                $translate = str_replace('"', "\\\"", $translate);

            $value = substr_replace($value, $translate, $start_pos, $offset - $start_pos);
            $offset = $start_pos;
        }

        return $value;
    }

    public static function parseTagParams(string $str) : array
    {
        $ret = array();

        while (strlen($str) > 0)
        {
            $str = trim($str);

            $space = \Flexio\Base\Util::zlstrpos($str, ' ');
            if ($space === false)
            {
                $piece = $str;
                $str = '';
            }
             else
            {
                $piece = substr($str, 0, $space);
                $str = substr($str, $space+1);
            }

            if (strpos($piece, '=') !== false)
            {
                $k = trim(\Flexio\Base\Util::beforeFirst($piece, '='));
                $v = trim(\Flexio\Base\Util::afterFirst($piece, '='));
            }
             else
            {
                $k = $piece;
                $v = '';
            }

            $vl = strlen($v);
            if ($vl >= 2 && $v[0] == '"' && $v[$vl-1] == '"')
                $v = substr($v, 1, $vl-2);
            $v = stripcslashes($v);

            $ret[$k] = $v;
        }

        return $ret;
    }
}
