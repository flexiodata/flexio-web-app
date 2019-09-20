<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-08-30
 *
 * @package flexio
 * @subpackage Services
 */


declare(strict_types=1);
namespace Flexio\Services;


class MailJet implements \Flexio\IFace\IConnection, \Flexio\IFace\IFileSystem
{
    private $authenticated = false;
    private $username = '';
    private $password = '';
    private $pagesize = 1; // rows to request per request; 1000 is maximum allowed per request
    private $request_throttle = 250; // milliseconds to wait between requests; mailjet may limit the requests per minute, so set this to something reasonable

    public static function create(array $params = null) : \Flexio\Services\MailJet
    {
        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'username' => array('type' => 'string', 'required' => true),
                'password' => array('type' => 'string', 'required' => true)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $validated_params = $validator->getParams();
        $username = $validated_params['username'];
        $password = $validated_params['password'];

        $service = new self;
        if ($service->initialize($uername, $password) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_SERVICE);

        return $service;
    }

    ////////////////////////////////////////////////////////////
    // IConnection interface
    ////////////////////////////////////////////////////////////

    public function connect() : bool
    {
        $username = $this->username;
        $password = $this->password;

        if ($service->initialize($username, $password) === false)
            return false;

        return true;
    }

    public function disconnect() : void
    {
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
                'hash' => '', // TODO: available?
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

        $offset = 0;
        while (true)
        {
            $rows = $this->fetchData($path, $offset);
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

    private function fetchData(string $path, int &$offset) // TODO: add return type
    {
        $path = self::cleanPath($path);
        if (!isset($offset))
            $offset = 0;

        // load the definition for the given path
        $definition = $this->lookupDefinition($path);
        if ($definition === false)
            return false;

        $location = $definition['location'];
        $content_root = $definition['content_root'];

        $request = $location;
        $request .= "?offset=$offset";
        $request .= "&limit=$this->pagesize";

        $username = $this->username;
        $password = $this->password;
        $apiauth = "$username:$password";

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $request);
        curl_setopt($ch, CURLOPT_HTTPGET, true);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, $apiauth);
        $result = curl_exec($ch);
        curl_close($ch);

        if ($result === false)
            return false;

        // return rows with uniform keys (flatten will flatten but not guarantee each row has
        // the same keys)
        $structure = $this->describeTable($path);
        $rows = $this->map($content_root, $result, $structure);
        $offset = $offset+$this->pagesize;

        return $rows;
    }

