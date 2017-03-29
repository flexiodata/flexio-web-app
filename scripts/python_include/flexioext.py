import sys as fx_sys
import json as fx_json

fx_header = ''
for fx_i in range(0,1000):
    fx_header += fx_sys.stdin.buffer.read(1).decode('utf-8')
    if fx_header[-4:] == "\r\n\r\n":
        break
if fx_header[-4:] != "\r\n\r\n":
    fx_sys.stdout.write('{"content_type":"application/octet-stream"}\r\n\r\nMissing payload header')
    fx_sys.exit()
fx_header = fx_json.loads(fx_header)


class Input(object):
    def __init__(self):
        self.header_written = False
        if 'content_type' in fx_header:
            self._content_type = fx_header['content_type']
        else:
            self._content_type = 'application/octet-stream123'

    @property
    def stream(self):
        return fx_sys.stdin

    @property
    def content_type(self):
        return self._content_type

class Output(object):
    def __init__(self):
        self.header_written = False
        self._content_type = 'application/octet-stream'

    @property
    def content_type(self):
        return self._content_type

    @content_type.setter
    def content_type(self, value):
        self._content_type = value

    @property
    def stream(self):
        if not self.header_written:
            str = fx_json.dumps({"content_type": self._content_type}) + "\r\n\r\n"
            fx_sys.stdout.write(str)
            fx_sys.stdout.flush()
            self.header_written = True
        return fx_sys.stdout

input = Input()
output = Output()

