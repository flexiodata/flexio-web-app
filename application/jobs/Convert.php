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


declare(strict_types=1);
namespace Flexio\Jobs;

/*
// DESCRIPTION:
{
    "op": "convert",      // string, required
    "input": {            // object, required
        "format": "",     // string, required
        "delimiter": "",  // string
        "header": true,   // boolean
        "qualifier": ""   // string
    },
    "output": {           // object, required
        "format": ""      // string, required
    }
}

// VALIDATOR:
$validator = \Flexio\Base\Validator::create();
if (($validator->check($params, array(
        'op'         => array('type' => 'string',     'required' => true)
    ))->hasErrors()) === true)
    throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);
*/

class Convert extends \Flexio\Jobs\Base
{
    // token delimiters; other types of delimiters are allowed as well
    public const DELIMITER_NONE              = '{none}';
    public const DELIMITER_COMMA             = '{comma}';
    public const DELIMITER_SEMICOLON         = '{semicolon}';
    public const DELIMITER_PIPE              = '{pipe}';
    public const DELIMITER_TAB               = '{tab}';
    public const DELIMITER_SPACE             = '{space}';

    // token text qualifiers
    public const TEXT_QUALIFIER_NONE         = '{none}';
    public const TEXT_QUALIFIER_SINGLE_QUOTE = '{single-quote}';
    public const TEXT_QUALIFIER_DOUBLE_QUOTE = '{double-quote}';

    // conversion formats
    public const FORMAT_DELIMITED_TEXT = 'delimited';
    public const FORMAT_FIXED_LENGTH   = 'fixed';
    public const FORMAT_JSON           = 'json';
    public const FORMAT_RSS            = 'rss';
    public const FORMAT_PDF            = 'pdf';
    public const FORMAT_TABLE          = 'table';

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
        // if a job format is specified, get the mime type from the job definition
        $job_params = $this->getJobParameters();

        // cast simple short-hand input/output strings to full format structure
        $input_string = (isset($job_params['input']) && is_string($job_params['input'])) ? $job_params['input'] : null;
        if ($input_string !== null)
        {
            if ($input_string === 'csv' || $input_string === 'tsv')
            {
                $job_params['input'] = [
                    'format' => 'delimited',
                    'delimiter' => ($input_string === 'csv' ? '{comma}' : '{tab}'),
                    'header' => true,
                    'qualifier' => '{double-quote}'
                ];
            }
             else
            {
                $job_params['input'] = [ 'format' => $input_string ];
            }
        }

        $output_string = (isset($job_params['output']) && is_string($job_params['output'])) ? $job_params['output'] : null;
        if ($output_string !== null)
        {
            if ($output_string === 'csv' || $output_string === 'tsv')
            {
                $job_params['output'] = [
                    'format' => 'delimited',
                    'delimiter' => ($output_string === 'csv' ? '{comma}' : '{tab}'),
                    'header' => true,
                    'qualifier' => '{double-quote}'
                ];
            }
             else
            {
                $job_params['output'] = [ 'format' => $output_string ];
            }
        }




        $input_content_type_from_definition = self::getInputMimeTypeFromDefinition($job_params);
        $output_content_type_from_definition = self::getOutputMimeTypeFromDefinition($job_params);

        // default to convert to table
        if ($output_content_type_from_definition === false)
            $output_content_type_from_definition = \Flexio\Base\ContentType::FLEXIO_TABLE;


        // get the mime type for the input; use the job format if it's
        // specified, as long as the input format isn't a flexio table
        $instream_mime_type = $instream->getMimeType();
        if ($instream_mime_type != \Flexio\Base\ContentType::FLEXIO_TABLE)
        {
            if ($input_content_type_from_definition === false)
                $instream_mime_type = $instream->getMimeType();
                    else
                $instream_mime_type = $input_content_type_from_definition;
        }


