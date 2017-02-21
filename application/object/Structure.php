<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-10-19
 *
 * @package flexio
 * @subpackage Services
 */


namespace Flexio\Object;


class Structure
{
    // column types
    const TYPE_INVALID = '';
    const TYPE_TEXT = 'text';
    const TYPE_CHARACTER = 'character';
    const TYPE_WIDECHARACTER = 'widecharacter';
    const TYPE_NUMERIC = 'numeric';
    const TYPE_DOUBLE = 'double';
    const TYPE_INTEGER = 'integer';
    const TYPE_DATE = 'date';
    const TYPE_DATETIME = 'datetime';
    const TYPE_BOOLEAN = 'boolean';

    // column wildcards for denoting type classes in specified column list
    const WILDCARD_INVALID = '';
    const WILDCARD_ALL = '*';
    const WILDCARD_TEXT = ':text';
    const WILDCARD_NUMBER = ':number';
    const WILDCARD_DATE = ':date';
    const WILDCARD_DATETIME = ':datetime';
    const WILDCARD_BOOLEAN = ':boolean';

    private $columns;
    private $column_index_lookup; // column lookup indexed by 'name'
    private $column_store_index_lookup; // column lookup indexed by 'store_name'

    public function __construct()
    {
        $this->initialize();
    }

    public static function create($columns = array())
    {
        // TODO: \Structure::union() allows Structure type input; allow
        // \Structure::create() to take structure input; in general, we may
        // want the create() classes of various objects to also allow a copy
        // constructor type syntax where the input can also be a class object

        // allow a structure to be created from 1) an array of columns,
        // 2) a json_string that represents an array of columns

        if (is_string($columns))
            $columns = json_decode($columns, true);

        if (!is_array($columns))
            return false;

        $object = (new self);
        foreach ($columns as $c)
        {
            $result = $object->push($c);
            if ($result === false)
                return false;
        }

        return $object;
    }

    public static function union($structures = array())
    {
        // TODO: make sure that union of structures handles column names
        // that are equivalent, differing only by case (e.g. Field1 vs. field1;
        // these should merge)

        // this function creates a "superset" structure from a set of input
        // structures; the algorithm takes the most commonly occurring structure
        // and diffs it with the next most commonly occurring structure recursively
        // until a final structure is obtained

        if (!is_array($structures))
            return false;

        $structure_list = array();

        // count the different structure kinds
        foreach ($structures as $structure_item)
        {
            if (!($structure_item instanceof \Flexio\Object\Structure))
            {
                // if we don't have a structure, we may have a list of columns
                // or a string that represents columns; try to create a structure
                // object from the input; if we can't, disregard the entry
                $structure_item = self::create($structure_item);
                if ($structure_item === false)
                    continue;
            }

            $structure = $structure_item->enum();
            $structure_signature = md5(serialize($structure));

            if (!isset($structure_list[$structure_signature]))
            {
                $structure_list[$structure_signature] = array("count" => 1, "structure" => $structure);
            }
             else
            {
                $count = $structure_list[$structure_signature]['count'];
                $structure_list[$structure_signature]['count'] = $count + 1;
            }
        }

        // sort the structures in descending order of frequency
        \Util::sortByFieldDesc($structure_list, 'count');

        // recursively merge the structures
        $structure_output = array();
        foreach ($structure_list as $structure_item)
        {
            $structure = $structure_item['structure'];
            $structure_output = self::mergeStructure($structure_output, $structure);
        }

        // create a structure object with the result
        return self::create($structure_output);
    }

    public static function intersect($structures)
    {
        // TODO: creates a structure that's the intersection of the specified structures
        return false;
    }

    public function copy()
    {
        $object_copy = self::create();
        $object_copy->columns = $this->columns;
        return $object_copy;
    }

