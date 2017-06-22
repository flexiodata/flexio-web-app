
import json
import requests
from bs4 import BeautifulSoup

def flexio_handler(inputs,outputs):

    input = inputs[0]
    output = outputs.create('links.json', content_type='application/json')

    url = 'https://www.flex.io'
    response = requests.get(url)
    result = parse_content(response.text)
    output.write(json.dumps(result))

def parse_content(content):

    result = []
    soup = BeautifulSoup(content, "html.parser")
    for item in soup.find_all('a'):
      href = item.get('href')
      i = {"link": href}
      result.append(i)

    return result

