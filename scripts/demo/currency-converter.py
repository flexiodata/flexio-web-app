
# ---
# name: currency-converter
# deployed: true
# title: Currency Converter
# description: Returns an amount in one currency type from an amount in another currency type
# params:
#   - name: amt
#     type: number
#     description: The value to convert from one currency to another
#     required: true
#   - name: cur1
#     type: string
#     description: The currency type to convert from
#     required: true
#   - name: cur2
#     type: string
#     description: The currency type to convert to
#     required: true
#   - name: date
#     type: date
#     description: The exchange rate date
#     required: false
# returns:
#   - name: amount
#     type: numeric
#     description: The converted currency amount
# examples:
#   - '100, "USD", "EUR"'
#   - '200, "EUR", "USD", "2018-12-31"'
#   - 'A1, B1, C1'
# notes: |-
#   This function uses the https://exchangeratesapi.io API to convert the rates from one into another.
#
#   The following currency types are allowed: CAD, HKD, ISK, PHP, DKK, HUF, CZK, GBP, RON, SEK, IDR, INR, BRL, RUB, HRK, JPY, THB, CHF, EUR, MYR, BGN, TRY, CNY, NOK, NZD, ZAR, USD, MXN, SGD, AUD, ILS, KRW, PLN
# ---


import json
import requests
from datetime import *
from cerberus import Validator
from collections import OrderedDict

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
    params['amt'] = {'required': True, 'type': 'number', 'coerce': float}
    params['cur1'] = {'required': True, 'validator': validate_currency, 'coerce': lambda s: s.upper()}
    params['cur2'] = {'required': True, 'validator': validate_currency, 'coerce': lambda s: s.upper()}
    params['date'] = {'required': False, 'type': 'date', 'coerce': to_date}
    input = dict(zip(params.keys(), input))

    # validate the mapped input against the validator
    # if the input is valid return an error
    v = Validator(params, allow_unknown = True)
    input = v.validated(input)
    if input is None:
        raise ValueError

    try:

        date = 'latest'
        if 'date' in input.keys():
            date = input['date'].strftime('%Y-%m-%d')

        if input['cur1'] == input['cur2']:
            flex.output.content_type = "application/json"
            flex.output.write([[input['amt']]])
            return

        url = 'https://api.exchangeratesapi.io/'+date+'?base=' + input['cur1']
        response = requests.get(url)
        rates = response.json()['rates']

        conversion_rate = rates[input['cur2']]
        result = [[input['amt']*conversion_rate]]

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
