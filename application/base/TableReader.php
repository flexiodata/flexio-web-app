<?php
/**
 *
 * Copyright (c) 2011, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams
 * Created:  2011-05-17
 *
 * @package flexio
 * @subpackage Base
 */


declare(strict_types=1);
namespace Flexio\Base;


class TableReader
{
    public static function open($filename)
    {
        $extension = \Flexio\Base\File::getFileExtension($filename);

        if (0 == strcasecmp($extension, 'csv') ||
            0 == strcasecmp($extension, 'icsv') ||
            0 == strcasecmp($extension, 'txt'))
        {
            $tbl = new CsvReader();
            if ($tbl->init($filename))
                return $tbl;
                 else
                return null;
        }
         else if (0 == strcasecmp($extension, 'dbf'))
        {
            $tbl = new XbaseReader();
            if ($tbl->init($filename))
                return $tbl;
                 else
                return null;
        }
         else
        {
            // unknown/unsupported extension
            return null;
        }
    }
}


abstract class BaseReader
{
    abstract public function getFieldNames();
    abstract public function getStructure();
    abstract public function isStructureKnown();
    abstract public function readRow();

    // common functions
    public function getFieldIndex($field_name) {
        $arr = $this->getFieldNames();
        return array_search(strtolower($field_name), array_map('strtolower',$arr));
    }

    // stubs for functions implemented only by CsvReader
    public function setFirstRowHeader($val = true) { }
    public function setDelimiter($val) { }
    public function setTextQualifier($val) { }
}


class CsvReader extends BaseReader
{
    private $filename = null;
    private $handle = null;
    private $header = null;
    private $structure = null;
    private $first_row_header = true;
    private $delimiter = ',';
    private $text_qualifier = '"';
    private $utf8 = false;

    public function init($filename)
    {
        $this->filename = $filename;

        $this->handle = @fopen($filename, 'r');
        if ($this->handle === false)
        {
            $this->handle = null;
            return false;
        }

        $this->rewindFile();

        $this->header = TableReader::readRow();

        return true;
    }

    function __destruct()
    {
        if (isset($this->handle))
        {
            fclose($this->handle);
            $this->handle = null;
        }
    }

    private function rewindFile()
    {
        rewind($this->handle);

        $bom = fread($this->handle, 3);

        if ($bom == "\xef\xbb\xbf")
        {
            $this->utf8 = true;
        }
         else
        {
            $this->utf8 = false;
            rewind($this->handle);
        }
    }

    public function setFirstRowHeader($val = true)
    {
        $this->rewindFile();

        $this->header = TableReader::readRow();

        $this->first_row_header = $val;
        if ($val)
            return true;

        $this->rewindFile();

        $new_header = array();

        $cnt = 1;
        foreach ($this->header as $h)
        {
            $new_header[] = 'Field' . $cnt;
            ++$cnt;
        }

        $this->header = $new_header;

        return true;
    }

    public function setDelimiter($val)
    {
        $this->delimiter = $val;
        $this->setFirstRowHeader($this->first_row_header);  // reset header
    }

    public function setTextQualifier($val)
    {
        if (!isset($val) || $val == '')
            $this->text_qualifier = chr(0);
             else
            $this->text_qualifier = $val;
        $this->setFirstRowHeader($this->first_row_header);  // reset header
    }

    public function getFieldNames()
    {
        $ret = array();

        if (!isset($this->handle))
            return $ret;

        foreach ($this->header as $fld)
        {
            $matches = array();
            if (preg_match('/^\s*(.*)\(.*\)\s*$/', $fld, $matches) == 0)
                $ret[] = $fld;
                 else
                $ret[] = $matches[1];
        }

        return $ret;
    }

    public function getStructure()
    {
        if (!isset($this->handle))
            return array();

        if (isset($this->structure))
            return $this->structure;

        $this->structure = $this->getStructureFromHeaderRow();

        if (isset($this->structure))
            return $this->structure;

        $this->structure = $this->determineStructureFromData();

        return $this->structure;
    }

    public function isStructureKnown()
    {
        if (!isset($this->handle))
            return false;

        if (isset($this->structure))
            return true;

        $this->structure = $this->getStructureFromHeaderRow();

        return isset($this->structure);
    }

