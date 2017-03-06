<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-08-30
 *
 * @package flexio
 * @subpackage Services
 */


namespace Flexio\Services;


require_once __DIR__ . DIRECTORY_SEPARATOR . 'Abstract.php';

class MailJet implements \Flexio\Services\IConnection
{
    private $is_ok = false;
    private $username = '';
    private $password = '';
    private $pagesize = 1; // rows to request per request; 1000 is maximum allowed per request
    private $request_throttle = 250; // milliseconds to wait between requests; mailjet may limit the requests per minute, so set this to something reasonable


    ////////////////////////////////////////////////////////////
    // IConnection interface
    ////////////////////////////////////////////////////////////

    public static function create($params = null)
    {
        $service = new self;

        if (isset($params))
            $service->connect($params);

        return $service;
    }

    public function connect($params)
    {
        $this->close();

        $validator = \Flexio\System\Validator::getInstance();
        if (($params = $validator->check($params, array(
                'username' => array('type' => 'string', 'required' => true),
                'password' => array('type' => 'string', 'required' => true)
            ))) === false)
            return false;

        $this->initialize($params);
        return $this->isOk();
    }

    public function isOk()
    {
        return $this->is_ok;
    }

    public function close()
    {
        $this->is_ok = false;
        $this->username = '';
        $this->password = '';
    }

    public function listObjects($path = '')
    {
        if (!$this->isOk())
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
                'is_dir' => false,
                'root' => 'mailjet'
            );
        }

        return $objects;
    }

    public function exists($path)
    {
        // TODO: implement
        return false;
    }

    public function getInfo($path)
    {
        // TODO: implement
        return false;
    }

    public function read($path, $callback)
    {
        if (!$this->isOk())
            return false;

        // TODO: only read the buffer amount
        // TODO: limit the request rate

        $page = false;
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

    public function write($path, $callback)
    {
        // TODO: implement
    }


    ////////////////////////////////////////////////////////////
    // additional functions
    ////////////////////////////////////////////////////////////

    public function describeTable($path)
    {
        if (!$this->isOk())
            return false;

        $path = self::cleanPath($path);

        // load the definition for the given path
        $definition = $this->lookupDefinition($path);
        if ($definition === false)
            return false;

        $structure = $definition['output'];
        return $structure;
    }

    private function fetchData($path, &$offset)
    {
        $path = self::cleanPath($path);
        if ($offset === false)
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
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_SSLVERSION, 1);
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

    public function insertRow($path, $row)
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
        $data_to_upload['Name'] = isset_or($row['name'],'');

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
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_SSLVERSION, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, $apiauth);
        $result = curl_exec($ch);
        curl_close($ch);

        if ($result === false)
            return false;

        return true;
    }

    private function map($content_root, $apidata, $structure)
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
            $r = \Flexio\System\Mapper::flatten($r, null, '_');
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

    private function initialize($params)
    {
        // TODO: test api key

        $this->close();
        $this->username = isset_or($params['username'],'');
        $this->password = isset_or($params['password'],'');
        $this->is_ok = true;
    }

    private function lookupDefinition($path)
    {
        $definitions = $this->getDefinitions();
        foreach ($definitions as $d)
        {
            if ($d['path'] === $path)
                return $d;
        }

        return false;
    }

    private function getRowData($content_root, $apidata)
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

    private function getDefinitions()
    {
        // note: "content_root" is the the json path where the data in the return
        // result is located; so "$.Data" means the data is stored in the "Data"
        // node in the root object

        $definitions = array();

        $definitions[] = '
        {
            "path": "contacts",
            "name": "Contacts",
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
            "name": "Contacts List",
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
            "name": "Senders",
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
            "name": "Campaigns",
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

    private static function cleanPath($path)
    {
        $path = trim(strtolower($path));
        return $path;
    }
}
