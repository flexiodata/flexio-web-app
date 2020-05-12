<?php
/**
 *
 * Copyright (c) 2015, Flex Research LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-04-22
 *
 * @package flexio
 * @subpackage Controller
 */


declare(strict_types=1);
namespace Flexio\Tests;


class Util
{
    public static function getModel()
    {
        return \Flexio\System\System::getModel();
    }

    public static function execSDKJS(string $code)
    {
        $dockerbin = \Flexio\System\System::getBinaryPath('docker');
        if (is_null($dockerbin))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $cmd = "$dockerbin run -a stdin -a stdout -a stderr --rm -i fxruntime sh -c '(echo ".base64_encode($code)." | base64 -d > /fxnodejs/script.js && timeout 30s nodejs /fxnodejs/script.js)' 2>&1";

        $f = popen($cmd, 'r');
        $result = stream_get_contents($f);
        pclose($f);

        return $result;
    }

    // $method = GET, POST, PUT, DELETE
    // $token = authentication token
    // $params = php array for normal post (files prefixed with @), or a string for a post buffer
    // $content_type = in the case the $params is a string, specify its content type here
    // returns an array [ "code" => http code, "response" => response body ];

    public static function callApi(array $call_params)
    {
        $method = $call_params['method'] ?? 'GET';
        $url = $call_params['url'] ?? '';
        $token = $call_params['token'] ?? '';
        $params = $call_params['params'] ?? [];
        $content_type = $call_params['content_type'] ?? null;

        if (strlen($url) == 0)
        {
            throw new \Error("Invalid method specified in call to \Flexio\Tests\Util::callApi");
        }

        if (is_array($params)) // content can be posted as a JSON string, so make sure we're working with an array
        {
            foreach ($params as $key => &$value)
            {
                if (is_string($value) && substr($value, 0, 1) == '@')
                {
                    $value = curl_file_create(substr($value, 1));
                }
            }
            unset($value);
        }

        $ch = curl_init();

        $headers = array();
        if (isset($content_type))
            $headers[] = 'Content-Type: ' . $content_type;
        if (isset($token))
            $headers[] = 'Authorization: Bearer ' . $token;

        switch ($method)
        {
            case 'GET':
                curl_setopt($ch, CURLOPT_HTTPGET, true);
                break;
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, TRUE);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
                break;
            case 'PUT':     curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');    break;
            case 'DELETE':  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE'); break;
            default:
                throw new \Error("Invalid method specified in call to \Flexio\Tests\Util::callApi");
                break;
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // because using localhost
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $content_type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        curl_close($ch);

        return [ 'code' => $http_code, 'content_type' => $content_type, 'response' => $response ];
    }

    public static function runProcessDebug(string $apibase, string $userid, string $token, array $tasks)
    {
        // debug mode for fixing tests
        $process = \Flexio\Jobs\Process::create()->setOwner($userid)->execute($tasks);
        $response = $process->getStdout()->getReader()->read();
        return [ 'code' => 200, 'content_type' => $process->getStdout()->getMimeType(), 'response' => $response ];
    }

    public static function runProcess(string $apibase, string $userid, string $token, array $tasks)
    {
        // wraps up the creation of a process and the running of that process
        $params = json_encode(['task' => $tasks]);
        $result = self::callApi(array(
            'method' => 'POST',
            'url' => "$apibase/$userid/processes",
            'token' => $token,
            'content_type' => 'application/json',
            'params' => $params
        ));
        $response = json_decode($result['response'],true);
        $objeid = $response['eid'] ?? '';
        $result = self::callApi(array(
            'method' => 'POST',
            'url' => "$apibase/$userid/processes/$objeid/run",
            'token' => $token
        ));
        return $result;
    }

    public static function evalExpression($expr)
    {
        $retval = null;
        $success = \Flexio\Base\ExprEvaluate::evaluate($expr, [], [], $retval);
        if ($success === false)
            return \Flexio\Tests\Base::ERROR_BAD_PARSE;

        return $retval;
    }

