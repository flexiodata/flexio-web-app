<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams
 * Created:  2015-08-26
 *
 * @package flexio
 * @subpackage Jobs
 */


namespace Flexio\Jobs;


require_once __DIR__ . DIRECTORY_SEPARATOR . 'Base.php';

class Create extends \Flexio\Jobs\Base
{
    public function run()
    {
        // create job adds new streams; add streams onto inputs we've
        // already received
        $job_definition = $this->getProperties();
        $this->getOutput()->merge($this->getInput());

        $validator = \Flexio\Base\ValidatorSchema::check($job_definition, \Flexio\Jobs\Create::SCHEMA);
        if ($validator->hasErrors() === true)
            return $this->fail(\Flexio\Base\Error::INVALID_PARAMETER, _(''), __FILE__, __LINE__);

        // get the content type
        $mime_type = $job_definition['params']['mime_type'] ?? \Flexio\Base\ContentType::MIME_TYPE_STREAM;
        switch ($mime_type)
        {
            default:
            case \Flexio\Base\ContentType::MIME_TYPE_STREAM:
            case \Flexio\Base\ContentType::MIME_TYPE_TXT:
            case \Flexio\Base\ContentType::MIME_TYPE_CSV:
            case \Flexio\Base\ContentType::MIME_TYPE_JSON:
                return $this->createStreamOutput();

            case \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE:
                return $this->createTableOutput();
        }

        // default fall through; shouldn't happen
        $this->fail(\Flexio\Base\Error::INVALID_PARAMETER, _(''), __FILE__, __LINE__);
    }

    private function createStreamOutput()
    {
        $job_definition = $this->getProperties();
        $name = $job_definition['params']['name'] ?? _('New File');
        $mime_type = ($job_definition['params']['mime_type'] ?? \Flexio\Base\ContentType::MIME_TYPE_STREAM);

        // get the content and decode it
        $content = '';
        if (isset($job_definition['params']['content']))
        {
            $content = $job_definition['params']['content'];
            if (!is_string($content))
                return $this->fail(\Flexio\Base\Error::INVALID_PARAMETER, _(''), __FILE__, __LINE__);

            $content = base64_decode($content);
        }

        // create the output stream
        $outstream_properties = array(
            'name' => $name,
            'mime_type' => $mime_type
        );
        $outstream = \Flexio\Object\Stream::create($outstream_properties);
        $this->getOutput()->push($outstream);

        $streamwriter = \Flexio\Object\StreamWriter::create($outstream);
        if ($streamwriter === false)
            return $this->fail(\Flexio\Base\Error::CREATE_FAILED, _(''), __FILE__, __LINE__);

        // write the content
        $streamwriter->write($content);
        $streamwriter->close();
        $outstream->setSize($streamwriter->getBytesWritten());
    }

    private function createTableOutput()
    {
        $job_definition = $this->getProperties();
        $name = $job_definition['params']['name'] ?? _('New Table');
        $mime_type = $job_definition['params']['mime_type'] ?? \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE;
        $structure = $job_definition['params']['columns'] ?? '[]';
        $outstream_properties = array(
            'name' => $name,
            'mime_type' => \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE,
            'structure' => $structure
        );
        $outstream = \Flexio\Object\Stream::create($outstream_properties);
        $this->getOutput()->push($outstream);

        $streamwriter = \Flexio\Object\StreamWriter::create($outstream);
        if ($streamwriter === false)
            return $this->fail(\Flexio\Base\Error::CREATE_FAILED, _(''), __FILE__, __LINE__);

        if (isset($job_definition['params']['content']))
        {
            $rows = $job_definition['params']['content'];
            if (!is_array($rows))
                return $this->fail(\Flexio\Base\Error::INVALID_PARAMETER, _(''), __FILE__, __LINE__);

            foreach ($rows as $row)
            {
                // if the row array is non-associative, then insert them
                // based on the index of the field compared to the structure
                if (\Flexio\Base\Util::isAssociativeArray($row) === false)
                {
                    $idx = 0;
                    $row_with_keys = array();
                    $output_structure = $outstream->getStructure()->enum();
                    foreach($output_structure as $col)
                    {
                        if (array_key_exists($idx, $row))
                            $row_with_keys[$col['name']] = $row[$idx];
                        $idx++;
                    }
                    $row = $row_with_keys;
                }

                $result = $streamwriter->write($row);
                if ($result === false)
                    $this->fail(\Flexio\Base\Error::WRITE_FAILED, _(''), __FILE__, __LINE__);
            }
        }

        $result = $streamwriter->close();
        if ($result === false)
            $this->fail(\Flexio\Base\Error::WRITE_FAILED, _(''), __FILE__, __LINE__);

        $outstream->setSize($streamwriter->getBytesWritten());
    }


    // job definition info
    const MIME_TYPE = 'flexio.create';
    const TEMPLATE = <<<EOD
    {
        "type": "flexio.create",
        "params": {
            "name": "test",
            "mime_type": "text/csv",
            "content": ""
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
                "enum": ["flexio.create"]
            },
            "params": {
                "type": "object",
                "properties": {
                    "name": {
                        "type": "string",
                        "minLength": 1
                    },
                    "mime_type": {
                        "type": "string"
                    },
                    "structure": {
                        "type": "array",
                        "minItems": 1,
                        "items": {
                            "type": "object",
                            "required": ["name"],
                            "properties": {
                                "name": {
                                    "type": "string",
                                    "format": "fx.fieldname"
                                },
                                "type": {
                                    "type": "string",
                                    "enum": ["text","character","widecharacter","numeric","double","integer","date","datetime","boolean"]
                                },
                                "width": {
                                    "type": "integer",
                                    "minimum": 0,
                                    "maximum": 10000
                                },
                                "scale": {
                                    "type": "integer",
                                    "minimum": 0,
                                    "maximum": 12
                                }
                            }
                        }
                    },
                    "content": {
                        "type": ["string", "array"]
                    }
                }
            }
        }
    }
EOD;
}
