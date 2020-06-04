<?php
/**
 *
 * Copyright (c) 2015, Flex Research LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams; David Z. Williams; Aaron L. Williams
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
        'op'         => array('required' => true,  'enum' => ['convert'])
    ))->hasErrors()) === true)
    throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);
*/

class Convert implements \Flexio\IFace\IJob
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

    // conversion formats; TODO: there are various overlapping types,
    // so these should be better articulated/standardized
    public const FORMAT_DELIMITED_TEXT = 'delimited';
    public const FORMAT_FIXED_LENGTH   = 'fixed';
    public const FORMAT_JSON           = 'json';
    public const FORMAT_NDJSON         = 'ndjson';
    public const FORMAT_RSS            = 'rss';
    public const FORMAT_ATOM           = 'atom';
    public const FORMAT_PDF            = 'pdf';
    public const FORMAT_TABLE          = 'table';
    public const FORMAT_CSV            = 'csv';
    public const FORMAT_TSV            = 'tsv';
    public const FORMAT_XLS            = 'xls';
    public const FORMAT_XLSX           = 'xlsx';
    public const FORMAT_SPREADSHEET    = 'spreadsheet';
    public const FORMAT_EXCEL          = 'excel';
    public const FORMAT_ODS            = 'ods';

    private $properties = array();

    public static function validate(array $task) : array
    {
        $errors = array();
        return $errors;
    }

    public static function run(\Flexio\IFace\IProcess $process, array $task) : void
    {
        unset($task['op']);
        \Flexio\Jobs\Util::replaceParameterTokens($process, $task);

        $object = new static();
        $object->properties = $task;
        $object->run_internal($process);
    }

    private function run_internal(\Flexio\IFace\IProcess $process) : void
    {
        $instream = $process->getStdin();
        $outstream = $process->getStdout();
        $job_params = $this->getJobParameters();
        self::process($job_params, $instream, $outstream);
    }

    private function getJobParameters() : array
    {
        return $this->properties;
    }

    private static function process(array $convert_params, \Flexio\IFace\IStream &$instream, \Flexio\IFace\IStream &$outstream) : void
    {
        // cast simple short-hand input/output strings to full format structure
        $input_string = (isset($convert_params['input']) && is_string($convert_params['input'])) ? $convert_params['input'] : null;
        if ($input_string !== null)
        {
            if ($input_string === self::FORMAT_CSV || $input_string === self::FORMAT_TSV)
            {
                $convert_params['input'] = [
                    'format' => self::FORMAT_DELIMITED_TEXT,
                    'delimiter' => ($input_string === 'csv' ? self::DELIMITER_COMMA : self::DELIMITER_TAB),
                    'header' => true,
                    'qualifier' => self::TEXT_QUALIFIER_DOUBLE_QUOTE
                ];
            }
             else
            {
                $convert_params['input'] = [ 'format' => $input_string ];
            }
        }

        $output_string = (isset($convert_params['output']) && is_string($convert_params['output'])) ? $convert_params['output'] : null;
        if ($output_string !== null)
        {
            if ($output_string === self::FORMAT_CSV || $output_string === self::FORMAT_TSV)
            {
                $convert_params['output'] = [
                    'format' =>  self::FORMAT_DELIMITED_TEXT,
                    'delimiter' => ($output_string === 'csv' ? self::DELIMITER_COMMA : self::DELIMITER_TAB),
                    'header' => true,
                    'qualifier' => self::TEXT_QUALIFIER_DOUBLE_QUOTE
                ];
            }
             else
            {
                $convert_params['output'] = [ 'format' => $output_string ];
            }
        }

        // get the content type from the format
        $input = $convert_params['input'] ?? '';
        $input_format = is_string($input) ? $input : ($convert_params['input']['format'] ?? '');
        $input_content_type_from_definition = self::getContentTypeFromFormat($input_format);

        $output = $convert_params['output'] ?? '';
        $output_format = is_string($output) ? $output : ($convert_params['output']['format'] ?? '');
        $output_content_type_from_definition = self::getContentTypeFromFormat($output_format);

        // default to convert to table
        if (!isset($output_content_type_from_definition))
            $output_content_type_from_definition = \Flexio\Base\ContentType::FLEXIO_TABLE;

        // get the mime type for the input; use the job format if it's
        // specified, as long as the input format isn't a flexio table
        $instream_mime_type = $instream->getMimeType();
        if ($instream_mime_type != \Flexio\Base\ContentType::FLEXIO_TABLE)
        {
            if (!isset($input_content_type_from_definition))
                $instream_mime_type = $instream->getMimeType();
                    else
                $instream_mime_type = $input_content_type_from_definition;
        }


        switch ($instream_mime_type)
        {
            // unhandled input
            default:
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED, 'The input format is not supported');

            // table input
            case \Flexio\Base\ContentType::FLEXIO_TABLE:
                self::createOutputFromTableInput($convert_params, $instream, $outstream, $output_content_type_from_definition);
                return;

            // rss input
            case \Flexio\Base\ContentType::RSS:
            case \Flexio\Base\ContentType::ATOM:
                self::createOutputFromRssAtom($convert_params, $instream, $outstream, $output_content_type_from_definition);
                return;

            // json input
            case \Flexio\Base\ContentType::JSON:
                self::createOutputFromJsonInput($convert_params, $instream, $outstream, $output_content_type_from_definition);
                return;

            // csv input; also handle raw stream content with csv handler
            case \Flexio\Base\ContentType::STREAM:
            case \Flexio\Base\ContentType::CSV:
                self::createOutputFromCsvInput($convert_params, $instream, $outstream, $output_content_type_from_definition);
                return;

            case 'spreadsheet': // TODO: handle this case
            case \Flexio\Base\ContentType::ODS:
            case \Flexio\Base\ContentType::XLS:
            case \Flexio\Base\ContentType::XLSX:
                self::createOutputFromSpreadsheetInput($convert_params, $instream, $outstream, $output_content_type_from_definition);
                return;

            // text input
            case \Flexio\Base\ContentType::TEXT:
                self::createOutputFromFixedLengthInput($convert_params, $instream, $outstream, $output_content_type_from_definition);
                return;

            // text input
            case \Flexio\Base\ContentType::PDF:
                self::createOutputFromPdfInput($convert_params, $instream, $outstream, $output_content_type_from_definition);
                return;
        }
    }

    private static function createOutputFromTableInput(array $convert_params, \Flexio\IFace\IStream &$instream, \Flexio\IFace\IStream &$outstream, string $output_mime_type) : void
    {
        $instream_mime_type = $instream->getMimeType();
        if ($instream_mime_type != \Flexio\Base\ContentType::FLEXIO_TABLE)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED, "Input file must be a table");


        if ($output_mime_type == \Flexio\Base\ContentType::FLEXIO_TABLE)
        {
            $streamreader = $instream->getReader();

            $properties = [];
            $properties['mime_type'] = \Flexio\Base\ContentType::FLEXIO_TABLE;
            $properties['structure'] = $instream->getStructure()->enum();
            $outstream->set($properties);

            $streamwriter = $outstream->getWriter();

            while (true)
            {
                $row = $streamreader->readRow();
                if ($row === false)
                    break;
                $streamwriter->write($row);
            }
        }
         else if ($output_mime_type == \Flexio\Base\ContentType::JSON)
        {
            $outstream->setMimeType($output_mime_type);
            $streamreader = $instream->getReader();
            $streamwriter = $outstream->getWriter();

            $rown = 0;
            $streamwriter->write("[");

            while (true)
            {
                $row = $streamreader->readRow();
                if ($row === false)
                    break;

                $row = json_encode($row, JSON_UNESCAPED_SLASHES);
                $streamwriter->write(($rown>0?",\n":"\n") . $row);

                ++$rown;
            }

            $streamwriter->write("\n]");
            $streamwriter->close();
        }
         else if ($output_mime_type == \Flexio\Base\ContentType::NDJSON)
        {
            $outstream->setMimeType($output_mime_type);
            $streamreader = $instream->getReader();
            $streamwriter = $outstream->getWriter();

            while (true)
            {
                $row = $streamreader->readRow();
                if ($row === false)
                    break;

                $json = json_encode($row, 0) . "\n";
                $streamwriter->write($json);
            }

            $streamwriter->close();
        }
         else if ($output_mime_type == \Flexio\Base\ContentType::XLSX || $output_mime_type == \Flexio\Base\ContentType::XLS || $output_mime_type == \Flexio\Base\ContentType::ODS)
        {
            $outstream->setMimeType($output_mime_type);
            $streamreader = $instream->getReader();
            $streamwriter = $outstream->getWriter();

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


            $header = toBoolean($convert_params['output']['header'] ?? true);
            if ($header && count($rows) > 0)
            {
                $structure = $instream->getStructure()->enum();
                $header_row = array_column($structure, 'name');
                array_unshift($rows, $header_row);
            }

            $worksheet->fromArray($rows);

            $storage_tmpbase = \Flexio\System\System::getStoreTempPath();
            $spreadsheet_fname = $storage_tmpbase . DIRECTORY_SEPARATOR . 'tmpspreadsheet-' . \Flexio\Base\Util::generateRandomString(30);
            switch ($output_mime_type)
            {
                default:
                case \Flexio\Base\ContentType::XLSX: $format = 'Xlsx'; break;
                case \Flexio\Base\ContentType::XLS:  $format = 'Xls';  break;
                case \Flexio\Base\ContentType::ODS:  $format = 'Ods';  break;
            }

            $spreadsheet_fname .= ('.' . strtolower($format));

            register_shutdown_function('unlink', $spreadsheet_fname);

            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, $format);
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
            $outstream->setMimeType($output_mime_type);
            $streamreader = $instream->getReader();
            $streamwriter = $outstream->getWriter();

            $delimiter = $convert_params['output']['delimiter'] ?? self::DELIMITER_COMMA;
            $header = toBoolean($convert_params['output']['header'] ?? true);
            $qualifier = $convert_params['output']['qualifier'] ?? self::TEXT_QUALIFIER_DOUBLE_QUOTE;
            $encoding = $convert_params['output']['encoding'] ?? 'UTF-8';  // by default, use UTF-8 output

            switch ($delimiter)
            {
                // convert the delimiter tokens into their literal equivalent;
                // for the default, use what we have
                default:
                    break;

                case self::DELIMITER_NONE:       $delimiter = "\r\n"; break; // use new-line so 'explode' doesn't flag a warning for no delimiter
                case self::DELIMITER_COMMA:      $delimiter = ",";    break;
                case self::DELIMITER_SEMICOLON:  $delimiter = ";";    break;
                case self::DELIMITER_PIPE:       $delimiter = "|";    break;
                case self::DELIMITER_TAB:        $delimiter = "\t";   break;
                case self::DELIMITER_SPACE:      $delimiter = " ";    break;
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

                // compatibility; TODO: remove when no longer needed
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
        }
    }

    private static function createOutputFromRssAtom(array $convert_params, \Flexio\IFace\IStream &$instream, \Flexio\IFace\IStream &$outstream, string $output_mime_type) : void
    {

        $streamreader = $instream->getReader();


        $rss_payload = '';
        while (($piece = $streamreader->read(16384)) !== false)
        {
            $rss_payload .= $piece;
        }


        require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'library'. DIRECTORY_SEPARATOR .'simplepie'. DIRECTORY_SEPARATOR . 'autoloader.php';

        $feed = new \SimplePie();
        $feed->enable_cache(false);
        $feed->set_raw_data($rss_payload);

        $feed->init();
        $feed->handle_content_type();
        $items = @$feed->get_items();


        if ($output_mime_type == \Flexio\Base\ContentType::JSON)
        {
            $streamwriter = $outstream->getWriter();
            $rows = [];
        }
         else
        {
            $structure = [
                [ 'name' => 'link', 'type' => 'text' ],
                [ 'name' => 'title', 'type' => 'text' ],
                [ 'name' => 'description', 'type' => 'text' ],
                [ 'name' => 'content', 'type' => 'text' ],
                [ 'name' => 'source', 'type' => 'text' ],
                [ 'name' => 'author', 'type' => 'text' ],
                [ 'name' => 'date', 'type' => 'text' ]
            ];

            $outstream->set(['mime_type' => \Flexio\Base\ContentType::FLEXIO_TABLE,
                             'structure' => $structure]);

            $streamwriter = $outstream->getWriter();
        }


        foreach ($items as $item)
        {
            $row = array(
                'link' => $item->get_link(),
                'title' => $item->get_title(),
                'description' => html_entity_decode(strip_tags($item->get_description())),
                'content' => $item->get_content(),
                'source' => $item->get_source(),
                'author' => $item->get_author(),
                'date' => $item->get_date()
            );

            if ($output_mime_type == \Flexio\Base\ContentType::JSON)
            {
                $rows[] = $row;
            }
            else if ($output_mime_type == \Flexio\Base\ContentType::NDJSON)
            {
                $json = json_encode($row,0);
                if (!is_string($json))
                    $json = '';

                $json = $json . "\n";
                $streamwriter->write($json);
            }
            else if ($output_mime_type == \Flexio\Base\ContentType::FLEXIO_TABLE)
            {
                $streamwriter->write($row);
            }
        }


        if ($output_mime_type == \Flexio\Base\ContentType::JSON)
        {
            $json = json_encode($rows, JSON_UNESCAPED_SLASHES);
            $streamwriter->write($json);

            $outstream->set(['mime_type' => \Flexio\Base\ContentType::JSON,
                             'size' => strlen($json)]);
        }
    }

    private static function createOutputFromPdfInput(array $convert_params, \Flexio\IFace\IStream &$instream, \Flexio\IFace\IStream &$outstream, string $output_mime_type) : void
    {
        $instream_mime_type = $instream->getMimeType();
        if ($instream_mime_type != \Flexio\Base\ContentType::PDF)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED, "Input must be a pdf");

        // get the pages to convert; pages is a range of pages given
        // by pages and page ranges and special keywords; examples of page ranges:
        // pages: none => []
        // pages: 1 => [1]
        // pages: 1,3 => [1,3]
        // pages: 1-3 => [1,2,3]
        // pages: 1,3-5 => [1,3,4,5]
        // pages: last => [<page_count>]
        // pages: 2-last => [2,3,4,...,<page_count>]
        // pages: something-invalid => []

        $pages_to_convert = $convert_params['input']['pages'] ?? '';
        if (is_string($pages_to_convert) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX, "Pages parameter must be a string");

        if (strlen($pages_to_convert) === 0)
            $pages_to_convert = false;

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

        // parse the pdf and output it to json
        try
        {
            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseContent($buffer);

            $details = $pdf->getDetails();

            $metadata = array();
            $metadata['author'] = $details['Author'] ?? '';
            $metadata['title'] = $details['Title'] ?? '';
            $metadata['subject'] = $details['Subject'] ?? '';
            $metadata['producer'] = $details['Producer'] ?? '';
            $metadata['keywords'] = $details['Keywords'] ?? '';
            $metadata['created_by'] = $details['Creator'] ?? '';
            $metadata['created'] = $details['CreationDate'] ?? '';
            $metadata['page_count'] = $details['Pages'] ?? '';

            $output = array("metadata" => $metadata);

            $output['content'] = array();

            // write out the text from each of the pages
            $pages  = $pdf->getPages();
            $page_count = count($pages);

            // if no string (or empty) string is specified, use the full set of pages;
            // otherwise use the specified range
            if ($pages_to_convert === false)
                $pages_to_convert = \Flexio\Base\Util::createPageRangeArray("1-$page_count", $page_count);
                 else
                $pages_to_convert = \Flexio\Base\Util::createPageRangeArray($pages_to_convert, $page_count);
            $pages_to_convert = array_flip($pages_to_convert);

            $page_idx = 0;
            foreach ($pages as $page)
            {
                $page_idx++; // pages are 1-based index

                // check if the page to convert exists in the list of pages to convert;
                // if not, move on
                if (array_key_exists($page_idx, $pages_to_convert) == false)
                    continue;

                $page = array("page" => $page_idx, "text" => $page->getText());
                $output['content'][] = $page;
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

        $outputtext = json_encode($output);
        if ($outputtext === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

        $streamwriter->write($outputtext);
        $streamwriter->close();
        $outstream->setMimeType(\Flexio\Base\ContentType::JSON);
    }

    private static function createOutputFromJsonInput(array $convert_params, \Flexio\IFace\IStream &$instream, \Flexio\IFace\IStream &$outstream, string $output_mime_type) : void
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
            if (!isset($structure))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

            foreach ($items as &$row)
                $row = array_values($row);
            unset($row);

            $header = toBoolean($convert_params['output']['header'] ?? true);
            if ($header && count($items) > 0)
            {
                $header_row = array_column($structure, 'name');
                array_unshift($items, $header_row);
            }

            $worksheet->fromArray($items);

            $storage_tmpbase = \Flexio\System\System::getStoreTempPath();
            $spreadsheet_fname = $storage_tmpbase . DIRECTORY_SEPARATOR . 'tmpspreadsheet-' . \Flexio\Base\Util::generateRandomString(30);
            switch ($output_mime_type)
            {
                default:
                case \Flexio\Base\ContentType::XLSX: $format = 'Xlsx'; break;
                case \Flexio\Base\ContentType::XLS:  $format = 'Xls'; break;
                case \Flexio\Base\ContentType::ODS:  $format = 'Ods'; break;
            }

            $spreadsheet_fname .= ('.' . strtolower($format));

            register_shutdown_function('unlink', $spreadsheet_fname);

            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, $format);
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
         else if ($output_mime_type == \Flexio\Base\ContentType::NDJSON)
        {
            $size = 0;
            foreach ($items as $i)
            {
                $json = json_encode($i,0) . "\n";
                $size += strlen($json);
                $streamwriter->write($json);
            }

            $outstream->set([
                'mime_type' => $output_mime_type,
                'size' => $size
            ]);
        }
         else
        {
            // set the output structure and write the rows
            $structure = self::determineStructureFromJsonArray($items);
            if (!isset($structure))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

            // input/output
            $outstream->set([
                'mime_type' => \Flexio\Base\ContentType::FLEXIO_TABLE,
                'structure' => $structure
            ]);

            $streamwriter = $outstream->getWriter();

            foreach ($items as $i)
            {
                $streamwriter->write($i);
            }

            $streamwriter->close();
        }
    }

    private static function createOutputFromCsvInput(array $convert_params, \Flexio\IFace\IStream &$instream, \Flexio\IFace\IStream &$outstream, string $output_mime_type) : void
    {
        // parameters
        $streamwriter = null;

        $delimiter = $convert_params['input']['delimiter'] ?? self::DELIMITER_COMMA;

        $output = 'table';

        if ($output_mime_type == \Flexio\Base\ContentType::JSON)
            $output = 'json';
        else if ($output_mime_type == \Flexio\Base\ContentType::NDJSON)
            $output = 'ndjson';
        else if ($output_mime_type == \Flexio\Base\ContentType::XLSX || $output_mime_type == \Flexio\Base\ContentType::XLS || $output_mime_type == \Flexio\Base\ContentType::ODS)
            $output = 'spreadsheet';
        else
            $output_mime_type = \Flexio\Base\ContentType::FLEXIO_TABLE;

        $spreadsheet_output = [];
        $total_json_size = 0;
        $total_ndjson_size = 0;

        $header = toBoolean($convert_params['input']['header'] ?? true);
        $qualifier = $convert_params['input']['qualifier'] ?? self::TEXT_QUALIFIER_DOUBLE_QUOTE;
        $encoding = $convert_params['input']['encoding'] ?? '';

        if (strtolower($encoding) == 'ascii')
            $encoding = 'ISO-8859-1'; // use this as it is a superset of ASCII

        switch ($delimiter)
        {
            // convert the delimiter tokens into their literal equivalent;
            // for the default, use what we have
            default:
                break;

            case self::DELIMITER_NONE:       $delimiter = "\r\n"; break; // use new-line so 'explode' doesn't flag a warning for no delimiter
            case self::DELIMITER_COMMA:      $delimiter = ",";    break;
            case self::DELIMITER_SEMICOLON:  $delimiter = ";";    break;
            case self::DELIMITER_PIPE:       $delimiter = "|";    break;
            case self::DELIMITER_TAB:        $delimiter = "\t";   break;
            case self::DELIMITER_SPACE:      $delimiter = " ";    break;
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


        if ($output == 'json')
        {
            // for json output, streamwriter is created here; for table output, streamwriter
            // is created below, because header row must be collected in advance
            $json= '[';
            $total_json_size += strlen($json);
            $streamwriter = $outstream->getWriter();
            $streamwriter->write($json);
        }
         else if ($output == 'ndjson')
        {
            $streamwriter = $outstream->getWriter();
        }
         else if ($output == 'spreadsheet')
        {
            $streamwriter = $outstream->getWriter();
        }
         else if ($output == 'table')
         {
            $outstream->set([
                'mime_type' => \Flexio\Base\ContentType::FLEXIO_TABLE
            ]);
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
                        if ($row === false)
                            break;

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
                    throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED, "Conversion from $encoding to UTF-8 failed");
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

            if ($output == 'json')
            {
                $row = json_encode($row, JSON_UNESCAPED_SLASHES);
                $json = ($rown>0?",\n":"\n") . $row;
                $total_json_size += strlen($json);
                $result = $streamwriter->write($json);
            }
            else if ($output == 'ndjson')
            {
                $json = json_encode($row, 0) . "\n";
                $result = $streamwriter->write($json);
            }
             else if ($output == 'spreadsheet')
            {
                $spreadsheet_output[] = array_values($row);
                $result = true;
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
            if ($output == 'json')
            {
                $json = "\n]";
                $total_json_size += strlen($json);
                $streamwriter->write($json);
            }
            else if ($output == 'ndjson')
            {
                // nothing
            }
            else if ($output == 'table')
            {
                $result = $streamwriter->close();

                if ($result === false)
                    throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);
            }
        }


        if ($output == 'spreadsheet')
        {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $worksheet = $spreadsheet->getActiveSheet();

            $structure = self::determineStructureFromJsonArray($spreadsheet_output);
            if (!isset($structure))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);

            $header = toBoolean($convert_params['output']['header'] ?? true);
            if ($header && count($spreadsheet_output) > 0)
            {
                $header_row = array_column($structure, 'name');
                array_unshift($spreadsheet_output, $header_row);
            }

            $worksheet->fromArray($spreadsheet_output);

            $storage_tmpbase = \Flexio\System\System::getStoreTempPath();
            $spreadsheet_fname = $storage_tmpbase . DIRECTORY_SEPARATOR . 'tmpspreadsheet-' . \Flexio\Base\Util::generateRandomString(30);
            switch ($output_mime_type)
            {
                default:
                case \Flexio\Base\ContentType::XLSX: $format = 'Xlsx'; break;
                case \Flexio\Base\ContentType::XLS:  $format = 'Xls'; break;
                case \Flexio\Base\ContentType::ODS:  $format = 'Ods'; break;
            }

            $spreadsheet_fname .= ('.' . strtolower($format));

            register_shutdown_function('unlink', $spreadsheet_fname);

            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, $format);
            $writer->save($spreadsheet_fname);
            $writer = null;
            $spreadsheet = null;

            $contents = file_get_contents($spreadsheet_fname);

            $streamwriter->write($contents);

            // input/output
            $outstream->set([
                'mime_type' => $output_mime_type,
                'size' => strlen($contents),
                'structure' => $structure
            ]);
        }

        if ($output == 'table')
        {
            $output_properties = array(
                'mime_type' => \Flexio\Base\ContentType::FLEXIO_TABLE,
                'structure' => $structure
            );

            $outstream->set($output_properties);
        }

        if ($output == 'json')
        {
            $output_properties = array(
                'mime_type' => \Flexio\Base\ContentType::JSON,
                'size' => $total_json_size,
                'structure' => $structure
            );

            $outstream->set($output_properties);
        }

        if ($output == 'ndjson')
        {
            $output_properties = array(
                'mime_type' => \Flexio\Base\ContentType::NDJSON,
                'size' => $total_ndjson_size,
                'structure' => $structure
            );

            $outstream->set($output_properties);
        }
    }

    private static function createOutputFromSpreadsheetInput(array $convert_params, \Flexio\IFace\IStream &$instream, \Flexio\IFace\IStream &$outstream, string $output_mime_type) : void
    {
        $reader = $instream->getReader();

        $storage_tmpbase = \Flexio\System\System::getStoreTempPath();
        $spreadsheet_fname = $storage_tmpbase . DIRECTORY_SEPARATOR . 'tmpspreadsheet-' . \Flexio\Base\Util::generateRandomString(30);
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
            $streamwriter->write(json_encode($rows, JSON_UNESCAPED_SLASHES));
            $streamwriter->close();

            // input/output
            $outstream->setMimeType(\Flexio\Base\ContentType::JSON);
        }
         else if ($output_mime_type == \Flexio\Base\ContentType::NDJSON)
        {
            // transfer the data
            $streamwriter = $outstream->getWriter();

            foreach ($rows as $r)
            {
                $json = json_encode($r, 0) . "\n";
                $streamwriter->write($json);
            }

            $streamwriter->close();
            $outstream->setMimeType(\Flexio\Base\ContentType::NDJSON);
        }
         else
        {
            // set the output structure and write the rows
            $structure = self::determineStructureFromJsonArray($rows);
            if (!isset($structure))
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

    private static function createOutputFromFixedLengthInput(array $convert_params, \Flexio\IFace\IStream &$instream, \Flexio\IFace\IStream &$outstream) : void
    {
        // parameters
        $start_offset = $convert_params['start_offset'] ?? 0;
        $row_width = $convert_params['row_width'] ?? 100;
        $line_delimiter = $convert_params['line_delimiter'] ?? false;
        $columns = $convert_params['columns'] ?? [];

        if ($row_width == 0)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        if (count($columns) == 0)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

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

    private static function determineStructureFromJsonArray(array $items) : ?array
    {
        // create the fields based off the first row
        if (count($items) === 0)
            return null;

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

    private static function getContentTypeFromFormat(string $format) : ?string
    {
        if ($format == self::FORMAT_DELIMITED_TEXT || $format == self::FORMAT_TSV || $format == self::FORMAT_CSV)
            return \Flexio\Base\ContentType::CSV;
        else if ($format == self::FORMAT_FIXED_LENGTH)
            return \Flexio\Base\ContentType::TEXT;
        else if ($format == self::FORMAT_JSON)
            return \Flexio\Base\ContentType::JSON;
        else if ($format == self::FORMAT_NDJSON)
            return \Flexio\Base\ContentType::NDJSON;
        else if ($format == self::FORMAT_RSS)
            return \Flexio\Base\ContentType::RSS;
        else if ($format == self::FORMAT_ATOM)
            return \Flexio\Base\ContentType::ATOM;
        else if ($format == self::FORMAT_PDF)
            return \Flexio\Base\ContentType::PDF;
        else if ($format == self::FORMAT_TABLE)
            return \Flexio\Base\ContentType::FLEXIO_TABLE;
        else if ($format == self::FORMAT_XLS)
            return \Flexio\Base\ContentType::XLS;
        else if ($format == self::FORMAT_XLSX || $format == self::FORMAT_EXCEL || $format == self::FORMAT_SPREADSHEET)
            return \Flexio\Base\ContentType::XLSX;
        else if ($format == self::FORMAT_ODS)
            return \Flexio\Base\ContentType::ODS;
        else
            return null;
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

