
import sys
import requests

def flexio_file_handler(input,output):

    # TODO: fill out connection info
    server = ''
    username = ''
    password = ''
    index = ''

    # get each row from the input and upload it as key/value pairs
    input.fetch_style = dict
    for row in input:
        upload_to_elasticsearch(server, username, password, index, row)

def upload_to_elasticsearch(server, username, password, index, data):

    url = server + index
    requests.post(url, auth=(username, password), json=data)
