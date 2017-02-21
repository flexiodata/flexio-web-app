<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-02-16
 *
 * @package flexio
 * @subpackage Jobs
 */


require_once __DIR__ . DIRECTORY_SEPARATOR . 'Base.php';

class ConvertJob extends Base
{
    // token delimiters; other types of delimiters are allowed as well
    const DELIMITER_NONE              = '{none}';
    const DELIMITER_COMMA             = '{comma}';
    const DELIMITER_SEMICOLON         = '{semicolon}';
    const DELIMITER_PIPE              = '{pipe}';
    const DELIMITER_TAB               = '{tab}';
    const DELIMITER_SPACE             = '{space}';

    // token text qualifiers
    const TEXT_QUALIFIER_NONE         = '{none}';
    const TEXT_QUALIFIER_SINGLE_QUOTE = '{single-quote}';
    const TEXT_QUALIFIER_DOUBLE_QUOTE = '{double-quote}';

    // conversion formats
    const FORMAT_DELIMITED_TEXT = 'delimited';
    const FORMAT_FIXED_LENGTH   = 'fixed';
    const FORMAT_JSON           = 'json';
    const FORMAT_PDF            = 'pdf';
    const FORMAT_TABLE          = 'table';

    public function run()
    {
        // if a job format is specified, get the mime type from the job definition
        $job_definition = $this->getProperties();
        $input_mime_type_from_definition = self::getInputMimeTypeFromDefinition($job_definition);
        $output_mime_type_from_definition = self::getOutputMimeTypeFromDefinition($job_definition);

        // default to convert to table
        if ($output_mime_type_from_definition === false)
            $output_mime_type_from_definition = \ContentType::MIME_TYPE_FLEXIO_TABLE;

        // iterate through the inputs
        $input = $this->getInput()->enum();
        foreach ($input as $instream)
        {
            // get the mime type for the input; use the job format if it's
            // specified, as long as the input format isn't a flexio table
            $mime_type = $instream->getMimeType();
            if ($mime_type != \ContentType::MIME_TYPE_FLEXIO_TABLE)
            {
                if ($input_mime_type_from_definition === false)
                    $mime_type = \ContentType::MIME_TYPE_CSV; // default to csv
                     else
                    $mime_type = $input_mime_type_from_definition;
            }

            switch ($mime_type)
            {
                // unhandled input
                default:
                    $this->getOutput()->push($instream->copy());
                    break;

                // table input
                case \ContentType::MIME_TYPE_FLEXIO_TABLE:
                    $this->createOutputFromTableInput($instream, $output_mime_type_from_definition);
                    break;

                // csv input
                case \ContentType::MIME_TYPE_CSV:
                    $this->createOutputFromCsvInput($instream, $output_mime_type_from_definition);
                    break;

                // text input
                case \ContentType::MIME_TYPE_TXT:
                    $this->createOutputFromFixedLengthInput($instream, $output_mime_type_from_definition);
                    break;

                // text input
                case \ContentType::MIME_TYPE_PDF:
                    $this->createOutputFromPdfInput($instream, $output_mime_type_from_definition);
                    break;
            }
        }
    }

    private function createOutputFromTableInput($instream, $output_mime_type)
    {
        $outstream = \Flexio\Object\Stream::create();
        $outstream->setName($instream->getName());
        $outstream->setPath(\Util::generateHandle());
        $outstream->setMimeType(\ContentType::MIME_TYPE_JSON);

        $this->getOutput()->push($outstream);

        $streamwriter = \Flexio\Object\StreamWriter::create($outstream);
        if ($streamwriter === false)
            return $this->fail(\Model::ERROR_WRITE_FAILED, _(''), __FILE__, __LINE__);

        // get ready to read the input
        $streamreader = \Flexio\Object\StreamReader::create($instream);
        if ($streamreader === false)
            return $this->fail(\Model::ERROR_READ_FAILED, _(''), __FILE__, __LINE__);

        $rown = 0;

        // transfer the data
        $streamwriter->write("[");

        while (true)
        {
            $row = $streamreader->readRow();
            if ($row === false)
                break;

            $row = json_encode($row);
            $streamwriter->write(($rown>0?",\n":"\n") . $row);

            ++$rown;
        }

        $streamwriter->write("\n]");
        $streamwriter->close();
        $outstream->setSize($streamwriter->getBytesWritten());
    }


