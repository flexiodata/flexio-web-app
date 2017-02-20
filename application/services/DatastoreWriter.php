<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-05-26
 *
 * @package flexio
 * @subpackage Services
 */


class DatastoreWriter
{
    private $postgres = null;
    private $path = '';
    private $stream = null;
    private $inserter = null;
    private $mime_type = '';
    private $buffer = '';
    private $structure = null;

    public static function create($postgres, $path)
    {
        $writer = new self;
        $writer->postgres = $postgres;
        $writer->path = $path;
        return $writer;
    }

    public function createFile($mime_type = "application/octet-stream")
    {
        if (!$this->postgres->createFile($this->path, $mime_type))
            return false;

        $stream = $this->postgres->openFile($this->path);
        if (!$stream)
            return false;

        $this->structure = null;
        $this->stream = $stream;
        $this->mime_type = $mime_type;
        $this->buffer = '';
        $this->type = 'stream';

        return true;
    }

    public function createTable($structure)
    {
        // note: DatastoreReader and DatastoreWriter abstract the reading/writing
        // of data from the storage; internally, an xdrowid is associated with
        // each row which raw operations may access, but which we want hidden in
        // normal use; implicitly add this field when new tables are created

        // prepend the structure with an xdrowid
        $extra_fields = array(
            array(
                'name' => 'xdrowid',
                'type' => 'bigserial',
                'width' => 18,
                'scale' => 0
            )
        );

        $structure =  array_merge($extra_fields, $structure);

        // filter out all extraneous structure info except what's needed to
        // create the table (e.g. any "expression" key/values); only pass
        // on structure elements that are available (e.g. width or scale
        // may not always be specified for some columns, such as a text
        // column)

        $create_structure = array();
        foreach ($structure as $c)
        {
            $column = array();

            if (isset($c['name']))
                $column['name'] = $c['name'];

            if (isset($c['type']))
                $column['type'] = $c['type'];

            if (isset($c['width']))
                $column['width'] = $c['width'];

            if (isset($c['scale']))
                $column['scale'] = $c['scale'];

            $create_structure[] = $column;
        }

        if (!$this->postgres->createTable($this->path, $create_structure))
            return false;

        $this->structure = $this->describeTable($this->path);
        $this->mime_type = ContentType::MIME_TYPE_FLEXIO_TABLE;
        $this->type = 'table';

        return true;
    }

    public function describeTable()
    {
        // if the structure is cached, return it
        if ($this->structure)
            return $this->structure;

        // get the structure
        $struct = $this->postgres->describeTable($this->path);

        // remove the xdrowid field
        $structure_local = array();
        foreach ($struct as $s)
        {
            if (isset($s['name']) && $s['name'] != 'xdrowid')
                $structure_local[] = $s;
        }

        $this->structure = $structure_local;
        return $this->structure;
    }

    public function write($data)
    {
        $this->stream->write($data);
    }

    public function insertRow($row)
    {
        // don't allow xdrowid values to overwrite default internal values
        unset($row['xdrowid']);

        if (!$this->inserter)
        {
            $flds = [];
            foreach ($this->structure as $f)
                $flds[] = $f['name'];

            $this->inserter = $this->postgres->bulkInsert($this->path);
            if ($this->inserter)
            {
                $result = $this->inserter->startInsert($flds);
                if ($result === false)
                    return false;
            }
        }

        if ($this->inserter)
        {
            $result = $this->inserter->insertRow($row);
            if ($result === false)
                return false;
        }

        return true;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function setMimeType($mime_type)
    {
        $this->mime_type = $mime_type;
    }

    public function getMimeType()
    {
        return $this->mime_type;
    }

    public function refresh()
    {
        $this->structure = null;
        return $this->describeTable();
    }

    public function flush()
    {
        $result = false;
        if (is_null($this->stream))
            $result = $this->flushTable();
             else
            $result = $this->flushStream();

        return $result;
    }

    public function close()
    {
        $result = false;
        if (is_null($this->stream))
            $result = $this->closeTable();
             else
            $result = $this->closeStream();

        return $result;
    }

    public function exec($sql)
    {
        if ($this->postgres->exec($sql) === false)
            return false;

        return true;
    }

    public function quote($str)
    {
        return $this->postgres->getPDO()->quote($str);
    }

    private function flushTable()
    {
        if ($this->inserter)
            return $this->inserter->flush();

        return true;
    }

    private function flushStream()
    {
        return true;
    }

    private function closeTable()
    {
        if ($this->inserter)
        {
            $result = $this->inserter->finishInsert();
            $this->inserter = null;

            if ($result === false)
                return false;
        }

        return true;
    }

    private function closeStream()
    {
        $this->stream = null;
        return true;
    }
}
