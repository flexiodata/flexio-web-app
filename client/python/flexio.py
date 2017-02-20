import requests
import pprint

class Flexio:
    def __init__(self, api_key = None, host = "www.flex.io"):
        self.api_key = api_key
        self.host = host
        self.pipe = ''

    def test(self):
        response = requests.get("https://%s/api/v1/projects" % self.host, verify=False, headers={'Authorization': "Bearer %s" % self.api_key})
        print(response.text)

    def hello(self):
        print("API key is %s" % self.api_key)

    def setApiKey(value):
        self.api_key = value
        return self

    def setHost(value):
        self.host = value
        return self

    def setPipe(value):
        self.pipe = value
        return self

    def addFile(value):
        return self

    def run():
        return true
