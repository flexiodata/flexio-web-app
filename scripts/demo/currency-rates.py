
# ---
# name: currency-rates
# deployed: true
# title: Currency Exchange Rates
# description: Returns a list of currency exchange rates for a particular currency and date
# params:
#   - name: cur
#     type: string
#     description: The currency type to use as the base
#     required: true
#   - name: date
#     type: date
#     description: The exchange rate date
#     required: false
# returns:
#   - name: currency
#     type: string
#     description: The type of currency
#   - name: amount
#     type: numeric
#     description: The currency exchange rate
# examples:
#   - '"USD"'
#   - '"EUR", "2018-12-31"'
#   - 'A1, B1'
# notes: |-
#   This function uses the https://exchangeratesapi.io API to list the rates.
#
#   The following currency types are allowed: CAD, HKD, ISK, PHP, DKK, HUF, CZK, GBP, RON, SEK, IDR, INR, BRL, RUB, HRK, JPY, THB, CHF, EUR, MYR, BGN, TRY, CNY, NOK, NZD, ZAR, USD, MXN, SGD, AUD, ILS, KRW, PLN
# ---


import json
import requests
from datetime import *
from cerberus import Validator
from collections import OrderedDict
from operator import itemgetter

# main function entry point
def flexio_handler(flex):

    # get the input
    input = flex.input.read()
    try:
        input = json.loads(input)
        if not isinstance(input, list): raise ValueError
    except ValueError:
        raise ValueError

    # define the expected parameters and map the values to the parameter names
    # based on the positions of the keys/values
    params = OrderedDict()
    params['cur'] = {'required': True, 'validator': validate_currency, 'coerce': lambda s: s.upper()}
    params['date'] = {'required': False, 'type': 'date', 'coerce': to_date}
    input = dict(zip(params.keys(), input))

    # validate the mapped input against the validator
    # if the input is valid return an error
    v = Validator(params, allow_unknown = True)
    input = v.validated(input)
    if input is None:
        raise ValueError

    try:

        date = 'latest';
        if 'date' in input.keys():
            date = input['date'].strftime('%Y-%m-%d')

        url = 'https://api.exchangeratesapi.io/'+date+'?base=' + input['cur']
        response = requests.get(url)
        rates = response.json()['rates']

        items = list()
        for currency, amount in rates.items():
            items.append([currency, amount])
        items = sorted(items, key=itemgetter(0))

        result = [['currency','amount']]
        result.extend(items)

        flex.output.content_type = "application/json"
        flex.output.write(result)

    except:
        raise RuntimeError


def validate_currency(field, value, error):
    currency_types = [
        'CAD','HKD','ISK','PHP','DKK','HUF','CZK','GBP','RON','SEK',
        'IDR','INR','BRL','RUB','HRK','JPY','THB','CHF','EUR','MYR',
        'BGN','TRY','CNY','NOK','NZD','ZAR','USD','MXN','SGD','AUD',
        'ILS','KRW','PLN'
    ]
    if any(value in c for c in currency_types):
        return
    error(field, 'is an invalid currency type')

def to_date(value):
    # if we have a number, treat it as numeric date value from
    # a spreadsheet (days since 1900; e.g. 1 is 1900-01-01)
    if isinstance(value, (int, float)):
        return datetime(1900,1,1) + timedelta(days=(value-1))
    if isinstance(value, str):
        return datetime.strptime(value, '%Y-%m-%d')
    return value
