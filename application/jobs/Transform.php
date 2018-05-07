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


declare(strict_types=1);
namespace Flexio\Jobs;

/*
// EXAMPLE:
{
    "op": "transform",
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
*/

class Transform extends \Flexio\Jobs\Base
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


    public function run(\Flexio\IFace\IProcess $process)
    {
        parent::run($process);

        // stdin/stdout
        $instream = $process->getStdin();
        $outstream = $process->getStdout();
        $this->processStream($instream, $outstream);
    }

    private function processStream(\Flexio\IFace\IStream &$instream, \Flexio\IFace\IStream &$outstream)
    {
        $mime_type = $instream->getMimeType();
        switch ($mime_type)
        {
            // unhandled input
            default:
                $outstream->copyFrom($instream);
                return;

            // table input
            case \Flexio\Base\ContentType::FLEXIO_TABLE:
                $this->getTableOutput($instream, $outstream);
                return;

            // stream/text/csv input
            case \Flexio\Base\ContentType::STREAM:
            case \Flexio\Base\ContentType::TEXT:
            case \Flexio\Base\ContentType::CSV:
                $this->getFileOutput($instream, $outstream);
                return;
        }
    }

    private function getTableOutput(\Flexio\IFace\IStream &$instream, \Flexio\IFace\IStream &$outstream)
    {
        $column_expression_map = $this->getTableExpressionMap($instream);
        if ($column_expression_map === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        // if there aren't any operations, simply create an output stream
        // pointing to the original content
        if (count($column_expression_map) === 0)
        {
            $outstream->copyFrom($instream);
            return;
        };

        // create the output with the replaced values
        $output_columns = $instream->getStructure()->enum();
        foreach ($output_columns as &$column)
        {
            $output_name = $column['name'];
            if (isset($column_expression_map[$output_name]))
            {
                $output_structure = $column_expression_map[$output_name]['structure'];
                $column['type'] = $output_structure['type'];
                $column['width'] = $output_structure['width'];
                $column['scale'] = $output_structure['scale'];
            }
        }


        $outstream->set(['mime_type' => \Flexio\Base\ContentType::FLEXIO_TABLE,
                         'structure' => $output_columns ]);

        $streamreader = $instream->getReader();
        $streamwriter = $outstream->getWriter();

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

    private function getFileOutput(\Flexio\IFace\IStream &$instream, \Flexio\IFace\IStream &$outstream)
    {
        $column_expression_map = $this->getStreamExpressionMap($instream);
        if ($column_expression_map === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        // if there aren't any operations, simply create an output stream
        // pointing to the original content
        if (count($column_expression_map) === 0)
        {
            $outstream->copyFrom($instream);
            return;
        }

        // create the output with the replaced values
        $outstream->set($instream->get());
        $outstream->setPath(\Flexio\Base\Util::generateHandle());
        $streamreader = $instream->getReader();
        $streamwriter = $outstream->getWriter();

        while (true)
        {
            $input = $streamreader->read();
            if ($input === false)
                break;

            // package the row in a field called 'xdrow'
            $data = array(
                'xdrow' => $input
            );

            $retval = null;
            $expression_evaluator = $column_expression_map['expreval'];
            $expression_evaluator->execute($data, $retval);
            $output = $retval;

            // write the output row
            $streamwriter->write($output);
        }

        $streamwriter->close();
        $outstream->setSize($streamwriter->getBytesWritten());
    }

    private function getTableExpressionMap(\Flexio\IFace\IStream &$instream)
    {
        // returns an array mapping column names to an expression
        // object that can be used for performing the transformation

        // properties
        $job_params = $this->getJobParameters();

        $operations = $job_params['operations'] ?? [];
        if (count($operations) == 0)
            return array(); // no operations

        // determine the list of columns to perform replace on
        $specified_column_names = $job_params['columns'];
        $columns = $instream->getStructure()->enum($specified_column_names);
        if (count($columns) == 0)
            return array(); // no columns to perform operation on; not a failure, but fall through to no operation

        $column_expression_map = array();
        foreach ($columns as $column)
        {
            // copy the structure info so we can adjust it if needed
            $new_structure = $column;

            $qname = \Flexio\Base\DbUtil::quoteIdentifierIfNecessary($column['name']);
            $expr = $qname;

            foreach ($operations as $operation)
            {
                $operation_type = $operation['operation'] ?? self::OPERATION_NONE;
                switch ($operation_type)
                {
                    default:
                    case self::OPERATION_NONE:
                        break;

                    case self::OPERATION_CHANGE_CASE:
                        $expr = self::getChangeCaseExpr($operation, $expr);
                        break;

                    case self::OPERATION_SUBSTRING:
                        $expr = self::getSubstrExpr($operation, $expr);
                        break;

                    case self::OPERATION_REMOVE_TEXT:
                        $expr = self::getRemoveTextExpr($operation, $expr);
                        break;

                    case self::OPERATION_TRIM_TEXT:
                        $expr = self::getTrimTextExpr($operation, $expr);
                        break;

                    case self::OPERATION_PAD_TEXT:
                        $expr = self::getPadTextExpr($operation, $expr, $column);
                        break;

                    case self::OPERATION_CHANGE_TYPE:
                        $expr = self::getChangeTypeExpr($operation, $expr, $column, $new_structure);
                        break;
                }

                if ($expr === false) // invalid parameter
                    return false;
            }

            // map the column to the expression
            $exprtext = $expr;
            $expreval = new \Flexio\Base\ExprEvaluate;
            $parse_result = $expreval->prepare($exprtext, $instream->getStructure()->enum());
            if ($parse_result === false)
                return false; // trouble building the expression

            $column_expression_map[$column['name']] = array(
                'exprtext' => $exprtext,
                'expreval' => $expreval,
                'structure' => $new_structure
            );
        }

        return $column_expression_map;
    }

    private function getStreamExpressionMap(\Flexio\IFace\IStream &$instream)
    {
        // returns an array mapping column names to an expression
        // object that can be used for performing the transformation

        // properties
        $job_params = $this->getJobParameters();

        $operations = $job_params['operations'] ?? [];
        if (count($operations) == 0)
            return array(); // no operations

        $column_expression_map = array();

        $expr = 'xdrow'; // default field containing row data
        foreach ($operations as $operation)
        {
            $operation_type = $operation['operation'] ?? self::OPERATION_NONE;
            switch ($operation_type)
            {
                default:
                case self::OPERATION_NONE:
                case self::OPERATION_PAD_TEXT: // not supported on text
                case self::OPERATION_CHANGE_TYPE: // not supported on text
                    break;

                case self::OPERATION_CHANGE_CASE:
                    $expr = self::getChangeCaseExpr($operation, $expr);
                    break;

                case self::OPERATION_SUBSTRING:
                    $expr = self::getSubstrExpr($operation, $expr);
                    break;

                case self::OPERATION_REMOVE_TEXT:
                    $expr = self::getRemoveTextExpr($operation, $expr);
                    break;

                case self::OPERATION_TRIM_TEXT:
                    $expr = self::getTrimTextExpr($operation, $expr);
                    break;
            }

            if ($expr === false) // invalid parameter
                return false;
        }

        // map the column to the expression
        $structure = array(
            array(
                'name' => 'xdrow',
                'type' => 'text'
            )
        );

        $exprtext = $expr;
        $expreval = new \Flexio\Base\ExprEvaluate;
        $parse_result = $expreval->prepare($expr, $structure);
        if ($parse_result === false)
            return false; // trouble building the expression

        $column_expression_map = array(
            'exprtext' => $exprtext,
            'expreval' => $expreval,
            'structure' => $structure
        );

        return $column_expression_map;
    }

    private static function getChangeCaseExpr(array $operation, string $expr)
    {
        $new_case = $operation['case'] ?? self::CAPITALIZE_NONE;

        if ($new_case == self::CAPITALIZE_UPPER)
            return "upper(($expr))";

        if ($new_case == self::CAPITALIZE_LOWER)
            return "lower(($expr))";

        if ($new_case == self::CAPITALIZE_PROPER)
            return "initcap(($expr))";

        if ($new_case == self::CAPITALIZE_FIRST_LETTER)
            return "concat(upper(substr(($expr),1,1)) , lower(substr(($expr),2)))";

        return false;
    }

    private static function getSubstrExpr(array $operation, string $expr)
    {
        $location = $operation['location'] ?? self::SUBSTRING_LOCATION_NONE;

        if ($location == self::SUBSTRING_LOCATION_LEFT)
        {
            if (!isset($operation['length']))
                return false;

            $length = (int)$operation['length'];
            return "left(($expr),$length)";
        }

        if ($location == self::SUBSTRING_LOCATION_RIGHT)
        {
            if (!isset($operation['length']))
                return false;

            $length = (int)$operation['length'];
            return "right(($expr),$length)";
        }

        if ($location == self::SUBSTRING_LOCATION_MID)
        {
            $offset = (int)($operation['offset'] ?? 1);
            $length = (int)($operation['length'] ?? null);

            if (is_null($length))
                return "substr(($expr),$length)";
                 else
                return "substr(($expr),$offset,$length)";
        }

        if ($location == self::SUBSTRING_LOCATION_PART)
        {
            if (!isset($operation['field']))
                return false;
            if (!isset($operation['delimiter']))
                return false;

            $field = (int)$operation['field'];
            $delimiter = \Flexio\Base\ExprUtil::quote(''.$operation['delimiter']);
            return "strpart(($expr),$delimiter,$field)";
        }

        // invalid location value
        return false;
    }

    private static function getRemoveTextExpr(array $operation, string $expr)
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
            $location = $operation['location'] ?? self::REMOVE_LOCATION_ANY;

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

            $qregex = \Flexio\Base\ExprUtil::quote($regex);
            return "regexp_replace(($expr),$qregex,'','$flags')";
        }
        else if (isset($operation['value']))
        {
            $value = preg_quote($operation['value']);
            $location = $operation['location'] ?? self::REMOVE_LOCATION_ANY;

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

            $qregex = \Flexio\Base\ExprUtil::quote($regex);
            return "regexp_replace(($expr),$qregex,'','$flags')";
        }
        else if (isset($operation['regex']))
        {
            $qregex = \Flexio\Base\ExprUtil::quote($operation['regex']);
            return "regexp_replace(($expr),$qregex,'','g')";
        }
        else
        {
            return false;
        }
    }

    private static function getTrimTextExpr(array $operation, string $expr)
    {
        $location = $operation['location'] ?? '';

        if ($location == self::TRIM_LOCATION_LEADING)
            return "ltrim($expr)";

        if ($location == self::TRIM_LOCATION_TRAILING)
            return "rtrim($expr)";

        if ($location == self::TRIM_LOCATION_LEADING_TRAILING)
            return "trim($expr)";

        return false;
    }

    private static function getPadTextExpr(array $operation, string $expr, array $columns)
    {
        $location = $operation['location'] ?? '';
        $length = $operation['length'] ?? 0;
        $value = $operation['value'] ?? ' ';
        $width = $columns['width'] ?? -1;

        if ($location != self::PAD_LOCATION_LEFT && $location != self::PAD_LOCATION_RIGHT)
            return false;

        if ($length < 0 || $value == '')
            return $expr;

        if (is_string($value))
            $value = \Flexio\Base\ExprUtil::quote($value); // if we have a string, make sure to do quote replacement
                else
            $value = "'".$value."'";

        $new_expr = $expr;

        if ($location == self::PAD_LOCATION_LEFT)
            $new_expr = "lpad(($expr),$length,$value)";

        if ($location == self::PAD_LOCATION_RIGHT)
            $new_expr = "rpad(($expr),$length,$value)";

        if ($width >= 0)
            $new_expr = "substr($new_expr, 1, $width)";

        return $new_expr;
    }

    private static function getChangeTypeExpr(array $operation, string $expr, array $column, array &$new_structure)
    {
        $type = $column['type'];
        $width = $column['width'] ?? -1;
        $scale = $column['scale'] ?? -1;
        $new_type = $operation['type'] ?? self::COLUMN_TYPE_NONE;

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
            return false;

        if ($new_type == self::COLUMN_TYPE_CHARACTER)
        {
            if ($type == self::COLUMN_TYPE_DATE)
            {
                $new_width = 10; $width = $new_width;
            }
            else if ($type == self::COLUMN_TYPE_DATETIME)
            {
                $new_width = 20; $width = $new_width;
            }
            else if ($type == self::COLUMN_TYPE_NUMERIC || $type == self::COLUMN_TYPE_DOUBLE)
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
            else if ($type == self::COLUMN_TYPE_INTEGER)
            {
                $new_width = 20; $width = $new_width;
            }
            else if ($type == self::COLUMN_TYPE_BOOLEAN)
            {
                $new_width = 5; $width = $new_width; // enough to hold "true"/"false"
            }
        }

        if ($new_type == self::COLUMN_TYPE_NUMERIC)
        {
            if ($type == self::COLUMN_TYPE_DATE)
            {
                $new_width = 8; $width = $new_width;
            }
            else if ($type == self::COLUMN_TYPE_DATETIME)
            {
                $new_width = 14; $width = $new_width;
            }
        }

        $old_type = $type;
        $expr = self::getChangeTypeExprDetail($column['name'], $old_type, $new_type, $width, $scale, $new_structure);
        return $expr;
    }

    private static function getChangeTypeExprDetail(string $name, string $old_type, string $new_type, $new_width, $new_scale, &$new_structure) // TODO: add parameter types
    {
        $width = $new_width;
        $scale = $new_scale;

        $expr = $name;

        // if the type is the same return the same thing; TODO: for now, ignore width changes
        if ($old_type === $new_type)
            return $expr;

        $new_structure['type'] = $new_type;
        $new_structure['width'] = $new_width;
        $new_structure['scale'] = $new_scale;

        switch ($new_type)
        {
            case 'text':
            case 'character':
            case 'widecharacter':
                if ($new_width == -1 || is_null($new_width))
                    $expr = "to_char($expr)";
                     else
                    $expr = "substr(to_char($expr),1,$width)";
                break;

            case 'numeric':
            case 'float':
            case 'double':
            case 'integer':
                $expr = "to_number($expr)";
                break;

            case 'date':
                $expr = "to_date($expr)";
                break;

            case 'timestamp':
            case 'datetime':
                $expr = "to_timestamp($expr)";
                break;

            case 'boolean':
                {
                    if ($old_type === 'text' || $old_type === 'character' || $old_type === 'widecharacter')
                        $expr = "if(lower($expr) = 'true' or lower($expr) = 't', true, false)";
                    if ($old_type === 'numeric' || $old_type === 'float' || $old_type === 'double' || $old_type === 'integer')
                        $expr = "if($expr != 0, true, false)";
                    if ($old_type === 'date' || $old_type === 'datetime' || $old_type === 'timestamp')
                        $expr = "if(isnull($expr), true, false)";
                }
                break;

            default:
                // unknown/unallowed type
                return null;
        }

        return $expr;
    }
}