    private function createOutputFromPdfInput($instream, $output_mime_type)
    {
        if (!isset($GLOBALS['pdfparser_included']))
        {
            $GLOBALS['pdfparser_included'] = true;
            set_include_path(get_include_path() . PATH_SEPARATOR . (dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'library' . DIRECTORY_SEPARATOR . 'pdfparser-0.9.25' . DIRECTORY_SEPARATOR . 'src'));
        }

        require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'library'. DIRECTORY_SEPARATOR .'tcpdf-6.2.12'. DIRECTORY_SEPARATOR . 'tcpdf_parser.php';
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'Base.php';


        // input/output
        $outstream = $instream->copy()->setPath(\Util::generateHandle());
        if ($outstream === false)
            return $this->fail(\Model::ERROR_WRITE_FAILED, _(''), __FILE__, __LINE__);

        $outstream->setMimeType(\ContentType::MIME_TYPE_TXT);
        $this->getOutput()->push($outstream);

        $streamwriter = \Flexio\Object\StreamWriter::create($outstream);
        if ($streamwriter === false)
            return $this->fail(\Model::ERROR_WRITE_FAILED, _(''), __FILE__, __LINE__);

        // read the pdf into a buffer
        $buffer = '';
        $instream->read(function ($data) use (&$buffer) {
            $buffer .= $data;
        });

        // parse the pdf
        try
        {
            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseContent($buffer);

            // write out the text from each of the pages
            $pages  = $pdf->getPages();
            foreach ($pages as $page)
            {
                $text = $page->getText();
                $streamwriter->write($text);
            }
        }
        catch (Exception $e)
        {
            return $this->fail(\Model::ERROR_READ_FAILED, _(''), __FILE__, __LINE__);
        }

        $streamwriter->close();
        $outstream->setSize($streamwriter->getBytesWritten());
    }