    public function insertRow(string $path, array $row) : bool
    {
        $path = self::cleanPath($path);

        // load the definition for the given path
        $definition = $this->lookupDefinition($path);
        if ($definition === false)
            return false;

        // TODO: factor this out into a reverse mapping;
        // this is coming up again and again: getting some type of input, getting
        // the part we're interested in and passing that on, while supplying defaults
        // to what's missing and igorning stuff we don't take; same as API input
        if (!is_array($row))
            return false;
        if (!isset($row['email']))
            return false;

        $data_to_upload = array();
        $data_to_upload['Email'] = $row['email'];
        $data_to_upload['Name'] = $row['name'] ?? '';

        $location = $definition['location'];
        $request = $location;

        $username = $this->username;
        $password = $this->password;
        $apiauth = "$username:$password";

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $request);
        curl_setopt($ch, CURLOPT_POST, count($data_to_upload));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_to_upload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, $apiauth);
        $result = curl_exec($ch);
        curl_close($ch);

        if ($result === false)
            return false;

        return true;
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

    private function initialize(string $username, string $password) : bool
    {
        // TODO: test api key

        $this->username = $username;
        $this->password = $password;
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

    private function getDefinitions() : array
    {
        // note: "content_root" is the the json path where the data in the return
        // result is located; so "$.Data" means the data is stored in the "Data"
        // node in the root object

        $definitions = array();

        $definitions[] = '
        {
            "path": "contacts",
            "name": "contacts",
            "location": "https://api.mailjet.com/v3/REST/contact",
            "content_root": "$.Data",
            "output": [
                { "name": "ID",                              "type": "numeric", "width":  15, "scale": 0 },
                { "name": "Name",                            "type": "text"      },
                { "name": "Email",                           "type": "text"      },
                { "name": "DeliveredCount",                  "type": "numeric", "width":  15, "scale": 0 },
                { "name": "UnsubscribedBy",                  "type": "text"      },
                { "name": "UnsubscribedAt",                  "type": "datetime"  },
                { "name": "IsExcludedFromCampaigns",         "type": "boolean"   },
                { "name": "IsOptInPending",                  "type": "boolean"   },
                { "name": "IsSpamComplaining",               "type": "boolean"   },
                { "name": "ExclusionFromCampaignsUpdatedAt", "type": "datetime"  },
                { "name": "LastActivityAt",                  "type": "datetime"  },
                { "name": "CreatedAt",                       "type": "datetime"  },
                { "name": "LastUpdateAt",                    "type": "datetime"  }
            ],
            "input": {
            }
        }
        ';

        $definitions[] = '
        {
            "path": "contactslist",
            "name": "contacts-list",
            "location": "https://api.mailjet.com/v3/REST/contactslist",
            "content_root": "$.Data",
            "output": [
                { "name": "ID",                              "type": "numeric", "width":  15, "scale": 0 },
                { "name": "Name",                            "type": "text"      },
                { "name": "Address",                         "type": "text"      },
                { "name": "SubscriberCount",                 "type": "numeric", "width":  15, "scale": 0 },
                { "name": "IsDeleted",                       "type": "boolean"   },
                { "name": "CreatedAt",                       "type": "datetime"  }
            ],
            "input": {
            }
        }
        ';

        $definitions[] = '
        {
            "path": "sender",
            "name": "senders",
            "location": "https://api.mailjet.com/v3/REST/sender",
            "content_root": "$.Data",
            "output": [
                { "name": "ID",                              "type": "numeric", "width":  15, "scale": 0 },
                { "name": "Name",                            "type": "text"      },
                { "name": "Email",                           "type": "text"      },
                { "name": "EmailType",                       "type": "text"      },
                { "name": "DNSID",                           "type": "numeric", "width":  15, "scale": 0 },
                { "name": "Filename",                        "type": "text"      },
                { "name": "IsDefaultSender",                 "type": "boolean"   },
                { "name": "Status",                          "type": "text"      },
                { "name": "CreatedAt",                       "type": "datetime"  }
            ],
            "input": {
            }
        }
        ';

        $definitions[] = '
        {
            "path": "campaign",
            "name": "campaigns",
            "location": "https://api.mailjet.com/v3/REST/campaign",
            "content_root": "$.Data",
            "output": [
                { "name": "ID",                              "type": "numeric", "width":  15, "scale": 0 },
                { "name": "FromID",                          "type": "numeric", "width":  15, "scale": 0 },
                { "name": "FromName",                        "type": "text"      },
                { "name": "FromEmail",                       "type": "text"      },
                { "name": "Subject",                         "type": "text"      },
                { "name": "SendStartAt",                     "type": "datetime"  },
                { "name": "SendEndAt",                       "type": "datetime"  },
                { "name": "ListID",                          "type": "numeric", "width":  15, "scale": 0 },
                { "name": "NewsLetterID",                    "type": "numeric", "width":  15, "scale": 0 },
                { "name": "CampaignType",                    "type": "numeric", "width":  15, "scale": 0 },
                { "name": "ClickTracked",                    "type": "numeric", "width":  15, "scale": 0 },
                { "name": "OpenTracked",                     "type": "numeric", "width":  15, "scale": 0 },
                { "name": "SpamassScore",                    "type": "numeric", "width":  15, "scale": 0 },
                { "name": "UnsubscribeTrackedCount",         "type": "numeric", "width":  15, "scale": 0 },
                { "name": "HasHtmlCount",                    "type": "numeric", "width":  15, "scale": 0 },
                { "name": "HasTxtCount",                     "type": "numeric", "width":  15, "scale": 0 },
                { "name": "IsStarred",                       "type": "boolean"   },
                { "name": "IsDeleted",                       "type": "boolean"   },
                { "name": "Status",                          "type": "text"      },
                { "name": "CustomValue",                     "type": "text"      },
                { "name": "FirstMessageID",                  "type": "text"      },
                { "name": "CreatedAt",                       "type": "datetime"  }
            ],
            "input": {
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