    public function push($column)
    {
        // note: returns the added column, or false if the column couldn't be added

        // make sure we have a column array
        if (!is_array($column))
            return false;

        // get the column info from the input
        $column_entry = array(
            "name" => isset_or($column['name'], ""),
            "store_name" => isset_or($column['store_name'], ""),
            "type" => isset_or($column['type'], self::TYPE_TEXT), // text is the default type
            "width" => isset_or($column['width'], null),
            "scale" => isset_or($column['scale'], null)
        );

        if (isset($column['expression']))
            $column_entry['expression'] = $column['expression'];

        // make sure we have a valid parameter values
        if (!is_string($column_entry['name']))
            return false;
        if (strlen($column_entry['name']) === 0)
            return false;
        if (!is_string($column_entry['type']))
            return false;
        if (strlen($column_entry['type']) === 0)
            return false;
        if (isset($column_entry['width']) && !is_integer($column_entry['width']))
            return false;
        if (isset($column_entry['scale']) && !is_integer($column_entry['scale']))
            return false;
        if (isset($column_entry['expression']) && !is_string($column_entry['expression']))
           return false;

        // make sure the name is valid and unique
        $temp_column_name = self::makeValidName($column_entry['name']);
        $column_entry['name'] = self::getUniqueName($temp_column_name, $this->column_index_lookup);

        // if a store name is supplied, use it; otherwise create a store name from the
        // column name; make sure the store name is valid and unique
        $store_name = strlen($column_entry['store_name']) > 0 ? $column_entry['store_name'] : $column_entry['name'];
        $temp_store_column_name = self::makeValidStoreName($store_name);
        $column_entry['store_name'] = self::getUniqueName($temp_store_column_name, $this->column_store_index_lookup);

        // convert type to make sure we have a valid type
        $column_entry['type'] = strtolower($column_entry['type']);
        switch ($column_entry['type'])
        {
            default:
                return false;

            case self::TYPE_TEXT:
            case self::TYPE_CHARACTER:
            case self::TYPE_WIDECHARACTER:
            case self::TYPE_NUMERIC:
            case self::TYPE_DOUBLE:
            case self::TYPE_INTEGER:
            case self::TYPE_DATE:
            case self::TYPE_DATETIME:
            case self::TYPE_BOOLEAN:
                break;
        }

        // adjust the width so it is reasonable for the specified type
        switch ($column_entry['type'])
        {
            case self::TYPE_TEXT:
                $column_entry['width'] = null;
                break;

            case self::TYPE_CHARACTER:
            case self::TYPE_WIDECHARACTER:
                // width can be either null or an integer between 1 and 255
                if (isset($column_entry['width']))
                {
                    if ($column_entry['width'] < 1)
                        $column_entry['width'] = 1;
                    if ($column_entry['width'] > 255)
                        $column_entry['width'] = 255;
                }
                break;

            case self::TYPE_NUMERIC:
                // numeric width must be between 2 and 18
                if (!isset($column_entry['width']))
                    $column_entry['width'] = 18;
                if ($column_entry['width'] < 2)
                    $column_entry['width'] = 2;
                if ($column_entry['width'] > 18)
                    $column_entry['width'] = 18;
                break;

            case self::TYPE_DOUBLE:
            case self::TYPE_INTEGER:
            case self::TYPE_DATE:
            case self::TYPE_DATETIME:
                $column_entry['width'] = 8;
                break;

            case self::TYPE_BOOLEAN:
                $column_entry['width'] = 1;
                break;
        }

        // adjust the scale so it is reasonable for the specified type
        switch ($column_entry['type'])
        {
            case self::TYPE_TEXT:
            case self::TYPE_CHARACTER:
            case self::TYPE_WIDECHARACTER:
            case self::TYPE_INTEGER:
            case self::TYPE_DATE:
            case self::TYPE_DATETIME:
            case self::TYPE_BOOLEAN:
                $column_entry['scale'] = null;
                break;

            case self::TYPE_NUMERIC:
            case self::TYPE_DOUBLE:
                if (!isset($column_entry['scale']))
                    $column_entry['scale'] = 0;
                if ($column_entry['scale'] < 0)
                    $column_entry['scale'] = 0;
                if ($column_entry['scale'] > 12)
                    $column_entry['scale'] = 12;
                break;
        }

        // if the type is numeric, make sure the scale is less than the width
        // by at least 2
        if ($column_entry['type'] === self::TYPE_NUMERIC && ($column_entry['scale'] > $column_entry['width'] - 2))
            return false;

        // save the column entry by the name and the store_name key
        $this->column_index_lookup[$column_entry['name']] = $column_entry;
        $this->column_store_index_lookup[$column_entry['store_name']] = $column_entry;

        // save the column entry
        $this->columns[] = $column_entry;

        // return the newly added column
        return $column_entry;
    }