    private function createOutputFromCsvInput($instream, $output_mime_type)
    {
        // parameters
        $job_definition = $this->getProperties();
        $streamwriter = null;

        $delimiter = isset_or($job_definition['params']['input']['delimiter'], self::DELIMITER_COMMA);
        $is_output_json = ($output_mime_type == \ContentType::MIME_TYPE_JSON ? true : false);

        if (isset($job_definition['params']['input']['header']))
            $header = $job_definition['params']['input']['header'];
        else if (isset($job_definition['params']['input']['header_row']))    // compatibility
            $header = $job_definition['params']['input']['header_row'];
        else
            $header = true; // default
        $header = toBoolean($header);

        if (isset($job_definition['params']['input']['qualifier']))
            $qualifier = $job_definition['params']['input']['qualifier'];
        else if (isset($job_definition['params']['input']['text_qualifier']))  // compatibility
            $qualifier = $job_definition['params']['input']['text_qualifier'];
        else
            $qualifier = self::TEXT_QUALIFIER_DOUBLE_QUOTE; // default


        switch ($delimiter)
        {
            // convert the delimiter tokens into their literal equivalent;
            // for the default, use what we have
            default:
                break;

            case self::DELIMITER_NONE:       $delimiter = "";   break;
            case self::DELIMITER_COMMA:      $delimiter = ",";  break;
            case self::DELIMITER_SEMICOLON:  $delimiter = ";";  break;
            case self::DELIMITER_PIPE:       $delimiter = "|";  break;
            case self::DELIMITER_TAB:        $delimiter = "\t"; break;
            case self::DELIMITER_SPACE:      $delimiter = " ";  break;
        }

        switch ($qualifier)
        {
            // convert the text qualifier tokens into their literal equivalent
            // for the default, use what we have
            default:
                break;

            case self::TEXT_QUALIFIER_NONE:          $qualifier = "";   break;
            case self::TEXT_QUALIFIER_SINGLE_QUOTE:  $qualifier = "'";  break;
            case self::TEXT_QUALIFIER_DOUBLE_QUOTE:  $qualifier = "\""; break;

            // compatibility
            case '{single_quote}':  $qualifier = "'";  break;
            case '{double_quote}':  $qualifier = "\"";  break;
        }

        $use_text_qualifier = (strlen($qualifier) > 0 ? true : false);

        // get the input
        $streamreader = \Flexio\Object\StreamReader::create($instream);
        if ($streamreader === false)
            return $this->fail(\Model::ERROR_READ_FAILED, _(''), __FILE__, __LINE__);

        // create the output
        $outstream = $instream->copy()->setPath(\Util::generateHandle());
        if ($outstream === false)
            return $this->fail(\Model::ERROR_CREATE_FAILED, _(''), __FILE__, __LINE__);


        $outstream->setMimeType($is_output_json ? \ContentType::MIME_TYPE_JSON : \ContentType::MIME_TYPE_FLEXIO_TABLE);
        $this->getOutput()->push($outstream);

        if ($is_output_json)
        {
            // for json output, streamwriter is created here; for table output, streamwriter
            // is created below, because header row must be collected in advance
            $streamwriter = \Flexio\Object\StreamWriter::create($outstream);
            if ($streamwriter === false)
                return $this->fail(\Model::ERROR_CREATE_FAILED, _(''), __FILE__, __LINE__);
            $streamwriter->write("[");
        }

        // parse each row into a table
        $first_row = true;
        $determine_structure = true;
        $is_icsv = false;
        $rown = 0;

        $structure = array();
        $output_structure = array();

        while (true)
        {
            // parse the row
            if ($use_text_qualifier)
            {
                $row = false;

                // read the row
                for ($n = 0; $n < 500; ++$n)
                {
                    $buf = $streamreader->readRow();

                    if ($buf === false)
                        break; // we're done
                    if ($row === false)
                        $row = $buf;
                         else
                        $row .= $buf;

                    // can we find an unquoted line terminator
                    $terminator = substr($buf, -1); // last char of row buffer
                    if ($terminator != "\r" && $terminator != "\n")
                        break;

                    $lfpos = self::indexOfWithQuoting($row, $terminator, $qualifier);
                    if ($lfpos !== false)
                        break; // if we found an LF, we have the whole CSV row; if not, read in another row
                }

                if ($row === false)
                    break; // reached EOF

                $row = str_getcsv($row, $delimiter, $qualifier);
                if ($row === false)
                    break;
                if ($first_row && $row === array(null)) // skip blank rows
                    continue;
            }
             else
            {
                // read the row
                $row = $streamreader->readRow();
                if ($row === false)
                    break; // we're done

                $row = trim($row,"\r\n");
                if ($first_row && $row == '')
                    continue;
                $row = explode($delimiter, $row);
            }

            // if we're on the first row, try to determine the structure
            // and the initial writer
            if ($first_row)
            {
                $first_row = false;
                $structure = self::structureFromIcsv($row);

                if ($structure)
                {
                    $determine_structure = false;
                    $is_icsv = true;
                }
                 else
                {
                    // we couldn't determine an icsv structure; create a default
                    // structure based off the first row
                    $structure = self::determineStructureFromRow($row, $header);
                }


                if (!$streamwriter)
                {
                    $outstream->setStructure($structure);
                    $streamwriter = \Flexio\Object\StreamWriter::create($outstream);
                    if ($streamwriter === false)
                        return $this->fail(\Model::ERROR_CREATE_FAILED, _(''), __FILE__, __LINE__);
                    $structure = $outstream->getStructure()->enum();
                }

                $output_structure = $structure; // save a version that won't be modified when type sensing
                if ($header === true)
                    continue;  // skip writing this row because it's the header row
            }

            // convert values over to appropriate items to insert
            $row = self::conformValuesToStructure($output_structure, $row);

            // if we don't know the structure, update it based on the contents
            // of the row so that after checking various values, we can set a
            // suitable structure later

            if (!$is_icsv)
                self::updateStructureFromRow($structure, $row);

            if ($is_output_json)
            {
                $row = json_encode($row);
                $result = $streamwriter->write(($rown>0?",\n":"\n") . $row);
            }
             else
            {
                // table output
                $result = $streamwriter->write($row);
            }

            if ($result === false)
                $this->fail(\Model::ERROR_WRITE_FAILED, _(''), __FILE__, __LINE__);

            ++$rown;
        }

        // make sure the buffer is flushed before converting values
        if ($streamwriter !== false)
        {
            if ($is_output_json)
            {
                $streamwriter->write("\n]");
            }

            $result = $streamwriter->close();
            if ($result === false)
                $this->fail(\Model::ERROR_WRITE_FAILED, _(''), __FILE__, __LINE__);

            $outstream->setSize($streamwriter->getBytesWritten());
        }

        // change the structure if the flag is set
/*
        if ($determine_structure)
        {
            $result = self::alterStructure($outstream, $structure);
            if ($result === false)
                return $this->fail(\Model::ERROR_WRITE_FAILED, _(''), __FILE__, __LINE__);
        }
*/

        if (!$is_output_json)
        {
            $output_properties = array(
                'mime_type' => \ContentType::MIME_TYPE_FLEXIO_TABLE,
                'structure' => $structure
            );

            $outstream->set($output_properties);
        }
    }

