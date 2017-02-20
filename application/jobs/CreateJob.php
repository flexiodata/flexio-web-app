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


require_once __DIR__ . DIRECTORY_SEPARATOR . 'Base.php';

class CreateJob extends Base
{
    public function run()
    {
        // create job adds new streams; add streams onto inputs we've
        // already received
        $job_definition = $this->getProperties();
        $this->getOutput()->merge($this->getInput());

        if (isset( $job_definition['params']['content']))
        {
            $this->createStreamOutput();
            return;
        }

        if (isset( $job_definition['params']['columns']))
        {
            $this->createTableOutput();
            return;
        }

        // default fall through
        $this->fail(\Model::ERROR_INVALID_PARAMETER, _(''), __FILE__, __LINE__);
    }

    private function createStreamOutput()
    {
        $job_definition = $this->getProperties();
        $name = isset_or($job_definition['params']['name'], 'New File');

        // get the content and decode it
        $content = $job_definition['params']['content'];
        $content = base64_decode($content);
        $mime_type = ContentType::getMimeType($name, $content);

        // create the output stream
        $outstream_properties = array(
            'name' => $name,
            'mime_type' => $mime_type
        );
        $outstream = \Flexio\Object\Stream::create($outstream_properties);
        $this->getOutput()->push($outstream);

        $streamwriter = \Flexio\Object\StreamWriter::create($outstream);
        if ($streamwriter === false)
            return $this->fail(\Model::ERROR_CREATE_FAILED, _(''), __FILE__, __LINE__);

        // write the content
        $streamwriter->write($content);
        $streamwriter->close();
        $outstream->setSize($streamwriter->getBytesWritten());
    }

    private function createTableOutput()
    {
        $job_definition = $this->getProperties();
        $name = isset_or($job_definition['params']['name'], 'New Table');
        $structure = isset_or($job_definition['params']['columns'], '[]');
        $outstream_properties = array(
            'name' => $name,
            'mime_type' => ContentType::MIME_TYPE_FLEXIO_TABLE,
            'structure' => $structure
        );
        $outstream = \Flexio\Object\Stream::create($outstream_properties);
        $this->getOutput()->push($outstream);

        $streamwriter = \Flexio\Object\StreamWriter::create($outstream);
        if ($streamwriter === false)
            return $this->fail(\Model::ERROR_CREATE_FAILED, _(''), __FILE__, __LINE__);

        if (isset($job_definition['params']['rows']))
        {
            foreach ($job_definition['params']['rows'] as $row)
            {
                // if the row array is non-associative, then insert them
                // based on the index of the field compared to the structure
                if (Util::isAssociativeArray($row) === false)
                {
                    $idx = 0;
                    $row_with_keys = array();
                    $output_structure = $outstream->getStructure()->enum();
                    foreach($output_structure as $col)
                    {
                        if (isset($row[$idx]))
                            $row_with_keys[$col['name']] = $row[$idx];
                        $idx++;
                    }
                    $row = $row_with_keys;
                }

                $result = $streamwriter->write($row);
                if ($result === false)
                    $this->fail(\Model::ERROR_WRITE_FAILED, _(''), __FILE__, __LINE__);
            }
        }

        $result = $streamwriter->close();
        if ($result === false)
            $this->fail(\Model::ERROR_WRITE_FAILED, _(''), __FILE__, __LINE__);

        $outstream->setSize($streamwriter->getBytesWritten());
    }


    // job definition info
    const MIME_TYPE = 'flexio.create';
    const TEMPLATE = <<<EOD
    {
        "type": "flexio.create",
        "params": {
            "columns": [
                { "name": "myfield", "type": "character", "width": 40, "scale": 0 }
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
                "enum": ["flexio.create"]
            },
            "params": {
                "type": "object",
                "properties": {
                    "name": {
                        "type": "string",
                        "minLength": 1
                    },
                    "content": {
                        "type": "string"
                    },
                    "columns": {
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
                                    "enum": ["text","character","numeric","double","integer","date","datetime","boolean"]
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
                    "rows" : {
                        "type": "array",
                        "items": {
                            "type": "array",
                            "items": {
                                "type": ["string","number","integer","boolean","null"]
                            }
                        }
                    }
                }
            }
        }
    }
EOD;
}
