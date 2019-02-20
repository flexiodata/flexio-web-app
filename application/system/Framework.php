<?php
/**
 *
 * Copyright (c) 2008-2011, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   David Z. Williams; Benjamin I. Williams
 * Created:  2008-09-05
 *
 * @package flexio
 * @subpackage System
 */


declare(strict_types=1);
namespace Flexio\System;


class FrameworkRequest
{
    public $controller = '';
    public $action = '';
    public $url_parts = array();
    public $params = array();

    public function setActionName(string $value) : void
    {
        $this->action = $value;
    }

    public function getActionName() : string
    {
        return $this->action;
    }

    public function setControllerName(string $value) : void
    {
        $this->controller = $value;
    }

    public function getControllerName() : string
    {
        return $this->controller;
    }

    public function getUrlParts() : array
    {
        return $this->url_parts;
    }

    public function getUrlPathPart(int $idx, string $default = null) : ?string
    {
        if ($idx >= count($this->url_parts))
            return $default;
             else
            return $this->url_parts[$idx];
    }

    public function getHttpOrigin() : string
    {
        return $_SERVER['HTTP_ORIGIN'] ?? '';
    }

    public function getHttpHost() : string
    {
        return $_SERVER['HTTP_HOST'] ?? '';
    }

    public function getUserAgent() : string
    {
        return $_SERVER['HTTP_USER_AGENT'] ?? '';
    }

    public function getIpAddress() : string
    {
        return $_SERVER['REMOTE_ADDR'] ?? '';
    }

    public function getUri() : string
    {
        return $_SERVER['REQUEST_URI'] ?? '';
    }

    public function getMethod() : string
    {
        return $_SERVER['REQUEST_METHOD'] ?? '';
    }

    public function getQuery(string $key, string $default = null) : ?string
    {
        return (isset($_GET[$key])) ? $_GET[$key] : $default;
    }

    public function setParam(string $key, string $value) : void
    {
        $this->params[$key] = $value;
    }

    public function getParam(string $key, string $default = null) : ?string
    {
        if (isset($this->params[$key]))
            return $this->params[$key];
        else if (isset($_GET[$key]))
            return $_GET[$key];
        else if (isset($_POST[$key]))
            return $_POST[$key];
        else
            return $default;
    }

    public function getHeaders() : array
    {
        return \getallheaders();
    }

    public function getParams() : array
    {
        $result = array();
        if (isset($_POST)) $result += $_POST;
        if (isset($_GET))  $result += $_GET;
        $result += $this->params;
        return $result;
    }

    public function getQueryParams() : array
    {
        $result = array();
        if (isset($_GET))  $result += $_GET;
        return $result;
    }

    public function getPostParams() : array
    {
        $result = array();
        if (isset($_POST)) $result += $_POST;
        return $result;
    }
}


class FrameworkResponse
{
    public $body = '';

    public function setBody(string $body) : void
    {
        $this->body = $body;
    }

    public function getBody() : string
    {
        return $this->body;
    }
}


class FrameworkViewHeadLinks
{
    public $arr = array();

    public function appendStylesheet(string $link) : void
    {
        $this->arr[] = array('type' => 'style', 'content' => $link);
    }

    public function __toString()
    {
        $ret = '';
        foreach ($this->arr as $item)
        {
            if ($item['type'] == 'style')
                $ret .= '<link rel="stylesheet" href="' . $item['content'] . '">';
        }
        return $ret;
    }
}


class FrameworkViewHeadStyle
{
    public $arr = array();

    public function appendStyle(string $style) : void
    {
        $this->arr[] = $style;
    }

    public function __toString()
    {
        $ret = '';
        foreach ($this->arr as $item)
            $ret .= $item;
        return '<style type="text/css">' . $ret . '</style>';
    }
}


class FrameworkViewInlineScript
{
    public $arr = array();

    public function captureStart() : void
    {
        ob_start();
    }

    public function captureEnd() : void
    {
        $this->arr[] = array('type' => 'script', 'content' => ob_get_clean());
    }

    public function appendFile(string $file) : void
    {
        $this->arr[] = array('type' => 'file', 'content' => $file);
    }