    public function pop()
    {
        // note: returns the removed column, or false if there was no column to remove

        // remove the entry from the lookups
        unset($this->column_index_lookup[$column_entry['name']]);
        unset($this->column_store_index_lookup[$column_entry['store_name']]);

        // remove the entry from the column list
        $removed_element = array_pop($this->columns);

        // return the column that was removed; return false
        if (!isset($removed_element))
            return false;

        return $removed_element;
    }

    public function get()
    {
        // return the structure "as is"
        return $this->getColumns(false);
    }

    public function enum($columns = false)
    {
        return $this->getColumns($columns);
    }

    public function getNames($columns = false)
    {
        $columns =  $this->getColumns($columns);

        $column_names = array();
        foreach ($columns as $col)
            $column_names[] = $col['name'];

        return $column_names;
    }

    public function clear()
    {
        $this->initialize();
        return true;
    }

    private function initialize()
    {
        $this->columns = array();
        $this->column_index_lookup = array();
        $this->column_store_index_lookup = array();
    }

    private function getColumns($specified_columns = false, $filter_type = false)
    {
        // takes an optional list of specified column names and returns a unique list of
        // output columns (structure) composed of: 1) any available column that satisfies
        // any specified column wildcard (e.g. "*"), 2) any available column explicitly
        // listing in the list of specified columns; if no columns satisfy the condition,
        // the function returns an empty array; if the $specified_columns isn't specified,
        // then all the columns are returned

        // note: the option filter_type allows the columns to be filtered by type when
        // specified (e.g. "filter_type" = "character"); TODO: this is temporary capability
        // for jobs that need to filter by type until these jobs are able to work on columns
        // of different types

        if ($specified_columns === false)
            return $this->columns;

        if (!is_array($specified_columns))
            return array();

        $column_names_to_output = array();
        $available_column_info_clean_by_name = array();   // structure info indexed by name
        $available_column_info_clean_by_idx = array();  // structure info indexed by natural column order


        // STEP 1: make sure the available columns are valid and lowercase
        foreach ($this->columns as $column_info)
        {
            // available columns are an array of column info (structure format)
            if (!isset($column_info['name']))
                continue;

            // if a column type filter is set, only allow through columns of that type
            if (is_string($filter_type) && $filter_type === self::TYPE_CHARACTER)
            {
                if (isset($column_info['type']) && $column_info['type'] !== self::TYPE_CHARACTER)
                    continue;
            }

            // if the column already exists in the lookup, move on
            $column_name = strtolower($column_info['name']);
            if (array_key_exists($column_name, $available_column_info_clean_by_name))
                continue;

            // save the lowercase name in the list of lookups
            $column_info_clean = $column_info;
            $column_info_clean['name'] = $column_name;

            $available_column_info_clean_by_name[$column_name] = $column_info_clean;
            $available_column_info_clean_by_idx[] = $column_info_clean;
        }


        // STEP 2: add any specified column that's in the available column list
        // to the list of output columns
        foreach ($specified_columns as $column_name)
        {
            // if the column is explicitly listed in the available column,
            // include it
            $column_name = strtolower($column_name);
            if (array_key_exists($column_name, $available_column_info_clean_by_name))
            {
                $column_names_to_output[] = $column_name;
                continue;
            }

            // if the column is a wildcard, then add any available column
            // satisfying the wildcard condition
            $wildcard_columns = self::getWildcardColumns($column_name, $available_column_info_clean_by_idx);
            $column_names_to_output = array_merge($column_names_to_output, $wildcard_columns);
        }


        // STEP 3: make sure column list is unique and return the columns
        $output_columns = array();
        $output_columns_lookup = array();

        foreach ($column_names_to_output as $column_name)
        {
            if (array_key_exists($column_name, $output_columns_lookup))
                continue;

            $output_columns_lookup[$column_name] = '';
            $output_columns[] = $available_column_info_clean_by_name[$column_name];
        }

        return $output_columns;
    }