    public static function getTestSDKSetup()
    {
        $default_user_token = \Flexio\Tests\Util::getDefaultTestUserToken();
        $test_api_endpoint = \Flexio\Tests\Util::getTestHost() . '/v1';

        $script = <<<EOD

const Flexio = require('flexio-sdk-js');
Flexio.setup('$default_user_token', { baseUrl: '$test_api_endpoint', insecure: true });

EOD;
        return $script;
    }

    public static function getTestHost()
    {
        $host = 'https://' . (IS_LOCALHOST() ? $_SERVER['SERVER_ADDR'] : $_SERVER['HTTP_HOST']);
        return $host;
    }

    public static function getDefaultTestUser()
    {
        // returns the eid of a default test user; creates the user if the
        // user doesn't exist
        $username = "testuser";
        $email = "test@flex.io";
        $password = 'test9999';

        // see if the user already exists
        $user_eid = \Flexio\Tests\Util::getModel()->user->getEidFromIdentifier($username);
        if (\Flexio\Base\Eid::isValid($user_eid))
            return $user_eid;

        $user_eid = \Flexio\Tests\Util::createUser($username, $email, $password);
        return $user_eid;
    }

    public static function getDefaultTestUserToken()
    {
        // returns an api token for the default test user
        $user_eid = self::getDefaultTestUser();

        // get the tokens
        $filter = array('owned_by' => $user_eid, 'eid_status' => \Model::STATUS_AVAILABLE);
        $tokens = \Flexio\Object\Token::list($filter);

        if (count($tokens) === 0)
            $tokens[] = \Flexio\Object\Token::create(array('owned_by' => $user_eid));

        // return the first token
        $token_info = $tokens[0]->get();
        return $token_info['access_code'];
    }

    public static function getTestStorageOwner()
    {
        // right now, the test storage owner is the user running the
        // test suite that's set up the connections; so the the current
        // user; this wrapped allows us to easily set up something else
        return \Flexio\System\System::getCurrentUserEid();
    }

    public static function getTestStorageOwnerToken()
    {
        // returns an api token for the owner of the storage
        $user_eid = self::getTestStorageOwner();

        // get the tokens
        $filter = array('owned_by' => $user_eid, 'eid_status' => \Model::STATUS_AVAILABLE);
        $tokens = \Flexio\Object\Token::list($filter);

        if (count($tokens) === 0)
            $tokens[] = \Flexio\Object\Token::create(array('owned_by' => $user_eid));

        // return the first token
        $token_info = $tokens[0]->get();
        return $token_info['access_code'];
    }

    public static function createUser(string $username = null, string $email = null, string $password = null) : string
    {
        if (!isset($username))
            $username =\Flexio\Base\Identifier::generate();
        if (!isset($email))
            $email = \Flexio\Tests\Util::createEmailAddress();
        if (!isset($password))
            $password = \Flexio\Base\Password::generate();

        $verify_code = \Flexio\Base\Util::generateHandle();
        $new_user_info = array(
                               'username' => $username,
                               'email' => $email,
                               'full_name' => $username,
                               'eid_status' => \Model::STATUS_AVAILABLE,
                               'password' => $password);

        $user = \Flexio\Object\User::create($new_user_info);
        $user_eid = $user->getEid();

        // owner and created have to be set after creation the user eid
        // isn't yet known, and the owner and creator are the eid of the
        // user created
        $additional_user_properties = array(
            'owned_by' => $user_eid,
            'created_by' => $user_eid
        );
        $user->set($additional_user_properties);
        return $user_eid;
    }

    public static function createToken(string $user_eid) : string
    {
        $token = \Flexio\Object\Token::create(array('owned_by' => $user_eid));
        $token_info = $token->get();
        return $token_info['access_code'];
    }

