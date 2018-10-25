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


// calls to the TestController are only allowed on localhost for
// now, and calls cut off when not running in debug model

class TestController extends \Flexio\System\FxControllerAction
{
    public function init() : void
    {
        parent::init();
    }

    public function constantsAction() : void
    {
        $constants = array();
        $constants['EID_TYPE_USER']       = \Model::TYPE_USER;
        $constants['EID_TYPE_PIPE']       = \Model::TYPE_PIPE;
        $constants['EID_TYPE_STREAM']     = \Model::TYPE_STREAM;
        $constants['EID_TYPE_CONNECTION'] = \Model::TYPE_CONNECTION;
        $constants['EID_TYPE_COMMENT']    = \Model::TYPE_COMMENT;
        $constants['EID_TYPE_PROCESS']    = \Model::TYPE_PROCESS;

        $result = json_encode($constants);
        echo $result;
    }

    public function phpinfoAction() : void
    {
        // PHP CONFIG INFO
        if (!IS_TESTING())
            return;

        $this->renderRaw();
        phpinfo();
    }

    public function embedAction() : void
    {
        // EMBED TEST
        if (!IS_TESTING())
            return;

        $this->view->echo_content = true;
        $this->render();
    }

    public function unitAction() : void
    {
        // TEST SUITE
        if (!IS_TESTING())
            return;

        $this->view->echo_content = true;
        $this->render();
    }

    public function gridAction() : void
    {
        // GRID TEST
        if (!IS_TESTING())
            return;

        $this->view->echo_content = true;
        $this->render();
    }

    public function waitAction() : void
    {
        if (!IS_TESTING())
            return;

        $this->renderRaw();

        sleep(20);
        echo "Done Waiting ". time();
    }

    public function echoAction() : void
    {
        if (!IS_TESTING())
            return;

        $this->renderRaw();
        echo "Echo ". time();
    }

    public function emptyAction() : void
    {
        if (!IS_TESTING())
            return;

        // keep this function empty
        $this->renderRaw();
        echo '{}';
    }

    public function uploadAction() : void
    {
        // note: following is a simple form to echo raw multipart data
        if (!IS_TESTING())
            return;

        // pass any encode parameter to the output
        $encode = $_GET['encode'] ?? 'false';
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

    public function uploadresultAction() : void
    {
        // note: following echoes the raw multipart data from uploadAction()
        if (!IS_TESTING())
            return;

        $encode = $_GET['encode'] ?? false;
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

    public function formAction() : void
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

    public function problemAction() : void
    {
        if (!IS_TESTING())
            return;

        $this->renderRaw();

        $processes = array();
        for ($i = 0; $i < 60; ++$i)
        {
            $processes[] = self::runPipe(true /*true = background*/);
        }

        // block a minute, or until processes are finished
        for ($i = 0; $i < 100; ++$i)
        {
            if (self::processesFinished($processes))
                break;

            sleep(1);
        }

        // return info for each of the finished processes
        $result = array();
        foreach ($processes as $p)
        {
            $info = $p->get();
            $result[] = array(
                'eid' => $info['eid'],
                'started' => $info['started'],
                'finished' => $info['finished'],
                'duration' => $info['duration'],
                'process_status' => $info['process_status'],
                'process_info' => $info['process_info']
            );
        }

        $response = @json_encode($result, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        echo "<pre>";
        echo $response;
    }

    private static function processesFinished($processes)
    {
        foreach ($processes as $p)
        {
            $process_info = $p->get();
            $process_status = $process_info['process_status'];
            if ($process_status !== 'C' && $process_status !== 'F')
                return false;
        }

        return true;
    }

    private static function runPipe($background)
    {
/*
$code = <<<EOT
pass
EOT;

        $task = array(
            "op" => "execute",
            "lang" => "python",
            "code" => base64_encode($code)
        );
*/

$code = <<<EOT
# basic hello world example
def flex_handler(flex):
    flex.end("Hello, World.")
EOT;

        $task = array(
            "op" => "execute",
            "lang" => "python",
            "code" => base64_encode($code)
        );


/*
$code = <<<EOT
// basic hello world example
exports.flex_handler = function(flex) {
  flex.end("Hello, World.")
}
EOT;

        $task = array(
            "op" => "execute",
            "lang" => "nodejs",
            "code" => base64_encode($code)
        );


$task = array(
    "op" => "sleep",
    "value" => 1
);
*/

        $process_properties = array(
            'task' => $task
        );

        // STEP 1: create the process
        $process = \Flexio\Object\Process::create($process_properties);

        // STEP 2: run the process
        $engine = \Flexio\Jobs\StoredProcess::create($process);
        $engine->run($background);

        /*

        // note: if not in background mode, following can be used to
        // echo results


        // STEP 3: return the result
        if ($engine->hasError())
        {
            $error = $engine->getError();
            \Flexio\Api\Response::sendError($error);
            exit(0);
        }

        $stream = $engine->getStdout();
        $stream_info = $stream->get();
        if ($stream_info === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

        $mime_type = $stream_info['mime_type'];
        $start = 0;
        $limit = PHP_INT_MAX;
        $content = \Flexio\Base\Util::getStreamContents($stream, $start, $limit);
        $response_code = $engine->getResponseCode();

        if ($mime_type !== \Flexio\Base\ContentType::FLEXIO_TABLE)
        {
            // return content as-is
            header('Content-Type: ' . $mime_type, true, $response_code);
        }
        else
        {
            // flexio table; return application/json in place of internal mime
            header('Content-Type: ' . \Flexio\Base\ContentType::JSON, true, $response_code);
            $content = json_encode($content, JSON_UNESCAPED_SLASHES);
        }

        echo $content;
        */


        return $process;
    }
}