    private static function getWildcardColumns($wildcard_column, $available_columns)
    {
        $output_column_names = array();

        foreach ($available_columns as $column_info)
        {
            $column_name = $column_info['name'];

            // check column type wildcards
            if ($wildcard_column === self::WILDCARD_ALL)
                $output_column_names[] = $column_name;

            if ($wildcard_column === self::WILDCARD_TEXT && self::isTextColumn($column_info))
                $output_column_names[] = $column_name;

            if ($wildcard_column === self::WILDCARD_NUMBER && self::isNumberColumn($column_info))
                $output_column_names[] = $column_name;

            if ($wildcard_column === self::WILDCARD_DATE && self::isDateColumn($column_info))
                $output_column_names[] = $column_name;

            if ($wildcard_column === self::WILDCARD_DATETIME && self::isDateTimeColumn($column_info))
                $output_column_names[] = $column_name;

            if ($wildcard_column === self::WILDCARD_BOOLEAN && self::isBooleanColumn($column_info))
                $output_column_names[] = $column_name;

            // check for column name regular expression
            $column_regex_expr = $wildcard_column;
            $column_regex_expr = str_replace('/','\/',$column_regex_expr);

            $matches = array();
            if (@preg_match("/^$column_regex_expr$/i", $column_name, $matches))
                $output_column_names[] = $column_name;
        }

        return $output_column_names;
    }

    private static function mergeStructure($structure1, $structure2)
    {
        $count_structure1 = count($structure1);
        $count_structure2 = count($structure2);

        if ($count_structure1 === 0)
            return $structure2;
        if ($count_structure2 === 0)
            return $structure1;
        if ($count_structure1 === 0 && $count_structure2 === 0)
            return array();

        // create an array of names and a key-value lookup for the structure
        // for each structure
        $structure1_names = array();
        $structure1_lookup_by_name = array();
        foreach ($structure1 as $s)
        {
            $structure1_names[] = $s['name'];
            $structure1_lookup_by_name[$s['name']] = $s;
        }
        $structure2_names = array();
        $structure2_lookup_by_name = array();
        foreach ($structure2 as $s)
        {
            $structure2_names[] = $s['name'];
            $structure2_lookup_by_name[$s['name']] = $s;
        }

        // get the diff between the two name arrays; produces an output as follows:
        //     \Util::diff(array('b','c','d'),array('a','b','c','e'))
        //     [{"+":"a"},{"=":"b"},{"=":"c"},{"-":"d"},{"+":"e"}]
        $diff = \Util::diff($structure1_names, $structure2_names);

        $merged_structure = array();
        foreach ($diff as $d)
        {
            // names in both structure; add info from first structure
            if (isset($d['=']))
            {
                $field1_info = $structure1_lookup_by_name[$d['=']];
                $field2_info = $structure2_lookup_by_name[$d['=']];
                $merged_structure[] = self::mergeFieldInfo($field1_info, $field2_info);
            }

            // names in structure2 but not structure1; add structure2 info
            if (isset($d['+']))
                $merged_structure[] = $structure2_lookup_by_name[$d['+']];

            // names in structure1 but not structure2; add structure2 info
            if (isset($d['-']))
                $merged_structure[] = $structure1_lookup_by_name[$d['-']];
        }

        return $merged_structure;
    }

    private static function mergeFieldInfo($field1_info, $field2_info)
    {
        // this function takes the information for two fields and merges
        // them to create an output type that can safely be used to represent
        // the information in each field

        // resolve the types
        $type1 = $field1_info['type'];
        $type2 = $field2_info['type'];
        $output_type = \DbUtil::getCompatibleType($type1, $type2);

        // resolve the widths
        $width1 = $field1_info['width'];
        $width2 = $field2_info['width'];
        $output_width = max($width1, $width2);

        // resolve the decimal places
        $scale1 = $field1_info['scale'];
        $scale2 = $field2_info['scale'];
        $output_scale = max($scale1, $scale2);
        if ($output_type !== 'numeric' && $output_type !== 'double' && $output_type !== 'integer')
            $output_scale = 0;

        $output_info = $field1_info;
        $output_info['type'] = $output_type;
        $output_info['width'] = $output_width;
        $output_info['scale'] = $output_scale;

        return $output_info;
    }

    private static function isTextColumn($column_info)
    {
        switch ($column_info['type'])
        {
            default:
                return false;

            case self::TYPE_TEXT:
            case self::TYPE_CHARACTER:
            case self::TYPE_WIDECHARACTER:
                return true;
        }
    }

