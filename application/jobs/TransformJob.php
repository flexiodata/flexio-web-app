<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams
 * Created:  2016-01-19
 *
 * @package flexio
 * @subpackage Jobs
 */


require_once __DIR__ . DIRECTORY_SEPARATOR . 'Base.php';

class TransformJob extends Base
{
    // transform options
    const OPERATION_NONE        = '';
    const OPERATION_CHANGE_TYPE = 'type';
    const OPERATION_CHANGE_CASE = 'case';
    const OPERATION_SUBSTRING   = 'substring';
    const OPERATION_REMOVE_TEXT = 'remove';
    const OPERATION_TRIM_TEXT   = 'trim';
    const OPERATION_PAD_TEXT    = 'pad';

    // transform parameters

    // transform options
    const CAPITALIZE_NONE         = '';
    const CAPITALIZE_LOWER        = 'lower';
    const CAPITALIZE_UPPER        = 'upper';
    const CAPITALIZE_PROPER       = 'proper';
    const CAPITALIZE_FIRST_LETTER = 'first-letter';

    const PAD_LOCATION_NONE  = '';
    const PAD_LOCATION_LEFT  = 'left';
    const PAD_LOCATION_RIGHT = 'right';

    const TRIM_LOCATION_NONE             = '';
    const TRIM_LOCATION_LEADING          = 'leading';
    const TRIM_LOCATION_TRAILING         = 'trailing';
    const TRIM_LOCATION_LEADING_TRAILING = 'leading-trailing';

    const SUBSTRING_LOCATION_NONE  = '';
    const SUBSTRING_LOCATION_LEFT  = 'left';
    const SUBSTRING_LOCATION_RIGHT = 'right';
    const SUBSTRING_LOCATION_MID   = 'mid';
    const SUBSTRING_LOCATION_PART  = 'part';

    const REMOVE_LOCATION_NONE             = '';
    const REMOVE_LOCATION_LEADING          = 'leading';
    const REMOVE_LOCATION_TRAILING         = 'trailing';
    const REMOVE_LOCATION_LEADING_TRAILING = 'leading-trailing';
    const REMOVE_LOCATION_ANY              = 'any';

    const REMOVE_TYPE_NONE           = '';
    const REMOVE_TYPE_SYMBOLS        = 'symbols';
    const REMOVE_TYPE_WHITESPACE     = 'whitespace';
    const REMOVE_TYPE_LETTERS        = 'letters';
    const REMOVE_TYPE_NUMBERS        = 'numbers';
    const REMOVE_TYPE_REGEX          = 'regex';
    const REMOVE_TYPE_SELECTED_TEXT  = 'selected-text';
    const REMOVE_TYPE_SELECTED_CHARS = 'selected-chars';

    // column classes
    const COLUMN_TYPE_NONE           = '';
    const COLUMN_TYPE_TEXT           = 'text';
    const COLUMN_TYPE_CHARACTER      = 'character';
    const COLUMN_TYPE_WIDECHARACTER  = 'widecharacter';
    const COLUMN_TYPE_NUMERIC        = 'numeric';
    const COLUMN_TYPE_DOUBLE         = 'double';
    const COLUMN_TYPE_INTEGER        = 'integer';
    const COLUMN_TYPE_DATE           = 'date';
    const COLUMN_TYPE_DATETIME       = 'datetime';
    const COLUMN_TYPE_BOOLEAN        = 'boolean';

    // character classes
    const CHARACTER_CLASS_NONE   = '';
    const CHARACTER_CLASS_ALNUM  = 'alnum';
    const CHARACTER_CLASS_ALPHA  = 'alpha';
    const CHARACTER_CLASS_ASCII  = 'ascii';
    const CHARACTER_CLASS_BLANK  = 'blank';
    const CHARACTER_CLASS_CNTRL  = 'cntrl';
    const CHARACTER_CLASS_DIGIT  = 'digit';
    const CHARACTER_CLASS_GRAPH  = 'graph';
    const CHARACTER_CLASS_LOWER  = 'lower';
    const CHARACTER_CLASS_PRINT  = 'print';
    const CHARACTER_CLASS_PUNCT  = 'punct';
    const CHARACTER_CLASS_SPACE  = 'space';
    const CHARACTER_CLASS_UPPER  = 'upper';
    const CHARACTER_CLASS_WORD   = 'word';
    const CHARACTER_CLASS_XDIGIT = 'xdigit';


