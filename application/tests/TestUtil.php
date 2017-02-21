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


namespace Flexio\Tests;


class TestError
{
    const ERROR_BAD_PARSE = 'ERROR_BAD_PARSE';
    const ERROR_EVAL_MISMATCH = 'ERROR_EVAL_MISMATCH';
}

class TestUtil
{
    const EPSILON = 0.000000000001;

    public static function evalExpression($expr)
    {
        // evaluate the expression with the native evaluator
        $result1 = self::evalExpressionNative($expr);

        // evaluate the expression with the postgres evaluator
        $result2 = self::evalExpressionPostgres($expr);

        if (is_double($result1) && is_double($result2))
        {
            if (self::dblcompare($result1, $result2) === 0)
                return $result1;
        }

        // compare the results; if they're equal and of the same
        // type, return the value, otherwise return a mismatch string
        if ($result1 === $result2)
            return $result1;

        $error = TestError::ERROR_EVAL_MISMATCH . ": PHP evaluation returned (" . gettype($result1) . ") $result1; Postgres evaluation returned (" . gettype($result2) . ") $result2";
        return $error;
    }

    public static function evalExpressionNative($expr)
    {
        $retval = null;
        $success = ExprEvaluate::evaluate($expr, [], [], $retval);
        if ($success === false)
            return TestError::ERROR_BAD_PARSE;

        return $retval;
    }

    public static function evalExpressionPostgres($expr)
    {
        global $g_store;

        $dbconfig = Model::getDatabaseConfig();

        // first, try to parse the expression
        $p = new ExprTranslatorPostgres;
        $err = $p->parse($expr);
        if ($err === false)
            return TestError::ERROR_BAD_PARSE;

        $postgres_expr = $p->getResult();

        // if the expression parses successfully, evaluate it
        $params = array();
        $params['host'] = $dbconfig['datastore_host'];
        $params['port'] = $dbconfig['datastore_port'];
        $params['database'] = $dbconfig['datastore_dbname'];
        $params['username'] = $dbconfig['datastore_username'];
        $params['password'] = $dbconfig['datastore_password'];

        $datastore_id = $params['database'] . ';' . $params['host'];
        if (!isset($g_store->datastores[$datastore_id]))
            $g_store->datastores[$datastore_id] = \PostgresService::create($params);

        $datastore = $g_store->datastores[$datastore_id];
        $pdo = $datastore->getPDO();

        try
        {
            $stmt = $pdo->query("SELECT $postgres_expr AS testval");
        }
        catch (Exception $e)
        {
            return 'Database Exception: ' . $e->getMessage();
        }

        $metadata = $stmt->getColumnMeta(0);

        foreach ($stmt as $row)
        {
            if (!array_key_exists('testval', $row))
            {
                return 'Database Error: No value returned (SQL comment?).  Row Array: ' . var_export($row,true);
            }

            if (is_null($row['testval']))
                return null;

            if (!isset($metadata['pgsql:oid']))
                return $row['testval'];

            // cast the value to the right type
            switch ($metadata['pgsql:oid'])
            {
                case 17:  // bytea
                case 20:  // int8
                case 21:  // int2
                case 23:  // int4
                case 26:  // oid
                    return (int)$row['testval'];

                case 1700: // numeric
                case 790:  // money
                case 700:  // float4
                case 701:  // float8
                        return (float)$row['testval'];

                default:
                    return $row['testval'];
            }

        }

        return '';
    }

    public static function getModel()
    {
        return new Model;
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
        if (\Eid::isValid($user_eid))
            return $user_eid;

        $user_eid = TestUtil::createTestUser($user_name, $email, $password);
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

        $search_path = "$user_eid->(".Model::EDGE_OWNS.")->(".Model::TYPE_PROJECT.")";
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
        $project_eid = TestUtil::createTestProject($user_eid, $project_name);
        return $project_eid;
    }

    public static function createTestUser($username, $email, $password)
    {
        $verify_code = \Util::generateHandle();
        $new_user_info = array('user_name' => $username,
                               'email' => $email,
                               'full_name' => $username,
                               'eid_status' => Model::STATUS_AVAILABLE,
                               'password' => $password,
                               'verify_code' => '');

        $user = \Flexio\Object\User::create($new_user_info);
        if ($user === false)
            return false;

        return $user->getEid();
    }

    public static function createTestProject($user_eid, $name = null, $description = null)
    {
        $properties['name'] = isset_or($name, _('Test Project'));
        $properties['description'] = isset_or($description, _('Test project with test data.'));

        $project = \Flexio\Object\Project::create($properties);
        if ($project === false)
            return false;

        $project->setOwner($user_eid);
        $project->setCreatedBy($user_eid);

        return $project->getEid();
    }

    public static function createTestPipe($user_eid, $project_eid, $pipe_name)
    {
        $properties['name'] = $pipe_name;

        $pipe = \Flexio\Object\Pipe::create($properties);
        if ($pipe === false)
            return false;

        // set the owner
        $pipe->setOwner($user_eid);
        $pipe->setCreatedBy($user_eid);

        // if a parent project is specified, add the object as a member of the project
        $project = \Flexio\Object\Project::load($project_eid);
        if ($project !== false)
            $project->addMember($pipe->getEid());

        return $pipe->getEid();
    }

    public static function getProcessResult($process, $start=0, $limit=100)
    {
        // fail if we don't have a process
        if (($process instanceof \Flexio\Object\Process) === false)
            return false;

        if ($process === false)
            return false;

        if ($process->getProcessStatus() !== \Model::PROCESS_STATUS_COMPLETED)
            return false;

        $streams = $process->getTaskOutputStreams();

        $result = array();
        foreach ($streams as $s)
        {
            $result[] = $s->content($start, $limit, true, true); // limit test data to 100 rows
        }

        return $result;
    }

    public static function getProcessSingleOutputResult($process, $with_keys=false, $start=0, $limit=100)
    {
        $result = self::getProcessResult($process, $start, $limit);
        if ($result === false)
            return false;

        if (count($result) === 0)
            return false;

        $columns = $result[0]['columns'];
        $rows = $result[0]['rows'];

        $result = array();
        $result['columns'] = $columns;
        $result['rows'] = array();

        if (is_array($rows))
        {
            foreach ($rows as $r)
            {
                $result['rows'][] = ($with_keys === true ? $r : array_values($r));
            }
        }

        return $result;
    }

    public static function getProcessSingleOutputColumnResult($process)
    {
        $result = self::getProcessResult($process);
        if ($result === false)
            return false;

        if (count($result) === 0)
            return false;

        if (!isset($result[0]['columns']))
            return false;

        return $result[0]['columns'];
    }

    public static function getProcessSingleOutputColumnNameResult($process)
    {
        $info = TestUtil::getProcessSingleOutputColumnResult($process);
        if ($info === false)
            return false;

        return array_column($info, 'name');
    }

    public static function getProcessSingleOutputRowResult($process, $with_keys = false)
    {
        $result = self::getProcessResult($process);
        if ($result === false)
            return false;

        if (count($result) === 0)
            return false;

        if (!isset($result[0]['rows']))
            return false;

        $rows = $result[0]['rows'];

        $result = array();

        foreach ($rows as $r)
        {
            $result[] = ($with_keys === true ? $r : array_values($r));
        }

        return $result;
    }

    public static function generateEmail()
    {
        $handle1 = \Util::generateHandle();
        $handle2 = \Util::generateHandle();
        return $handle1 . '@' . $handle2 . '.com';
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
