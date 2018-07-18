<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-02-02
 *
 * @package flexio
 * @subpackage Services
 */


declare(strict_types=1);
namespace Flexio\Services;


class PipelineDeals implements \Flexio\IFace\IConnection, \Flexio\IFace\IFileSystem
{
    private $authenticated = false;
    private $access_token = '';
    private $pagesize = 200; // rows to request per request; 200 is maximum allowed per request
    private $request_throttle = 250; // milliseconds to wait between requests; pipeline deals allows up to 5 requests per second

    public static function create(array $params = null) : \Flexio\Services\PipelineDeals
    {
        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'access_token' => array('type' => 'string', 'required' => true)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $validated_params = $validator->getParams();
        $access_token = $validated_params['access_token'];

        $service = new self;
        if ($service->initialize($access_token) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_SERVICE);

        return $service;
    }

    public function authenticated() : bool
    {
        return $this->authenticated;
    }

    ////////////////////////////////////////////////////////////
    // IFileSystem interface
    ////////////////////////////////////////////////////////////

    public function getFlags() : int
    {
        return 0;
    }

    public function list(string $path = '', array $options = []) : array
    {
        if (!$this->authenticated())
            return array();

        $objects = array();

        $definitions = $this->getDefinitions();
        foreach ($definitions as $d)
        {
            $objects[] = array(
                'name' => $d['name'],
                'path' => $d['path'],
                'size' => null,
                'modified' => null,
                'type' => 'FILE'
            );
        }

        return $objects;
    }

    public function getFileInfo(string $path) : array
    {
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
    }

    public function exists(string $path) : bool
    {
        // TODO: implement
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
        return false;
    }

    public function createFile(string $path, array $properties = []) : bool
    {
        // TODO: implement
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
    }

    public function createDirectory(string $path, array $properties = []) : bool
    {
        // TODO: implement
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
    }

    public function unlink(string $path) : bool
    {
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
        return false;
    }

    public function open($path) : \Flexio\IFace\IStream
    {
        // TODO: implement
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
    }

    public function read(array $params, callable $callback) // TODO: add return type
    {
        $path = $params['path'] ?? '';

        if (!$this->authenticated())
            return false;

        // TODO: only read the buffer amount
        // TODO: limit the request rate

        $page = null;
        while (true)
        {
            $rows = $this->fetchData($path, $page);
            if ($rows === false)
                break;
            if (count($rows) === 0)
                break;

            foreach ($rows as $r)
            {
                $callback($r);
            }

            usleep($this->request_throttle*1000);
        }

        return true;
    }

    public function write(array $params, callable $callback) // TODO: add return type
    {
        $path = $params['path'] ?? '';
        $content_type = $params['content_type'] ?? \Flexio\Base\ContentType::STREAM;

        // TODO: implement
    }

    ////////////////////////////////////////////////////////////
    // additional functions
    ////////////////////////////////////////////////////////////

    public function describeTable(string $path) // TODO: add return type
    {
        if (!$this->authenticated())
            return false;

        $path = self::cleanPath($path);

        // load the definition for the given path
        $definition = $this->lookupDefinition($path);
        if ($definition === false)
            return false;

        $structure = $definition['output'];
        return $structure;
    }

    private function fetchData(string $path, &$page = null) // TODO: add return type
    {
        $path = self::cleanPath($path);
        if (!isset($page))
            $page = 1;

        // load the definition for the given path
        $definition = $this->lookupDefinition($path);
        if ($definition === false)
            return false;

        $location = $definition['location'];
        $content_root = $definition['content_root'];

        $access_token = $this->access_token;
        $request = str_replace('{key}', $access_token, $location);
        $request .= "&page=$page";
        $request .= "&per_page=$this->pagesize";

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $request);
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        $result = curl_exec($ch);
        curl_close($ch);

        if ($result === false)
            return false;

        // return rows with uniform keys (flatten will flatten but not guarantee each row has
        // the same keys)
        $structure = $this->describeTable($path);
        $rows = $this->map($content_root, $result, $structure);
        $page = $page+1;