    public static function createPipe(string $user_eid, string $pipe_name) : string
    {
        $properties['name'] = $pipe_name;
        $properties['owned_by'] = $user_eid;
        $properties['created_by'] = $user_eid;
        $pipe = \Flexio\Object\Pipe::create($properties);
        return $pipe->getEid();
    }

    public static function createStream(string $test_file_local_path) : \Flexio\Base\Stream
    {
        $file = \Flexio\System\System::getTestDataDirectory() . $test_file_local_path;
        return self::createStreamFromFile($file);
    }

    public static function createStreamFromFile(string $path) : \Flexio\Base\Stream
    {
        $f = @fopen($path, 'rb');
        if (!$f)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

        $mime_type = false;

        $stream = \Flexio\Base\Stream::create();
        $writer = $stream->getWriter();
        while (!feof($f))
        {
            $buffer = fread($f, 2048);
            $writer->write($buffer);

            if ($mime_type === false)
                $mime_type = \Flexio\Base\ContentType::getMimeType($path, $buffer);
        }

        fclose($f);

        $stream->setMimeType($mime_type);

        return $stream;
    }

    public static function createEmailAddress() : string
    {
        $handle1 = \Flexio\Base\Util::generateHandle();
        $handle2 = \Flexio\Base\Util::generateHandle();
        return $handle1 . '@' . $handle2 . '.com';
    }

    public static function getTestDataSamples() : array
    {
        // TODO: get a selection of test data samples

        $testdata_dir = \Flexio\System\System::getTestDataDirectory() . DIRECTORY_SEPARATOR . 'image' . DIRECTORY_SEPARATOR;
        $files = scandir($testdata_dir);
        if (!$files)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED, "Problem in getTestDataSamples()");

        $result = array();
        foreach ($files as $f)
        {
            if ($f == '.' || $f == '..')
                continue;

            $result[] = $testdata_dir . $f;
        }

        return $result;
    }


    public static function getTestDataFolder($folder /* for example, 'images'... */) : array
    {
        // TODO: get a selection of test data samples

        $testdata_dir = \Flexio\System\System::getTestDataDirectory() . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR;
        $files = scandir($testdata_dir);
        if (!$files)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED, "Problem in getTestDataFolder('$folder')");


        $result = array();
        foreach ($files as $f)
        {
            if ($f == '.' || $f == '..')
                continue;

            $result[] = $testdata_dir . $f;
        }

        return $result;
    }


    public static function getOutputFilePath(string $output_folderpath, string $input_filepath) : string
    {
        $filename = \Flexio\Base\File::getFilename($input_filepath);
        $fileextension = \Flexio\Base\File::getFileExtension($input_filepath);
        $output_filepath = $output_folderpath . $filename . "." . $fileextension;
        return $output_filepath;
    }

    public static function getTimestampName() : string
    {
        return date("YmdHis", time());
    }

    public static function convertToNumber(string $size_str) : int
    {
        switch (strtoupper(substr($size_str, -1)))
        {
            case 'G': return (int)$size_str * 1073741824;
            case 'M': return (int)$size_str * 1048576;
            case 'K': return (int)$size_str * 1024;
            default:  return (int)$size_str;
        }
    }

    public static function dblcompare($a, $b)
    {
        // note: this implementation currently parallels the implementation in ExprEvaluate

        $a = (double)$a;
        $b = (double)$b;

        if (is_nan($a) && is_nan($b))
            return 0;
        if (is_nan($a))
            return -1;
        if (is_nan($b))
            return 1;

        if (($a - $b) > ( (abs($a) < abs($b) ? abs($b) : abs($a)) * \Flexio\Tests\Base::DOUBLE_EPSILON))
            return 1;
        if (($b - $a) > ( (abs($a) < abs($b) ? abs($b) : abs($a)) * \Flexio\Tests\Base::DOUBLE_EPSILON))
            return -1;

        return 0;
    }
}
