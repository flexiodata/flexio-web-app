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


class TestError
{
    const ERROR_EXCEPTION = 'ERROR_EXCEPTION';
    const ERROR_NO_EXCEPTION = 'ERROR_NO_EXCEPTION';
    const ERROR_BAD_PARSE = 'ERROR_BAD_PARSE';
    const ERROR_EVAL_MISMATCH = 'ERROR_EVAL_MISMATCH';
}


class TestUtil
{
    const EPSILON = 0.000000000001;
    const CONTENT_TYPE_BUFFER_TEST_SIZE = 2048;

    public static function getModel()
    {
        return new \Model;
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
    // $path = /api/v1/... + GET parameters
    // $token = authentication token
    // $params = php array for normal post (files prefixed with @), or a string for a post buffer
    // $content_type = in the case the $params is a string, specify its content type here
    // returns an array [ "code" => http code, "response" => response body ];

    public static function callApi($method, $path, $token, $params, $content_type = null)
    {
        if (strlen($path) == 0)
        {
            throw new \Error("Invalid method specified in call to TestUtil::callApi");
        }
        if ($path[0] != '/')
            $path = '/' . $path;

        foreach ($params as $key => &$value)
        {
            if (substr($value, 0, 1) == '@')
            {
                $value = curl_file_create(substr($value, 1));
            }
        }
        unset($value);

        $ch = curl_init();

        switch ($method)
        {
            case 'GET':
                curl_setopt($ch, CURLOPT_HTTPGET, true);
                break;
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, TRUE);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
                if (isset($content_type))
                    curl_setopt($ch, CURLOPT_HTTPHEADER, [ 'Content-Type: '. $content_type ]);
                break;
            case 'PUT':     curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');    break;
            case 'DELETE':  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE'); break;
            default:
                throw new \Error("Invalid method specified in call to TestUtil::callApi");
                break;
        }


        curl_setopt($ch, CURLOPT_URL, "https://localhost" . $path);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer '.$token]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // because using localhost
        $result = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return [ 'code' => $http_code, 'response' => $result ];
    }

    public static function evalExpression($expr)
    {
        $retval = null;
        $success = \Flexio\Base\ExprEvaluate::evaluate($expr, [], [], $retval);
        if ($success === false)
            return TestError::ERROR_BAD_PARSE;

        return $retval;
    }

    public static function getTestSDKSetup()
    {
        $default_user_token = TestUtil::getDefaultTestUserToken();
        $test_api_endpoint = TestUtil::getTestApiEndpoint();

        $script = <<<EOD

const Flexio = require('flexio-sdk-js');
Flexio.setup('$default_user_token', { baseUrl: '$test_api_endpoint', insecure: true });

EOD;
        return $script;
    }

    public static function getTestApiEndpoint()
    {
        $host = IS_LOCALHOST() ? $_SERVER['SERVER_ADDR'] : $_SERVER['HTTP_HOST'];
        return 'https://' . $host . '/api/v1';
    }

    public static function getDefaultTestUser()
    {
        // returns the eid of a default test user; creates the user if the
        // user doesn't exist
        $user_name = "testuser";
        $email = "test@flex.io";
        $password = 'test@flex.io';

        // see if the user already exists
        $user_eid = TestUtil::getModel()->user->getEidFromIdentifier($user_name);
        if (\Flexio\Base\Eid::isValid($user_eid))
            return $user_eid;

        $user_eid = TestUtil::createUser($user_name, $email, $password);
        return $user_eid;
    }

    public static function getDefaultTestUserToken()
    {
        // returns an api token for the default test user
        $user_eid = self::getDefaultTestUser();
        $user = \Flexio\Object\User::load($user_eid);
        $tokens = $user->getTokenList();

        if (count($tokens) === 0)
            $tokens[] = \Flexio\Object\Token::create(array('user_eid' => $user_eid));

        // return the first token
        $token_info = $tokens[0]->get();
        return $token_info['access_code'];
    }