    public function run()
    {
        $input = $this->getInput()->enum();
        foreach ($input as $instream)
        {
            $mime_type = $instream->getMimeType();
            switch ($mime_type)
            {
                // unhandled input
                default:
                    $this->getOutput()->push($instream->copy());
                    break;

                // table input
                case ContentType::MIME_TYPE_FLEXIO_TABLE:
                    //$this->createOutputFromTableNative($instream); // TODO: new implementation
                    $this->createOutputFromTableSql($instream); // TODO: old implementation
                    break;
            }
        }
    }

    private function createOutputFromTableNative($instream)
    {
        $column_expression_map = $this->getColumnExpressionMap($instream);
        if ($column_expression_map === false)
            return $this->fail(\Model::ERROR_INVALID_PARAMETER, _(''), __FILE__, __LINE__); // something went wrong with the params

        // if there aren't any operations, simply create an output stream
        // pointing to the origina content
        if (count($column_expression_map) === 0)
        {
            $this->getOutput()->push($instream->copy());
            return;
        }

        // create the output with the replaced values
        $outstream = $instream->copy()->setPath(Util::generateHandle());
        $this->getOutput()->push($outstream);

        $streamreader = \Flexio\Object\StreamReader::create($instream);
        $streamwriter = \Flexio\Object\StreamWriter::create($outstream);

        while (true)
        {
            $input_row = $streamreader->readRow();
            if ($input_row === false)
                break;

            $output_row = array();
            foreach ($input_row as $name => $value)
            {
                // if we have nothing to evaluate, copy the value
                if (!isset($column_expression_map[$name]))
                {
                    $output_row[$name] = $value;
                    continue;
                }

                // we have something to replace; execute the expression
                $retval = null;
                $expression_evaluator = $column_expression_map[$name]['expreval'];
                $expression_evaluator->execute($input_row, $retval);
                $output_row[$name] = $retval;
            }

            // write the output row
            $streamwriter->write($output_row);
        }

        $streamwriter->close();
        $outstream->setSize($streamwriter->getBytesWritten());
    }

