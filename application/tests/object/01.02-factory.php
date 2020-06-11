<?php
/**
 *
 * Copyright (c) 2020, Flex Research LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2020-06-10
 *
 * @package flexio
 * @subpackage Tests
 */


declare(strict_types=1);
namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        $demo_dir = dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'scripts' . DIRECTORY_SEPARATOR . 'demo' . DIRECTORY_SEPARATOR;

        // TEST: pipe info extraction from files

        // BEGIN TEST
        $file_name = $demo_dir . 'flex-sample-contacts.py';
        $content = \Flexio\Base\File::read($file_name);
        $extension = strtolower(\Flexio\Base\File::getFileExtension($file_name));
        $properties = \Flexio\Object\Factory::getPipeInfoFromContent($content, $extension);
        $actual = array();
        $actual['name'] = $properties['name'] ?? '';
        $actual['deploy_mode'] = $properties['deploy_mode'] ?? '';
        $actual['run_mode'] = $properties['run_mode'] ?? '';
        $actual['title'] = $properties['title'] ?? '';
        $actual['description'] = $properties['description'] ?? '';
        $expected = '
        {
            "name": "flex-sample-contacts",
            "deploy_mode": "R",
            "run_mode": "I",
            "title": "Flex Sample Contacts",
            "description": "Returns sample contact information"
        }';
        \Flexio\Tests\Check::assertInArray('A.1', '\Flexio\Object\Factory::getPipeInfoFromContent(); check that properties are properly read',  $actual, $expected, $results);
    }
}
