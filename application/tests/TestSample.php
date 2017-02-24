<?php
/**
 *
 * Copyright (c) 2015-2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-02-01
 *
 * @package flexio
 * @subpackage Controller
 */


namespace Flexio\Tests;


class TestSample
{
    public static function createProjectSample($user_count = 4, $project_count = 2, $pipe_count = 2)
    {
        for ($user = 1; $user <= $user_count; ++$user)
        {
            if (($user % 10) == 0)
                echo "<br>$user";

            $user_name = "user$user";
            $email = "user$user@flex.io";
            $password = 'test99';

            if (\Flexio\System\Eid::isValid(TestUtil::getModel()->user->getEidFromIdentifier($user_name)))
            {
                // this user already exists
                continue;
            }

            $user_eid = TestUtil::createTestUser($user_name, $email, $password);

            for ($project = 1; $project <= $project_count; ++$project)
            {
                $project_name = "Project$project";
                $project_eid = TestUtil::createTestProject($user_eid, $project_name);

                for ($pipe = 1; $pipe <= $pipe_count; ++$pipe)
                {
                    $pipe_name = "Pipe$pipe";
                    $pipe_eid = TestUtil::createTestPipe($user_eid, $project_eid, $pipe_name);
                }
            }
        }

        return true;
    }

    public static function getCreateSampleDataTask()
    {
        $task = <<<EOD
        {
            "type": "flexio.create",
            "params": {
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
                "rows" : [
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