        switch ($instream_mime_type)
        {
            // unhandled input
            default:
                $outstream->copyFrom($instream);
                return;

            // table input
            case \Flexio\Base\ContentType::FLEXIO_TABLE:
                $this->createOutputFromTableInput($instream, $outstream, $output_content_type_from_definition);
                return;

            // rss input
            case \Flexio\Base\ContentType::RSS:
                $this->createOutputFromRssAtom($instream, $outstream, $output_content_type_from_definition);
                return;

            // json input
            case \Flexio\Base\ContentType::JSON:
                $this->createOutputFromJsonInput($instream, $outstream, $output_content_type_from_definition);
                return;

            // csv input; also handle raw stream content with csv handler
            case \Flexio\Base\ContentType::STREAM:
            case \Flexio\Base\ContentType::CSV:
                $this->createOutputFromCsvInput($instream, $outstream, $output_content_type_from_definition);
                return;

            case 'spreadsheet':
            case \Flexio\Base\ContentType::ODS:
            case \Flexio\Base\ContentType::XLS:
            case \Flexio\Base\ContentType::XLSX:
                $this->createOutputFromSpreadsheetInput($instream, $outstream, $output_content_type_from_definition);
                return;
            
            // text input
            case \Flexio\Base\ContentType::TEXT:
                $this->createOutputFromFixedLengthInput($instream, $outstream, $output_content_type_from_definition);
                return;

            // text input
            case \Flexio\Base\ContentType::PDF:
                $this->createOutputFromPdfInput($instream, $outstream, $output_content_type_from_definition);
                return;
        }
    }

    private function createOutputFromTableInput(\Flexio\IFace\IStream &$instream, \Flexio\IFace\IStream &$outstream, string $output_mime_type) : void
    {
        $instream_mime_type = $instream->getMimeType();
        if ($instream_mime_type != \Flexio\Base\ContentType::FLEXIO_TABLE)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_FORMAT, "Input file must be a table");
        }

        $job_params = $this->getJobParameters();

        $outstream->setMimeType($output_mime_type);

        $streamreader = $instream->getReader();
        $streamwriter = $outstream->getWriter();

        if ($output_mime_type == \Flexio\Base\ContentType::JSON)
        {
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
         else if ($output_mime_type == \Flexio\Base\ContentType::XLSX || $output_mime_type == \Flexio\Base\ContentType::XLS || $output_mime_type == \Flexio\Base\ContentType::ODS)
        {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $worksheet = $spreadsheet->getActiveSheet();

            $streamreader = $instream->getReader();
            $rows = [];
            while (($row = $streamreader->readRow()) !== false)
            {
                if ($row === false)
                    break;
                $rows[] = array_values($row);
            }


            $header = toBoolean($job_params['output']['header'] ?? true);
            if ($header && count($rows) > 0)
            {
                $structure = $instream->getStructure()->enum();
                $header_row = array_column($structure, 'name');
                array_unshift($rows, $header_row);
            }

            $worksheet->fromArray($rows);




            $storage_tmpbase = $GLOBALS['g_config']->storage_root . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR;
            $spreadsheet_fname = $storage_tmpbase . "tmpspreadsheet-" . \Flexio\Base\Util::generateRandomString(30);
            switch ($output_mime_type)
            {
                default:
                case \Flexio\Base\ContentType::XLSX: $spreadsheet_fname .= '.xlsx'; break;
                case \Flexio\Base\ContentType::XLS:  $spreadsheet_fname .= '.xls'; break;
                case \Flexio\Base\ContentType::ODS:  $spreadsheet_fname .= '.ods'; break;
            }

            register_shutdown_function('unlink', $spreadsheet_fname);

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save($spreadsheet_fname);
            $writer = null;
            $spreadsheet = null;

            $contents = file_get_contents($spreadsheet_fname);
            $streamwriter->write($contents);

            // input/output
            $outstream->set([
                'mime_type' => $output_mime_type,
                'size' => strlen($contents)
            ]);
        }
         else
        {
            $delimiter = $job_params['output']['delimiter'] ?? self::DELIMITER_COMMA;
            $header = toBoolean($job_params['output']['header'] ?? true);
            $qualifier = $job_params['output']['qualifier'] ?? self::TEXT_QUALIFIER_DOUBLE_QUOTE;
            $encoding = $job_params['output']['encoding'] ?? 'UTF-8';  // by default, use UTF-8 output

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

            $fp = fopen('php://temp', 'w');

            if ($header)
            {
                $structure = $instream->getStructure()->enum();
                $header_row = array_column($structure, 'name');
                fputcsv($fp, $header_row, $delimiter, $qualifier);
            }

            $rown = 0;

            while (true)
            {
                $row = $streamreader->readRow();

                if (ftell($fp) > 100000 || $row === false)
                {
                    fseek($fp, 0);
                    $contents = stream_get_contents($fp);
                    fclose($fp);
                    $streamwriter->write($contents);

                    if ($row === false)
                        break;
                         else
                        $fp = fopen('php://temp', 'w');
                }

                fputcsv($fp, $row, $delimiter, $qualifier);

                ++$rown;
            }

            $streamwriter->close();
            $outstream->setSize($streamwriter->getBytesWritten());
        }
    }

    private function createOutputFromRssAtom(\Flexio\IFace\IStream &$instream, \Flexio\IFace\IStream &$outstream, string $output_mime_type) : void
    {
        $outstream->set($instream->get());
        $outstream->setPath(\Flexio\Base\Util::generateHandle());
        $outstream->setMimeType(\Flexio\Base\ContentType::FLEXIO_TABLE);

        $structure = [
            [ 'name' => 'link', 'type' => 'text' ],
            [ 'name' => 'title', 'type' => 'text' ],
            [ 'name' => 'description', 'type' => 'text' ],
            [ 'name' => 'content', 'type' => 'text' ],
            [ 'name' => 'source', 'type' => 'text' ],
            [ 'name' => 'author', 'type' => 'text' ],
            [ 'name' => 'date', 'type' => 'text' ]
        ];

        $outstream->setStructure($structure);
        $streamreader = $instream->getReader();
        $streamwriter = $outstream->getWriter();


        $rss_payload = '';
        while (($piece = $streamreader->read(16384)) !== false)
        {
            $rss_payload .= $piece;
        }


        $rss = new \Flexio\Services\Rss;
        $rss->read(array('data'=> $rss_payload), function ($row) use (&$streamwriter) {
            $result = $streamwriter->write($row);
        });
    }

    private function createOutputFromPdfInput(\Flexio\IFace\IStream &$instream, \Flexio\IFace\IStream &$outstream, string $output_mime_type) : void
    {
        if (!isset($GLOBALS['pdfparser_included']))
        {
            $GLOBALS['pdfparser_included'] = true;

            spl_autoload_register(function ($class) {
                $class = ltrim($class, '\\');
                if (strpos($class, 'Smalot\\') === 0)
                {
                    $sl = strpos($class,'\\', 1);
                    $class = str_replace('\\', '/', $class);
                    $class = dirname(dirname(__DIR__)) . '/library/pdfparser-0.9.25/src/Smalot/' . substr($class,$sl+1) . '.php';
                    if (file_exists($class))
                    {
                        require_once $class;
                        return true;
                    }
                    return false;
                }
            });

        }

        require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'library'. DIRECTORY_SEPARATOR .'tcpdf-6.2.12'. DIRECTORY_SEPARATOR . 'tcpdf_parser.php';


        // input/output
        $outstream->set($instream->get());
        $outstream->setPath(\Flexio\Base\Util::generateHandle());
        $outstream->setMimeType(\Flexio\Base\ContentType::TEXT);

        $streamwriter = $outstream->getWriter();

        // read the pdf into a buffer
        $buffer = '';
        $streamreader = $instream->getReader();
        while (true)
        {
            $data = $streamreader->read();
            if ($data === false)
                break;

            $buffer .= $data;
        }

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
        catch (\Exception $e)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);
        }
        catch (\Error $e)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);
        }

        $streamwriter->close();
        $outstream->setSize($streamwriter->getBytesWritten());
    }

    private function createOutputFromJsonInput(\Flexio\IFace\IStream &$instream, \Flexio\IFace\IStream &$outstream, string $output_mime_type) : void
    {
        // read the json into a buffer
        $buffer = '';
        $streamreader = $instream->getReader();
        while (true)
        {
            $data = $streamreader->read();
            if ($data === false)
                break;

            $buffer .= $data;
        }

        // flatten the json
        $items = \Flexio\Base\Mapper::flatten($buffer);


        if ($output_mime_type == \Flexio\Base\ContentType::XLSX || $output_mime_type == \Flexio\Base\ContentType::XLS || $output_mime_type == \Flexio\Base\ContentType::ODS)
        {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $worksheet = $spreadsheet->getActiveSheet();


            $structure = self::determineStructureFromJsonArray($items);
            if ($structure === false)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);

            foreach ($items as &$row)
                $row = array_values($row);
            unset($row);

            $job_params = $this->getJobParameters();
            $header = toBoolean($job_params['output']['header'] ?? true);
            if ($header && count($items) > 0)
            {
                $header_row = array_column($structure, 'name');
                array_unshift($items, $header_row);
            }

            $worksheet->fromArray($items);

            $storage_tmpbase = $GLOBALS['g_config']->storage_root . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR;
            $spreadsheet_fname = $storage_tmpbase . "tmpspreadsheet-" . \Flexio\Base\Util::generateRandomString(30);
            switch ($output_mime_type)
            {
                default:
                case \Flexio\Base\ContentType::XLSX: $spreadsheet_fname .= '.xlsx'; break;
                case \Flexio\Base\ContentType::XLS:  $spreadsheet_fname .= '.xls'; break;
                case \Flexio\Base\ContentType::ODS:  $spreadsheet_fname .= '.ods'; break;
            }

            register_shutdown_function('unlink', $spreadsheet_fname);

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save($spreadsheet_fname);
            $writer = null;
            $spreadsheet = null;

            $contents = file_get_contents($spreadsheet_fname);
            
            $streamwriter = $outstream->getWriter();
            $streamwriter->write($contents);

            // input/output
            $outstream->set([
                'mime_type' => $output_mime_type,
                'size' => strlen($contents)
            ]);
        }
         else
        {
            // set the output structure and write the rows
            $structure = self::determineStructureFromJsonArray($items);
            if ($structure === false)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);

            // input/output
            $outstream->set([
                'mime_type' => \Flexio\Base\ContentType::FLEXIO_TABLE,
                'structure' => $structure
            ]);

            $streamwriter = $outstream->getWriter();

            foreach($items as $i)
            {
                $streamwriter->write($i);
            }

            $streamwriter->close();
        }


    }

    private function createOutputFromCsvInput(\Flexio\IFace\IStream &$instream, \Flexio\IFace\IStream &$outstream, string $output_mime_type) : void
    {
        // parameters
        $job_params = $this->getJobParameters();
        $streamwriter = null;

        $delimiter = $job_params['input']['delimiter'] ?? self::DELIMITER_COMMA;
        $is_output_json = ($output_mime_type == \Flexio\Base\ContentType::JSON ? true : false);

        $header = toBoolean($job_params['input']['header'] ?? true);
        $qualifier = $job_params['input']['qualifier'] ?? self::TEXT_QUALIFIER_DOUBLE_QUOTE;
        $encoding = $job_params['input']['encoding'] ?? '';

        if (strtolower($encoding) == 'ascii')
            $encoding = 'ISO-8859-1'; // use this as it is a superset of ASCII

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
        $streamreader = $instream->getReader();
        $outstream->set($instream->get());
        $outstream->setPath(\Flexio\Base\Util::generateHandle());
        $outstream->setMimeType($is_output_json ? \Flexio\Base\ContentType::JSON : \Flexio\Base\ContentType::FLEXIO_TABLE);

        if ($is_output_json)
        {
            // for json output, streamwriter is created here; for table output, streamwriter
            // is created below, because header row must be collected in advance
            $streamwriter = $outstream->getWriter();
            $streamwriter->write("[");
        }

        // parse each row into a table
        $first_row = true;
        $determine_structure = true;
        $is_icsv = false;
        $rown = 0;

        $buf = '';
        $buf_head = 0;

        $structure = array();
        $output_structure = array();
        $eof_reached = false;
        $first = true;


        while (true)
        {
            $row = false;

            while (true)
            {
                $lineend_pos = self::indexOfLineTerminator($buf, $qualifier, $buf_head);
                if ($lineend_pos !== false)
                {
                    $row = substr($buf, $buf_head, $lineend_pos - $buf_head + 1);

                    if ($buf[$lineend_pos] == "\r")
                    {
                        // if next char is a \n, skip past it, because it's a \r\n line ending

                        if ($lineend_pos == strlen($buf)-1)
                        {
                            // need more data to determine if next char is \n
                            $chunk = $streamreader->read(8192);
                            if ($chunk !== false)
                                $buf .= $chunk;
                        }

                        if ($lineend_pos+1 < strlen($buf) && $buf[$lineend_pos+1] == "\n")
                            $lineend_pos++;
                    }

                    $buf_head = $lineend_pos + 1;

                    break;
                }
                 else
                {
                    if ($buf_head > 0 && $buf_head < strlen($buf))
                    {
                        $buf = substr($buf, $buf_head);
                        $buf_head = 0;
                    }

                    if ($eof_reached)
                    {
                        // get any last row without LF
                        $row = substr($buf, $buf_head);
                        $buf_head += strlen($row);
                        if (strlen($row) == 0)
                            $row = false;
                        break;
                    }
                     else
                    {
                        $chunk = $streamreader->read(16384);

                        if ($chunk !== false)
                        {
                            if ($first)
                            {
                                $first = false;

                                // look for utf-8 byte-order mark
                                if (substr($chunk, 0, 3) == pack("CCC", 0xef, 0xbb, 0xbf))
                                {
                                    $chunk = substr($chunk, 3);
                                    if ($encoding == '')
                                        $encoding = 'UTF-8';
                                }

                                if ($encoding == '')
                                {
                                    $encoding = mb_detect_encoding($chunk,'UTF-8,ISO-8859-1');
                                    if ($encoding === false)
                                    {
                                        // encoding could not be detected, default to UTF-8
                                        $encoding = 'UTF-8';
                                    }
                                }
                            }

                            $buf .= $chunk;
                        }
                         else
                        {
                            $eof_reached = true;
                        }
                    }
                }
            }

            if ($row === false)
                break; // reached EOF

            if ($encoding != 'UTF-8')
            {
                $row = iconv($encoding, 'UTF-8', $row);
                if ($row === false)
                    throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER, "Conversion from $encoding to UTF-8 failed");
            }

            // parse the row
            if ($use_text_qualifier)
            {
                $row = str_getcsv($row, $delimiter, $qualifier);
            }
             else
            {
                $row = trim($row,"\r\n");
                if ($first_row && $row == '')
                    continue;
                $row = explode($delimiter, $row);
            }

            if ($row === false)
                break;
            if ($first_row && $row === array(null)) // skip blank rows
                continue;


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
                    $streamwriter = $outstream->getWriter();
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
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);

            ++$rown;
        }

        // make sure the buffer is flushed before converting values
        if (isset($streamwriter))
        {
            if ($is_output_json)
            {
                $streamwriter->write("\n]");
            }

            $result = $streamwriter->close();

            if ($result === false)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);

            $outstream->setSize($streamwriter->getBytesWritten());
        }

        // change the structure if the flag is set
