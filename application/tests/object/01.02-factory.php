<?php
/**
 *
 * Copyright (c) 2020, Flex Research LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2020-06-10
 *
 * @package flexio
 * @subpackage Tests
 */


declare(strict_types=1);
namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        // TEST: pipe info extraction from files

        // BEGIN TEST
        $actual = \Flexio\Object\Factory::getPipeInfoFromContent(self::SAMPLE_CONTACTS_CONTENT_PY, 'py');
        $expected = '
        {
            "name": "flex-sample-contacts",
            "deploy_mode": "R",
            "run_mode": "I",
            "title": "Flex Sample Contacts",
            "description": "Returns sample contact information",
            "task": {
                "op": "sequence"
            }
        }';
        \Flexio\Tests\Check::assertInArray('A.1', '\Flexio\Object\Factory::getPipeInfoFromContent(); check that properties are properly read',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Object\Factory::getPipeInfoFromContent(self::SAMPLE_CONTACTS_CONTENT_YML, 'yml');
        $expected = '
        {
            "name": "flex-sample-contacts",
            "deploy_mode": "R",
            "run_mode": "I",
            "title": "Flex Sample Contacts",
            "description": "Returns sample contact information",
            "task": {
                "op": "execute",
                "lang": "python"
            }
        }';
        \Flexio\Tests\Check::assertInArray('A.2', '\Flexio\Object\Factory::getPipeInfoFromContent(); check that properties are properly read',  $actual, $expected, $results);
    }

    private const SAMPLE_CONTACTS_CONTENT_PY = <<<EOD
# ---
# name: flex-sample-contacts
# deployed: true
# config: index
# title: Flex Sample Contacts
# description: Returns sample contact information
# params:
#   - name: properties
#     type: string
#     description: The properties to return, given as a string or array; defaults to all properties; see "Returns" for available properties
#     required: false
#   - name: search
#     type: string
#     description: Search query to determine the rows to return, given as a string or array
#     required: false
# returns:
#   - name: id
#     type: integer
#     description: The id of the contact
#   - name: first_name
#     type: string
#     description: The first name of the contact
#   - name: last_name
#     type: string
#     description: The last name of the contact
#   - name: street_address
#     type: string
#     description: The street address of the contact
#   - name: city
#     type: string
#     description: The city where the contact is located
#   - name: state
#     type: string
#     description: The state where the contact is located
#   - name: zipcode
#     type: string
#     description: The zipcode where the contact is located
#   - name: phone
#     type: string
#     description: The phone number of the contact
#   - name: email
#     type: string
#     description: The email of the contact
#   - name: birthday
#     type: string
#     description: The birthday of the contact
# examples:
#   - '""'
#   - '"first_name, last_name, email"'
#   - '"first_name, city, state, zipcode", "CA"'
# ---

import csv
import json
import requests
from requests.adapters import HTTPAdapter
from requests.packages.urllib3.util.retry import Retry
from contextlib import closing
from collections import OrderedDict
from datetime import *

def flex_handler(flex):

    # configuration
    params = {
        'url': 'https://raw.githubusercontent.com/flexiodata/data/master/sample/sample-contacts.csv'
    }

    # set the output type to ndjson for loading into an index
    flex.output.content_type = 'application/x-ndjson'

    # get the data for each line in each file and write it to
    # stdout with one json object per-line (ndjson) for loading
    # into an index
    for row in get_data(params):
        item = json.dumps(row) + "\n"
        flex.output.write(item)

def get_data(params):

    # get the data
    headers = {
        'User-Agent': 'Flex.io Web App'
    }
    request = requests_retry_session().get(params['url'], stream=True, headers=headers)
    with closing(request) as r:
        # get each line and return a dictionary item for each line
        f = (line.decode('utf-8-sig') for line in r.iter_lines())
        reader = csv.DictReader(f, delimiter=',', quotechar='"')
        for row in reader:
            data = get_item(row)
            yield data

def get_item(row):

    # convert keys to lowercase and make sure the values are formatted
    row = {k.lower(): v for k, v in row.items()}

    item = OrderedDict()
    item['id'] = to_number(row.get('id'))
    item['first_name'] = row.get('first_name')
    item['last_name'] = row.get('last_name')
    item['street_address'] = row.get('street_address')
    item['city'] = row.get('city')
    item['state'] = row.get('state')
    item['zipcode'] = row.get('zipcode','').rjust(5,'0') # pad zipcodes
    item['phone'] = row.get('phone')
    item['email'] = row.get('email')
    item['birthday'] = to_date(row.get('birthday'))
    return item

def requests_retry_session(
    retries=3,
    backoff_factor=0.3,
    status_forcelist=(500, 502, 504),
    session=None,
):
    session = session or requests.Session()
    retry = Retry(
        total=retries,
        read=retries,
        connect=retries,
        backoff_factor=backoff_factor,
        status_forcelist=status_forcelist,
    )
    adapter = HTTPAdapter(max_retries=retry)
    session.mount('http://', adapter)
    session.mount('https://', adapter)
    return session

def to_date(value):
    try:
        return datetime.strptime(value, '%m/%d/%Y').strftime('%Y-%m-%d')
    except:
        return value

def to_number(value):
    try:
        return float(value)
    except:
        return value

EOD;


private const SAMPLE_CONTACTS_CONTENT_YML = <<<EOD
name: flex-sample-contacts
deployed: true
config: index
title: Flex Sample Contacts
description: Returns sample contact information
params:
  - name: properties
    type: string
    description: The properties to return, given as a string or array; defaults to all properties; see "Returns" for available properties
    required: false
  - name: search
    type: string
    description: Search query to determine the rows to return, given as a string or array
    required: false
returns:
  - name: id
    type: integer
    description: The id of the contact
  - name: first_name
    type: string
    description: The first name of the contact
  - name: last_name
    type: string
    description: The last name of the contact
  - name: street_address
    type: string
    description: The street address of the contact
  - name: city
    type: string
    description: The city where the contact is located
  - name: state
    type: string
    description: The state where the contact is located
  - name: zipcode
    type: string
    description: The zipcode where the contact is located
  - name: phone
    type: string
    description: The phone number of the contact
  - name: email
    type: string
    description: The email of the contact
  - name: birthday
    type: string
    description: The birthday of the contact
examples:
  - '""'
  - '"first_name, last_name, email"'
  - '"first_name, city, state, zipcode", "CA"'
task:
  op: execute
  lang: python
EOD;
}