    public static function getDefaultTestProject()
    {
        // returns the eid of a default test project; creates the project if the
        // project doesn't exist
        $project_name = 'Test Project';

        // see if the project exists (look for a project named the same that's owned by
        // the default test user)
        $user_eid = self::getDefaultTestUser();

        $search_path = "$user_eid->(".\Model::EDGE_OWNS.")->(".\Model::TYPE_PROJECT.")";
        $projects = TestUtil::getModel()->search($search_path);

        if ($projects !== false)
        {
            foreach ($projects as $project_eid)
            {
                $object = TestUtil::getModel()->get($project_eid);
                if ($object['name'] === $project_name)
                    return $project_eid;
            }
        }

        // we couldn't find a default test project for the default test user;
        // create a default project for the specified user
        $project_eid = TestUtil::createProject($user_eid, $project_name);
        return $project_eid;
    }

    public static function createUser($username, $email, $password)
    {
        $verify_code = \Flexio\Base\Util::generateHandle();
        $new_user_info = array('user_name' => $username,
                               'email' => $email,
                               'full_name' => $username,
                               'eid_status' => \Model::STATUS_AVAILABLE,
                               'password' => $password,
                               'verify_code' => '');

        $user = \Flexio\Object\User::create($new_user_info);
        return $user->getEid();
    }

    public static function createProject($user_eid, $name = null, $description = null)
    {
        $properties['name'] = $name ?? 'Test Project';
        $properties['description'] = $description ?? 'Test project with test data.';

        $project = \Flexio\Object\Project::create($properties);
        $project->setOwner($user_eid);
        $project->setCreatedBy($user_eid);

        return $project->getEid();
    }

    public static function createPipe($user_eid, $project_eid, $pipe_name)
    {
        $properties['name'] = $pipe_name;

        $pipe = \Flexio\Object\Pipe::create($properties);
        $pipe->setOwner($user_eid);
        $pipe->setCreatedBy($user_eid);

        // if a parent project is specified, add the object as a member of the project
        $project = \Flexio\Object\Project::load($project_eid);
        if ($project !== false)
            $project->addPipe($pipe);

        return $pipe->getEid();
    }

    public static function createStreamFromFile($path) : \Flexio\Base\Stream
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

    public static function createEmailAddress()
    {
        $handle1 = \Flexio\Base\Util::generateHandle();
        $handle2 = \Flexio\Base\Util::generateHandle();
        return $handle1 . '@' . $handle2 . '.com';
    }

    public static function getTestDataFiles() : array
    {
        $testdata_dir = __DIR__ . DIRECTORY_SEPARATOR . 'testdata' . DIRECTORY_SEPARATOR;
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

    public static function fileExistsInList($name, $list) : bool
    {
        // note: be tolerant in parameters, since this is used for testing and not
        // all results passed in may be what's needed to evaluate if the name is
        // in the list

        if (!is_string($name))
            return false;

        if (!is_array($list))
            return false;

        foreach ($list as $l)
        {
            if (!isset($l['name']))
                continue;

            if ($l['name'] === $name)
                return true;
        }

        return false;
    }

    public static function getTimestampName() : string
    {
        return date("YmdHis", time());
    }

    public static function convertToNumber($size_str)
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

        if (($a - $b) > ( (abs($a) < abs($b) ? abs($b) : abs($a)) * self::EPSILON))
            return 1;
        if (($b - $a) > ( (abs($a) < abs($b) ? abs($b) : abs($a)) * self::EPSILON))
            return -1;

        return 0;
    }