    private function getStructureFromHeaderRow()
    {
        if (!isset($this->handle))
            return null;
        if (!$this->first_row_header)
            return null;

        $ret = array();

        // look through the header row - if each row has a field type
        // descriptor, then the structure is known
        foreach ($this->header as $fld)
        {
            $matches = array();
            if (preg_match('/^\s*(.*)\(\s*([C|N|D|T|B])(\s+\d+)?(\s+\d+)?\s*\)\s*$/', $fld, $matches) == 0)
                return null;


            $name = trim($matches[1]);
            $type = trim($matches[2]);
            $width = isset($matches[3]) ? trim($matches[3]) : null;
            $scale = isset($matches[4]) ? trim($matches[4]) : null;

            if ($type == 'N')
            {
                if (!isset($width))
                    return null;
                if (!isset($scale))
                    $scale = 0;
            }

            switch ($type)
            {
                case 'C':   $ftype = "character"; break;
                case 'N':   $ftype = "numeric";   break;
                case 'D':   $ftype = "date";      if (!isset($width)) $width = 10; break;
                case 'T':   $ftype = "datetime";  if (!isset($width)) $width = 19; break;
                case 'B':   $ftype = "boolean";   if (!isset($width)) $width = 1;  break;
                default:    return null;  // unknown field type
            }


            if (!isset($width) || $width == 0)
                $width = 1; // minimum width

            $ret[] = array('name' => $name,
               'type' => $ftype,
               'width' => (int)$width,
               'scale' => (int)$scale);
        }

        return $ret;
    }

    public function determineStructureFromData()
    {
        if (!isset($this->handle))
            return null;

        $arr = array();
        $finfo = array();

        $handle = $this->handle;

        $this->rewindFile();

        if ($this->first_row_header)
            $fnames = TableReader::readRow();
             else
            $fnames = $this->header;

        $rowcnt = 0;

        while (($data = TableReader::readRow($handle)) !== null)
        {
            $colcnt = count($data);

            if ($colcnt > count($finfo))
            {
                $finfo = array_pad($finfo, $colcnt, array('maxlen' => 0, 'maxscale' => 0, 'type' => 'D'));
            }

            for ($i = 0; $i < $colcnt; ++$i)
            {
                $len = strlen($data[$i]);
                if ($len > $finfo[$i]['maxlen'])
                    $finfo[$i]['maxlen'] = $len;

                if ($finfo[$i]['type'] == 'D')
                {
                    if ($len > 0 && strtotime($data[$i]) === false)
                        $finfo[$i]['type'] = 'N';
                }
                 else if ($finfo[$i]['type'] == 'N')
                {
                    if ($len > 0 && !is_numeric($data[$i]))
                        $finfo[$i]['type'] = 'C';

                    $decpos = strrpos($data[$i], '.');
                    if ($decpos !== false)
                    {
                        $deccnt = $len - $decpos - 1;
                        if ($deccnt > $finfo[$i]['maxscale'])
                            $finfo[$i]['maxscale'] = $deccnt;
                    }
                }
            }


            $rowcnt++;

            //if ($rowcnt == 500)
            //    break;
        }


        for ($i = 0; $i < count($fnames); $i++)
        {
            switch ($finfo[$i]['type'])
            {
                default:
                case 'C': $type = 'character'; break;
                case 'N': $type = 'numeric'; break;
                case 'D': $type = 'date'; break;
            }

            $arr[] = array('name' => strtolower($fnames[$i]),
                           'type' => $type,
                           'width' => $finfo[$i]['maxlen'],
                           'scale' => $finfo[$i]['maxscale']);
        }

        $this->rewindFile();

        if ($this->first_row_header)
            $fnames = TableReader::readRow($handle);

        return $arr;
    }

    public function readRow($handle = null)
    {
        if (!isset($handle))
            $handle = $this->handle;

        $res = fgetcsv($handle, 0, $this->delimiter, $this->text_qualifier);

        if ($res === false)
            return null;

        if ($this->utf8)
        {
            $cnt = count($res);
            for ($i = 0; $i < $cnt; $i++)
                $res[$i] = utf8_decode($res[$i]);
        }

        return $res;
    }

    public function readRowAssoc()
    {
        $data = TableReader::readRow();
        if (!$data)
            return $data;
        return array_combine($this->header, $data);
    }
}


class XbaseReader extends BaseReader
{
    private $filename = null;
    private $handle = null;
    private $header = null;
    private $structure = null;
    private $first_row_header = true;
    private $recno = 1;

    public function init($filename)
    {
        $this->filename = $filename;

        $this->handle = xbase_open($this->filename, 0);

        if (!$this->handle)
            return false;

        return true;
    }

    function __destruct()
    {
        if ($this->handle)
            xbase_close($this->handle);

    }

    public function setFirstRowHeader($val = true)
    {
        return true; // has no effect in XbaseReader
    }

    public function getFieldNames()
    {
        if (!$this->handle)
            return array();

        $info = xbase_get_structure($this->handle);
        if (!$info)
            return array();

        $res = array();
        foreach ($info as $field)
        {
            if (isset($field['name']))
                $res[] = $field['name'];
        }

        return $res;
    }

