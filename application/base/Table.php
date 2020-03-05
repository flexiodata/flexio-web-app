<?php
/**
 *
 * Copyright (c) 2020, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2020-03-04
 *
 * @package flexio
 * @subpackage Base
 */


declare(strict_types=1);
namespace Flexio\Base;


class Table
{
    // simple table implementation based on two-dimensional array of
    // scalers; useful as a helper for working with two-dimensional
    // arrays

    private $data;

    public static function create(array $data) : \Flexio\Base\Table
    {
        // [
        //     ["col1","col2","col3"]
        //     ["val1","val2","val3"]
        //     ["val4","val5","val6"]
        // ]

        // make sure we have an array of arrays of scalers with
        // with the same number of columns in each row

        $min_col_count = PHP_INT_MAX;
        $max_col_count = 0;

        foreach ($data as $row)
        {
            if (!is_array($row))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED);

            $col_count = 0;
            foreach ($row as $value)
            {
                if (!is_scalar($value))
                    throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED);
                $col_count++;
            }

            if ($col_count < $min_col_count)
                $min_col_count = $col_count;
            if ($col_count > $max_col_count)
                $max_col_count = $col_count;
        }

        if ($min_col_count !== $max_col_count)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED);

        // make sure we have at least one row/col
        if ($max_col_count === 0)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED);

        $object = (new self);
        $object->data = $data;

        return $object;
    }

    public function getRowCount() : int
    {
        return count($this->data);
    }

    public function getColCount() : int
    {
        return count($this->data[0]);
    }

    public function getRow(int $idx) : array
    {
        if ($idx < 0)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);
        if ($idx > ($this->getRowCount()-1))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

        return $this->data[$idx];
    }

    public function getCol(int $idx) : array
    {
        if ($idx < 0)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);
        if ($idx > ($this->getRowCount()-1))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

        return array_column($this->data,$idx);
    }

    public function getRange(int $col1 = null, int $row1 = null, int $col2 = null, int $row2 = null) : array
    {
        $row_count = self::getRowCount();
        $col_count = self::getColCount();

        $col1 = $col1 ?? 0;
        $row1 = $row1 ?? 0;
        $col2 = $col2 ?? $col_count - 1;
        $row2 = $row2 ?? $row_count - 1;

        if ($col1 < 0)
            $col1 = 0;
        if ($row1 < 0)
            $row1 = 0;
        if ($col2 < 0)
            $col2 = 0;
        if ($row2 < 0)
            $row2 = 0;

        if ($col1 >= $col_count)
            $col1 = $col_count - 1;
        if ($row1 >= $row_count)
            $row1 = $row_count - 1;
        if ($col2 >= $col_count)
            $col2 = $col_count - 1;
        if ($row2 >= $row_count)
            $row2 = $row_count - 1;

        if ($row2 < $row1)
        {
            $t = $row1;
            $row1 = $row2;
            $row2 = $t;
        }

        if ($col2 < $col1)
        {
            $t = $col1;
            $col1 = $col2;
            $col2 = $t;
        }

        $row_data = array();
        for ($row_idx = $row1; $row_idx <= $row2; $row_idx++)
        {
            $col_data = array();
            for ($col_idx = $col1; $col_idx <= $col2; $col_idx++)
            {
                $col_data[] = $this->data[$row_idx][$col_idx];
            }
            $row_data[] = $col_data;
        }
        return $row_data;
    }
}