    private function createOutputFromTableSql($instream)
    {
        // properties
        $job_definition = $this->getProperties();
        $params = $job_definition['params'];
        unset($job_definition['params']);

        $operations = isset_or($params['operations'], []);
        if (count($operations) == 0)
            return; // nothing to do

        // determine the list of columns to use in the job
        $input_columns = $instream->getStructure()->enum($params['columns']);
        if (count($input_columns) == 0)
            return $this->fail(\Model::ERROR_INVALID_PARAMETER, _(''), __FILE__, __LINE__);

        $copy_params = [ 'actions' => [] ];

        foreach ($input_columns as $column)
        {
            $qname = DbUtil::quoteIdentifierIfNecessary($column['name']);
            $width = isset_or($column['width'], -1);
            if ($width == 1024) $width = -1; // PostgresService is returning us huge columns of indeterminate width
            $scale = $column['scale'];

            $expr = $qname;
            $is_character = ($column['type'] == self::COLUMN_TYPE_TEXT || $column['type'] == self::COLUMN_TYPE_CHARACTER || $column['type'] == self::COLUMN_TYPE_WIDECHARACTER);

/*
            // certain types of operations, like substring, will force a conversion to
            // the text type, if the field does not already have that type
            if (!$is_character)
            {
                if (isset($params[self::OPERATION_SUBSTRING]) || isset($params[self::OPERATION_PAD_TEXT]))
                    $params['type'] = self::COLUMN_TYPE_CHARACTER;
            }
*/

            foreach ($operations as $operation)
            {
                $operation_type = isset_or($operation['operation'], self::OPERATION_NONE);

                if ($operation_type == self::OPERATION_CHANGE_TYPE)   // change type operation
                {
                    $old_type = $column['type'];
                    $new_type = isset_or($operation['type'], self::COLUMN_TYPE_NONE);

                    // make sure it's a valid type
                    $column_types = array(
                        self::COLUMN_TYPE_TEXT,
                        self::COLUMN_TYPE_CHARACTER,
                        self::COLUMN_TYPE_WIDECHARACTER,
                        self::COLUMN_TYPE_NUMERIC,
                        self::COLUMN_TYPE_DOUBLE,
                        self::COLUMN_TYPE_INTEGER,
                        self::COLUMN_TYPE_DATE,
                        self::COLUMN_TYPE_DATETIME,
                        self::COLUMN_TYPE_BOOLEAN
                    );

                    if (!array_search($new_type, $column_types))
                    {
                        return $this->fail(\Model::ERROR_INVALID_PARAMETER, _(''), __FILE__, __LINE__);
                    }

                    if ($new_type == self::COLUMN_TYPE_CHARACTER)
                    {
                        if ($old_type == self::COLUMN_TYPE_DATE)
                        {
                            $new_width = 10; $width = $new_width;
                        }
                        else if ($old_type == self::COLUMN_TYPE_DATETIME)
                        {
                            $new_width = 20; $width = $new_width;
                        }
                        else if ($old_type == self::COLUMN_TYPE_NUMERIC || $old_type == self::COLUMN_TYPE_DOUBLE)
                        {
                            if ($width < 0)
                            {
                                $new_width = 20; $width = $new_width;
                            }
                            else
                            {
                                $new_width = $width+2; $width = $new_width;
                            }
                        }
                        else if ($old_type == self::COLUMN_TYPE_INTEGER)
                        {
                            $new_width = 20; $width = $new_width;
                        }
                        else if ($old_type == self::COLUMN_TYPE_BOOLEAN)
                        {
                            $new_width = 5; $width = $new_width; // enough to hold "true"/"false"
                        }

                        $is_character = true;
                    }

                    if ($new_type == self::COLUMN_TYPE_NUMERIC)
                    {
                        if ($old_type == self::COLUMN_TYPE_DATE)
                        {
                            $new_width = 8; $width = $new_width;
                        }
                        else if ($old_type == self::COLUMN_TYPE_DATETIME)
                        {
                            $new_width = 14; $width = $new_width;
                        }
                    }

                    $expr = ExprUtil::getCastExpression($column['name'], $old_type, $new_type, $width, $scale);
                }

                if ($operation_type == self::OPERATION_CHANGE_CASE && $is_character)
                {
                    $new_case = isset_or($operation['case'], self::CAPITALIZE_NONE);

                    if ($new_case == self::CAPITALIZE_UPPER)
                        $expr = "upper(($expr))";
                    else if ($new_case == self::CAPITALIZE_LOWER)
                        $expr = "lower(($expr))";
                    else if ($new_case == self::CAPITALIZE_PROPER)
                        $expr = "initcap(($expr))";
                    else if ($new_case == self::CAPITALIZE_FIRST_LETTER)
                        $expr = "concat(upper(substr(($expr),1,1)) , lower(substr(($expr),2)))";
                    else
                        return $this->fail(\Model::ERROR_INVALID_PARAMETER, _(''), __FILE__, __LINE__);
                }

                if ($operation_type == self::OPERATION_SUBSTRING)
                {
                    $location = isset_or($operation['location'], self::SUBSTRING_LOCATION_NONE);

                    if ($location == self::SUBSTRING_LOCATION_LEFT)
                    {
                        if (!isset($operation['length']))
                            return $this->fail(\Model::ERROR_INVALID_PARAMETER, _(''), __FILE__, __LINE__);
                        $length = (int)$operation['length'];
                        $expr = "left(($expr),$length)";
                    }
                    else if ($location == self::SUBSTRING_LOCATION_RIGHT)
                    {
                        if (!isset($operation['length']))
                            return $this->fail(\Model::ERROR_INVALID_PARAMETER, _(''), __FILE__, __LINE__);
                        $length = (int)$operation['length'];
                        $expr = "right(($expr),$length)";
                    }
                    else if ($location == self::SUBSTRING_LOCATION_MID)
                    {
                        $offset = (int)isset_or($operation['offset'], 1);
                        $length = (int)isset_or($operation['length'], null);

                        if (is_null($length))
                            $expr = "substr(($expr),$length)";
                            else
                            $expr = "substr(($expr),$offset,$length)";
                    }
                    else if ($location == self::SUBSTRING_LOCATION_PART)
                    {
                        if (!isset($operation['field']))
                            return $this->fail(\Model::ERROR_INVALID_PARAMETER, _(''), __FILE__, __LINE__);
                        if (!isset($operation['delimiter']))
                            return $this->fail(\Model::ERROR_INVALID_PARAMETER, _(''), __FILE__, __LINE__);

                        $field = (int)$params['substring']['field'];
                        $delimiter = ExprUtil::quote(''.$params['substring']['delimiter']);

                        $expr = "strpart(($expr),$delimiter,$field)";
                    }
                    else
                    {
                        // invalid location value
                        return $this->fail(\Model::ERROR_INVALID_PARAMETER, _(''), __FILE__, __LINE__);
                    }
                }

                if ($operation_type == self::OPERATION_REMOVE_TEXT)
                {
                    $characters_regex = '';

                    if (isset($operation['character_class']))
                    {
                        $posix_character_classes = [
                            CHARACTER_CLASS_ALNUM,
                            CHARACTER_CLASS_ALPHA,
                            CHARACTER_CLASS_ASCII,
                            CHARACTER_CLASS_BLANK,
                            CHARACTER_CLASS_CNTRL,
                            CHARACTER_CLASS_DIGIT,
                            CHARACTER_CLASS_GRAPH,
                            CHARACTER_CLASS_LOWER,
                            CHARACTER_CLASS_PRINT,
                            CHARACTER_CLASS_PUNCT,
                            CHARACTER_CLASS_SPACE,
                            CHARACTER_CLASS_UPPER,
                            CHARACTER_CLASS_WORD,
                            CHARACTER_CLASS_XDIGIT
                        ];

                        if (!in_array($operation['character_class'], $posix_character_classes))
                            return $this->fail(\Model::ERROR_INVALID_PARAMETER, _(''), __FILE__, __LINE__);

                        $characters_regex = '[:' . $operation['character_class'] . ':]';
                    }

                    if (isset($operation['characters']))
                    {
                        $characters = $operation['characters'];

                        // we are going to build a regex character class, so we
                        // need to put -, ^, and [] in the appropriate places

                        $characters = str_replace("\\", "\\\\", $characters);
                        $characters = str_replace("-", "\\-",   $characters);
                        $characters = str_replace("^", "\\^",   $characters);
                        $characters = str_replace(":", "\\:",   $characters);
                        $characters_regex .= $characters;
                    }

                    if (isset($operation['character_class']) || isset($operation['characters']))
                    {
                        $value = $characters_regex;
                        $location = isset_or($operation['location'],self::REMOVE_LOCATION_ANY);

                        $flags = '';

                        if ($location == self::REMOVE_LOCATION_ANY)
                        {
                            $regex = "[$value]";
                            $flags = 'g';
                        }
                        else if ($location == self::REMOVE_LOCATION_LEADING)
                            $regex = "^[$value]+";
                        else if ($location == self::REMOVE_LOCATION_TRAILING)
                            $regex = "[$value]+$";
                        else if ($location == self::REMOVE_LOCATION_LEADING_TRAILING)
                            $regex = "(^[$value]+)|([$value]+$)";

                        $qregex = ExprUtil::quote($regex);
                        $expr = "regexp_replace(($expr),$qregex,'','$flags')";
                    }
                    else if (isset($operation['value']))
                    {
                        $value = preg_quote($operation['value']);
                        $location = isset_or($operation['location'],self::REMOVE_LOCATION_ANY);

                        $flags = '';

                        if ($location == self::REMOVE_LOCATION_ANY)
                        {
                            $regex = "$value";
                            $flags = 'g';
                        }
                        else if ($location == self::REMOVE_LOCATION_LEADING)
                            $regex = "^$value";
                        else if ($location == self::REMOVE_LOCATION_TRAILING)
                            $regex = "$value$$";
                        else if ($location == self::REMOVE_LOCATION_LEADING_TRAILING)
                            $regex = "(^$value)|($value$$)";
                        else
                            return $this->fail(\Model::ERROR_INVALID_PARAMETER, _(''), __FILE__, __LINE__);

                        $qregex = ExprUtil::quote($regex);
                        $expr = "regexp_replace(($expr),$qregex,'','$flags')";
                    }
                    else if (isset($operation['regex']))
                    {
                        $qregex = ExprUtil::quote($operation['regex']);
                        $expr = "regexp_replace(($expr),$qregex,'','g')";
                    }
                    else
                    {
                        return $this->fail(\Model::ERROR_INVALID_PARAMETER, _(''), __FILE__, __LINE__);
                    }
                }

                if ($operation_type == self::OPERATION_TRIM_TEXT && $is_character)
                {
                    $location = isset_or($operation['location'], '');

                    if ($location == self::TRIM_LOCATION_LEADING)
                        $expr = "ltrim($expr)";
                    else if ($location == self::TRIM_LOCATION_TRAILING)
                        $expr = "rtrim($expr)";
                    else if ($location == self::TRIM_LOCATION_LEADING_TRAILING)
                        $expr = "trim($expr)";
                    else
                        return $this->fail(\Model::ERROR_INVALID_PARAMETER, _(''), __FILE__, __LINE__);
                }

                if ($operation_type == self::OPERATION_PAD_TEXT)
                {
                    $location = isset_or($operation['location'],'');
                    $length = isset_or($operation['length'],0);
                    $value = isset_or($operation['value'],' ');

                    if ($location != self::PAD_LOCATION_LEFT && $location != self::PAD_LOCATION_RIGHT)
                        return $this->fail(\Model::ERROR_INVALID_PARAMETER, _(''), __FILE__, __LINE__);

                    if ($length >= 0 && $value != '')
                    {
                        $value = ExprUtil::quote($value);

                        if ($location == self::PAD_LOCATION_LEFT)
                            $expr = "lpad(($expr),$length,$value)";
                        else if ($location == self::PAD_LOCATION_RIGHT)
                            $expr = "rpad(($expr),$length,$value)";

                        if ($width >= 0)
                        {
                            $expr = "cast($expr, character($width))";
                        }
                    }
                }
            }


            if ($expr != $qname)
            {
                $action = array(
                    'action'     => 'alter',
                    'name'       => $column['name'],
                    'params'     => array('expression' => $expr)
                );

                if (isset($new_type))  $action['params']['type']  = $new_type;
                if (isset($new_width)) $action['params']['width'] = $new_width;
                if (isset($new_scale)) $action['params']['scale'] = $new_scale;

                $copy_params['actions'][] = $action;
            }
        }

        $job_definition['type'] = 'flexio.copy';
        $job_definition['params'] = $copy_params;

        $job = CopyJob::create($this->getProcess(), $job_definition);
        $job->getInput()->push($instream);
        $job->run();
        $this->getOutput()->merge($job->getOutput());
    }

