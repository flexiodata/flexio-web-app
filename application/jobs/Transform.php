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
// DESCRIPTION:
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
    // TODO: fill out? or deprecated?
}

// VALIDATOR:
$validator = \Flexio\Base\Validator::create();
if (($validator->check($params, array(
        'op'         => array('required' => true,  'enum' => ['transform'])
    ))->hasErrors()) === true)
    throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);
*/

class Transform extends \Flexio\Jobs\Base
{
    // transform options
    public const OPERATION_NONE        = '';
    public const OPERATION_CHANGE_TYPE = 'type';
    public const OPERATION_CHANGE_CASE = 'case';
    public const OPERATION_SUBSTRING   = 'substring';
    public const OPERATION_REMOVE_TEXT = 'remove';
    public const OPERATION_TRIM_TEXT   = 'trim';
    public const OPERATION_PAD_TEXT    = 'pad';

    // transform parameters

    // transform options
    public const CAPITALIZE_NONE         = '';
    public const CAPITALIZE_LOWER        = 'lower';
    public const CAPITALIZE_UPPER        = 'upper';
    public const CAPITALIZE_PROPER       = 'proper';
    public const CAPITALIZE_FIRST_LETTER = 'first-letter';

    public const PAD_LOCATION_NONE  = '';
    public const PAD_LOCATION_LEFT  = 'left';
    public const PAD_LOCATION_RIGHT = 'right';

    public const TRIM_LOCATION_NONE             = '';
    public const TRIM_LOCATION_LEADING          = 'leading';
    public const TRIM_LOCATION_TRAILING         = 'trailing';
    public const TRIM_LOCATION_LEADING_TRAILING = 'leading-trailing';

    public const SUBSTRING_LOCATION_NONE  = '';
    public const SUBSTRING_LOCATION_LEFT  = 'left';
    public const SUBSTRING_LOCATION_RIGHT = 'right';
    public const SUBSTRING_LOCATION_MID   = 'mid';
    public const SUBSTRING_LOCATION_PART  = 'part';

    public const REMOVE_LOCATION_NONE             = '';
    public const REMOVE_LOCATION_LEADING          = 'leading';
    public const REMOVE_LOCATION_TRAILING         = 'trailing';
    public const REMOVE_LOCATION_LEADING_TRAILING = 'leading-trailing';
    public const REMOVE_LOCATION_ANY              = 'any';

    public const REMOVE_TYPE_NONE           = '';
    public const REMOVE_TYPE_SYMBOLS        = 'symbols';
    public const REMOVE_TYPE_WHITESPACE     = 'whitespace';
    public const REMOVE_TYPE_LETTERS        = 'letters';
    public const REMOVE_TYPE_NUMBERS        = 'numbers';
    public const REMOVE_TYPE_REGEX          = 'regex';
    public const REMOVE_TYPE_SELECTED_TEXT  = 'selected-text';
    public const REMOVE_TYPE_SELECTED_CHARS = 'selected-chars';

    // column classes
    public const COLUMN_TYPE_NONE           = '';
    public const COLUMN_TYPE_TEXT           = 'text';
    public const COLUMN_TYPE_CHARACTER      = 'character';
    public const COLUMN_TYPE_WIDECHARACTER  = 'widecharacter';
    public const COLUMN_TYPE_NUMERIC        = 'numeric';
    public const COLUMN_TYPE_FLOAT          = 'float';
    public const COLUMN_TYPE_DOUBLE         = 'double';
    public const COLUMN_TYPE_INTEGER        = 'integer';
    public const COLUMN_TYPE_DATE           = 'date';
    public const COLUMN_TYPE_DATETIME       = 'datetime';
    public const COLUMN_TYPE_TIMESTAMP      = 'timestamp';
    public const COLUMN_TYPE_BOOLEAN        = 'boolean';

    // character classes
    public const CHARACTER_CLASS_NONE   = '';
    public const CHARACTER_CLASS_ALNUM  = 'alnum';
    public const CHARACTER_CLASS_ALPHA  = 'alpha';
    public const CHARACTER_CLASS_ASCII  = 'ascii';
    public const CHARACTER_CLASS_BLANK  = 'blank';
    public const CHARACTER_CLASS_CNTRL  = 'cntrl';
    public const CHARACTER_CLASS_DIGIT  = 'digit';
    public const CHARACTER_CLASS_GRAPH  = 'graph';
    public const CHARACTER_CLASS_LOWER  = 'lower';
    public const CHARACTER_CLASS_PRINT  = 'print';
    public const CHARACTER_CLASS_PUNCT  = 'punct';
    public const CHARACTER_CLASS_SPACE  = 'space';
    public const CHARACTER_CLASS_UPPER  = 'upper';
    public const CHARACTER_CLASS_WORD   = 'word';
    public const CHARACTER_CLASS_XDIGIT = 'xdigit';


    public function run(\Flexio\IFace\IProcess $process) : void
    {
        parent::run($process);

        // stdin/stdout
        $instream = $process->getStdin();
        $outstream = $process->getStdout();
        $this->processStream($instream, $outstream);
    }

    private function processStream(\Flexio\IFace\IStream &$instream, \Flexio\IFace\IStream &$outstream) : void
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