    private static function isNumberColumn($column_info)
    {
        switch ($column_info['type'])
        {
            default:
                return false;

            case self::TYPE_NUMERIC:
            case self::TYPE_DOUBLE:
            case self::TYPE_INTEGER:
                return true;
        }
    }

    private static function isDateColumn($column_info)
    {
        switch ($column_info['type'])
        {
            default:
                return false;

            case self::TYPE_DATE:
                return true;
        }
    }

    private static function isDateTimeColumn($column_info)
    {
        switch ($column_info['type'])
        {
            default:
                return false;

            case self::TYPE_DATETIME:
                return true;
        }
    }

    private static function isBooleanColumn($column_info)
    {
        switch ($column_info['type'])
        {
            default:
                return false;

            case self::TYPE_BOOLEAN:
                return true;
        }
    }

    private static function getUniqueName($name, $column_lookup)
    {
        // if the column name doesn't exist, the name is valid; so return it
        if (!isset($column_lookup[$name]))
            return $name;

        // if the name exists, add a number that will make the name unique
        $counter = 1;
        while (true)
        {
            $new_name = $name . "_$counter";
            if (!isset($column_lookup[$new_name]))
                return $new_name;

            ++$counter;
        }
    }

    private static function makeValidName($name)
    {
        // TODO: for now, make names lowercase (the datastore stores fields
        // in lowercase, so until we map the datastore structure to the
        // stream structure using the store name, we need to do this so
        // that there isn't a name mismatch between the stream and datastore
        // structures

        // trim the name from leading/trailing spaces
        $name = strtolower($name);
        return trim($name);
    }

    private static function makeValidStoreName($name)
    {
        // store names are always lowercase
        $name = strtolower($name);

        // replace certain characters
        $invalid_col_chars = "*|!@$&^*:\"<>()[]{}?\\:;'`~-+=,./\x00\x09\x0A\x0B\x0C\x0D\xFF\t\n ";
        $result = trim($name);
        $count = strlen($result);

        // if it's an empty string, return empty
        if ($count == 0)
            return $result;

        for ($i = 0; $i < $count; ++$i)
        {
            if (false !== strchr($invalid_col_chars, $result[$i]))
            {
                $delete_char = false;

                // if the character is the first or the last character in the string, delete it
                if ($i == 0 || $i == ($count-1))
                    $delete_char = true;

                // if the previous character is an underscore or a space, don't duplicate
                // the underscore; rather delete the special character
                else if ($i > 0 && ($result[$i-1] == '_' || $result[$i-1] == chr(1) || ctype_space($result[$i-1])))
                    $delete_char = true;

                // if the next character is an underscore, delete the special character
                else if ($i < ($count-1) && ($result[$i+1] == '_' || $result[$i+1] == chr(1)))
                    $delete_char = true;

                if ($delete_char)
                {
                    $result = substr($result, 0, $i) . substr($result, $i + 1);
                    $count = strlen($result);
                    if ($count == 0)
                        return $result;
                    $i--;
                    continue;
                }

                $result[$i] = chr(1);
            }
        }

        $result = trim($result, chr(1));
        $result = str_replace(chr(1), '_', $result);

        // make sure the field name is not a keyword
        if (self::isKeyword($result))
            $result = 'f_' . $result;

        // if the field starts with a number, prefix it with f_
        if (ctype_digit($result[0]))
            $result = 'f_' . $result;

        // rename badly named columns to something that will work in all databases
        $result = str_replace('#', 'no',  $result);
        $result = str_replace('%', 'pct', $result);
        $result = str_replace('-', '_',   $result);

        return trim($result);
    }