        return $rows;
    }

    private function map($content_root, $apidata, $structure) // TODO: add return type; TODO: add parameter type
    {
        $result = array();

        $apidata = json_decode($apidata);
        $rows = self::getRowData($content_root, $apidata);
        if (!is_array($rows))
            return $result;

        $keys = array();
        foreach ($structure as $s)
        {
            $keys[] = $s['name'];
        }

        foreach ($rows as $r)
        {
            $r = \Flexio\Base\Mapper::flatten($r, null, '_');
            $r = $r[0];

            $output_row = array();
            foreach ($keys as $k)
            {
                $output_row[$k] = null;
                if (isset($r[$k]))
                    $output_row[$k] = $r[$k];
            }

            $result[] = $output_row;
        }

        return $result;
    }

    private function getRowData($content_root, $apidata) // TODO: add return type; TODO: add parameter type
    {
        // find out where the data is located in the result
        if (!is_string($content_root))
            return array();

        // set the current path to the root apidata object; find out the
        // path to the data node
        $currentpath = $apidata;
        $datapath = explode('.', $content_root);

        foreach ($datapath as $node)
        {
            // skip the root node
            if ($node === '$')
                continue;

            // if we can't find the current path, return an empty set of rows
            if (!isset($currentpath->$node))
                return array();

            // update the current path
            $currentpath = $currentpath->$node;
        }

        // we found the specified path
        return $currentpath;
    }

    private function connect() : bool
    {
        $access_token = $this->access_token;
        if ($this->initialize($access_token) === false)
            return false;

        return true;
    }

    private function initialize(string $access_token) : bool
    {
        // TODO: test api key

        $this->access_token = $access_token;
        $this->authenticated = true;
        return true;
    }

    private function lookupDefinition(string $path) // TODO: add return type
    {
        $definitions = $this->getDefinitions();
        foreach ($definitions as $d)
        {
            if ($d['path'] === $path)
                return $d;
        }

        return false;
    }

    private function getDefinitions() : array
    {
        // note: "content_root" is the the json path where the data in the return
        // result is located; so "$.entries" means the data is stored in the "entries"
        // node in the root object

        $definitions = array();

        $definitions[] = '
        {
            "path": "companies",
            "name": "companies",
            "location": "https://api.pipelinedeals.com/api/v3/companies.json?api_key={key}",
            "content_root": "$.entries",
            "output": [
                { "name": "id",               "type": "numeric", "width":  15, "scale": 0 },
                { "name": "name",             "type": "text"     },
                { "name": "description",      "type": "text"     },
                { "name": "email",            "type": "text"     },
                { "name": "web",              "type": "text"     },
                { "name": "address_1",        "type": "text"     },
                { "name": "address_2",        "type": "text"     },
                { "name": "city",             "type": "text"     },
                { "name": "state",            "type": "text"     },
                { "name": "postal_code",      "type": "text"     },
                { "name": "country",          "type": "text"     },
                { "name": "phone1",           "type": "text"     },
                { "name": "phone1_desc",      "type": "text"     },
                { "name": "phone2",           "type": "text"     },
                { "name": "phone2_desc",      "type": "text"     },
                { "name": "phone3",           "type": "text"     },
                { "name": "phone3_desc",      "type": "text"     },
                { "name": "phone4",           "type": "text"     },
                { "name": "phone4_desc",      "type": "text"     },
                { "name": "image_thumb_url",  "type": "text"     },
                { "name": "image_mobile_url", "type": "text"     },
                { "name": "created_at",       "type": "datetime" },
                { "name": "updated_at",       "type": "datetime" }
            ],
            "input": {
                "entries": [
                    {
                        "id": null,
                        "name": null,
                        "email": null,
                        "web": null,
                        "image_thumb_url": null,
                        "image_mobile_url": null,
                        "address_1": null,
                        "address_2": null,
                        "created_at": null,
                        "fax": null,
                        "phone3_desc": null,
                        "city": null,
                        "phone1": null,
                        "phone2": null,
                        "phone3": null,
                        "phone4": null,
                        "postal_code": null,
                        "phone1_desc": null,
                        "phone4_desc": null,
                        "state": null,
                        "phone2_desc": null,
                        "country": null,
                        "import_id": null,
                        "updated_at": null,
                        "description": null
                    }
                ]
            }
        }
        ';

        $definitions[] = '
        {
            "path": "people",
            "name": "people",
            "location": "https://api.pipelinedeals.com/api/v3/people.json?api_key={key}",
            "content_root": "$.entries",
            "output": [
                { "name": "id",                "type": "numeric", "width":  15, "scale": 0 },
                { "name": "type",              "type": "text"     },
                { "name": "user_id",           "type": "numeric", "width":  15, "scale": 0 },
                { "name": "full_name",         "type": "text"     },
                { "name": "first_name",        "type": "text"     },
                { "name": "last_name",         "type": "text"     },
                { "name": "position",          "type": "text"     },
                { "name": "company_id",        "type": "numeric", "width":  15, "scale": 0 },
                { "name": "company_name",      "type": "text"     },
                { "name": "website",           "type": "text"     },
                { "name": "mobile",            "type": "text"     },
                { "name": "phone",             "type": "text"     },
                { "name": "fax",               "type": "text"     },
                { "name": "email",             "type": "text"     },
                { "name": "email2",            "type": "text"     },
                { "name": "home_phone",        "type": "text"     },
                { "name": "home_email",        "type": "text"     },
                { "name": "facebook_url",      "type": "text"     },
                { "name": "linked_in_url",     "type": "text"     },
                { "name": "twitter",           "type": "text"     },
                { "name": "instant_message",   "type": "text"     },
                { "name": "work_address_1",    "type": "text"     },
                { "name": "work_address_2",    "type": "text"     },
                { "name": "work_city",         "type": "text"     },
                { "name": "work_state",        "type": "text"     },
                { "name": "work_postal_code",  "type": "text"     },
                { "name": "work_country",      "type": "text"     },
                { "name": "home_address_1",    "type": "text"     },
                { "name": "home_address_2",    "type": "text"     },
                { "name": "home_city",         "type": "text"     },
                { "name": "home_state",        "type": "text"     },
                { "name": "home_postal_code",  "type": "text"     },
                { "name": "home_country",      "type": "text"     },
                { "name": "viewed_at",         "type": "datetime" },
                { "name": "created_at",        "type": "datetime" },
                { "name": "updated_at",        "type": "datetime" }
            ],
            "input": {
                "entries": [
                    {
                        "fax": null,
                        "id": null,
                        "facebook_url": null,
                        "lead_source": {
                            "id": null,
                            "name": null
                        },
                        "viewed_at": null,
                        "work_city": null,
                        "lead_status": {
                            "id": null,
                            "name": null
                        },
                        "last_name": null,
                        "user": {
                            "id": null,
                            "full_name": null
                        },
                        "first_name": null,
                        "updated_at": null,
                        "work_country": null,
                        "created_at": null,
                        "email": null,
                        "position": null,
                        "company_name": null,
                        "home_country": null,
                        "home_state": null,
                        "image_thumb_url": null,
                        "image_mobile_url": null,
                        "home_city": null,
                        "home_phone": null,
                        "work_state": null,
                        "email2": null,
                        "twitter": null,
                        "custom_fields": {
                        },
                        "work_postal_code": null,
                        "instant_message": null,
                        "linked_in_url": null,
                        "predefined_contacts_tags": [
                        ],
                        "work_address_1": null,
                        "work_address_2": null,
                        "home_email": null,
                        "home_address_1": null,
                        "home_address_2": null,
                        "type": null,
                        "company_id": null,
                        "user_id": null,
                        "predefined_contacts_tag_ids": [
                        ],
                        "mobile": null,
                        "full_name": null,
                        "phone": null,
                        "website": null,
                        "home_postal_code": null
                    }
                ]
            }
        }
        ';

        $definitions[] = '
        {
            "path": "deals",
            "name": "deals",
            "location": "https://api.pipelinedeals.com/api/v3/deals.json?api_key={key}",
            "content_root": "$.entries",
            "output": [
                { "name": "id",                           "type": "numeric", "width":  15, "scale": 0 },
                { "name": "name",                         "type": "text"     },
                { "name": "summary",                      "type": "text"     },
                { "name": "company_id",                   "type": "numeric", "width":  15, "scale": 0 },
                { "name": "company_name",                 "type": "text"     },
                { "name": "status",                       "type": "text"     },
                { "name": "user_id",                      "type": "numeric", "width":  15, "scale": 0 },
                { "name": "full_name",                    "type": "text"     },
                { "name": "primary_contact_id",           "type": "numeric", "width":  15, "scale": 0 },
                { "name": "closed_time",                  "type": "date"     },
                { "name": "expected_close_date_event_id", "type": "numeric", "width":  15, "scale": 0 },
                { "name": "expected_close_date",          "type": "date"     },
                { "name": "deal_stage_id",                "type": "numeric", "width":  15, "scale": 0 },
                { "name": "deal_stage_name",              "type": "text"     },
                { "name": "deal_loss_reason_id",          "type": "numeric", "width":  15, "scale": 0 },
                { "name": "deal_loss_reason_name",        "type": "text"     },
                { "name": "probability",                  "type": "numeric", "width":  15, "scale": 0 },
                { "name": "value",                        "type": "numeric", "width":  15, "scale": 2 },
                { "name": "import_id",                    "type": "numeric", "width":  15, "scale": 0 },
                { "name": "source_id",                    "type": "numeric", "width":  15, "scale": 0 },
                { "name": "is_archived",                  "type": "boolean"  },
                { "name": "created_at",                   "type": "datetime" },
                { "name": "updated_at",                   "type": "datetime" }
            ],
            "input": {
                "entries": [
                    {
                        "people": [
                            {
                            "full_name": null,
                            "id": null
                            }
                        ],
                        "value_in_cents": null,
                        "is_archived": null,
                        "primary_contact_id": null,
                        "closed_time": null,
                        "user_id": null,
                        "expected_close_date": null,
                        "source_id": null,
                        "expected_close_date_event_id": null,
                        "name": null,
                        "id": null,
                        "user": {
                            "full_name": null,
                            "id": null
                        },
                        "company_id": null,
                        "primary_contact": {
                            "full_name": null,
                            "id": null
                        },
                        "probability": null,
                        "import_id": null,
                        "is_example": null,
                        "company": {
                            "name": null,
                            "id": null
                        },
                        "status": null,
                        "created_at": null,
                        "updated_at": null,
                        "summary": null,
                        "deal_stage_id": null,
                        "deal_stage": {
                            "percent": null,
                            "name": null,
                            "id": null
                        }
                    }
                ]
            }
        }
        ';

        $definitions[] = '
        {
            "path": "tasks",
            "name": "tasks",
            "location": "https://api.pipelinedeals.com/api/v3/calendar_entries.json?api_key={key}",
            "content_root": "$.entries",
            "output": [
                { "name": "id",                 "type": "numeric", "width":  15, "scale": 0 },
                { "name": "type",               "type": "text"     },
                { "name": "name",               "type": "text"     },
                { "name": "description",        "type": "text"     },
                { "name": "owner_id",           "type": "numeric", "width":  15, "scale": 0 },
                { "name": "owner_full_name",    "type": "text"     },
                { "name": "category_id",        "type": "numeric", "width":  15, "scale": 0 },
                { "name": "category_name",      "type": "text"     },
                { "name": "company",            "type": "numeric", "width":  15, "scale": 0 },
                { "name": "start_time",         "type": "datetime" },
                { "name": "end_time",           "type": "datetime" },
                { "name": "all_day",            "type": "boolean"  },
                { "name": "due_date",           "type": "date"     },
                { "name": "active",             "type": "boolean"  },
                { "name": "complete",           "type": "boolean"  },
                { "name": "completed_at",       "type": "datetime" },
                { "name": "rrule",              "type": "text"     },
                { "name": "rdate",              "type": "text"     },
                { "name": "exrule",             "type": "text"     },
                { "name": "exdate",             "type": "text"     },
                { "name": "recurrence_end",     "type": "date"     },
                { "name": "created_at",         "type": "datetime" },
                { "name": "updated_at",         "type": "datetime" }
            ],
            "input": {
                "entries": [
                    {
                        "exdate": null,
                        "due_date": null,
                        "completed_at": null,
                        "company_id": null,
                        "base_entry_id": null,
                        "type": null,
                        "start_time": null,
                        "id": null,
                        "end_time": null,
                        "all_day": null,
                        "part_of_recurring_series": null,
                        "updated_at": null,
                        "exrule": null,
                        "complete": null,
                        "association_id": null,
                        "rrule": null,
                        "description": null,
                        "category": {
                            "id": null,
                            "name": null
                        },
                        "owner_id": null,
                        "name": null,
                        "created_at": null,
                        "category_id": null,
                        "owner": {
                            "id": null,
                            "full_name": null
                        },
                        "association_type": null,
                        "recurrence_end": null,
                        "google_calendar_id": null,
                        "rdate": null
                    }
                ]
            }
        }
        ';

        $definitions[] = '
        {
            "path": "activities",
            "name": "activities",
            "location": "https://api.pipelinedeals.com/api/v3/notes.json?api_key={key}",
            "content_root": "$.entries",
            "output": [
                { "name": "id",                 "type": "numeric", "width":  15, "scale": 0 },
                { "name": "user_id",            "type": "numeric", "width":  15, "scale": 0 },
                { "name": "user_first",         "type": "text"     },
                { "name": "user_last_name",     "type": "text"     },
                { "name": "title",              "type": "text"     },
                { "name": "company_id",         "type": "numeric", "width":  15, "scale": 0 },
                { "name": "person_id",          "type": "numeric", "width":  15, "scale": 0 },
                { "name": "deal_id",            "type": "numeric", "width":  15, "scale": 0 },
                { "name": "note_category_name", "type": "text"     },
                { "name": "content",            "type": "text"     },
                { "name": "mime",               "type": "text"     },
                { "name": "created_at",         "type": "datetime" },
                { "name": "updated_at",         "type": "datetime" }
            ],
            "input": {
                "entries": [
                    {
                        "company_id": null,
                        "note_category": {
                            "name": null,
                            "id": null
                        },
                        "deal_id": null,
                        "person_id": null,
                        "title": null,
                        "user": {
                            "id": null,
                            "first_name": null,
                            "last_name": null,
                            "avatar_thumb_url": null
                        },
                        "content": null,
                        "note_category_id": null,
                        "user_id": null,
                        "created_at": null,
                        "mime": null,
                        "id": null,
                        "updated_at": null
                    }
                ]
            }
        }
        ';

        $result = array();
        foreach ($definitions as $d)
        {
            $result[] = json_decode($d,true);
        }

        return $result;
    }

    private static function cleanPath(string $path) : string
    {
        $path = trim(strtolower($path));
        return $path;
    }
}