    private function getTableOutput(\Flexio\IFace\IStream &$instream, \Flexio\IFace\IStream &$outstream) : void
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

    private function getFileOutput(\Flexio\IFace\IStream &$instream, \Flexio\IFace\IStream &$outstream) : void
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

    private function getTableExpressionMap(\Flexio\IFace\IStream &$instream) // TODO: add return type
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

    private function getStreamExpressionMap(\Flexio\IFace\IStream &$instream) // TODO: add return type
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
                'type' => self::COLUMN_TYPE_TEXT
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

    private static function getChangeCaseExpr(array $operation, string $expr) // TODO: add return type
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

    private static function getSubstrExpr(array $operation, string $expr) // TODO: add return type
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

    private static function getRemoveTextExpr(array $operation, string $expr) // TODO: add return type
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

    private static function getTrimTextExpr(array $operation, string $expr) // TODO: add return type
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

    private static function getPadTextExpr(array $operation, string $expr, array $columns) // TODO: add return type
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

    private static function getChangeTypeExpr(array $operation, string $expr, array $old_structure, array &$new_structure) // TODO: add return type
    {
        $old_structure['width'] = $old_structure['width'] ?? -1;
        $old_structure['scale'] = $old_structure['scale'] ?? -1;

        $new_structure['type'] = $operation['type'] ?? self::COLUMN_TYPE_NONE;
        $new_structure['width'] = $old_structure['width'];
        $new_structure['scale'] = $old_structure['scale'];

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

        if (!array_search($new_structure['type'], $column_types))
            return false;

        if ($new_structure['type'] == self::COLUMN_TYPE_CHARACTER)
        {
            switch ($old_structure['type'])
            {
                case self::COLUMN_TYPE_DATE:
                    $new_structure['width'] = 10;
                    break;

                case self::COLUMN_TYPE_DATETIME:
                    $new_structure['width'] = 20;
                    break;

                case self::COLUMN_TYPE_NUMERIC:
                case self::COLUMN_TYPE_DOUBLE:
                    if ($old_structure['width'] < 0)
                        $new_structure['width'] = 20;
                        else
                        $new_structure['width'] = $old_structure['width'] + 2;
                    break;

                case self::COLUMN_TYPE_INTEGER:
                    $new_structure['width'] = 20;
                    break;

                case self::COLUMN_TYPE_BOOLEAN:
                    $new_structure['width'] = 5; // enough to hold "true"/"false"
                    break;
            }
        }

        if ($new_structure['type'] == self::COLUMN_TYPE_NUMERIC)
        {
            switch ($old_structure['type'])
            {
                case self::COLUMN_TYPE_DATE:
                    $new_structure['width'] = 8;
                    break;

                case self::COLUMN_TYPE_DATETIME:
                    $new_structure['width'] = 14;
                    break;
            }
        }

        $expr = self::getChangeTypeExprDetail($old_structure, $new_structure);
        return $expr;
    }

    private static function getChangeTypeExprDetail($old_structure, &$new_structure) // TODO: add return type; TODO: add parameter type
    {
        $old_name = $old_structure['name'];
        $old_type = $old_structure['type'];

        $new_type = $new_structure['type'];
        $new_width = $new_structure['width'];
        $new_scale = $new_structure['scale'];

        $expr = $old_name;

        // if the type is the same return the same thing; TODO: for now, ignore width changes
        if ($old_type === $new_type)
            return $expr;

        switch ($new_type)
        {
            case self::COLUMN_TYPE_TEXT:
            case self::COLUMN_TYPE_CHARACTER:
            case self::COLUMN_TYPE_WIDECHARACTER:
                if ($new_width == -1 || is_null($new_width))
                    $expr = "to_char($expr)";
                     else
                    $expr = "substr(to_char($expr),1,$new_width)";
                break;

            case self::COLUMN_TYPE_NUMERIC:
            case self::COLUMN_TYPE_FLOAT:
            case self::COLUMN_TYPE_DOUBLE:
            case self::COLUMN_TYPE_INTEGER:
                $expr = "to_number($expr)";
                break;

            case self::COLUMN_TYPE_DATE:
                $expr = "to_date($expr)";
                break;

            case self::COLUMN_TYPE_TIMESTAMP:
            case self::COLUMN_TYPE_DATETIME:
                $expr = "to_timestamp($expr)";
                break;

            case self::COLUMN_TYPE_BOOLEAN:
                {
                    if ($old_type === self::COLUMN_TYPE_TEXT || $old_type === self::COLUMN_TYPE_CHARACTER || $old_type === self::COLUMN_TYPE_WIDECHARACTER)
                        $expr = "if(lower($expr) = 'true' or lower($expr) = 't', true, false)";
                    if ($old_type === self::COLUMN_TYPE_NUMERIC || $old_type === self::COLUMN_TYPE_FLOAT || $old_type === self::COLUMN_TYPE_DOUBLE || $old_type === self::COLUMN_TYPE_INTEGER)
                        $expr = "if($expr != 0, true, false)";
                    if ($old_type === self::COLUMN_TYPE_DATE || $old_type === self::COLUMN_TYPE_DATETIME || $old_type === self::COLUMN_TYPE_TIMESTAMP)
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
