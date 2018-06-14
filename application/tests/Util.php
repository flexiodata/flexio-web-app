<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
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
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

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
                               'password' => $password,
                               'verify_code' => '');

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

        $user->grant($user_eid, \Model::ACCESS_CODE_TYPE_EID,
            array(
                \Flexio\Object\Right::TYPE_READ_RIGHTS,
                \Flexio\Object\Right::TYPE_WRITE_RIGHTS,
                \Flexio\Object\Right::TYPE_READ,
                \Flexio\Object\Right::TYPE_WRITE,
                \Flexio\Object\Right::TYPE_DELETE
            )
        );

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

        $stream = \Flexio\Base\Stream::create();
        while (!feof($f))
        {
            $buffer = fread($f, 2048);
            $stream->getWriter()->write($buffer);
        }

        fclose($f);

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
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

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

    public static function createSampleDataTask() : array
    {
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "create",
                "content_type" => "application/vnd.flexio.table",
                "columns" => [
                    ["name" => "id", "type" => "integer", "width" => 4, "scale" => 0],
                    ["name" => "char_1a", "type" => "character", "width" => 10, "scale" => 0],
                    ["name" => "char_1b", "type" => "character", "width" => 10, "scale" => 0],
                    ["name" => "char_1c", "type" => "character", "width" => 10, "scale" => 0],
                    ["name" => "char_1d", "type" => "character", "width" => 10, "scale" => 0],
                    ["name" => "char_1e", "type" => "character", "width" => 254, "scale" => 0],
                    ["name" => "char_1f", "type" => "character", "width" => 254, "scale" => 0],
                    ["name" => "char_1g", "type" => "character", "width" => 254, "scale" => 0],
                    ["name" => "char_1h", "type" => "character", "width" => 254, "scale" => 0],
                    ["name" => "num_1a", "type" => "numeric", "width" => 10, "scale" => 0],
                    ["name" => "num_1b", "type" => "numeric", "width" => 10, "scale" => 0],
                    ["name" => "num_1c", "type" => "numeric", "width" => 10, "scale" => 0],
                    ["name" => "num_1d", "type" => "numeric", "width" => 10, "scale" => 0],
                    ["name" => "num_1e", "type" => "numeric", "width" => 18, "scale" => 0],
                    ["name" => "num_1f", "type" => "numeric", "width" => 18, "scale" => 12],
                    ["name" => "num_1g", "type" => "numeric", "width" => 18, "scale" => 0],
                    ["name" => "num_1h", "type" => "numeric", "width" => 18, "scale" => 12],
                    ["name" => "num_2a", "type" => "double", "width" => 8, "scale" => 0],
                    ["name" => "num_2b", "type" => "double", "width" => 8, "scale" => 0],
                    ["name" => "num_2c", "type" => "double", "width" => 8, "scale" => 0],
                    ["name" => "num_2d", "type" => "double", "width" => 8, "scale" => 0],
                    ["name" => "num_2e", "type" => "double", "width" => 8, "scale" => 0],
                    ["name" => "num_2f", "type" => "double", "width" => 8, "scale" => 12],
                    ["name" => "num_2g", "type" => "double", "width" => 8, "scale" => 0],
                    ["name" => "num_2h", "type" => "double", "width" => 8, "scale" => 12],
                    ["name" => "num_3a", "type" => "integer", "width" => 4, "scale" => 0],
                    ["name" => "num_3b", "type" => "integer", "width" => 4, "scale" => 0],
                    ["name" => "num_3c", "type" => "integer", "width" => 4, "scale" => 0],
                    ["name" => "num_3d", "type" => "integer", "width" => 4, "scale" => 0],
                    ["name" => "num_3e", "type" => "integer", "width" => 4, "scale" => 0],
                    ["name" => "num_3f", "type" => "integer", "width" => 4, "scale" => 0],
                    ["name" => "num_3g", "type" => "integer", "width" => 4, "scale" => 0],
                    ["name" => "num_3h", "type" => "integer", "width" => 4, "scale" => 0],
                    ["name" => "date_1a", "type" => "date", "width" => 4, "scale" => 0],
                    ["name" => "date_1b", "type" => "date", "width" => 4, "scale" => 0],
                    ["name" => "date_1c", "type" => "date", "width" => 4, "scale" => 0],
                    ["name" => "date_1d", "type" => "date", "width" => 4, "scale" => 0],
                    ["name" => "date_1e", "type" => "date", "width" => 4, "scale" => 0],
                    ["name" => "date_1f", "type" => "date", "width" => 4, "scale" => 0],
                    ["name" => "date_1g", "type" => "date", "width" => 4, "scale" => 0],
                    ["name" => "date_1h", "type" => "date", "width" => 4, "scale" => 0],
                    ["name" => "date_2a", "type" => "datetime", "width" => 8, "scale" => 0],
                    ["name" => "date_2b", "type" => "datetime", "width" => 8, "scale" => 0],
                    ["name" => "date_2c", "type" => "datetime", "width" => 8, "scale" => 0],
                    ["name" => "date_2d", "type" => "datetime", "width" => 8, "scale" => 0],
                    ["name" => "date_2e", "type" => "datetime", "width" => 8, "scale" => 0],
                    ["name" => "date_2f", "type" => "datetime", "width" => 8, "scale" => 0],
                    ["name" => "date_2g", "type" => "datetime", "width" => 8, "scale" => 0],
                    ["name" => "date_2h", "type" => "datetime", "width" => 8, "scale" => 0],
                    ["name" => "bool_1a", "type" => "boolean", "width" => 1, "scale" => 0],
                    ["name" => "bool_1b", "type" => "boolean", "width" => 1, "scale" => 0],
                    ["name" => "bool_1c", "type" => "boolean", "width" => 1, "scale" => 0],
                    ["name" => "bool_1d", "type" => "boolean", "width" => 1, "scale" => 0],
                    ["name" => "bool_1e", "type" => "boolean", "width" => 1, "scale" => 0],
                    ["name" => "bool_1f", "type" => "boolean", "width" => 1, "scale" => 0],
                    ["name" => "bool_1g", "type" => "boolean", "width" => 1, "scale" => 0],
                    ["name" => "bool_1h", "type" => "boolean", "width" => 1, "scale" => 0]
                ],
                "content" => [
                    ["1",  "A", "A", "",  "D",  "D00000", "00000D", "D00000", "00000D", 1, 1, 0,    4,  4000000000000,  0.000000000004,  4000000000000,  0.000000000004, 1, 1, 0,     4,  4000000000000,  0.000000000004,  4000000000000,  0.000000000004, 1, 1, 0,     4,  400000000,  4,  400000000,  4, "2001-01-01", "2001-01-01", "",           "2001-01-05", "2005-01-01", "2001-01-05", "2005-01-01", "2001-01-05", "2001-01-01 01:01:01", "2001-01-01 01:01:01", "",                    "2001-01-05 01:01:01", "2000-01-02 00:00:00", "2000-01-01 00:01:00", "2000-01-02 00:00:00", "2000-01-01 00:01:00", true, true,  false, true,  true,  false, true,  false],
                    ["2",  "A", "",  "",  "D",  "D00000", "00000D", "D00000", "00000D", 1, 0, 0,    4,  4000000000000,  0.000000000004,  4000000000000,  0.000000000004, 1, 0, 0,     4,  4000000000000,  0.000000000004,  4000000000000,  0.000000000004, 1, 0, 0,     4,  400000000,  4,  400000000,  4, "2001-01-01", "",           "",           "2001-01-05", "2005-01-01", "2001-01-05", "2005-01-01", "2001-01-05", "2001-01-01 01:01:01", "",                    "",                    "2001-01-05 01:01:01", "2000-01-02 00:00:00", "2000-01-01 00:01:00", "2000-01-02 00:00:00", "2000-01-01 00:01:00", true, false, false, true,  true,  false, true,  false],
                    ["3",  "A", "",  "",  "D",  "D00000", "00000D", "D00000", "00000D", 1, 0, 0,    4,  4000000000000,  0.000000000004,  4000000000000,  0.000000000004, 1, 0, 0,     4,  4000000000000,  0.000000000004,  4000000000000,  0.000000000004, 1, 0, 0,     4,  400000000,  4,  400000000,  4, "2001-01-01", "",           "",           "2001-01-05", "2005-01-01", "2001-01-05", "2005-01-01", "2001-01-05", "2001-01-01 01:01:01", "",                    "",                    "2001-01-05 01:01:01", "2000-01-02 00:00:00", "2000-01-01 00:01:00", "2000-01-02 00:00:00", "2000-01-01 00:01:00", true, false, false, true,  true,  false, true,  false],
                    ["4",  "A", "",  "",  null, "D00000", "00000D", "D00000", "00000D", 1, 0, 0, null,  4000000000000,  0.000000000004,  4000000000000,  0.000000000004, 1, 0, 0,  null,  4000000000000,  0.000000000004,  4000000000000,  0.000000000004, 1, 0, 0,  null,  400000000,  4,  400000000,  4, "2001-01-01", "",           "",           null,         "2005-01-01", "2001-01-05", "2005-01-01", "2001-01-05", "2001-01-01 01:01:01", "",                    "",                    null,                  "2000-01-02 00:00:00", "2000-01-01 00:01:00", "2000-01-02 00:00:00", "2000-01-01 00:01:00", true, false, false, null,  true,  false, true,  false],
                    ["5",  "A", "",  "",  null, "D00000", "00000D", "D00000", "00000D", 1, 0, 0, null,  4000000000000,  0.000000000004,  4000000000000,  0.000000000004, 1, 0, 0,  null,  4000000000000,  0.000000000004,  4000000000000,  0.000000000004, 1, 0, 0,  null,  400000000,  4,  400000000,  4, "2001-01-01", "",           "",           null,         "2005-01-01", "2001-01-05", "2005-01-01", "2001-01-05", "2001-01-01 01:01:01", "",                    "",                    null,                  "2000-01-02 00:00:00", "2000-01-01 00:01:00", "2000-01-02 00:00:00", "2000-01-01 00:01:00", true, false, false, null,  true,  false, true,  false],
                    ["6",  "A", "",  "",  "b",  "b00000", "00000b", "b00000", "00000b", 1, 0, 0,   -2, -2000000000000, -0.000000000002, -2000000000000, -0.000000000002, 1, 0, 0,    -2, -2000000000000, -0.000000000002, -2000000000000, -0.000000000002, 1, 0, 0,    -2, -200000000, -2, -200000000, -2, "2001-01-01", "",           "",           "2000-12-30", "1970-12-31", "1999-12-31", "1970-12-31", "1999-12-31", "2001-01-01 01:01:01", "",                    "",                    "2000-12-30 01:01:01", "1970-01-01 00:01:00", "1970-01-01 00:00:01", "1970-01-01 00:01:00", "1970-01-01 00:00:01", true, false, false, false, false, true,  false, true ],
                    ["7",  "A", "",  "",  "c",  "c00000", "00000c", "c00000", "00000c", 1, 0, 0,   -3, -3000000000000, -0.000000000003, -3000000000000, -0.000000000003, 1, 0, 0,    -3, -3000000000000, -0.000000000003, -3000000000000, -0.000000000003, 1, 0, 0,    -3, -300000000, -3, -300000000, -3, "2001-01-01", "",           "",           "2000-12-29", "1970-01-01", "1999-11-30", "1970-01-01", "1999-11-30", "2001-01-01 01:01:01", "",                    "",                    "2000-12-29 01:01:01", "1970-01-01 00:00:00", "1970-01-01 00:00:00", "1970-01-01 00:00:00", "1970-01-01 00:00:00", true, false, false, false, false, true,  false, true ],
                    ["8",  "A", "",  "",  "a",  "a00000", "00000a", "a00000", "00000a", 1, 0, 0,   -1, -1000000000000, -0.000000000001, -1000000000000, -0.000000000001, 1, 0, 0,    -1, -1000000000000, -0.000000000001, -1000000000000, -0.000000000001, 1, 0, 0,    -1, -100000000, -1, -100000000, -1, "2001-01-01", "",           "",           "2000-12-31", "2000-01-01", "2000-01-01", "2000-01-01", "2000-01-01", "2001-01-01 01:01:01", "",                    "",                    "2000-12-31 01:01:01", "1999-12-31 23:00:00", "1999-12-31 23:59:59", "1999-12-31 23:00:00", "1999-12-31 23:59:59", true, false, false, false, false, true,  false, true ],
                    ["9",  "A", "",  "",  "B",  "B00000", "00000B", "B00000", "00000B", 1, 0, 0,    2,  2000000000000,  0.000000000002,  2000000000000,  0.000000000002, 1, 0, 0,     2,  2000000000000,  0.000000000002,  2000000000000,  0.000000000002, 1, 0, 0,     2,  200000000,  2,  200000000,  2, "2001-01-01", "",           "",           "2001-01-03", "2002-01-01", "2000-01-03", "2002-01-01", "2000-01-03", "2001-01-01 01:01:01", "",                    "",                    "2001-01-03 01:01:01", "2000-01-01 01:00:00", "2000-01-01 00:00:01", "2000-01-01 01:00:00", "2000-01-01 00:00:01", true, false, false, true,  true,  false, true,  false],
                    ["10", "A", "",  "",  "C",  "C00000", "00000C", "C00000", "00000C", 1, 0, 0,    3,  3000000000000,  0.000000000003,  3000000000000,  0.000000000003, 1, 0, 0,     3,  3000000000000,  0.000000000003,  3000000000000,  0.000000000003, 1, 0, 0,     3,  300000000,  3,  300000000,  3, "2001-01-01", "",           "",           "2001-01-04", "2003-01-01", "2000-02-03", "2003-01-01", "2000-02-03", "2001-01-01 01:01:01", "",                    "",                    "2001-01-04 01:01:01", "2000-01-01 23:00:00", "2000-01-01 00:00:59", "2000-01-01 23:00:00", "2000-01-01 00:00:59", true, false, false, true,  true,  false, true,  false],
                    ["11", "A", "",  "",  "A",  "A00000", "00000A", "A00000", "00000A", 1, 0, 0,    1,  1000000000000,  0.000000000001,  1000000000000,  0.000000000001, 1, 0, 0,     1,  1000000000000,  0.000000000001,  1000000000000,  0.000000000001, 1, 0, 0,     1,  100000000,  1,  100000000,  1, "2001-01-01", "",           "",           "2001-01-02", "2001-01-01", "2000-01-02", "2001-01-01", "2000-01-02", "2001-01-01 01:01:01", "",                    "",                    "2001-01-02 01:01:01", "2000-01-01 00:00:00", "2000-01-01 00:00:00", "2000-01-01 00:00:00", "2000-01-01 00:00:00", true, false, false, true,  true,  false, true,  false],
                    ["12", "A", "",  "",  "d",  "d00000", "00000d", "d00000", "00000d", 1, 0, 0,   -4, -4000000000000, -0.000000000004, -4000000000000, -0.000000000004, 1, 0, 0,    -4, -4000000000000, -0.000000000004, -4000000000000, -0.000000000004, 1, 0, 0,    -4, -400000000, -4, -400000000, -4, "2001-01-01", "",           "",           "2000-12-28", "1969-12-31", "1998-12-28", "1969-12-31", "1998-12-28", "2001-01-01 01:01:01", "",                    "",                    "2000-12-28 01:01:01", "1969-12-31 23:59:00", "1969-12-31 23:59:59", "1969-12-31 23:59:00", "1969-12-31 23:59:59", true, false, false, false, false, true,  false, true ],
                    ["13", "A", "",  "",  "d",  "d00000", "00000d", "d00000", "00000d", 1, 0, 0,   -4, -4000000000000, -0.000000000004, -4000000000000, -0.000000000004, 1, 0, 0,    -4, -4000000000000, -0.000000000004, -4000000000000, -0.000000000004, 1, 0, 0,    -4, -400000000, -4, -400000000, -4, "2001-01-01", "",           "",           "2000-12-28", "1969-12-31", "1998-12-28", "1969-12-31", "1998-12-28", "2001-01-01 01:01:01", "",                    "",                    "2000-12-28 01:01:01", "1969-12-31 23:59:00", "1969-12-31 23:59:59", "1969-12-31 23:59:00", "1969-12-31 23:59:59", true, false, false, false, false, true,  false, true ],
                    ["14", "A", "",  "",  "d",  "d00000", "00000d", "d00000", "00000d", 1, 0, 0,   -4, -4000000000000, -0.000000000004, -4000000000000, -0.000000000004, 1, 0, 0,    -4, -4000000000000, -0.000000000004, -4000000000000, -0.000000000004, 1, 0, 0,    -4, -400000000, -4, -400000000, -4, "2001-01-01", "",           "",           "2000-12-28", "1969-12-31", "1998-12-28", "1969-12-31", "1998-12-28", "2001-01-01 01:01:01", "",                    "",                    "2000-12-28 01:01:01", "1969-12-31 23:59:00", "1969-12-31 23:59:59", "1969-12-31 23:59:00", "1969-12-31 23:59:59", true, false, false, false, false, true,  false, true ],
                    ["15", "A", "",  "",  null, "d00000", "00000d", "d00000", "00000d", 1, 0, 0, null, -4000000000000, -0.000000000004, -4000000000000, -0.000000000004, 1, 0, 0,  null, -4000000000000, -0.000000000004, -4000000000000, -0.000000000004, 1, 0, 0,  null, -400000000, -4, -400000000, -4, "2001-01-01", "",           "",           null,         "1969-12-31", "1998-12-28", "1969-12-31", "1998-12-28", "2001-01-01 01:01:01", "",                    "",                    null,                  "1969-12-31 23:59:00", "1969-12-31 23:59:59", "1969-12-31 23:59:00", "1969-12-31 23:59:59", true, false, false, null,  false, true,  false, true ],
                    ["16", "A", "",  "",  null, "E00000", "00000E", "E00000", "00000E", 1, 0, 0, null,  5000000000000,  0.000000000005,  5000000000000,  0.000000000005, 1, 0, 0,  null,  5000000000000,  0.000000000005,  5000000000000,  0.000000000005, 1, 0, 0,  null,  500000000,  5,  500000000,  5, "2001-01-01", "",           "",           null,         "2006-01-01", "2001-01-06", "2006-01-01", "2001-01-06", "2001-01-01 01:01:01", "",                    "",                    null,                  "2001-01-01 00:00:00", "2000-01-01 01:00:00", "2001-01-01 00:00:00", "2000-01-01 01:00:00", true, false, false, null,  true,  false, true,  false],
                    ["17", "A", "",  "",  null, "E00000", "00000E", "E00000", "00000E", 1, 0, 0, null,  5000000000000,  0.000000000005,  5000000000000,  0.000000000005, 1, 0, 0,  null,  5000000000000,  0.000000000005,  5000000000000,  0.000000000005, 1, 0, 0,  null,  500000000,  5,  500000000,  5, "2001-01-01", "",           "",           null,         "2006-01-01", "2001-01-06", "2006-01-01", "2001-01-06", "2001-01-01 01:01:01", "",                    "",                    null,                  "2001-01-01 00:00:00", "2000-01-01 01:00:00", "2001-01-01 00:00:00", "2000-01-01 01:00:00", true, false, false, null,  true,  false, true,  false],
                    ["18", "A", "",  "",  "E",  "E00000", "00000E", "E00000", "00000E", 1, 0, 0,    5,  5000000000000,  0.000000000005,  5000000000000,  0.000000000005, 1, 0, 0,     5,  5000000000000,  0.000000000005,  5000000000000,  0.000000000005, 1, 0, 0,     5,  500000000,  5,  500000000,  5, "2001-01-01", "",           "",           "2001-01-06", "2006-01-01", "2001-01-06", "2006-01-01", "2001-01-06", "2001-01-01 01:01:01", "",                    "",                    "2001-01-06 01:01:01", "2001-01-01 00:00:00", "2000-01-01 01:00:00", "2001-01-01 00:00:00", "2000-01-01 01:00:00", true, false, false, true,  true,  false, true,  false],
                    ["19", "A", "",  "",  "E",  "E00000", "00000E", "E00000", "00000E", 1, 0, 0,    5,  5000000000000,  0.000000000005,  5000000000000,  0.000000000005, 1, 0, 0,     5,  5000000000000,  0.000000000005,  5000000000000,  0.000000000005, 1, 0, 0,     5,  500000000,  5,  500000000,  5, "2001-01-01", "",           "",           "2001-01-06", "2006-01-01", "2001-01-06", "2006-01-01", "2001-01-06", "2001-01-01 01:01:01", "",                    "",                    "2001-01-06 01:01:01", "2001-01-01 00:00:00", "2000-01-01 01:00:00", "2001-01-01 00:00:00", "2000-01-01 01:00:00", true, false, false, true,  true,  false, true,  false],
                    ["20", "A", "",  "A", "E",  "E00000", "00000E", "E00000", "00000E", 1, 0, 1,    5,  5000000000000,  0.000000000005,  5000000000000,  0.000000000005, 1, 0, 1,     5,  5000000000000,  0.000000000005,  5000000000000,  0.000000000005, 1, 0, 1,     5,  500000000,  5,  500000000,  5, "2001-01-01", "",           "2001-01-01", "2001-01-06", "2006-01-01", "2001-01-06", "2006-01-01", "2001-01-06", "2001-01-01 01:01:01", "",                    "2001-01-01 01:01:01", "2001-01-06 01:01:01", "2001-01-01 00:00:00", "2000-01-01 01:00:00", "2001-01-01 00:00:00", "2000-01-01 01:00:00", true, false, true,  true,  true,  false, true,  false]
                ]
            ]
        ]);
        return $task;
    }
}