    private function indexOfWithQuoting($haystack, $needle, $qualifier)
    {
        if ($needle == '')
            return false;
        $haystack_len = strlen($haystack);
        $needle_len = strlen($needle);
        $firstch = $needle[0];
        $offset = 0;
        $quotec = false;

        while ($offset < $haystack_len)
        {
            $ch = $haystack[$offset];

            if ($quotec === false)
            {
                if ($ch == $qualifier)
                {
                    $quotec = $ch;
                }
                 else
                {
                    if ($ch == $firstch)
                    {
                        if (substr($haystack, $offset, $needle_len) == $needle)
                            return $offset;
                    }
                }
            }
             else
            {
                if ($ch == $qualifier)
                {
                    if ($offset + 1 < $haystack_len && $haystack[$offset+1] == $qualifier) // csv double-quote "15"" Pizza"
                    {
                        ++$offset;
                    }
                     else
                    {
                        $quotec = false;
                    }
                }
            }

            ++$offset;
        }

        return false;
    }

    private function createOutputFromFixedLengthInput($instream)
    {
        // parameters
        $job_definition = $this->getProperties();
        $params = $job_definition['params'];
        $start_offset = isset_or($params['start_offset'], 0);
        $row_width = isset_or($params['row_width'], 100);
        $line_delimiter = isset_or($params['line_delimiter'], false);
        $columns = isset_or($params['columns'], []);

        if ($row_width == 0)
            return $this->fail(\Model::ERROR_INVALID_PARAMETER, _(''), __FILE__, __LINE__);

        // get the input
        $streamreader = \Flexio\Object\StreamReader::create($instream);
        if ($streamreader === false)
            return $this->fail(\Model::ERROR_READ_FAILED, _(''), __FILE__, __LINE__);

        // create the output
        $outstream = $instream->copy()->setPath(\Util::generateHandle());
        if ($outstream === false)
            return $this->fail(\Model::ERROR_CREATE_FAILED, _(''), __FILE__, __LINE__);

        $outstream->setMimeType(\ContentType::MIME_TYPE_FLEXIO_TABLE);
        $this->getOutput()->push($outstream);

        $structure = [];
        foreach ($columns as $col)
        {
            /*
            col.name = (*it)["name"];
            col.type = xd::stringToDbtype((*it)["type"]);
            col.width = (*it)["width"].getInteger();
            col.scale = (*it)["scale"].getInteger();
            col.source_offset = (*it)["source_offset"].getInteger();
            col.source_width = (*it)["source_width"].getInteger();
            */

            $c = array(
                'name' =>          $col['name'],
                'type' =>          $col['type']
            );

            switch ($col['type'])
            {
                case 'text':
                    $c['type'] = 'character';
                case 'widecharacter':
                case 'character':
                    $c['width'] = isset_or($col['width'], null);
                case 'numeric':
                    $c['numeric'] = isset_or($col['width'], null);
                    $c['scale'] = isset_or($col['scale'], null);
                case 'date':
                    break;
                case 'datetime':
                    break;
                case 'boolean':
                    break;
                default:
                    // unknown type
                    break;
            }

            $structure[] = $c;
        }

        $outstream->setStructure($structure);
        $streamwriter = \Flexio\Object\StreamWriter::create($outstream);
        if ($streamwriter === false)
            return $this->fail(\Model::ERROR_CREATE_FAILED, _(''), __FILE__, __LINE__);


        $bufsize = 65536;

        $buf = '';
        while (true)
        {
            // read in a chunk, append it to any existing buffer
            $readbuf = $streamreader->readRow();
            if ($readbuf === false)
                break;

            $readlen = strlen($readbuf);
            $buf .= $readbuf;

            $buf_offset = 0;
            while (true)
            {
                if ($buf_offset + $row_width >= strlen($buf))
                    break;

                $row = [];
                $col_offset = 0;
                foreach ($columns as $col)
                {
                    $value = trim(substr($buf, $buf_offset + $col['source_offset'], $col['source_width']));

                    // handle ebcdic encoding
                    if (isset_or($col['source_encoding'],'') == 'ebcdic')
                    {
                        $ebcdicvalue = $value;
                        $len = strlen($ebcdicvalue);
                        $value = '';
                        for ($i = 0; $i < $len; ++$i)
                        {
                            $chn = ord($ebcdicvalue[$i]);
                            if ($chn >= 0 && $chn <= 255)
                            {
                                $chn = self::$ebcdicToAscii[$chn];
                                if ($chn > 0 && $chn < 127)
                                    $value .= chr($chn);
                            }
                        }
                    }

                    switch ($col['type'])
                    {
                        case 'numeric':
                            if (is_numeric($value))
                                $value = $value + 0;
                                 else
                                $value = null;
                                break;
                        case 'date':
                            $value = date_parse($value);
                            if ($value === false)
                                $value = null;
                                 else
                                $value = sprintf('%04d-%02d-%02d', $value['year'], $value['month'], $value['day']);
                            break;
                        case 'datetime':
                            $value = date_parse($value);
                            if ($value === false)
                                $value = null;
                                 else
                                $value = sprintf('%04d-%02d-%02d %02d:%02d:%02d', $value['year'], $value['month'], $value['day'],
                                                    ($value['hour'] !== false ? $value['hour'] : 0),
                                                    ($value['minute'] !== false ? $value['minute'] : 0),
                                                    ($value['second'] !== false ? $value['second'] : 0));
                            break;
                        case 'boolean':
                            $value = strtolower($value);
                            if ($value == '1' || $value == 'true' || $value == 't' || $value == 'on' || $value == 'x')
                                $value = true;
                                 else
                                $value = false;
                            break;
                    }


                    $row[] = $value;
                }

                $result = $streamwriter->write($row);
                if ($result === false)
                    $this->fail(\Model::ERROR_WRITE_FAILED, _(''), __FILE__, __LINE__);

                $buf_offset += $row_width;
            }

            if ($readlen != $bufsize)
                break; // all done

            $buf = substr($buf, $buf_offset);
        }

        $outstream->close();
    }