    private function getColumnExpressionMap($instream)
    {
        // returns an array mapping column names to an expression
        // object that can be used for performing the transformation

        // properties
        $job_definition = $this->getProperties();
        $params = $job_definition['params'];

        $operations = isset_or($params['operations'], []);
        if (count($operations) == 0)
            return array(); // no operations

        // determine the list of columns to perform replace on
        $specified_column_names = $params['columns'];
        $columns = $instream->getStructure()->enum($specified_column_names);
        if (count($columns) == 0)
            return array(); // no columns to perform operation on; not a failure, but fall through to no operation

        $column_expression_map = array();
        foreach ($columns as $column)
        {
            $qname = DbUtil::quoteIdentifierIfNecessary($column['name']);
            $width = isset_or($column['width'], -1);
            if ($width == 1024) $width = -1; // PostgresService is returning us huge columns of indeterminate width
            $scale = $column['scale'];

            $expr = $qname;
            $is_character = ($column['type'] == self::COLUMN_TYPE_TEXT || $column['type'] == self::COLUMN_TYPE_CHARACTER || $column['type'] == self::COLUMN_TYPE_WIDECHARACTER);

/*
            // certain types of operations, like substring, will force a conversion to
            // the text type, if the field does not already have that type
            if (!$is_character)
            {
                if (isset($params[self::OPERATION_SUBSTRING]) || isset($params[self::OPERATION_PAD_TEXT]))
                    $params['type'] = self::COLUMN_TYPE_CHARACTER;
            }
*/

            foreach ($operations as $operation)
            {
                $operation_type = isset_or($operation['operation'], self::OPERATION_NONE);

                if ($operation_type == self::OPERATION_CHANGE_TYPE)   // change type operation
                {
                    $old_type = $column['type'];
                    $new_type = isset_or($operation['type'], self::COLUMN_TYPE_NONE);

                    // make sure it's a valid type
                    $column_types = array(
                        self::COLUMN_TYPE_TEXT,
                        self::COLUMN_TYPE_CHARACTER,
                        self::COLUMN_TYPE_WIDECHARACTER,
                        self::COLUMN_TYPE_NUMERIC,
                        self::COLUMN_TYPE_DOUBLE,
                        self::COLUMN_TYPE_INTEGER,
                        self::COLUMN_TYPE_DATE,
                        self::COLUMN_TYPE_DATETIME,
                        self::COLUMN_TYPE_BOOLEAN
                    );

                    if (!array_search($new_type, $column_types))
                    {
                        return false;
                    }

                    if ($new_type == self::COLUMN_TYPE_CHARACTER)
                    {
                        if ($old_type == self::COLUMN_TYPE_DATE)
                        {
                            $new_width = 10; $width = $new_width;
                        }
                        else if ($old_type == self::COLUMN_TYPE_DATETIME)
                        {
                            $new_width = 20; $width = $new_width;
                        }
                        else if ($old_type == self::COLUMN_TYPE_NUMERIC || $old_type == self::COLUMN_TYPE_DOUBLE)
                        {
                            if ($width < 0)
                            {
                                $new_width = 20; $width = $new_width;
                            }
                            else
                            {
                                $new_width = $width+2; $width = $new_width;
                            }
                        }
                        else if ($old_type == self::COLUMN_TYPE_INTEGER)
                        {
                            $new_width = 20; $width = $new_width;
                        }
                        else if ($old_type == self::COLUMN_TYPE_BOOLEAN)
                        {
                            $new_width = 5; $width = $new_width; // enough to hold "true"/"false"
                        }

                        $is_character = true;
                    }

                    if ($new_type == self::COLUMN_TYPE_NUMERIC)
                    {
                        if ($old_type == self::COLUMN_TYPE_DATE)
                        {
                            $new_width = 8; $width = $new_width;
                        }
                        else if ($old_type == self::COLUMN_TYPE_DATETIME)
                        {
                            $new_width = 14; $width = $new_width;
                        }
                    }

                    $expr = ExprUtil::getCastExpression($column['name'], $old_type, $new_type, $width, $scale);
                }

                if ($operation_type == self::OPERATION_CHANGE_CASE && $is_character)
                {
                    $new_case = isset_or($operation['case'], self::CAPITALIZE_NONE);

                    if ($new_case == self::CAPITALIZE_UPPER)
                        $expr = "upper(($expr))";
                    else if ($new_case == self::CAPITALIZE_LOWER)
                        $expr = "lower(($expr))";
                    else if ($new_case == self::CAPITALIZE_PROPER)
                        $expr = "initcap(($expr))";
                    else if ($new_case == self::CAPITALIZE_FIRST_LETTER)
                        $expr = "concat(upper(substr(($expr),1,1)) , lower(substr(($expr),2)))";
                    else
                        return false;
                }

                if ($operation_type == self::OPERATION_SUBSTRING)
                {
                    $location = isset_or($operation['location'], self::SUBSTRING_LOCATION_NONE);

                    if ($location == self::SUBSTRING_LOCATION_LEFT)
                    {
                        if (!isset($operation['length']))
                            return false;
                        $length = (int)$operation['length'];
                        $expr = "left(($expr),$length)";
                    }
                    else if ($location == self::SUBSTRING_LOCATION_RIGHT)
                    {
                        if (!isset($operation['length']))
                            return false;
                        $length = (int)$operation['length'];
                        $expr = "right(($expr),$length)";
                    }
                    else if ($location == self::SUBSTRING_LOCATION_MID)
                    {
                        $offset = (int)isset_or($operation['offset'], 1);
                        $length = (int)isset_or($operation['length'], null);

                        if (is_null($length))
                            $expr = "substr(($expr),$length)";
                            else
                            $expr = "substr(($expr),$offset,$length)";
                    }
                    else if ($location == self::SUBSTRING_LOCATION_PART)
                    {
                        if (!isset($operation['field']))
                            return false;
                        if (!isset($operation['delimiter']))
                            return false;
                        $field = (int)$params['substring']['field'];
                        $delimiter = ExprUtil::quote(''.$params['substring']['delimiter']);

                        $expr = "strpart(($expr),$delimiter,$field)";
                    }
                    else
                    {
                        // invalid location value
                        return false;
                    }
                }

                if ($operation_type == self::OPERATION_REMOVE_TEXT)
                {
                    $characters_regex = '';

                    if (isset($operation['character_class']))
                    {
                        $posix_character_classes = [
                            CHARACTER_CLASS_ALNUM,
                            CHARACTER_CLASS_ALPHA,
                            CHARACTER_CLASS_ASCII,
                            CHARACTER_CLASS_BLANK,
                            CHARACTER_CLASS_CNTRL,
                            CHARACTER_CLASS_DIGIT,
                            CHARACTER_CLASS_GRAPH,
                            CHARACTER_CLASS_LOWER,
                            CHARACTER_CLASS_PRINT,
                            CHARACTER_CLASS_PUNCT,
                            CHARACTER_CLASS_SPACE,
                            CHARACTER_CLASS_UPPER,
                            CHARACTER_CLASS_WORD,
                            CHARACTER_CLASS_XDIGIT
                        ];

                        if (!in_array($operation['character_class'], $posix_character_classes))
                            return false;

                        $characters_regex = '[:' . $operation['character_class'] . ':]';
                    }

                    if (isset($operation['characters']))
                    {
                        $characters = $operation['characters'];

                        // we are going to build a regex character class, so we
                        // need to put -, ^, and [] in the appropriate places

                        $characters = str_replace("\\", "\\\\", $characters);
                        $characters = str_replace("-", "\\-",   $characters);
                        $characters = str_replace("^", "\\^",   $characters);
                        $characters = str_replace(":", "\\:",   $characters);
                        $characters_regex .= $characters;
                    }

                    if (isset($operation['character_class']) || isset($operation['characters']))
                    {
                        $value = $characters_regex;
                        $location = isset_or($operation['location'],self::REMOVE_LOCATION_ANY);

                        $flags = '';

                        if ($location == self::REMOVE_LOCATION_ANY)
                        {
                            $regex = "[$value]";
                            $flags = 'g';
                        }
                        else if ($location == self::REMOVE_LOCATION_LEADING)
                            $regex = "^[$value]+";
                        else if ($location == self::REMOVE_LOCATION_TRAILING)
                            $regex = "[$value]+$";
                        else if ($location == self::REMOVE_LOCATION_LEADING_TRAILING)
                            $regex = "(^[$value]+)|([$value]+$)";

                        $qregex = ExprUtil::quote($regex);
                        $expr = "regexp_replace(($expr),$qregex,'','$flags')";
                    }
                    else if (isset($operation['value']))
                    {
                        $value = preg_quote($operation['value']);
                        $location = isset_or($operation['location'],self::REMOVE_LOCATION_ANY);

                        $flags = '';

                        if ($location == self::REMOVE_LOCATION_ANY)
                        {
                            $regex = "$value";
                            $flags = 'g';
                        }
                        else if ($location == self::REMOVE_LOCATION_LEADING)
                            $regex = "^$value";
                        else if ($location == self::REMOVE_LOCATION_TRAILING)
                            $regex = "$value$$";
                        else if ($location == self::REMOVE_LOCATION_LEADING_TRAILING)
                            $regex = "(^$value)|($value$$)";
                        else
                            return false;

                        $qregex = ExprUtil::quote($regex);
                        $expr = "regexp_replace(($expr),$qregex,'','$flags')";
                    }
                    else if (isset($operation['regex']))
                    {
                        $qregex = ExprUtil::quote($operation['regex']);
                        $expr = "regexp_replace(($expr),$qregex,'','g')";
                    }
                    else
                    {
                        return false;
                    }
                }

                if ($operation_type == self::OPERATION_TRIM_TEXT && $is_character)
                {
                    $location = isset_or($operation['location'], '');

                    if ($location == self::TRIM_LOCATION_LEADING)
                        $expr = "ltrim($expr)";
                    else if ($location == self::TRIM_LOCATION_TRAILING)
                        $expr = "rtrim($expr)";
                    else if ($location == self::TRIM_LOCATION_LEADING_TRAILING)
                        $expr = "trim($expr)";
                    else
                        return false;
                }

                if ($operation_type == self::OPERATION_PAD_TEXT)
                {
                    $location = isset_or($operation['location'],'');
                    $length = isset_or($operation['length'],0);
                    $value = isset_or($operation['value'],' ');

                    if ($location != self::PAD_LOCATION_LEFT && $location != self::PAD_LOCATION_RIGHT)
                        return false;

                    if ($length >= 0 && $value != '')
                    {
                        if (is_string($value))
                            $value = ExprUtil::quote($value); // if we have a string, make sure to do quote replacement
                             else
                            $value = "'".$value."'";

                        if ($location == self::PAD_LOCATION_LEFT)
                            $expr = "lpad(($expr),$length,$value)";
                        else if ($location == self::PAD_LOCATION_RIGHT)
                            $expr = "rpad(($expr),$length,$value)";

                        if ($width >= 0)
                        {
                            //$expr = "cast($expr, character($width))";
                        }
                    }
                }
            }

            // map the column to the expression
            $exprtext = $expr;
            $expreval = new ExprEvaluate;
            $parse_result = $expreval->prepare($exprtext, $instream->getStructure()->enum());
            if ($parse_result === false)
                return false; // trouble building the expression

            $column_expression_map[$column['name']] = array(
                'exprtext' => $exprtext,
                'expreval' => $expreval
            );
        }

        return $column_expression_map;
    }


    // example transform operation
/*
    {
        "type": "flexio.transform",
        "params": {
            "columns": [],
            "operations": [
                { "operation":"pad", "value":"0", "length":7,"location":"left" },
                { "operation":"trim", "location":"leading" },
                { "operation":"type": "character" },
                { "operation":"remove", "location":"any","character_class":"alpha" },
                { "operation":"substring", "length":7, "location":"left" },
                { "operation":"case", "case":"lower" }
            ]
        }
    }
*/
    // job definition info
    const MIME_TYPE = 'flexio.transform';
    const TEMPLATE = <<<EOD
    {
        "type": "flexio.transform",
        "params": {
            "columns": [],
            "operations": [
            ]
        }
    }
EOD;
    const SCHEMA = <<<EOD
    {
        "type": "object",
        "required": ["type","params"],
        "properties": {
            "type": {
                "type": "string",
                "enum": ["flexio.transform"]
            },
            "params": {
                "type": "object",
                "required": ["columns"],
                "properties": {
                    "group": {
                        "type": "array",
                        "items": {
                            "type": "string",
                            "format": "fx.fieldname"
                        }
                    }
                }
            }
        }
    }
EOD;
}
