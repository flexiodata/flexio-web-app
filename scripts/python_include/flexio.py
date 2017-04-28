import sys
import json
import csv

fx_header = ''
for i in range(0,1000):
    fx_header += sys.stdin.buffer.read(1).decode('utf-8')
    if fx_header[-4:] == "\r\n\r\n":
        break
if fx_header[-4:] != "\r\n\r\n":
    sys.stdout.write('{"content_type":"application/octet-stream"}\r\n\r\nMissing payload header')
    sys.exit()
fx_header = json.loads(fx_header)


class Input(object):
    def __init__(self):
        self.header_written = False
        if 'content_type' in fx_header:
            self._content_type = fx_header['content_type']
        else:
            self._content_type = 'application/octet-stream123'

    @property
    def stream(self):
        return sys.stdin

    @property
    def content_type(self):
        return self._content_type

class Output(object):
    def __init__(self):
        self.header_written = False
        self._content_type = 'application/octet-stream'
        self.header = {"content_type": self._content_type}

    @property
    def content_type(self):
        return self._content_type

    @content_type.setter
    def content_type(self, value):
        self._content_type = value
        self.header["content_type"] = self._content_type

    @property
    def stream(self):
        if not self.header_written:
            str = json.dumps(self.header) + "\r\n\r\n"
            sys.stdout.write(str)
            sys.stdout.flush()
            self.header_written = True
        return sys.stdout

    def create_table(self, structure):
        self.content_type = "application/vnd.flexio.table"
        self.header["structure"] = structure
        return csv.writer(self.stream)

input = Input()
output = Output()