    private static function alterStructure($outstream, $structure)
    {
        $service = $outstream->getService();

        $sql = 'ALTER TABLE '. $outstream->getPath() . ' ';

        $cnt = 0;
        foreach ($structure as $fld)
        {
            $name = $fld['store_name']; // use the internal fieldname

            $detected_type = isset($fld['detected_type']) ? $fld['detected_type'] : '';

            if ($detected_type == 'D1' || $detected_type == 'D2' || $detected_type == 'D3')
            {
                $cast = \ExprUtil::getCastExpression($name, $fld['type'], 'date');
                $cast = \ExprTranslatorPostgres::translate($cast, $structure);

                if ($cast !== false)
                {
                    $sql .= ($cnt>0?',':'') . 'ALTER COLUMN ' . $name . ' TYPE date USING ' . $cast;
                    ++$cnt;
                }
            }

            if ($detected_type == 'N')
            {
                $width = $fld['max_width'];
                $scale = $fld['max_scale'];
                $type = 'numeric';
                if (!is_null($width))
                {
                    $type .= "($width";
                    if (!is_null($scale))
                        $type .= ",$scale)";
                            else
                        $type .= ')';
                }

                $cast = \ExprUtil::getCastExpression($name, $fld['type'], 'numeric', $width, $scale);
                $cast = \ExprTranslatorPostgres::translate($cast, $structure);

                if ($cast !== false)
                {
                    $sql .= ($cnt>0?',':'') . 'ALTER COLUMN ' . $name . ' TYPE '.$type.' USING ' . $cast;
                    ++$cnt;
                }
            }
        }

        if ($cnt > 0)
        {
            if ($service->exec($sql) === false)
                return false;
        }

        return true;
    }

