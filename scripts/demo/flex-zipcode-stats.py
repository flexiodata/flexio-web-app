
# ---
# name: flex-zipcode-stats
# deployed: true
# config: index
# title: Flex Zipcode Stats
# description: Returns zipcode statistic information
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
#   - name: zipcode
#     type: string
#     description: The zip code number
#   - name: city
#     type: string
#     description: The city associated with the zip code
#   - name: state
#     type: string
#     description: The state identifier where the zip code is located
#   - name: state_name
#     type: string
#     description: The state name where the zip code is located
#   - name: latitude
#     type: number
#     description: The latitude for the zip code
#   - name: longitude
#     type: number
#     description: The longitude for the zip code
#   - name: population_2010
#     type: integer
#     description: The population in 2010 for the zip code
#   - name: land_sq_miles
#     type: number
#     description: The land area in square miles for the zip code
#   - name: density_per_sq_mile
#     type: number
#     description: The population density per square mile for the zip code
# examples:
#   - '""'
#   - '"zipcode, population_2010"'
#   - '"", "CA"'
# ---

import csv
import json
import requests
from requests.adapters import HTTPAdapter
from requests.packages.urllib3.util.retry import Retry
from contextlib import closing
from collections import OrderedDict

def flex_handler(flex):

    # configuration
    params = {
        'url': 'https://raw.githubusercontent.com/flexiodata/data/master/sample/zipcode-stats.csv'
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
    item['zipcode'] = row.get('zipcode')
    item['city'] = row.get('city')
    item['state'] = row.get('state')
    item['state_name'] = row.get('state_name')
    item['latitude'] = to_number(row.get('latitude'))
    item['longitude'] = to_number(row.get('longitude'))
    item['population_2010'] = to_number(row.get('population_2010'))
    item['land_sq_miles'] = to_number(row.get('land_sq_miles'))
    item['density_per_sq_mile'] = to_number(row.get('density_per_sq_mile'))
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

def to_number(value):
    try:
        return float(value)
    except:
        return value
