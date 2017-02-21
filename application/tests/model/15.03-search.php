<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-07-14
 *
 * @package flexio
 * @subpackage Tests
 */


namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        // SETUP
        $model = TestUtil::getModel();



        // TEST: search tests when results for single eid

        // BEGIN TEST
        $model->clearErrors();
        $info = array(
        );
        $eid = $model->create(\Model::TYPE_OBJECT, $info);
        $path = "$eid";
        $result = $model->search($path);
        $actual = $result;
        $expected = array(
            $eid
        );
        TestCheck::assertArray('A.1', '\Model::search(); search for single eid that exists',  $actual, $expected, $results);
    }
}
