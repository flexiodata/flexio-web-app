<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-04-20
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
        // SETUP
        $model = TestUtil::getModel();



        // TEST: object creation

        // BEGIN TEST
        $object = \Flexio\Object\Stream::create();
        $actual = 'Flexio\Object\Stream';
        $expected = get_class($object);
        TestCheck::assertString('A.1', 'Stream::create(); return the object if it\'s successfully created',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Stream::create();
        $actual = $object->getType();
        $expected = \Model::TYPE_STREAM;
        TestCheck::assertString('A.2', 'Stream::create(); make sure the correct type is set',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Stream::create();
        $actual = \Flexio\Base\Eid::isValid($object->getEid());
        $expected = true;
        TestCheck::assertBoolean('A.4', 'Stream::create(); make sure a valid eid is set when an object is created',  $actual, $expected, $results);



        // TEST: object loading

        // BEGIN TEST
        $object = \Flexio\Object\Stream::load('');
        $actual = $object;
        $expected = false;
        TestCheck::assertBoolean('B.1', 'Stream::load(); return false if an object fails to load',  $actual, $expected, $results);

        // BEGIN TEST
        $eid = $model->create(\Model::TYPE_OBJECT, null);
        $object = \Flexio\Object\Stream::load($eid);
        $actual = $object;
        $expected = false;
        TestCheck::assertBoolean('B.2', 'Stream::load(); return the object if it\'s successfully loaded',  $actual, $expected, $results);

        // BEGIN TEST
        $eid = $model->create(\Model::TYPE_STREAM, null);
        $object = \Flexio\Object\Stream::load($eid);
        $actual = 'Flexio\Object\Stream';
        $expected = get_class($object);
        TestCheck::assertString('B.3', 'Stream::load(); return the object if it\'s successfully loaded',  $actual, $expected, $results);

        // BEGIN TEST
        $eid = $model->create(\Model::TYPE_STREAM, null);
        $object = \Flexio\Object\Stream::load($eid);
        $actual = $object->getType();
        $expected = \Model::TYPE_STREAM;
        TestCheck::assertString('B.4', 'Stream::load(); make sure the type is set when an object is loaded',  $actual, $expected, $results);

        // BEGIN TEST
        $eid = $model->create(\Model::TYPE_STREAM, null);
        $object = \Flexio\Object\Stream::load($eid);
        $actual = $eid;
        $expected = $object->getEid();
        TestCheck::assertString('B.5', 'Stream::load(); make sure the eid is set when an object is loaded',  $actual, $expected, $results);



        // TEST: object deletion

        // BEGIN TEST
        $object = \Flexio\Object\Stream::create();
        $object = $object->delete();
        $actual =  'Flexio\Object\Stream';
        $expected = get_class($object);
        TestCheck::assertString('C.1', 'Stream::delete(); return the object',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Stream::create();
        $eid1 = $object->getEid();
        $eid2 = $object->delete()->getEid();
        $actual =  \Flexio\Base\Eid::isValid($eid1) && $eid1 === $eid2;
        $expected = true;
        TestCheck::assertBoolean('C.2', 'Stream::delete(); deleting an object shouldn\'t change its eid',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Stream::create();
        $object = $object->delete();
        $actual =  $object->getType();
        $expected = \Model::TYPE_STREAM;
        TestCheck::assertString('C.3', 'Stream::delete(); deleting an object shouldn\'t change its type',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Stream::create();
        $status1 = $object->getStatus();
        $status2 = $object->delete()->getStatus();
        $actual =  ($status1 !== \Model::STATUS_DELETED && $status2 === \Model::STATUS_DELETED);
        $expected = true;
        TestCheck::assertBoolean('C.4', 'Stream::delete(); make sure the status is set to deleted',  $actual, $expected, $results);



        // TEST: object property setting

        // BEGIN TEST
        $object = \Flexio\Object\Stream::create();
        $object = $object->set([]);
        $actual =  'Flexio\Object\Stream';
        $expected = get_class($object);
        TestCheck::assertString('D.1', 'Stream::set(); return the object',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Stream::create();
        $eid1 = $object->getEid();
        $eid2 = $object->set([])->getEid();
        $actual =  \Flexio\Base\Eid::isValid($eid1) && $eid1 === $eid2;
        $expected = true;
        TestCheck::assertBoolean('D.2', 'Stream::set(); don\'t allow the eid to be changed',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Stream::create();
        $eid1 = $object->getEid();
        $eid2 = $object->set(array('eid'=>'xxxxxxxxxxxx'))->getEid();
        $actual =  \Flexio\Base\Eid::isValid($eid1) && $eid1 === $eid2;
        $expected = true;
        TestCheck::assertBoolean('D.3', 'Stream::set(); don\'t allow the eid to be changed',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Stream::create();
        $object = $object->set([]);
        $actual =  $object->getType();
        $expected = \Model::TYPE_STREAM;
        TestCheck::assertString('D.4', 'Stream::set(); don\'t allow the type to be changed',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Stream::create();
        $object = $object->set(array('eid_type'=>\Model::TYPE_OBJECT));
        $actual =  $object->getType();
        $expected = \Model::TYPE_STREAM;
        TestCheck::assertString('D.5', 'Stream::set(); don\'t allow the type to be changed',  $actual, $expected, $results);



        // TEST: object property retrieval

        // BEGIN TEST
        $object = \Flexio\Object\Stream::create();
        $properties = $object->get();
        $actual =  is_array($properties);
        $expected = true;
        TestCheck::assertString('E.1', 'Stream::get(); return the properties as an array',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Stream::create();
        $properties = $object->get();
        $actual =  $properties;
        $expected = json_decode('
        {
            "eid" : null,
            "eid_type" : null,
            "eid_status" : null,
            "name" : null,
            "path" : null,
            "size" : null,
            "hash" : null,
            "mime_type" : null,
            "structure" : {
            },
            "file_created" : null,
            "file_modified" : null,
            "connection_eid" :  null,
            "created" : null,
            "updated" : null
        }
        ',true);
        TestCheck::assertArrayKeys('E.2', 'Stream::get(); return the properties as an array',  $actual, $expected, $results);



        // TEST: object status change

        // BEGIN TEST
        $object = \Flexio\Object\Stream::create();
        $object = $object->setStatus(\Model::STATUS_TRASH);
        $actual =  'Flexio\Object\Stream';
        $expected = get_class($object);
        TestCheck::assertString('F.1', 'Stream::setStatus(); return the object',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Stream::create();
        $eid1 = $object->getEid();
        $eid2 = $object->setStatus(\Model::STATUS_TRASH)->getEid();
        $actual =  \Flexio\Base\Eid::isValid($eid1) && $eid1 === $eid2;
        $expected = true;
        TestCheck::assertBoolean('F.2', 'Stream::setStatus(); setting status of an object shouldn\'t change its eid',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Stream::create();
        $object = $object->setStatus(\Model::STATUS_TRASH);
        $actual =  $object->getType();
        $expected = \Model::TYPE_STREAM;
        TestCheck::assertString('F.3', 'Stream::setStatus(); setting status of an object shouldn\'t change its type',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = '';
        try
        {
            $object = \Flexio\Object\Stream::create();
            $status1 = $object->setStatus(\Model::STATUS_TRASH)->getStatus();
            $status2 = $object->setStatus('.')->getStatus();
            $actual = \Flexio\Tests\TestError::ERROR_NO_EXCEPTION;
        }
        catch (\Exception $e)
        {
            $actual = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        TestCheck::assertString('F.4', 'Stream::setStatus(); don\'t allow an invalid status',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Stream::create();
        $status1 = $object->setStatus(\Model::STATUS_PENDING)->getStatus();
        $status2 = $object->setStatus(\Model::STATUS_TRASH)->getStatus();
        $actual =  ($status1 === \Model::STATUS_PENDING && $status2 === \Model::STATUS_TRASH);
        $expected = true;
        TestCheck::assertBoolean('F.5', 'Stream::setStatus(); make sure the status is set',  $actual, $expected, $results);



        // TEST: basic content query

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_TXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = $stream->content(0, PHP_INT_MAX, 1);
        $expected = "abcdefg";
        TestCheck::assertString('G.1', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_TXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = $stream->content(0, PHP_INT_MAX, 2);
        $expected = "abcdefg";
        TestCheck::assertString('G.2', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_TXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = $stream->content(0, PHP_INT_MAX, 3);
        $expected = "abcdefg";
        TestCheck::assertString('G.3', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_TXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = $stream->content(0, PHP_INT_MAX, 4);
        $expected = "abcdefg";
        TestCheck::assertString('G.4', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_TXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = $stream->content(0, PHP_INT_MAX, 5);
        $expected = "abcdefg";
        TestCheck::assertString('G.5', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_TXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = $stream->content(0, PHP_INT_MAX, 6);
        $expected = "abcdefg";
        TestCheck::assertString('G.6', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_TXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = $stream->content(0, PHP_INT_MAX, 7);
        $expected = "abcdefg";
        TestCheck::assertString('G.7', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_TXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = $stream->content(0, PHP_INT_MAX, 100);
        $expected = "abcdefg";
        TestCheck::assertString('G.8', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_TXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = $stream->content(0, 0, 1);
        $expected = '';
        TestCheck::assertString('G.9', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_TXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = $stream->content(0, 0, 2);
        $expected = '';
        TestCheck::assertString('G.10', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_TXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = $stream->content(0, 0, 3);
        $expected = '';
        TestCheck::assertString('G.11', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_TXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = $stream->content(0, 0, 4);
        $expected = '';
        TestCheck::assertString('G.12', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_TXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = $stream->content(0, 0, 5);
        $expected = '';
        TestCheck::assertString('G.13', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_TXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = $stream->content(0, 0, 6);
        $expected = '';
        TestCheck::assertString('G.14', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_TXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = $stream->content(0, 0, 7);
        $expected = '';
        TestCheck::assertString('G.15', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_TXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = $stream->content(0, 0, 100);
        $expected = '';
        TestCheck::assertString('G.16', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_TXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = $stream->content(0, 1, 1);
        $expected = "a";
        TestCheck::assertString('G.17', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_TXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = $stream->content(0, 1, 2);
        $expected = "a";
        TestCheck::assertString('G.18', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_TXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = $stream->content(0, 1, 3);
        $expected = "a";
        TestCheck::assertString('G.19', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_TXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = $stream->content(0, 1, 4);
        $expected = "a";
        TestCheck::assertString('G.20', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_TXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = $stream->content(0, 1, 5);
        $expected = "a";
        TestCheck::assertString('G.21', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_TXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = $stream->content(0, 1, 6);
        $expected = "a";
        TestCheck::assertString('G.22', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_TXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = $stream->content(0, 1, 7);
        $expected = "a";
        TestCheck::assertString('G.23', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_TXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = $stream->content(0, 1, 100);
        $expected = "a";
        TestCheck::assertString('G.24', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_TXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = $stream->content(1, 1, 1);
        $expected = "b";
        TestCheck::assertString('G.25', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_TXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = $stream->content(1, 1, 2);
        $expected = "b";
        TestCheck::assertString('G.26', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_TXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = $stream->content(1, 1, 3);
        $expected = "b";
        TestCheck::assertString('G.27', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_TXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = $stream->content(1, 1, 4);
        $expected = "b";
        TestCheck::assertString('G.28', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_TXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = $stream->content(1, 1, 5);
        $expected = "b";
        TestCheck::assertString('G.29', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_TXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = $stream->content(1, 1, 6);
        $expected = "b";
        TestCheck::assertString('G.30', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_TXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = $stream->content(1, 1, 7);
        $expected = "b";
        TestCheck::assertString('G.31', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_TXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = $stream->content(1, 1, 100);
        $expected = "b";
        TestCheck::assertString('G.32', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_TXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = $stream->content(6, 1, 1);
        $expected = "g";
        TestCheck::assertString('G.33', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_TXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = $stream->content(6, 1, 2);
        $expected = "g";
        TestCheck::assertString('G.34', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_TXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = $stream->content(6, 1, 3);
        $expected = "g";
        TestCheck::assertString('G.35', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_TXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = $stream->content(6, 1, 4);
        $expected = "g";
        TestCheck::assertString('G.36', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_TXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = $stream->content(6, 1, 5);
        $expected = "g";
        TestCheck::assertString('G.37', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_TXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = $stream->content(6, 1, 6);
        $expected = "g";
        TestCheck::assertString('G.38', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_TXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = $stream->content(6, 1, 7);
        $expected = "g";
        TestCheck::assertString('G.39', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_TXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = $stream->content(6, 1, 100);
        $expected = "g";
        TestCheck::assertString('G.40', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_TXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = $stream->content(7, 1, 1);
        $expected = '';
        TestCheck::assertString('G.41', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_TXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = $stream->content(7, 1, 2);
        $expected = '';
        TestCheck::assertString('G.42', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_TXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = $stream->content(7, 1, 3);
        $expected = '';
        TestCheck::assertString('G.43', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_TXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = $stream->content(7, 1, 4);
        $expected = '';
        TestCheck::assertString('G.44', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_TXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = $stream->content(7, 1, 5);
        $expected = '';
        TestCheck::assertString('G.45', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_TXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = $stream->content(7, 1, 6);
        $expected = '';
        TestCheck::assertString('G.46', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_TXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = $stream->content(7, 1, 7);
        $expected = '';
        TestCheck::assertString('G.47', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_TXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = $stream->content(7, 1, 100);
        $expected = '';
        TestCheck::assertString('G.48', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_TXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = $stream->content(0, 2, 1);
        $expected = "ab";
        TestCheck::assertString('G.49', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_TXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = $stream->content(0, 2, 2);
        $expected = "ab";
        TestCheck::assertString('G.50', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_TXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = $stream->content(0, 2, 3);
        $expected = "ab";
        TestCheck::assertString('G.51', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_TXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = $stream->content(0, 2, 4);
        $expected = "ab";
        TestCheck::assertString('G.52', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_TXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = $stream->content(0, 2, 5);
        $expected = "ab";
        TestCheck::assertString('G.53', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_TXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = $stream->content(0, 2, 6);
        $expected = "ab";
        TestCheck::assertString('G.54', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_TXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = $stream->content(0, 2, 7);
        $expected = "ab";
        TestCheck::assertString('G.55', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_TXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = $stream->content(0, 2, 100);
        $expected = "ab";
        TestCheck::assertString('G.56', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_TXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = $stream->content(4, 2, 1);
        $expected = "ef";
        TestCheck::assertString('G.57', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_TXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = $stream->content(4, 2, 2);
        $expected = "ef";
        TestCheck::assertString('G.58', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_TXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = $stream->content(4, 2, 3);
        $expected = "ef";
        TestCheck::assertString('G.59', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_TXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = $stream->content(4, 2, 4);
        $expected = "ef";
        TestCheck::assertString('G.60', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_TXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = $stream->content(4, 2, 5);
        $expected = "ef";
        TestCheck::assertString('G.61', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_TXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = $stream->content(4, 2, 6);
        $expected = "ef";
        TestCheck::assertString('G.62', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_TXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = $stream->content(4, 2, 7);
        $expected = "ef";
        TestCheck::assertString('G.63', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_TXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = $stream->content(4, 2, 100);
        $expected = "ef";
        TestCheck::assertString('G.64', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_TXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = $stream->content(3, 3, 1);
        $expected = "def";
        TestCheck::assertString('G.65', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_TXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = $stream->content(3, 3, 2);
        $expected = "def";
        TestCheck::assertString('G.66', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_TXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = $stream->content(3, 3, 3);
        $expected = "def";
        TestCheck::assertString('G.67', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_TXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = $stream->content(3, 3, 4);
        $expected = "def";
        TestCheck::assertString('G.68', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_TXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = $stream->content(3, 3, 5);
        $expected = "def";
        TestCheck::assertString('G.69', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_TXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = $stream->content(3, 3, 6);
        $expected = "def";
        TestCheck::assertString('G.70', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_TXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = $stream->content(3, 3, 7);
        $expected = "def";
        TestCheck::assertString('G.71', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_TXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = $stream->content(3, 3, 100);
        $expected = "def";
        TestCheck::assertString('G.72', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_TXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = $stream->content(-1, 2, 1);
        $expected = "ab";
        TestCheck::assertString('G.73', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_TXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = $stream->content(0, -1, 1);
        $expected = '';
        TestCheck::assertString('G.74', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_TXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = $stream->content(0, 100, 1);
        $expected = "abcdefg";
        TestCheck::assertString('G.75', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_TXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = $stream->content(0, 100, 0);
        $expected = "abcdefg";
        TestCheck::assertString('G.76', 'Stream; check basic content query',  $actual, $expected, $results);
    }
}