/*
        if ($determine_structure)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
        }
*/

        if (!$is_output_json)
        {
            $output_properties = array(
                'mime_type' => \Flexio\Base\ContentType::FLEXIO_TABLE,
                'structure' => $structure
            );

            $outstream->set($output_properties);
        }
    }



    private function createOutputFromSpreadsheetInput(\Flexio\IFace\IStream &$instream, \Flexio\IFace\IStream &$outstream, string $output_mime_type) : void
    {
        $reader = $instream->getReader();

        $storage_tmpbase = $GLOBALS['g_config']->storage_root . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR;
        $spreadsheet_fname = $storage_tmpbase . "tmpspreadsheet-" . \Flexio\Base\Util::generateRandomString(30);
        register_shutdown_function('unlink', $spreadsheet_fname);

        $f = fopen($spreadsheet_fname, 'wb');
        while (($piece = $reader->read(16384)) !== false)
        {
            fwrite($f, $piece);
        }
        fclose($f);

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($spreadsheet_fname);

        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();


        if ($output_mime_type == \Flexio\Base\ContentType::JSON)
        {
            $rown = 0;

            // transfer the data
            $streamwriter = $outstream->getWriter();
            $streamwriter->write(json_encode($rows));
            $streamwriter->close();

            // input/output
            $outstream->set([
                'mime_type' => \Flexio\Base\ContentType::JSON,
                'size' => $streamwriter->getBytesWritten()
            ]);
        }
         else
        {
            // set the output structure and write the rows
            $structure = self::determineStructureFromJsonArray($rows);
            if ($structure === false)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);
            
            // input/output
            $outstream->set([
                'mime_type' => \Flexio\Base\ContentType::FLEXIO_TABLE,
                'structure' => $structure
            ]);

            $streamwriter = $outstream->getWriter();

            foreach($rows as $row)
            {
                $streamwriter->write($row);
            }

            $streamwriter->close();
        }
    }



    private function createOutputFromFixedLengthInput(\Flexio\IFace\IStream &$instream, \Flexio\IFace\IStream &$outstream) : void
    {
        // parameters
        $job_params = $this->getJobParameters();
        $start_offset = $job_params['start_offset'] ?? 0;
        $row_width = $job_params['row_width'] ?? 100;
        $line_delimiter = $job_params['line_delimiter'] ?? false;
        $columns = $job_params['columns'] ?? [];

        if ($row_width == 0)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        if (count($columns) == 0)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        // get the input
        $streamreader = $instream->getReader();
        $outstream->set($instream->get());
        $outstream->setPath(\Flexio\Base\Util::generateHandle());
        $outstream->setMimeType(\Flexio\Base\ContentType::FLEXIO_TABLE);

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
                    $c['width'] = $col['width'] ?? null;
                case 'numeric':
                    $c['numeric'] = $col['width'] ?? null;
                    $c['scale'] = $col['scale'] ?? null;
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
        $streamwriter = $outstream->getWriter();

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
                    if (($col['source_encoding'] ?? '') == 'ebcdic')
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
                    throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);

                $buf_offset += $row_width;
            }

            if ($readlen != $bufsize)
                break; // all done

            $buf = substr($buf, $buf_offset);
        }

        $outstream->close();
    }

    private static function indexOfLineTerminator(string $haystack, string $qualifier, int $start = 0) // TODO: add return type
    {
        if (empty($qualifier))
        {
            $off1 = strpos($haystack, "\r", $start);
            $off2 = strpos($haystack, "\n", $start);
            if ($off1 === false)
                return $off2;
            if ($off2 === false)
                return $off1;
            return min($off1, $off2);
        }

        $haystack_len = strlen($haystack);
        $offset = $start;
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
                    if ($ch == "\r" || $ch == "\n")
                        return $offset;
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

    private static function conformValuesToStructure(array $structure, array $row) : array
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

    private static function updateStructureFromRow(array &$structure, array $row) : void
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

    private static function structureFromIcsv(array $row) : ?array
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

            switch ($parts[0] ?? '')
            {
                default:
                case 'C':   $type = 'character'; $width = $parts[1] ?? 80; break;
                case 'N':   $type = 'numeric';   $width = $parts[1] ?? 12; $scale = $parts[2] ?? 0; break;
                case 'D':   $type = 'date';      $width = $parts[1] ??  8; break;
                case 'T':   $type = 'datetime';  $width = $parts[1] ?? 16; break;
                case 'B':   $type = 'boolean';   $width = $parts[1] ??  1; break;
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

    private static function determineStructureFromJsonArray(array $items) // TODO: add return type
    {
        // create the fields based off the first row
        if (count($items) === 0)
            return false;

        $structure = array();

        $idx = 0;

        $first_item = $items[0];
        foreach ($first_item as $key => $value)
        {
            $idx++;
            if (is_numeric($key))
                $name = 'field' . $idx;
                 else
                $name = $key;

            $structure[] = array(
                'name'  => $name,
                'type'  => 'text',
                'width' => null,
                'scale' => 0
            );
        }

        return $structure;
    }

    private static function determineStructureFromRow(array $row, bool $header) : array
    {
        // if the first row is a header row, turn it into field names
        if ($header === true)
        {
            $structure = [];
            $cnt = 0;
            foreach ($row as $fld)
            {
                ++$cnt;
                if (strlen(trim($fld)) == 0)
                {
                    $fld = 'field'.$cnt;
                }

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

    private static function getInputMimeTypeFromDefinition(array $job_params) // TODO: add return type
    {
        $input = $job_params['input'] ?? '';
        $format = is_string($input) ? $input : ($job_params['input']['format'] ?? '');

        if ($format == self::FORMAT_DELIMITED_TEXT || $format == 'csv')
            return \Flexio\Base\ContentType::CSV;
        else if ($format == self::FORMAT_FIXED_LENGTH)
            return \Flexio\Base\ContentType::TEXT;
        else if ($format == self::FORMAT_JSON)
            return \Flexio\Base\ContentType::JSON;
        else if ($format == self::FORMAT_RSS)
            return \Flexio\Base\ContentType::RSS;
        else if ($format == self::FORMAT_PDF)
            return \Flexio\Base\ContentType::PDF;
        else if ($format == self::FORMAT_TABLE)
            return \Flexio\Base\ContentType::FLEXIO_TABLE;
        else if ($format == 'xls')
            return \Flexio\Base\ContentType::XLS;
        else if ($format == 'xlsx' || $format == 'excel')
            return \Flexio\Base\ContentType::XLSX;
        else if ($format == 'ods')
            return \Flexio\Base\ContentType::ODS;
        else
            return false;
    }

    private static function getOutputMimeTypeFromDefinition(array $job_params) // TODO: add return type
    {
        $output = $job_params['output'] ?? '';
        $format = is_string($output) ? $output : ($output['format'] ?? '');

        if ($format == self::FORMAT_DELIMITED_TEXT || $format == 'csv')
            return \Flexio\Base\ContentType::CSV;
        else if ($format == self::FORMAT_FIXED_LENGTH)
            return \Flexio\Base\ContentType::TEXT;
        else if ($format == self::FORMAT_JSON)
            return \Flexio\Base\ContentType::JSON;
        else if ($format == self::FORMAT_PDF)
            return \Flexio\Base\ContentType::PDF;
        else if ($format == self::FORMAT_TABLE)
            return \Flexio\Base\ContentType::FLEXIO_TABLE;
        else if ($format == 'xls')
            return \Flexio\Base\ContentType::XLS;
        else if ($format == 'xlsx' || $format == 'excel' || $format == 'spreadsheet')
            return \Flexio\Base\ContentType::XLSX;
        else if ($format == 'ods')
            return \Flexio\Base\ContentType::ODS;
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
}