    private static function conformValuesToStructure($structure, $row)
    {
        $result_row = array();

        $idx = 0;
        foreach ($row as &$val)
        {
            if ($idx >= count($structure))
                break;

            $type = $structure[$idx]['type'];

            if ($type == 'boolean')
            {
                if ($val == 'null' || $val == "\\N" || $val == '')
                {
                    $val = null;
                }
                else
                {
                    $ch = strtoupper($val[0]);
                    if ($ch == 'T' || $ch == 'Y' || $ch == '1')
                        $val = true;
                            else
                        $val = false;
                }
            }
            else if ($type == 'date' || $type == 'datetime' || $type == 'numeric')
            {
                if ($val == 'null' || $val == "\\N" || $val == '')
                    $val = null;
            }
            else
            {
                if ($val == "\\N")
                    $val = null;
            }

            $result_row[strtolower($structure[$idx]['name'])] = $val;

            ++$idx;
        }

        return $result_row;
    }

    private static function updateStructureFromRow(&$structure, $row)
    {
        $idx = 0;
        foreach ($row as $key => $val)
        {
            if ($idx >= count($structure))
                break;

            if (!isset($structure[$idx]['max_width']))
            {
                $structure[$idx]['max_width'] = 1;
                $structure[$idx]['max_scale'] = 0;
                $structure[$idx]['detected_type'] = 'D1'; // every field starts out as a YYYYMMDD
                $structure[$idx]['fallback_type'] = 'N';  // fallback type after date
            }

            $val = (string)$val;
            $len = strlen($val);

            if ($len > $structure[$idx]['max_width'])
                $structure[$idx]['max_width'] = $len;


            if ($structure[$idx]['detected_type'] == 'D1' && $len > 0)
            {
                if (preg_match('~^[0-9]{4}[-./ ][0-9]{2}[-./ ][0-9]{2}~', $val))
                {
                    // we matched something with date separators, so if we need to fall back
                    // to a different type later, it should be character
                    $structure[$idx]['fallback_type'] = 'C';
                }
                 else
                {
                    // not a YYYY-MM-DD, fall back to MM-DD-YYYY
                    $structure[$idx]['detected_type'] = 'D2';
                }
            }

            if ($structure[$idx]['detected_type'] == 'D2' && $len > 0)
            {
                if (preg_match('~^[0-9]{2}[-./ ][0-9]{2}[-./ ][0-9]{4}~', $val))
                {
                    $structure[$idx]['fallback_type'] = 'C';
                }
                 else
                {
                    // not a MM-DD-YYYY, fall back to YYYYMMDD
                    $structure[$idx]['detected_type'] = 'D3';
                }
            }

            if ($structure[$idx]['detected_type'] == 'D3' && $len > 0)
            {
                if (!(ctype_digit($val) && $len == 8 && (int)$val >= 17530101 && (int)$val <= 22000101))
                {
                    // not a YYYYMMDD, fall back to numeric
                    $structure[$idx]['detected_type'] = $structure[$idx]['fallback_type'];
                }
            }


            if ($structure[$idx]['detected_type'] == 'N' && $len > 0)
            {
                $v = str_replace(['$','%'], '', $val);
                if (is_numeric($v))
                {
                    // count number of decimal places
                    $pos = strpos($v,'.');
                    if ($pos !== false)
                    {
                        $scale = strlen(substr($v, $pos+1));
                        if ($len > $structure[$idx]['max_scale'])
                            $structure[$idx]['max_scale'] = $scale;
                    }
                }
                 else
                {
                    $structure[$idx]['detected_type'] = 'C';
                    $structure[$idx]['reason'] = $v;
                }
            }

            ++$idx;
        }
    }

    private static function structureFromIcsv($row)
    {
        $structure = [];
        foreach ($row as $fld)
        {
            $matches = [];
            if (!preg_match('/^(.*)[(]([A-Z][ 0-9]*)[)]/', $fld, $matches))
                return null;

            $name = $matches[1];
            $parts = explode(' ', $matches[2]);

            $width = 80;
            $scale = 0;

            switch (isset_or($parts[0],''))
            {
                default:
                case 'C':   $type = 'character'; $width = isset_or($parts[1],80); break;
                case 'N':   $type = 'numeric';   $width = isset_or($parts[1],12); $scale = isset_or($parts[2],0); break;
                case 'D':   $type = 'date';      $width = isset_or($parts[1],8);  break;
                case 'T':   $type = 'datetime';  $width = isset_or($parts[1],16); break;
                case 'B':   $type = 'boolean';   $width = isset_or($parts[1],1);  break;
            }


            $structure[] = array(
                'name' =>          $name,
                'type' =>          $type,
                'width' =>         (int)$width,
                'scale' =>         (int)$scale
            );
        }

        return $structure;
    }