    public function __toString()
    {
        $ret = '';
        foreach ($this->arr as $item)
        {
            if ($item['type'] == 'script')
                $ret .= ('<script type="text/javascript">' . $item['content'] . '</script>');
            else if ($item['type'] == 'file')
                $ret .= ('<script type="text/javascript" src="'. $item['content'] . '"></script>');
        }

        return $ret;
    }
}


class FrameworkView
{
    public $head_links = null;
    public $head_style = null;
    public $head_title = '';
    public $inline_script = null;

    public function init()
    {
    }

    public function headLink() : \Flexio\System\FrameworkViewHeadLinks
    {
        if (!$this->head_links)
            $this->head_links = new FrameworkViewHeadLinks;
        return $this->head_links;
    }

    public function headStyle() : \Flexio\System\FrameworkViewHeadStyle
    {
        if (!$this->head_style)
            $this->head_style = new FrameworkViewHeadStyle;
        return $this->head_style;
    }

    public function headTitle(string $title = null) : string
    {
        if (!is_null($title))
            $this->head_title = $title;
        return '<title>' . $this->head_title . '</title>';
    }

    public function inlineScript() : \Flexio\System\FrameworkViewInlineScript
    {
        if (!$this->inline_script)
            $this->inline_script = new FrameworkViewInlineScript;
        return $this->inline_script;
    }

    public function render(string $file) : string
    {
        if (false === strpos($file, DIRECTORY_SEPARATOR))
        {
            $file = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . $file;
        }

        ob_start();
        include_once $file;
        return ob_get_clean();
    }
}


class FxControllerAction
{
    protected $view = null;
    protected $request = null;
    protected $response = null;
    protected $render_raw = false;
    private $contoller = '';
    private $action = '';
    public $forward = null;

    public function __construct()
    {
    }

    public function initControllerAction(\Flexio\System\FrameworkView $view,
                                         \Flexio\System\FrameworkRequest $request,
                                         \Flexio\System\FrameworkResponse $response) : void
    {
        $this->view = $view;
        $this->request = $request;
        $this->response = $response;
        $this->init();
    }

    public function init() : void
    {
    }

    public function invokeAction(string $controller, string $action) : void
    {
        if (!method_exists($this, $action . 'Action'))
        {
            http_response_code(404);
            echo "Invalid action";
            exit();
        }

        $this->controller = $controller;
        $this->action = $action;

        call_user_func_array(array($this, $action . 'Action'), array());
    }

    public function render(string $action = null) : void
    {
        if (is_null($action))
            $action = $this->action;

        if (!$this->render_raw)
        {
            $file = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . $this->controller . DIRECTORY_SEPARATOR . $action . '.phtml';
            FxControllerAction::getResponse()->setBody($this->view->render($file));
        }
    }

    public function renderScript(string $file) : void
    {
        if (!$this->render_raw)
        {
            $file = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . $file;
            FxControllerAction::getResponse()->setBody($this->view->render($file));
        }
    }

    public function getRequest() : \Flexio\System\FrameworkRequest
    {
        return $this->request;
    }

    public function getResponse() : \Flexio\System\FrameworkResponse
    {
        if (!$this->response)
            $this->response = new FrameworkResponse;
        return $this->response;
    }

    public function getView() : \Flexio\System\FrameworkView
    {
        if (!$this->view)
        {
            $view = new FrameworkView;
        }
        return $this->view;
    }

    public function getRenderRaw() : bool
    {
        return $this->render_raw;
    }

    public function _redirect(string $location) : void
    {
        header("Location: $location");
        exit(0);
    }

    public function _forward(string $action, string $controller = null) : void
    {
        $this->forward = array('controller' => $controller, 'action' => $action);
    }

    /**
     * simple error when an action doesn't exist
     *
     * @return void
     */
    public function __call(string $name, array $args) : void
    {
        throw new \Exception('Sorry, the requested action is unavailable');
    }

    protected function renderRaw() : void
    {
        $this->view->render_raw = true;
        $this->render_raw = true;
    }

    protected function renderBodyOnly() : void
    {
        $this->view->render_body_only = true;
        $this->render_body_only = true;
    }

    protected function renderPublic() : void
    {
        $this->view->render_public = true;
        $this->render_public = true;
    }