    public function getStructure()
    {
        if (!$this->handle)
            return array();

        $info = xbase_get_structure($this->handle);

        $res = array();

        foreach ($info as $field)
        {
            switch ($field['type'])
            {
                default:
                case 'C': $type = 'character'; break;
                case 'Y': $type = 'numeric'; break;   // currency
                case 'F': $type = 'numeric'; break;   // float
                case 'B': $type = 'numeric'; break;   // double
                case 'I': $type = 'numeric'; break;   // integer
                case 'N': $type = 'numeric'; break;
                case 'D': $type = 'date'; break;
                case 'T': $type = 'datetime'; break;
                case 'L': $type = 'boolean'; break;
            }

            $res[] = array('name' => $field['name'],
                           'type' => $type,
                           'width' => $field['width'],
                           'scale' => $field['scale']);
        }

        return $res;
    }

    public function isStructureKnown()
    {
        return true;
    }

    public function readRow()
    {
        if (!$this->handle)
            return array();

        $res = xbase_get_record($this->handle, $this->recno++);
        if ($res === false)
            return null;
        return $res;
    }
}

function xbase_open($filename, $mode)
{
    if (is_numeric($mode))
    {
             if ($mode == 0) $mode = 'r';
        else if ($mode == 1) $mode = 'r+';
        else if ($mode == 2) $mode = 'r+';
        else return false;
    }

    $file = @fopen($filename, $mode);
    if (!$file)
        return false;

    $buf = fread($file, 32);
    if ($buf === false)
    {
        fclose($file);
        return false;
    }

    $res = unpack('Vrecord_count/vdata_offset/vrecord_length', substr($buf, 4, 8));
    $res['file'] = $file;


    // read structure

    $fields = array();
    $offset = 1;
    while (!feof($file))
    {
        $buf = fread($file, 32);

        if (substr($buf, 0, 1) == chr(13))
            break;

        $fld = unpack('a11name/A1type/Voffset/Cwidth/Cscale', substr($buf, 0, 18));

        // calculate row offset manually
        $fld['offset'] = $offset;
        $offset += $fld['width'];

        $fields[] = $fld;
    }

    $res['fields'] = $fields;

    return $res;
}

function xbase_close($handle)
{
    if (!isset($handle) || !is_array($handle) || !isset($handle['file']))
        return false;

    fclose($handle['file']);
    unset($handle['file']);
}

function xbase_get_structure($handle)
{
    if (!isset($handle) || !is_array($handle) || !isset($handle['file']))
        return false;

    return $handle['fields'];
}

function xbase_get_record($handle, $recno, &$deleted = null, $fieldnames = false)
{
    if (!isset($handle) || !is_array($handle) || !isset($handle['file']))
        return false;

    if ($recno < 1)
        return false;

    $file = $handle['file'];
    $reclen = $handle['record_length'];
    $dataoff = $handle['data_offset'];
    $offset = ($reclen * ($recno - 1)) + $dataoff;

    if (0 != fseek($file, $offset))
        return false;

    $buf = fread($file, $reclen);
    if ($buf === false || strlen($buf) != $reclen)
        return false;

    if (isset($deleted))
        $deleted = (substr($buf, 0, 1) == '*' ? true : false);

    $res = array();
    $idx = 0;
    foreach ($handle['fields'] as $field)
    {
        $val = substr($buf, $field['offset'], $field['width']);

        if ($fieldnames)
            $idx = $field['name'];
             else
            $res[] = null;

        if ($field['type'] == 'D')
        {
            $res[$idx] = substr($val,0,4) . '-' . substr($val,4,2) . '-' . substr($val,6,2);
        }
         else if ($field['type'] == 'T')
        {
            $arr = unpack('Vjulian/Vtimestamp', $val);
            $gregorian = jdtogregorian($arr['julian']);
            $timestamp = $arr['timestamp'];

            $hour = (int)($timestamp / 3600000);
            $timestamp -= ($hour * 3600000);

            $minute = (int)($timestamp / 60000);
            $timestamp -= ($minute * 60000);

            $second = (int)($timestamp / 1000);
            $timestamp -= ($second * 1000);

            $parts = explode('/', $gregorian);
            $res[$idx] = sprintf('%04d-%02d-%02d %02d:%02d:%02d', $parts[2], $parts[0], $parts[1], $hour, $minute, $second);
        }
         else if ($field['type'] == 'B')
        {
            $arr = unpack('dval', $val);
            $dec = $field['scale'];
            $res[$idx] = sprintf("%.${dec}f",  $arr['val']);
        }
         else
        {
            $res[$idx] = trim($val);
        }

        if (!$fieldnames)
            $idx++;
    }

    return $res;
}

function xbase_get_record_with_names($handle, $recno, &$deleted = null)
{
    return xbase_get_record($handle, $recno, $deleted, true);
}