    public static function getCreateSampleDataTask()
    {
        $task = <<<EOD
        {
            "op": "create",
            "params": {
                "content_type": "application/vnd.flexio.table",
                "columns" : [
                    {"name" : "id", "type" : "integer", "width" : 4, "scale" : 0},
                    {"name" : "char_1a", "type" : "character", "width" : 10, "scale" : 0},
                    {"name" : "char_1b", "type" : "character", "width" : 10, "scale" : 0},
                    {"name" : "char_1c", "type" : "character", "width" : 10, "scale" : 0},
                    {"name" : "char_1d", "type" : "character", "width" : 10, "scale" : 0},
                    {"name" : "char_1e", "type" : "character", "width" : 254, "scale" : 0},
                    {"name" : "char_1f", "type" : "character", "width" : 254, "scale" : 0},
                    {"name" : "char_1g", "type" : "character", "width" : 254, "scale" : 0},
                    {"name" : "char_1h", "type" : "character", "width" : 254, "scale" : 0},
                    {"name" : "num_1a", "type" : "numeric", "width" : 10, "scale" : 0},
                    {"name" : "num_1b", "type" : "numeric", "width" : 10, "scale" : 0},
                    {"name" : "num_1c", "type" : "numeric", "width" : 10, "scale" : 0},
                    {"name" : "num_1d", "type" : "numeric", "width" : 10, "scale" : 0},
                    {"name" : "num_1e", "type" : "numeric", "width" : 18, "scale" : 0},
                    {"name" : "num_1f", "type" : "numeric", "width" : 18, "scale" : 12},
                    {"name" : "num_1g", "type" : "numeric", "width" : 18, "scale" : 0},
                    {"name" : "num_1h", "type" : "numeric", "width" : 18, "scale" : 12},
                    {"name" : "num_2a", "type" : "double", "width" : 8, "scale" : 0},
                    {"name" : "num_2b", "type" : "double", "width" : 8, "scale" : 0},
                    {"name" : "num_2c", "type" : "double", "width" : 8, "scale" : 0},
                    {"name" : "num_2d", "type" : "double", "width" : 8, "scale" : 0},
                    {"name" : "num_2e", "type" : "double", "width" : 8, "scale" : 0},
                    {"name" : "num_2f", "type" : "double", "width" : 8, "scale" : 12},
                    {"name" : "num_2g", "type" : "double", "width" : 8, "scale" : 0},
                    {"name" : "num_2h", "type" : "double", "width" : 8, "scale" : 12},
                    {"name" : "num_3a", "type" : "integer", "width" : 4, "scale" : 0},
                    {"name" : "num_3b", "type" : "integer", "width" : 4, "scale" : 0},
                    {"name" : "num_3c", "type" : "integer", "width" : 4, "scale" : 0},
                    {"name" : "num_3d", "type" : "integer", "width" : 4, "scale" : 0},
                    {"name" : "num_3e", "type" : "integer", "width" : 4, "scale" : 0},
                    {"name" : "num_3f", "type" : "integer", "width" : 4, "scale" : 0},
                    {"name" : "num_3g", "type" : "integer", "width" : 4, "scale" : 0},
                    {"name" : "num_3h", "type" : "integer", "width" : 4, "scale" : 0},
                    {"name" : "date_1a", "type" : "date", "width" : 4, "scale" : 0},
                    {"name" : "date_1b", "type" : "date", "width" : 4, "scale" : 0},
                    {"name" : "date_1c", "type" : "date", "width" : 4, "scale" : 0},
                    {"name" : "date_1d", "type" : "date", "width" : 4, "scale" : 0},
                    {"name" : "date_1e", "type" : "date", "width" : 4, "scale" : 0},
                    {"name" : "date_1f", "type" : "date", "width" : 4, "scale" : 0},
                    {"name" : "date_1g", "type" : "date", "width" : 4, "scale" : 0},
                    {"name" : "date_1h", "type" : "date", "width" : 4, "scale" : 0},
                    {"name" : "date_2a", "type" : "datetime", "width" : 8, "scale" : 0},
                    {"name" : "date_2b", "type" : "datetime", "width" : 8, "scale" : 0},
                    {"name" : "date_2c", "type" : "datetime", "width" : 8, "scale" : 0},
                    {"name" : "date_2d", "type" : "datetime", "width" : 8, "scale" : 0},
                    {"name" : "date_2e", "type" : "datetime", "width" : 8, "scale" : 0},
                    {"name" : "date_2f", "type" : "datetime", "width" : 8, "scale" : 0},
                    {"name" : "date_2g", "type" : "datetime", "width" : 8, "scale" : 0},
                    {"name" : "date_2h", "type" : "datetime", "width" : 8, "scale" : 0},
                    {"name" : "bool_1a", "type" : "boolean", "width" : 1, "scale" : 0},
                    {"name" : "bool_1b", "type" : "boolean", "width" : 1, "scale" : 0},
                    {"name" : "bool_1c", "type" : "boolean", "width" : 1, "scale" : 0},
                    {"name" : "bool_1d", "type" : "boolean", "width" : 1, "scale" : 0},
                    {"name" : "bool_1e", "type" : "boolean", "width" : 1, "scale" : 0},
                    {"name" : "bool_1f", "type" : "boolean", "width" : 1, "scale" : 0},
                    {"name" : "bool_1g", "type" : "boolean", "width" : 1, "scale" : 0},
                    {"name" : "bool_1h", "type" : "boolean", "width" : 1, "scale" : 0}
                ],
                "content" : [
                    [ "1",  "A", "A", "",  "D",  "D00000", "00000D", "D00000", "00000D", 1, 1, 0,    4,  4000000000000,  0.000000000004,  4000000000000,  0.000000000004, 1, 1, 0,     4,  4000000000000,  0.000000000004,  4000000000000,  0.000000000004, 1, 1, 0,     4,  400000000,  4,  400000000,  4, "2001-01-01", "2001-01-01", "",           "2001-01-05", "2005-01-01", "2001-01-05", "2005-01-01", "2001-01-05", "2001-01-01 01:01:01", "2001-01-01 01:01:01", "",                    "2001-01-05 01:01:01", "2000-01-02 00:00:00", "2000-01-01 00:01:00", "2000-01-02 00:00:00", "2000-01-01 00:01:00", true, true,  false, true,  true,  false, true,  false ],
                    [ "2",  "A", "",  "",  "D",  "D00000", "00000D", "D00000", "00000D", 1, 0, 0,    4,  4000000000000,  0.000000000004,  4000000000000,  0.000000000004, 1, 0, 0,     4,  4000000000000,  0.000000000004,  4000000000000,  0.000000000004, 1, 0, 0,     4,  400000000,  4,  400000000,  4, "2001-01-01", "",           "",           "2001-01-05", "2005-01-01", "2001-01-05", "2005-01-01", "2001-01-05", "2001-01-01 01:01:01", "",                    "",                    "2001-01-05 01:01:01", "2000-01-02 00:00:00", "2000-01-01 00:01:00", "2000-01-02 00:00:00", "2000-01-01 00:01:00", true, false, false, true,  true,  false, true,  false ],
                    [ "3",  "A", "",  "",  "D",  "D00000", "00000D", "D00000", "00000D", 1, 0, 0,    4,  4000000000000,  0.000000000004,  4000000000000,  0.000000000004, 1, 0, 0,     4,  4000000000000,  0.000000000004,  4000000000000,  0.000000000004, 1, 0, 0,     4,  400000000,  4,  400000000,  4, "2001-01-01", "",           "",           "2001-01-05", "2005-01-01", "2001-01-05", "2005-01-01", "2001-01-05", "2001-01-01 01:01:01", "",                    "",                    "2001-01-05 01:01:01", "2000-01-02 00:00:00", "2000-01-01 00:01:00", "2000-01-02 00:00:00", "2000-01-01 00:01:00", true, false, false, true,  true,  false, true,  false ],
                    [ "4",  "A", "",  "",  null, "D00000", "00000D", "D00000", "00000D", 1, 0, 0, null,  4000000000000,  0.000000000004,  4000000000000,  0.000000000004, 1, 0, 0,  null,  4000000000000,  0.000000000004,  4000000000000,  0.000000000004, 1, 0, 0,  null,  400000000,  4,  400000000,  4, "2001-01-01", "",           "",           null,         "2005-01-01", "2001-01-05", "2005-01-01", "2001-01-05", "2001-01-01 01:01:01", "",                    "",                    null,                  "2000-01-02 00:00:00", "2000-01-01 00:01:00", "2000-01-02 00:00:00", "2000-01-01 00:01:00", true, false, false, null,  true,  false, true,  false ],
                    [ "5",  "A", "",  "",  null, "D00000", "00000D", "D00000", "00000D", 1, 0, 0, null,  4000000000000,  0.000000000004,  4000000000000,  0.000000000004, 1, 0, 0,  null,  4000000000000,  0.000000000004,  4000000000000,  0.000000000004, 1, 0, 0,  null,  400000000,  4,  400000000,  4, "2001-01-01", "",           "",           null,         "2005-01-01", "2001-01-05", "2005-01-01", "2001-01-05", "2001-01-01 01:01:01", "",                    "",                    null,                  "2000-01-02 00:00:00", "2000-01-01 00:01:00", "2000-01-02 00:00:00", "2000-01-01 00:01:00", true, false, false, null,  true,  false, true,  false ],
                    [ "6",  "A", "",  "",  "b",  "b00000", "00000b", "b00000", "00000b", 1, 0, 0,   -2, -2000000000000, -0.000000000002, -2000000000000, -0.000000000002, 1, 0, 0,    -2, -2000000000000, -0.000000000002, -2000000000000, -0.000000000002, 1, 0, 0,    -2, -200000000, -2, -200000000, -2, "2001-01-01", "",           "",           "2000-12-30", "1970-12-31", "1999-12-31", "1970-12-31", "1999-12-31", "2001-01-01 01:01:01", "",                    "",                    "2000-12-30 01:01:01", "1970-01-01 00:01:00", "1970-01-01 00:00:01", "1970-01-01 00:01:00", "1970-01-01 00:00:01", true, false, false, false, false, true,  false, true  ],
                    [ "7",  "A", "",  "",  "c",  "c00000", "00000c", "c00000", "00000c", 1, 0, 0,   -3, -3000000000000, -0.000000000003, -3000000000000, -0.000000000003, 1, 0, 0,    -3, -3000000000000, -0.000000000003, -3000000000000, -0.000000000003, 1, 0, 0,    -3, -300000000, -3, -300000000, -3, "2001-01-01", "",           "",           "2000-12-29", "1970-01-01", "1999-11-30", "1970-01-01", "1999-11-30", "2001-01-01 01:01:01", "",                    "",                    "2000-12-29 01:01:01", "1970-01-01 00:00:00", "1970-01-01 00:00:00", "1970-01-01 00:00:00", "1970-01-01 00:00:00", true, false, false, false, false, true,  false, true  ],
                    [ "8",  "A", "",  "",  "a",  "a00000", "00000a", "a00000", "00000a", 1, 0, 0,   -1, -1000000000000, -0.000000000001, -1000000000000, -0.000000000001, 1, 0, 0,    -1, -1000000000000, -0.000000000001, -1000000000000, -0.000000000001, 1, 0, 0,    -1, -100000000, -1, -100000000, -1, "2001-01-01", "",           "",           "2000-12-31", "2000-01-01", "2000-01-01", "2000-01-01", "2000-01-01", "2001-01-01 01:01:01", "",                    "",                    "2000-12-31 01:01:01", "1999-12-31 23:00:00", "1999-12-31 23:59:59", "1999-12-31 23:00:00", "1999-12-31 23:59:59", true, false, false, false, false, true,  false, true  ],
                    [ "9",  "A", "",  "",  "B",  "B00000", "00000B", "B00000", "00000B", 1, 0, 0,    2,  2000000000000,  0.000000000002,  2000000000000,  0.000000000002, 1, 0, 0,     2,  2000000000000,  0.000000000002,  2000000000000,  0.000000000002, 1, 0, 0,     2,  200000000,  2,  200000000,  2, "2001-01-01", "",           "",           "2001-01-03", "2002-01-01", "2000-01-03", "2002-01-01", "2000-01-03", "2001-01-01 01:01:01", "",                    "",                    "2001-01-03 01:01:01", "2000-01-01 01:00:00", "2000-01-01 00:00:01", "2000-01-01 01:00:00", "2000-01-01 00:00:01", true, false, false, true,  true,  false, true,  false ],
                    [ "10", "A", "",  "",  "C",  "C00000", "00000C", "C00000", "00000C", 1, 0, 0,    3,  3000000000000,  0.000000000003,  3000000000000,  0.000000000003, 1, 0, 0,     3,  3000000000000,  0.000000000003,  3000000000000,  0.000000000003, 1, 0, 0,     3,  300000000,  3,  300000000,  3, "2001-01-01", "",           "",           "2001-01-04", "2003-01-01", "2000-02-03", "2003-01-01", "2000-02-03", "2001-01-01 01:01:01", "",                    "",                    "2001-01-04 01:01:01", "2000-01-01 23:00:00", "2000-01-01 00:00:59", "2000-01-01 23:00:00", "2000-01-01 00:00:59", true, false, false, true,  true,  false, true,  false ],
                    [ "11", "A", "",  "",  "A",  "A00000", "00000A", "A00000", "00000A", 1, 0, 0,    1,  1000000000000,  0.000000000001,  1000000000000,  0.000000000001, 1, 0, 0,     1,  1000000000000,  0.000000000001,  1000000000000,  0.000000000001, 1, 0, 0,     1,  100000000,  1,  100000000,  1, "2001-01-01", "",           "",           "2001-01-02", "2001-01-01", "2000-01-02", "2001-01-01", "2000-01-02", "2001-01-01 01:01:01", "",                    "",                    "2001-01-02 01:01:01", "2000-01-01 00:00:00", "2000-01-01 00:00:00", "2000-01-01 00:00:00", "2000-01-01 00:00:00", true, false, false, true,  true,  false, true,  false ],
                    [ "12", "A", "",  "",  "d",  "d00000", "00000d", "d00000", "00000d", 1, 0, 0,   -4, -4000000000000, -0.000000000004, -4000000000000, -0.000000000004, 1, 0, 0,    -4, -4000000000000, -0.000000000004, -4000000000000, -0.000000000004, 1, 0, 0,    -4, -400000000, -4, -400000000, -4, "2001-01-01", "",           "",           "2000-12-28", "1969-12-31", "1998-12-28", "1969-12-31", "1998-12-28", "2001-01-01 01:01:01", "",                    "",                    "2000-12-28 01:01:01", "1969-12-31 23:59:00", "1969-12-31 23:59:59", "1969-12-31 23:59:00", "1969-12-31 23:59:59", true, false, false, false, false, true,  false, true  ],
                    [ "13", "A", "",  "",  "d",  "d00000", "00000d", "d00000", "00000d", 1, 0, 0,   -4, -4000000000000, -0.000000000004, -4000000000000, -0.000000000004, 1, 0, 0,    -4, -4000000000000, -0.000000000004, -4000000000000, -0.000000000004, 1, 0, 0,    -4, -400000000, -4, -400000000, -4, "2001-01-01", "",           "",           "2000-12-28", "1969-12-31", "1998-12-28", "1969-12-31", "1998-12-28", "2001-01-01 01:01:01", "",                    "",                    "2000-12-28 01:01:01", "1969-12-31 23:59:00", "1969-12-31 23:59:59", "1969-12-31 23:59:00", "1969-12-31 23:59:59", true, false, false, false, false, true,  false, true  ],
                    [ "14", "A", "",  "",  "d",  "d00000", "00000d", "d00000", "00000d", 1, 0, 0,   -4, -4000000000000, -0.000000000004, -4000000000000, -0.000000000004, 1, 0, 0,    -4, -4000000000000, -0.000000000004, -4000000000000, -0.000000000004, 1, 0, 0,    -4, -400000000, -4, -400000000, -4, "2001-01-01", "",           "",           "2000-12-28", "1969-12-31", "1998-12-28", "1969-12-31", "1998-12-28", "2001-01-01 01:01:01", "",                    "",                    "2000-12-28 01:01:01", "1969-12-31 23:59:00", "1969-12-31 23:59:59", "1969-12-31 23:59:00", "1969-12-31 23:59:59", true, false, false, false, false, true,  false, true  ],
                    [ "15", "A", "",  "",  null, "d00000", "00000d", "d00000", "00000d", 1, 0, 0, null, -4000000000000, -0.000000000004, -4000000000000, -0.000000000004, 1, 0, 0,  null, -4000000000000, -0.000000000004, -4000000000000, -0.000000000004, 1, 0, 0,  null, -400000000, -4, -400000000, -4, "2001-01-01", "",           "",           null,         "1969-12-31", "1998-12-28", "1969-12-31", "1998-12-28", "2001-01-01 01:01:01", "",                    "",                    null,                  "1969-12-31 23:59:00", "1969-12-31 23:59:59", "1969-12-31 23:59:00", "1969-12-31 23:59:59", true, false, false, null,  false, true,  false, true  ],
                    [ "16", "A", "",  "",  null, "E00000", "00000E", "E00000", "00000E", 1, 0, 0, null,  5000000000000,  0.000000000005,  5000000000000,  0.000000000005, 1, 0, 0,  null,  5000000000000,  0.000000000005,  5000000000000,  0.000000000005, 1, 0, 0,  null,  500000000,  5,  500000000,  5, "2001-01-01", "",           "",           null,         "2006-01-01", "2001-01-06", "2006-01-01", "2001-01-06", "2001-01-01 01:01:01", "",                    "",                    null,                  "2001-01-01 00:00:00", "2000-01-01 01:00:00", "2001-01-01 00:00:00", "2000-01-01 01:00:00", true, false, false, null,  true,  false, true,  false ],
                    [ "17", "A", "",  "",  null, "E00000", "00000E", "E00000", "00000E", 1, 0, 0, null,  5000000000000,  0.000000000005,  5000000000000,  0.000000000005, 1, 0, 0,  null,  5000000000000,  0.000000000005,  5000000000000,  0.000000000005, 1, 0, 0,  null,  500000000,  5,  500000000,  5, "2001-01-01", "",           "",           null,         "2006-01-01", "2001-01-06", "2006-01-01", "2001-01-06", "2001-01-01 01:01:01", "",                    "",                    null,                  "2001-01-01 00:00:00", "2000-01-01 01:00:00", "2001-01-01 00:00:00", "2000-01-01 01:00:00", true, false, false, null,  true,  false, true,  false ],
                    [ "18", "A", "",  "",  "E",  "E00000", "00000E", "E00000", "00000E", 1, 0, 0,    5,  5000000000000,  0.000000000005,  5000000000000,  0.000000000005, 1, 0, 0,     5,  5000000000000,  0.000000000005,  5000000000000,  0.000000000005, 1, 0, 0,     5,  500000000,  5,  500000000,  5, "2001-01-01", "",           "",           "2001-01-06", "2006-01-01", "2001-01-06", "2006-01-01", "2001-01-06", "2001-01-01 01:01:01", "",                    "",                    "2001-01-06 01:01:01", "2001-01-01 00:00:00", "2000-01-01 01:00:00", "2001-01-01 00:00:00", "2000-01-01 01:00:00", true, false, false, true,  true,  false, true,  false ],
                    [ "19", "A", "",  "",  "E",  "E00000", "00000E", "E00000", "00000E", 1, 0, 0,    5,  5000000000000,  0.000000000005,  5000000000000,  0.000000000005, 1, 0, 0,     5,  5000000000000,  0.000000000005,  5000000000000,  0.000000000005, 1, 0, 0,     5,  500000000,  5,  500000000,  5, "2001-01-01", "",           "",           "2001-01-06", "2006-01-01", "2001-01-06", "2006-01-01", "2001-01-06", "2001-01-01 01:01:01", "",                    "",                    "2001-01-06 01:01:01", "2001-01-01 00:00:00", "2000-01-01 01:00:00", "2001-01-01 00:00:00", "2000-01-01 01:00:00", true, false, false, true,  true,  false, true,  false ],
                    [ "20", "A", "",  "A", "E",  "E00000", "00000E", "E00000", "00000E", 1, 0, 1,    5,  5000000000000,  0.000000000005,  5000000000000,  0.000000000005, 1, 0, 1,     5,  5000000000000,  0.000000000005,  5000000000000,  0.000000000005, 1, 0, 1,     5,  500000000,  5,  500000000,  5, "2001-01-01", "",           "2001-01-01", "2001-01-06", "2006-01-01", "2001-01-06", "2006-01-01", "2001-01-06", "2001-01-01 01:01:01", "",                    "2001-01-01 01:01:01", "2001-01-06 01:01:01", "2001-01-01 00:00:00", "2000-01-01 01:00:00", "2001-01-01 00:00:00", "2000-01-01 01:00:00", true, false, true,  true,  true,  false, true,  false ]
                ]
            }
        }
EOD;
        return $task;
    }
}