    protected function renderEmbed() : void
    {
        $this->view->render_embed = true;
        $this->render_embed = true;
    }

    protected function setViewTitle(string $title) : void
    {
        $this->view->title = $title;
    }
}


abstract class FrameworkPlugin
{
    protected $request;
    protected $response;
    protected $view;

    public function setRequest(\Flexio\System\FrameworkRequest $request) : void
    {
        $this->request = $request;
    }

    public function getRequest() : \Flexio\System\FrameworkRequest
    {
        return $this->request;
    }

    public function setResponse(\Flexio\System\FrameworkResponse $response) : void
    {
        $this->response = $response;
    }

    public function getResponse() : \Flexio\System\FrameworkResponse
    {
        return $this->response;
    }

    public function setView(\Flexio\System\FrameworkView $view) : void
    {
        $this->view = $view;
    }

    public function getView() : \Flexio\System\FrameworkView
    {
        return $this->view;
    }

    public function preDispatch() : void
    {
    }

    public function postDispatch() : void
    {
    }

    public function dispatchLoopShutdown() : void
    {
    }
}


class Framework
{
    protected static $_instance = null;
    protected $prefix = null;
    protected $suffix = null;
    protected $plugins = array();

    public static function getInstance() : \Flexio\System\Framework
    {
        if (null === self::$_instance)
            self::$_instance = new self();

        return self::$_instance;
    }

    public function setControllerPrefix(string $prefix) : void
    {
        $this->prefix = $prefix;
    }

    public function setControllerSuffix(string $suffix) : void
    {
        $this->suffix = $suffix;
    }

    public function registerPlugin(\Flexio\System\FlexioPlugin $plugin) : void
    {
        $this->plugins[] = $plugin;
    }

    public function dispatch() : void
    {
        $uri = $_SERVER['REQUEST_URI'];

        // trim off any GET query
        $pos = strpos($uri, '?');
        if ($pos !== false)
            $uri = substr($uri, 0, $pos);

        // retrieve the controller and action names

        $parts = explode('/', $uri);
        $parts = array_values(array_diff($parts, array(''))); // remove empty strings

        $controller = (count($parts) > 0) ? $parts[0] : 'index';
        $action = (count($parts) > 1) ? $parts[1] : 'index';

        // check for dangerous chars
        if (strpbrk($controller, "./\\") !== false)
            die("Bad controller name");
        if (strpbrk($action, "./\\") !== false)
            die("Bad action name");

        // create necessary objects
        $response = new FrameworkResponse;
        $request = new FrameworkRequest;
        $request->controller = $controller;
        $request->action = $action;
        $request->url_parts = $parts;
        $request->REQUEST_URI = $uri;
        if (strlen($_SERVER['QUERY_STRING']) > 0)
            $request->REQUEST_URI .= '?' . $_SERVER['QUERY_STRING'];
        $view = new FrameworkView;
        $view->request = $request;

        $obj = null;

        while (true)
        {
            foreach ($this->plugins as $plugin)
            {
                $plugin->setRequest($request);
                $plugin->setResponse($response);
                $plugin->setView($view);
                $plugin->preDispatch();
            }

            // create the controller class name
            $classname = ucfirst($request->controller);

            if (isset($this->prefix))
                $classname = $this->prefix . $classname;

            if (isset($this->suffix))
                $classname = $classname . $this->suffix;

            if (!class_exists($classname))
                die("Invalid controller class $classname");

            if (!$obj)
                $obj = new $classname;

            $obj->initControllerAction($view, $request, $response);
            $obj->invokeAction($request->controller, $request->action);

            if ($obj->getRenderRaw())
                return;

            if ($obj->forward === null)
            {
                break;
            }
             else
            {
                if (is_null($obj->forward['controller']))
                {
                    // re-use same controller object with new action
                    $request->action = $obj->forward['action'];
                }
                 else
                {
                    $request->controller = $obj->forward['controller'];
                    $request->action = $obj->forward['action'];
                    $obj = null; // force next loop iteration to create new controller object
               }
            }
        }

        foreach ($this->plugins as $plugin)
        {
            $plugin->dispatchLoopShutdown();
        }

        echo $obj->getResponse()->getBody();
    }
}
