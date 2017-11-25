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

    public static function getModel()
    {
        return new \Model;
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

    public static function createEmailAddress()
    {
        $handle1 = \Flexio\Base\Util::generateHandle();
        $handle2 = \Flexio\Base\Util::generateHandle();
        return $handle1 . '@' . $handle2 . '.com';
    }

    public static function getTable(\Flexio\Base\IStream $stream) : array
    {
        $r = array();
        $r['columns'] = $stream->getStructure()->get();
        $r['rows'] = \Flexio\Base\Util::getStreamContents($stream, $start, $limit);
        $result[] = $r;
    }

    public static function getContent(\Flexio\Base\IStream $stream) : array
    {
        $content = \Flexio\Base\Util::getStreamContents($stream);
        $result = array();
        if (is_array($content))
        {
            foreach ($content as $row)
            {
                $result[] = array_values($row);
            }
        }

        return $result;
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
}