    private static function determineStructureFromRow($row, $header)
    {
        // if the first row is a header row, turn it into field names
        if ($header === true)
        {
            $structure = [];
            foreach ($row as $fld)
            {
                $structure[] = array(
                    'name' =>          $fld,
                    'type' =>         'text',
                    'width' =>         null,
                    'scale' =>         0
                );
            }
        }
        else
        {
            $structure = [];
            $cnt = 0;
            foreach ($row as $fld)
            {
                ++$cnt;
                $fld = 'field'.$cnt;

                $structure[] = array(
                    'name' =>          $fld,
                    'type' =>          'text',
                    'width' =>         null,
                    'scale' =>         0
                );
            }
        }

        return $structure;
    }

    private static function getInputMimeTypeFromDefinition($job_definition)
    {
        if (!isset($job_definition['params']['input']['format']))
            return false;
        $format = $job_definition['params']['input']['format'];

        if ($format == self::FORMAT_DELIMITED_TEXT || $format == 'delimited_text' /* compatibility */)
            return \ContentType::MIME_TYPE_CSV;
        else if ($format == self::FORMAT_FIXED_LENGTH || $format == 'fixed_length_text' /* compatibility */)
            return \ContentType::MIME_TYPE_TXT;
        else if ($format == self::FORMAT_JSON)
            return \ContentType::MIME_TYPE_JSON;
        else if ($format == self::FORMAT_PDF)
            return \ContentType::MIME_TYPE_PDF;
        else if ($format == self::FORMAT_TABLE)
            return \ContentType::MIME_TYPE_FLEXIO_TABLE;
        else
            return false;
    }

    private static function getOutputMimeTypeFromDefinition($job_definition)
    {
        if (!isset($job_definition['params']['output']['format']))
            return false;
        $format = $job_definition['params']['output']['format'];

        if ($format == self::FORMAT_DELIMITED_TEXT)
            return \ContentType::MIME_TYPE_CSV;
        else if ($format == self::FORMAT_FIXED_LENGTH)
            return \ContentType::MIME_TYPE_TXT;
        else if ($format == self::FORMAT_JSON)
            return \ContentType::MIME_TYPE_JSON;
        else if ($format == self::FORMAT_PDF)
            return \ContentType::MIME_TYPE_PDF;
        else if ($format == self::FORMAT_TABLE)
            return \ContentType::MIME_TYPE_FLEXIO_TABLE;
        else
            return false;
    }

    private static $ebcdicToAscii  = [
        /* 0   1   2   3   4   5   6   7   8   9   A   B   C   D   E   F */
           0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  /* 0x */
           0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  /* 1x */
           0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  /* 2x */
           0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  /* 3x */
           0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  /* 4x */
           0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  /* 5x */
           0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  0, 95,  0,  0,  /* 6x */
           0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  /* 7x */
           0, 97, 98, 99,100,101,102,103,104,105,  0,  0,  0,  0,  0,  0,  /* 8x */
           0,106,107,108,109,110,111,112,113,114,  0,  0,  0,  0,  0,  0,  /* 9x */
           0,  0,115,116,117,118,119,120,121,122,  0,  0,  0,  0,  0,  0,  /* Ax */
           0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  /* Bx */
           0, 97, 98, 99,100,101,102,103,104,105,  0,  0,  0,  0,  0,  0,  /* Cx */
           0,106,107,108,109,110,111,112,113,114,  0,  0,  0,  0,  0,  0,  /* Dx */
           0,  0,115,116,117,118,119,120,121,122,  0,  0,  0,  0,  0,  0,  /* Ex */
           0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  /* Fx */
        ];

    // job definition info
    const MIME_TYPE = 'flexio.convert';
    const TEMPLATE = <<<EOD
    {
        "type": "flexio.convert",
        "params": {
            "input": {
                "format": "delimited_text",
                "delimiter": "{comma}",
                "header": true,
                "qualifier": "{none}"
            },
            "output": {
                "format": ""
            }
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
                "enum": ["flexio.convert"]
            },
            "params": {
                "type": "object"
            }
        }
    }
EOD;
}