    public static function isKeyword($str)
    {
        $res = array_search(strtoupper($str),
            ["ABORT", "ABS", "ABSOLUTE", "ACCESS", "ACTION", "ADA", "ADD", "ADMIN", "AFTER", "AGGREGATE",
            "ALIAS", "ALL", "ALLOCATE", "ALSO", "ALTER", "ALWAYS", "ANALYSE", "ANALYZE", "AND", "ANY", "ARE",
            "ARRAY", "AS", "ASC", "ASENSITIVE", "ASSERTION", "ASSIGNMENT", "ASYMMETRIC", "AT", "ATOMIC",
            "ATTRIBUTE", "ATTRIBUTES", "AUTHORIZATION", "AVG", "BACKWARD", "BEFORE", "BEGIN", "BERNOULLI",
            "BETWEEN", "BIGINT", "BINARY", "BIT", "BITVAR", "BIT_LENGTH", "BLOB", "BOOLEAN", "BOTH", "BREADTH", "BY",
            "C", "CACHE", "CALL", "CALLED", "CARDINALITY", "CASCADE", "CASCADED", "CASE", "CAST", "CATALOG",
            "CATALOG_NAME", "CEIL", "CEILING", "CHAIN", "CHAR", "CHARACTER", "CHARACTERISTICS", "CHARACTERS",
            "CHARACTER_LENGTH", "CHARACTER_SET_CATALOG", "CHARACTER_SET_NAME", "CHARACTER_SET_SCHEMA",
            "CHAR_LENGTH", "CHECK", "CHECKED", "CHECKPOINT", "CLASS", "CLASS_ORIGIN", "CLOB", "CLOSE", "CLUSTER",
            "COALESCE", "COBOL", "COLLATE", "COLLATION", "COLLATION_CATALOG", "COLLATION_NAME", "COLLATION_SCHEMA",
            "COLLECT", "COLUMN", "COLUMN_NAME", "COMMAND_FUNCTION", "COMMAND_FUNCTION_CODE", "COMMENT", "COMMIT",
            "COMMITTED", "COMPLETION", "CONDITION", "CONDITION_NUMBER", "CONNECT", "CONNECTION", "CONNECTION_NAME",
            "CONSTRAINT", "CONSTRAINTS", "CONSTRAINT_CATALOG", "CONSTRAINT_NAME", "CONSTRAINT_SCHEMA", "CONSTRUCTOR",
            "CONTAINS", "CONTINUE", "CONVERSION", "CONVERT", "COPY", "CORR", "CORRESPONDING", "COUNT", "COVAR_POP",
            "COVAR_SAMP", "CREATE", "CREATEDB", "CREATEROLE", "CREATEUSER", "CROSS", "CSV", "CUBE", "CUME_DIST",
            "CURRENT", "CURRENT_DATE", "CURRENT_DEFAULT_TRANSFORM_GROUP", "CURRENT_PATH", "CURRENT_ROLE",
            "CURRENT_TIME", "CURRENT_TIMESTAMP", "CURRENT_TRANSFORM_GROUP_FOR_TYPE", "CURRENT_USER", "CURSOR",
            "CURSOR_NAME", "CYCLE", "DATA", "DATABASE", "DATE", "DATETIME_INTERVAL_CODE", "DATETIME_INTERVAL_PRECISION",
            "DAY",  "DEALLOCATE", "DEC", "DECIMAL", "DECLARE", "DEFAULT", "DEFAULTS", "DEFERRABLE", "DEFERRED", "DEFINED",
            "DEFINER", "DEGREE", "DELETE", "DELIMITER", "DELIMITERS", "DENSE_RANK", "DEPTH", "DEREF", "DERIVED",
            "DESC", "DESCRIBE", "DESCRIPTOR", "DESTROY", "DESTRUCTOR", "DETERMINISTIC", "DIAGNOSTICS", "DICTIONARY",
            "DISABLE", "DISCONNECT", "DISPATCH", "DISTINCT", "DO", "DOMAIN", "DOUBLE", "DROP", "DYNAMIC",
            "DYNAMIC_FUNCTION", "DYNAMIC_FUNCTION_CODE", "EACH", "ELEMENT", "ELSE", "ENABLE", "ENCODING", "ENCRYPTED",
            "END", "END-EXEC", "EQUALS", "ESCAPE", "EVERY", "EXCEPT", "EXCEPTION", "EXCLUDE", "EXCLUDING", "EXCLUSIVE",
            "EXEC", "EXECUTE", "EXISTING", "EXISTS", "EXP", "EXPLAIN", "EXTERNAL", "EXTRACT", "FALSE", "FETCH", "FILTER",
            "FINAL", "FIRST", "FLOAT", "FLOOR", "FOLLOWING", "FOR", "FORCE", "FOREIGN", "FORTRAN", "FORWARD", "FOUND",
            "FREE", "FREEZE", "FROM", "FULL", "FUNCTION", "FUSION", "GENERAL", "GENERATED", "GET", "GLOBAL", "GO", "GOTO",
            "GRANT", "GRANTED", "GREATEST", "GROUP", "GROUPING", "HANDLER", "HAVING", "HEADER", "HIERARCHY", "HOLD", "HOST",
            "HOUR", "IDENTITY", "IGNORE", "ILIKE", "IMMEDIATE", "IMMUTABLE", "IMPLEMENTATION", "IMPLICIT", "IN",
            "INCLUDING", "INCREMENT", "INDEX", "INDICATOR", "INFIX", "INHERIT", "INHERITS", "INITIALIZE",
            "INITIALLY", "INNER", "INOUT", "INPUT", "INSENSITIVE", "INSERT", "INSTANCE", "INSTANTIABLE", "INSTEAD",
            "INT", "INTEGER", "INTERSECT", "INTERSECTION", "INTERVAL", "INTO", "INVOKER", "IS", "ISNULL",
            "ISOLATION", "ITERATE", "JOIN", "KEY", "KEY_MEMBER", "KEY_TYPE",
            "LANCOMPILER", "LANGUAGE", "LARGE", "LAST", "LATERAL", "LEADING", "LEAST", "LEFT", "LENGTH", "LESS",
            "LEVEL", "LIKE", "LIMIT", "LISTEN", "LN", "LOAD", "LOCAL", "LOCALTIME", "LOCALTIMESTAMP", "LOCATION",
            "LOCATOR", "LOCK", "LOGIN", "LOWER", "MAP", "MATCH", "MATCHED", "MAX", "MAXVALUE", "MEMBER", "MERGE", "MESSAGE_LENGTH",
            "MESSAGE_OCTET_LENGTH", "MESSAGE_TEXT", "METHOD", "MIN", "MINUTE", "MINVALUE", "MOD", "MODE",
            "MODIFIES", "MODIFY", "MODULE", "MONTH", "MORE", "MOVE", "MULTISET", "MUMPS",
            "NAME", "NAMES", "NATIONAL", "NATURAL", "NCHAR", "NCLOB", "NESTING", "NEW", "NEXT", "NO", "NOCREATEDB",
            "NOCREATEROLE", "NOCREATEUSER", "NOINHERIT", "NOLOGIN", "NONE", "NORMALIZE", "NORMALIZED", "NOSUPERUSER",
            "NOT", "NOTHING", "NOTIFY", "NOTNULL", "NOWAIT", "NULL", "NULLABLE", "NULLIF", "NULLS", "NUMBER", "NUMERIC",
            "OBJECT", "OCTETS", "OCTET_LENGTH", "OF", "OFF", "OFFSET", "OIDS", "OLD", "ON", "ONLY", "OPEN",
            "OPERATION", "OPERATOR", "OPTION", "OPTIONS", "OR", "ORDER", "ORDERING", "ORDINALITY", "OTHERS", "OUT",
            "OUTER", "OUTPUT", "OVER", "OVERLAPS", "OVERLAY", "OVERRIDING", "OWNER",
            "PAD", "PARAMETER", "PARAMETERS", "PARAMETER_MODE", "PARAMETER_NAME", "PARAMETER_ORDINAL_POSITION",
            "PARAMETER_SPECIFIC_CATALOG", "PARAMETER_SPECIFIC_NAME", "PARAMETER_SPECIFIC_SCHEMA", "PARTIAL",
            "PARTITION", "PASCAL", "PASSWORD", "PATH", "PERCENTILE_CONT", "PERCENTILE_DISC", "PERCENT_RANK",
            "PLACING", "PLI", "POSITION", "POSTFIX", "POWER", "PRECEDING", "PRECISION", "PREFIX", "PREORDER",
            "PREPARE", "PREPARED", "PRESERVE", "PRIMARY", "PRIOR", "PRIVILEGES", "PROCEDURAL", "PROCEDURE", "PUBLIC", "QUOTE",
            "RANGE", "RANK", "READ", "READS", "REAL", "RECHECK", "RECURSIVE", "REF", "REFERENCES", "REFERENCING",
            "REGR_AVGX", "REGR_AVGY", "REGR_COUNT", "REGR_INTERCEPT", "REGR_R2", "REGR_SLOPE", "REGR_SXX",
            "REGR_SXY", "REGR_SYY", "REINDEX", "RELATIVE", "RELEASE", "RENAME", "REPEATABLE", "REPLACE", "RESET",
            "RESTART", "RESTRICT", "RESULT", "RETURN", "RETURNED_CARDINALITY", "RETURNED_LENGTH", "RETURNED_OCTET_LENGTH",
            "RETURNED_SQLSTATE", "RETURNS", "REVOKE", "RIGHT", "ROLE", "ROLLBACK", "ROLLUP", "ROUTINE",
            "ROUTINE_CATALOG", "ROUTINE_NAME", "ROUTINE_SCHEMA", "ROW", "ROWS", "ROW_NUMBER", "RULE",
            "SAVEPOINT", "SCALE", "SCHEMA", "SCHEMA_NAME", "SCOPE", "SCOPE_CATALOG", "SCOPE_NAME", "SCOPE_SCHEMA",
            "SCROLL", "SEARCH", "SECOND", "SECTION", "SECURITY", "SELECT", "SELF", "SENSITIVE", "SEQUENCE",
            "SERIALIZABLE", "SERVER_NAME", "SESSION", "SESSION_USER", "SET", "SETOF", "SETS", "SHARE", "SHOW",
            "SIMILAR", "SIMPLE", "SIZE", "SMALLINT", "SOME", "SOURCE", "SPACE", "SPECIFIC", "SPECIFICTYPE",
            "SPECIFIC_NAME", "SQL", "SQLCODE", "SQLERROR", "SQLEXCEPTION", "SQLSTATE", "SQLWARNING", "SQRT",
            "STABLE", "START",  "STATEMENT", "STATIC", "STATISTICS", "STDDEV_POP", "STDDEV_SAMP", "STDIN",
            "STDOUT", "STORAGE", "STRICT", "STRUCTURE", "STYLE", "SUBCLASS_ORIGIN", "SUBLIST", "SUBMULTISET",
            "SUBSTRING", "SUM", "SUPERUSER", "SYMMETRIC", "SYSID", "SYSTEM", "SYSTEM_USER",
            "TABLE", "TABLESAMPLE", "TABLESPACE", "TABLE_NAME", "TEMP", "TEMPLATE", "TEMPORARY", "TERMINATE",
            "THAN", "THEN", "TIES", "TIME", "TIMESTAMP", "TIMEZONE_HOUR", "TIMEZONE_MINUTE", "TO", "TOAST",
            "TOP_LEVEL_COUNT", "TRAILING", "TRANSACTION", "TRANSACTIONS_COMMITTED", "TRANSACTIONS_ROLLED_BACK",
            "TRANSACTION_ACTIVE", "TRANSFORM", "TRANSFORMS", "TRANSLATE", "TRANSLATION", "TREAT", "TRIGGER",
            "TRIGGER_CATALOG", "TRIGGER_NAME", "TRIGGER_SCHEMA", "TRIM", "TRUE", "TRUNCATE", "TRUSTED", "TYPE",
            "UESCAPE", "UNBOUNDED", "UNCOMMITTED", "UNDER", "UNENCRYPTED", "UNION", "UNIQUE", "UNKNOWN", "UNLISTEN",
            "UNNAMED", "UNNEST", "UNTIL", "UPDATE", "UPPER", "USAGE", "USER", "USER_DEFINED_TYPE_CATALOG",
            "USER_DEFINED_TYPE_CODE", "USER_DEFINED_TYPE_NAME", "USER_DEFINED_TYPE_SCHEMA", "USING",
            "VACUUM", "VALID", "VALIDATOR", "VALUE", "VALUES", "VARCHAR", "VARIABLE", "VARYING", "VAR_POP",
            "VAR_SAMP", "VERBOSE", "VIEW", "VOLATILE", "WHEN", "WHENEVER", "WHERE", "WIDTH_BUCKET", "WINDOW", "WITH",
            "WITHIN", "WITHOUT", "WORK", "WRITE", "YEAR", "ZONE"]);

        return ($res !== false ? true : false);
    }
}
