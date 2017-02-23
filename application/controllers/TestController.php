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


namespace Flexio\Controllers;


// calls to the TestController are only allowed on localhost for
// now, and calls cut off when not running in debug model

class TestController extends \Flexio\System\FxControllerAction
{
    public function init()
    {
        parent::init();
    }

    public function constantsAction()
    {
        $constants = array();
        $constants['EID_TYPE_OBJECT']     = \Model::TYPE_OBJECT;
        $constants['EID_TYPE_USER']       = \Model::TYPE_USER;
        $constants['EID_TYPE_PROJECT']    = \Model::TYPE_PROJECT;
        $constants['EID_TYPE_PIPE']       = \Model::TYPE_PIPE;
        $constants['EID_TYPE_STREAM']     = \Model::TYPE_STREAM;
        $constants['EID_TYPE_CONNECTION'] = \Model::TYPE_CONNECTION;
        $constants['EID_TYPE_COMMENT']    = \Model::TYPE_COMMENT;
        $constants['EID_TYPE_PROCESS']    = \Model::TYPE_PROCESS;

        $result = json_encode($constants);
        echo $result;
    }

    public function phpinfoAction()
    {
        // PHP CONFIG INFO
        if (!IS_TESTING())
            return;

        $this->renderRaw();
        phpinfo();
    }

    public function embedAction()
    {
        // EMBED TEST
        if (!IS_TESTING())
            return;

        $this->view->echo_content = true;
        $this->render();
    }

    public function unitAction()
    {
        // TEST SUITE
        if (!IS_TESTING())
            return;

        $this->view->echo_content = true;
        $this->render();
    }

    public function gridAction()
    {
        // GRID TEST
        if (!IS_TESTING())
            return;

        $this->view->echo_content = true;
        $this->render();
    }

    public function waitAction()
    {
        if (!IS_TESTING())
            return;

        $this->renderRaw();

        sleep(20);
        echo "Done Waiting ". time();
    }

    public function echoAction()
    {
        if (!IS_TESTING())
            return;

        $this->renderRaw();
        echo "Echo ". time();
    }

    public function emptyAction()
    {
        if (!IS_TESTING())
            return;

        // keep this function empty
        $this->renderRaw();
        echo '{}';
    }

    public function benAction()
    {
        if (!IS_TESTING())
            return;

        // keep this function empty
        $this->renderRaw();
                $actual = \Flexio\Tests\TestUtil::evalExpressionNative('to_char(0,"9.9$")');
                var_dump($actual);
    }

    public function uploadAction()
    {
        // note: following is a simple form to echo raw multipart data
        if (!IS_TESTING())
            return;

        // pass any encode parameter to the output
        $encode = isset_or($_GET['encode'], 'false');
        $encode = (string)$encode;
        $this->renderRaw();

        $simple_upload = <<<EOD
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <title>upload</title>
</head>
<body>
<form action="/test/uploadresult?encode=$encode" method="post" enctype="multipart/form-data">
  <p><input type="file" name="file1">
  <p><button type="submit">Submit</button>
</form>
</body>
</html>
EOD;

        $multiple_upload = <<<EOD
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <title>upload</title>
</head>
<body>
<form action="/test/uploadresult?encode=$encode" method="post" enctype="multipart/form-data">
  <p><input type="file" name="file1">
  <p><input type="file" name="file2">
  <p><input type="file" name="file3">
  <p><button type="submit">Submit</button>
</form>
</body>
</html>
EOD;

        $multiple_upload_with_fields = <<<EOD
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <title>upload</title>
</head>
<body>
<form action="/test/uploadresult?encode=$encode" method="post" enctype="multipart/form-data">
  <p><input type="text" name="text1" value="abcde">
  <p><input type="text" name="text2" value="&#x03B1;&#x03B2;&#x03B3;&#x03B4;&#x03B5;">
  <p><input type="file" name="file1">
  <p><input type="file" name="file2">
  <p><input type="file" name="file3">
  <p><button type="submit">Submit</button>
</form>
</body>
</html>
EOD;

        echo($simple_upload);
    }

    public function uploadresultAction()
    {
        // note: following echoes the raw multipart data from uploadAction()
        if (!IS_TESTING())
            return;

        $encode = isset_or($_GET['encode'], false);
        $encode = toboolean($encode);
        $this->renderRaw();

        $php_stream_handle = fopen('php://input', 'rb');
        $content = fread($php_stream_handle, 32768);

        // if encode is false, return the raw data
        if ($encode === false)
        {
            header('Content-Type: text/plain');
            echo($content);
            return;
        }

        // if encode is true, generate a test case with base64 encoded data

        $content_type = $_SERVER['CONTENT_TYPE'];
        $content_base64 = base64_encode($content);

        $result = '';
        $result .= 'function getTestInfo()' . "\n";
        $result .= '{' . "\n";
        $result .= '    $test_info = array();' . "\n";
        $result .= '    $test_info[\'content_type\'] = \''.$content_type.'\';' . "\n";
        $result .= '    $test_info[\'content_base64\'] = \''.$content_base64.'\';' . "\n";
        $result .= '    $test_info[\'content\'] = <<<EOD' . "\n";
        $result .=      $content;
        $result .= ''  . "\nEOD;\n";
        $result .= '    return $test_info;' . "\n";
        $result .= '}' . "\n";

        header('Content-Type: text/plain');
        echo($result);
    }

    public function formAction()
    {
        if (!IS_TESTING())
            return;

        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST');
        header('Access-Control-Allow-Headers: authorization');


        // keep this function empty
        $this->renderRaw();

        echo json_encode($_POST);
    }
}
