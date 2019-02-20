<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie LLC. All rights reserved.
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
        $model = \Flexio\Tests\Util::getModel();


        // TEST: object creation

        // BEGIN TEST
        $object = \Flexio\Object\Stream::create();
        $actual = 'Flexio\Object\Stream';
        $expected = get_class($object);
        \Flexio\Tests\Check::assertString('A.1', 'Stream::create(); return the object if it\'s successfully created',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Stream::create();
        $actual = $object->getType();
        $expected = \Model::TYPE_STREAM;
        \Flexio\Tests\Check::assertString('A.2', 'Stream::create(); make sure the correct type is set',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Stream::create();
        $actual = \Flexio\Base\Eid::isValid($object->getEid());
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.4', 'Stream::create(); make sure a valid eid is set when an object is created',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Stream::create();
        $actual = $object->getOwner();
        $expected = '';
        \Flexio\Tests\Check::assertString('A.5', 'Stream::create(); objects are created with no owner by default',  $actual, $expected, $results);

        // BEGIN TEST
        $object1 = \Flexio\Object\User::create();
        $object2 = \Flexio\Object\Stream::create(array('owned_by' => $object1->getEid()));
        $actual = $object2->getOwner();
        $expected = $object1->getEid();
        \Flexio\Tests\Check::assertString('A.6', 'Stream::create(); make sure the owner can be set properly',  $actual, $expected, $results);



        // TEST: object loading

        // BEGIN TEST
        $actual = '';
        try
        {
            $object = \Flexio\Object\Stream::load('');
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Flexio\Base\Exception $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('B.1', 'Stream::load(); throw exception if an object fails to load',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = '';
        try
        {
            $eid = $model->pipe->create(array()); // make sure eid of other types can't be loaded
            $object = \Flexio\Object\Stream::load($eid);
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Flexio\Base\Exception $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('B.2', 'Stream::load(); throw exception if an object fails to load',  $actual, $expected, $results);

        // BEGIN TEST
        $eid = $model->stream->create(array());
        $object = \Flexio\Object\Stream::load($eid);
        $actual = 'Flexio\Object\Stream';
        $expected = get_class($object);
        \Flexio\Tests\Check::assertString('B.3', 'Stream::load(); return the object if it\'s successfully loaded',  $actual, $expected, $results);

        // BEGIN TEST
        $eid = $model->stream->create(array());
        $object = \Flexio\Object\Stream::load($eid);
        $actual = $object->getType();
        $expected = \Model::TYPE_STREAM;
        \Flexio\Tests\Check::assertString('B.4', 'Stream::load(); make sure the type is set when an object is loaded',  $actual, $expected, $results);

        // BEGIN TEST
        $eid = $model->stream->create(array());
        $object = \Flexio\Object\Stream::load($eid);
        $actual = $eid;
        $expected = $object->getEid();
        \Flexio\Tests\Check::assertString('B.5', 'Stream::load(); make sure the eid is set when an object is loaded',  $actual, $expected, $results);



        // TEST: object deletion

        // BEGIN TEST
        $object = \Flexio\Object\Stream::create();
        $object = $object->delete();
        $actual =  'Flexio\Object\Stream';
        $expected = get_class($object);
        \Flexio\Tests\Check::assertString('C.1', 'Stream::delete(); return the object',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Stream::create();
        $eid1 = $object->getEid();
        $eid2 = $object->delete()->getEid();
        $actual =  \Flexio\Base\Eid::isValid($eid1) && $eid1 === $eid2;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.2', 'Stream::delete(); deleting an object shouldn\'t change its eid',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Stream::create();
        $object = $object->delete();
        $actual =  $object->getType();
        $expected = \Model::TYPE_STREAM;
        \Flexio\Tests\Check::assertString('C.3', 'Stream::delete(); deleting an object shouldn\'t change its type',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Stream::create();
        $status1 = $object->getStatus();
        $status2 = $object->delete()->getStatus();
        $actual =  ($status1 !== \Model::STATUS_DELETED && $status2 === \Model::STATUS_DELETED);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.4', 'Stream::delete(); make sure the status is set to deleted',  $actual, $expected, $results);



        // TEST: object property setting

        // BEGIN TEST
        $object = \Flexio\Object\Stream::create();
        $object = $object->set([]);
        $actual =  'Flexio\Object\Stream';
        $expected = get_class($object);
        \Flexio\Tests\Check::assertString('D.1', 'Stream::set(); return the object',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Stream::create();
        $eid1 = $object->getEid();
        $eid2 = $object->set([])->getEid();
        $actual =  \Flexio\Base\Eid::isValid($eid1) && $eid1 === $eid2;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('D.2', 'Stream::set(); don\'t allow the eid to be changed',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Stream::create();
        $eid1 = $object->getEid();
        $eid2 = $object->set(array('eid'=>'xxxxxxxxxxxx'))->getEid();
        $actual =  \Flexio\Base\Eid::isValid($eid1) && $eid1 === $eid2;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('D.3', 'Stream::set(); don\'t allow the eid to be changed',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Stream::create();
        $object = $object->set([]);
        $actual =  $object->getType();
        $expected = \Model::TYPE_STREAM;
        \Flexio\Tests\Check::assertString('D.4', 'Stream::set(); don\'t allow the type to be changed',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Stream::create();
        $object = $object->set(array('eid_type'=>\Model::TYPE_PIPE));
        $actual =  $object->getType();
        $expected = \Model::TYPE_STREAM;
        \Flexio\Tests\Check::assertString('D.5', 'Stream::set(); don\'t allow the type to be changed',  $actual, $expected, $results);



        // TEST: object property retrieval

        // BEGIN TEST
        $object = \Flexio\Object\Stream::create();
        $properties = $object->get();
        $actual =  is_array($properties);
        $expected = true;
        \Flexio\Tests\Check::assertString('E.1', 'Stream::get(); return the properties as an array',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Stream::create();
        $properties = $object->get();
        $actual =  $properties;
        $expected = json_decode('
        {
            "eid" : null,
            "eid_type" : null,
            "eid_status" : null,
            "stream_type" : null,
            "parent_eid" : null,
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
            "expires" : null,
            "owned_by" : {
                "eid" : null,
                "eid_type" : null
            },
            "created" : null,
            "updated" : null
        }
        ',true);
        \Flexio\Tests\Check::assertArrayKeys('E.2', 'Stream::get(); return the properties as an array',  $actual, $expected, $results);



        // TEST: object status change

        // BEGIN TEST
        $object = \Flexio\Object\Stream::create();
        $object = $object->setStatus(\Model::STATUS_PENDING);
        $actual =  'Flexio\Object\Stream';
        $expected = get_class($object);
        \Flexio\Tests\Check::assertString('F.1', 'Stream::setStatus(); return the object',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Stream::create();
        $eid1 = $object->getEid();
        $eid2 = $object->setStatus(\Model::STATUS_PENDING)->getEid();
        $actual =  \Flexio\Base\Eid::isValid($eid1) && $eid1 === $eid2;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('F.2', 'Stream::setStatus(); setting status of an object shouldn\'t change its eid',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Stream::create();
        $object = $object->setStatus(\Model::STATUS_PENDING);
        $actual =  $object->getType();
        $expected = \Model::TYPE_STREAM;
        \Flexio\Tests\Check::assertString('F.3', 'Stream::setStatus(); setting status of an object shouldn\'t change its type',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = '';
        try
        {
            $object = \Flexio\Object\Stream::create();
            $status1 = $object->setStatus(\Model::STATUS_PENDING)->getStatus();
            $status2 = $object->setStatus('.')->getStatus();
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Flexio\Base\Exception $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('F.4', 'Stream::setStatus(); don\'t allow an invalid status',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Stream::create();
        $status1 = $object->setStatus(\Model::STATUS_PENDING)->getStatus();
        $status2 = $object->setStatus(\Model::STATUS_DELETED)->getStatus();
        $actual =  ($status1 === \Model::STATUS_PENDING && $status2 === \Model::STATUS_DELETED);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('F.5', 'Stream::setStatus(); make sure the status is set',  $actual, $expected, $results);



        // TEST: object owner change

        // BEGIN TEST
        $random_eid = \Flexio\Base\Eid::generate();
        $object = \Flexio\Object\Stream::create();
        $object = $object->setOwner($random_eid);
        $actual = $object->getOwner();
        $expected = $random_eid;
        \Flexio\Tests\Check::assertString('G.1', 'Stream::setOwner(); make sure the owner is set',  $actual, $expected, $results);



        // TEST: basic content query

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 0, PHP_INT_MAX, 1);
        $expected = "abcdefg";
        \Flexio\Tests\Check::assertString('H.1', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 0, PHP_INT_MAX, 2);
        $expected = "abcdefg";
        \Flexio\Tests\Check::assertString('H.2', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 0, PHP_INT_MAX, 3);
        $expected = "abcdefg";
        \Flexio\Tests\Check::assertString('H.3', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 0, PHP_INT_MAX, 4);
        $expected = "abcdefg";
        \Flexio\Tests\Check::assertString('H.4', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 0, PHP_INT_MAX, 5);
        $expected = "abcdefg";
        \Flexio\Tests\Check::assertString('H.5', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 0, PHP_INT_MAX, 6);
        $expected = "abcdefg";
        \Flexio\Tests\Check::assertString('H.6', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 0, PHP_INT_MAX, 7);
        $expected = "abcdefg";
        \Flexio\Tests\Check::assertString('H.7', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 0, PHP_INT_MAX, 100);
        $expected = "abcdefg";
        \Flexio\Tests\Check::assertString('H.8', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 0, 0, 1);
        $expected = '';
        \Flexio\Tests\Check::assertString('H.9', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 0, 0, 2);
        $expected = '';
        \Flexio\Tests\Check::assertString('H.10', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 0, 0, 3);
        $expected = '';
        \Flexio\Tests\Check::assertString('H.11', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 0, 0, 4);
        $expected = '';
        \Flexio\Tests\Check::assertString('H.12', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 0, 0, 5);
        $expected = '';
        \Flexio\Tests\Check::assertString('H.13', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 0, 0, 6);
        $expected = '';
        \Flexio\Tests\Check::assertString('H.14', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 0, 0, 7);
        $expected = '';
        \Flexio\Tests\Check::assertString('H.15', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 0, 0, 100);
        $expected = '';
        \Flexio\Tests\Check::assertString('H.16', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 0, 1, 1);
        $expected = "a";
        \Flexio\Tests\Check::assertString('H.17', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 0, 1, 2);
        $expected = "a";
        \Flexio\Tests\Check::assertString('H.18', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 0, 1, 3);
        $expected = "a";
        \Flexio\Tests\Check::assertString('H.19', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 0, 1, 4);
        $expected = "a";
        \Flexio\Tests\Check::assertString('H.20', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 0, 1, 5);
        $expected = "a";
        \Flexio\Tests\Check::assertString('H.21', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 0, 1, 6);
        $expected = "a";
        \Flexio\Tests\Check::assertString('H.22', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 0, 1, 7);
        $expected = "a";
        \Flexio\Tests\Check::assertString('H.23', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 0, 1, 100);
        $expected = "a";
        \Flexio\Tests\Check::assertString('H.24', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 1, 1, 1);
        $expected = "b";
        \Flexio\Tests\Check::assertString('H.25', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 1, 1, 2);
        $expected = "b";
        \Flexio\Tests\Check::assertString('H.26', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 1, 1, 3);
        $expected = "b";
        \Flexio\Tests\Check::assertString('H.27', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 1, 1, 4);
        $expected = "b";
        \Flexio\Tests\Check::assertString('H.28', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 1, 1, 5);
        $expected = "b";
        \Flexio\Tests\Check::assertString('H.29', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 1, 1, 6);
        $expected = "b";
        \Flexio\Tests\Check::assertString('H.30', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 1, 1, 7);
        $expected = "b";
        \Flexio\Tests\Check::assertString('H.31', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 1, 1, 100);
        $expected = "b";
        \Flexio\Tests\Check::assertString('H.32', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 6, 1, 1);
        $expected = "g";
        \Flexio\Tests\Check::assertString('H.33', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 6, 1, 2);
        $expected = "g";
        \Flexio\Tests\Check::assertString('H.34', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 6, 1, 3);
        $expected = "g";
        \Flexio\Tests\Check::assertString('H.35', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 6, 1, 4);
        $expected = "g";
        \Flexio\Tests\Check::assertString('H.36', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 6, 1, 5);
        $expected = "g";
        \Flexio\Tests\Check::assertString('H.37', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 6, 1, 6);
        $expected = "g";
        \Flexio\Tests\Check::assertString('H.38', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 6, 1, 7);
        $expected = "g";
        \Flexio\Tests\Check::assertString('H.39', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 6, 1, 100);
        $expected = "g";
        \Flexio\Tests\Check::assertString('H.40', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 7, 1, 1);
        $expected = '';
        \Flexio\Tests\Check::assertString('H.41', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 7, 1, 2);
        $expected = '';
        \Flexio\Tests\Check::assertString('H.42', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 7, 1, 3);
        $expected = '';
        \Flexio\Tests\Check::assertString('H.43', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 7, 1, 4);
        $expected = '';
        \Flexio\Tests\Check::assertString('H.44', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 7, 1, 5);
        $expected = '';
        \Flexio\Tests\Check::assertString('H.45', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 7, 1, 6);
        $expected = '';
        \Flexio\Tests\Check::assertString('H.46', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 7, 1, 7);
        $expected = '';
        \Flexio\Tests\Check::assertString('H.47', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 7, 1, 100);
        $expected = '';
        \Flexio\Tests\Check::assertString('H.48', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 0, 2, 1);
        $expected = "ab";
        \Flexio\Tests\Check::assertString('H.49', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 0, 2, 2);
        $expected = "ab";
        \Flexio\Tests\Check::assertString('H.50', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 0, 2, 3);
        $expected = "ab";
        \Flexio\Tests\Check::assertString('H.51', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 0, 2, 4);
        $expected = "ab";
        \Flexio\Tests\Check::assertString('H.52', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 0, 2, 5);
        $expected = "ab";
        \Flexio\Tests\Check::assertString('H.53', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 0, 2, 6);
        $expected = "ab";
        \Flexio\Tests\Check::assertString('H.54', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 0, 2, 7);
        $expected = "ab";
        \Flexio\Tests\Check::assertString('H.55', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 0, 2, 100);
        $expected = "ab";
        \Flexio\Tests\Check::assertString('H.56', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 4, 2, 1);
        $expected = "ef";
        \Flexio\Tests\Check::assertString('H.57', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 4, 2, 2);
        $expected = "ef";
        \Flexio\Tests\Check::assertString('H.58', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 4, 2, 3);
        $expected = "ef";
        \Flexio\Tests\Check::assertString('H.59', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 4, 2, 4);
        $expected = "ef";
        \Flexio\Tests\Check::assertString('H.60', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 4, 2, 5);
        $expected = "ef";
        \Flexio\Tests\Check::assertString('H.61', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 4, 2, 6);
        $expected = "ef";
        \Flexio\Tests\Check::assertString('H.62', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 4, 2, 7);
        $expected = "ef";
        \Flexio\Tests\Check::assertString('H.63', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 4, 2, 100);
        $expected = "ef";
        \Flexio\Tests\Check::assertString('H.64', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 3, 3, 1);
        $expected = "def";
        \Flexio\Tests\Check::assertString('H.65', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 3, 3, 2);
        $expected = "def";
        \Flexio\Tests\Check::assertString('H.66', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 3, 3, 3);
        $expected = "def";
        \Flexio\Tests\Check::assertString('H.67', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 3, 3, 4);
        $expected = "def";
        \Flexio\Tests\Check::assertString('H.68', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 3, 3, 5);
        $expected = "def";
        \Flexio\Tests\Check::assertString('H.69', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 3, 3, 6);
        $expected = "def";
        \Flexio\Tests\Check::assertString('H.70', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 3, 3, 7);
        $expected = "def";
        \Flexio\Tests\Check::assertString('H.71', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 3, 3, 100);
        $expected = "def";
        \Flexio\Tests\Check::assertString('H.72', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, -1, 2, 1);
        $expected = "ab";
        \Flexio\Tests\Check::assertString('H.73', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 0, -1, 1);
        $expected = '';
        \Flexio\Tests\Check::assertString('H.74', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 0, 100, 1);
        $expected = "abcdefg";
        \Flexio\Tests\Check::assertString('H.75', 'Stream; check basic content query',  $actual, $expected, $results);

        // BEGIN TEST
        $stream_info = array();
        $stream_info['path'] = \Flexio\Base\Util::generateHandle();
        $stream_info['mime_type'] = \Flexio\Base\ContentType::TEXT;
        $stream_info['structure'] = array();
        $stream = \Flexio\Object\Stream::create($stream_info);
        $writer = $writer = $stream->getWriter();
        $writer->write("abcdefg");
        $writer->close();
        $actual = \Flexio\Base\Util::getStreamContents($stream, 0, 100, 0);
        $expected = "abcdefg";
        \Flexio\Tests\Check::assertString('H.76', 'Stream; check basic content query',  $actual, $expected, $results);
    }
}
