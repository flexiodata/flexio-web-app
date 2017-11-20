import sys
import json
import csv
import datetime


import pprint

class StdinoutProxy(object):
    def __init__(self):
        self.inited = False

    def invoke(self, func, params):
        payload = self.encodepart(func)
        for param in params:
            payload = payload + self.encodepart(param)
        self.send_message(payload)
        response = self.read_message()
        result, nextoff = self.decodepart(response, 0)
        return result
        #print("Received reply %s" % response)

    def send_message(self, payload):
        msg = b'--MSGqQp8mf~' + str(len(payload)).encode('utf-8') + b',' + payload
        sys.stdout.buffer.write(msg)
        sys.stdout.flush()

    def read_message(self):
        buf = b''
        while True:
            buf += sys.stdin.buffer.read(1)
            if len(buf) > 100:
                break
            if buf == b'--MSGqQp8mf~':
                lenstr = b''
                while True:
                    ch = sys.stdin.buffer.read(1)
                    if ch < b'0' or ch > b'9':
                        break
                    lenstr = lenstr + ch
                if ch != b',':
                    #print("***" + ch.decode() + "***" + lenstr.decode())
                    sys.exit(1)
                    return None
                msglen = int(lenstr.decode())
                return sys.stdin.buffer.read(msglen)
        return None
    
    def encodepart(self, var):
        if var is None:
            return b'N0,'
        type = b's'
        if isinstance(var, (bytes,bytearray)):
            type = b'B'
        if isinstance(var, bool):
            type = b'b'
            var = b'1' if var else b'0' 
        elif isinstance(var, int):
            type = b'i'
            var = str(var).encode('utf-8')
        elif isinstance(var, str):
            type = b's'
            var = var.encode('utf-8')
        elif isinstance(var, list):
            type = b'a'
            arraypayload = b''
            for l in var:
                arraypayload = arraypayload + self.encodepart(l)
            var = arraypayload
        elif isinstance(var, dict):
            type = b'o'
            objpayload = b''
            for key,value in var.items():
                objpayload = objpayload + self.encodepart(key) + self.encodepart(value)
            var = objpayload
        return type + str(len(var)).encode('utf-8') + b',' + var

    def decodeparts(self, vars):
        off = 0
        res = []
        while off is not None:
            if off >= len(vars):
                break
            content, off = self.decodepart(vars, off)
            res.append(content)
        return res

    def decodepart(self, var, offset):
        commapos = var.find(b',', offset)
        if commapos == -1:
            return None, None
        type = chr(var[offset])
        lenpart = int(var[offset+1:commapos].decode())
        start = commapos+1
        end = start+lenpart
        part = var[start:end]
        if end >= len(var):
            end = None
        if type == 'i':
            return int(part.decode('utf-8')), end
        elif type == 'b':
            return False if part == b'0' else True, end
        elif type == 'B':
            return part, end
        elif type == 's':
            return part.decode('utf-8'), end
        elif type == 'a':
            return self.decodeparts(part), end
        elif type == 'o':
            off = 0
            res = {}
            while off is not None:
                key, off = self.decodepart(part, off)
                if off is not None:
                    value, off = self.decodepart(part, off)
                    res[key] = value
            return res, end
        elif type == 'N':
            return None, end

proxy = StdinoutProxy()




class InputEnv(object):

    def __init__(self):
        self.inited = False
        self.env = {}

    def initialize(self):
        self.env = proxy.invoke('getInputEnv', [])
        self.inited = True

    def __getitem__(self, key):
        if not self.inited:
            self.initialize()
        return self.env[key]

    def __iter__(self):
        if not self.inited:
            self.initialize()
        return iter(self.env)

    def items(self):
        if not self.inited:
            self.initialize()
        return self.env.items()

    def keys(self):
        if not self.inited:
            self.initialize()
        return self.env.keys()
    
    def values(self):
        if not self.inited:
            self.initialize()
        return self.env.values()

class OutputEnv(object):

    def __init__(self):
        self.inited = False
        self.env = {}

    def initialize(self):
        self.env = proxy.invoke('getOutputEnv', [])
        self.inited = True

    def __getitem__(self, key):
        if not self.inited:
            self.initialize()
        return self.env[key]

    def __setitem__(self, key, value):
        if not self.inited:
            self.initialize()
        if proxy.invoke('setOutputEnvValue', [key,value]) is True:
            self.env[key] = value
    
    def __iter__(self):
        if not self.inited:
            self.initialize()
        return iter(self.env)

    def items(self):
        if not self.inited:
            self.initialize()
        return self.env.items()

    def keys(self):
        if not self.inited:
            self.initialize()
        return self.env.keys()
    
    def values(self):
        if not self.inited:
            self.initialize()
        return self.env.values()

input_env = InputEnv()
output_env = OutputEnv()

class Input(object):

    # used for setting fetch style
    Dictionary = 1
    Tuple = 2

    def __init__(self, info):
        if info:
            self._name = info['name']
            self._content_type = info['content_type']
            self._is_table = True if self._content_type == 'application/vnd.flexio.table' else False
            self._size = info['size']
            self._handle = info['handle']
            self._structure = None
            self._dict = False
            self._fetch_style = self.Dictionary
            self._casts = None
            self._casting = True
        else:
            self._name = ''
            self._content_type = 'application/octet-stream'
            self._is_table = False
            self._size = 0
            self._handle = -1
            self._structure = None
            self._dict = False
            self._fetch_style = self.Dictionary
            self._casts = None
            self._casting = True
        
    @property
    def env(self):
        return input_env

    @property
    def name(self):
        return self._name

    @property
    def content_type(self):
        return self._content_type

    @property
    def size(self):
        return self._size
    
    @property
    def structure(self):
        if not self.is_table:
            return None
        if not self._structure:
            self._structure = proxy.invoke('getInputStreamStructure', [self._handle])
        return self._structure
    
    @property
    def is_table(self):
        return self._is_table

    @property
    def fetch_style(self):
        return self._fetch_style

    @fetch_style.setter
    def fetch_style(self, value):
        self._casts = None
        if type({}) == value:
            self._fetch_style = self.Dictionary
        elif type(()) == value:
            self._fetch_style = self.Tuple
        else:
            raise ValueError("fetch style must be set to dict or tuple")

    @property
    def casting(self):
        return self._casting

    @casting.setter
    def casting(self, value):
        self._casting = value

    def read(self, length=None):
        if length is None:
            return self.readall()
        if self._is_table:
            return None
        data = proxy.invoke('read', [self._handle, length])
        if data is False:
            return b''
        return data

    def readline(self):
        if self._fetch_style == self.Tuple:
            row = proxy.invoke('readline', [self._handle, False])
            if row is False:
                return None
            if self._casting:
                self.type_casts(row)
            return tuple(row)
        else:
            row = proxy.invoke('readline', [self._handle, True])
            if row is False:
                return None
            if self._casting:
                self.type_casts(row)
            return row
    
    def readall(self):
        if self.is_table:
            rows = []
            while True:
                row = self.readline()
                if row is None:
                    break
                rows.append(row)
            return rows
        else:
            buf = b''
            while True:
                chunk = self.read(length=4096)
                if chunk is None:
                    break
                buf += chunk
            return buf

    def __iter__(self):
        return self

    def __next__(self):
        row = self.readline()
        if row is None:
            raise StopIteration
        return row

    def type_casts(self, row):
        if self._casts is None:
            self._casts = {}
            structure = self.structure
            idx = 0
            for col in self.structure:
                if self._fetch_style == self.Tuple:
                    key = idx
                else:
                    key = col['name']
                if col['type'] == 'numeric':
                    self._casts[key] = float
                elif col['type'] == 'integer':
                    self._casts[key] = int
                elif col['type'] == 'date':
                    self._casts[key] = lambda s: datetime.datetime.strptime(s, '%Y-%m-%d').date()
                elif col['type'] == 'datetime':
                    self._casts[key] = lambda s: datetime.datetime.strptime(s, '%Y-%m-%d')
                idx = idx + 1
        for key,func in self._casts.items():
            try:
                row[key] = func(row[key])
            except IndexError:
                continue
            except KeyError:
                continue

class Output(object):
    def __init__(self, info):
        if info:
            self._name = info['name']
            self._content_type = info['content_type']
            self._size = info['size']
            self._handle = info['handle']
        else:
            self._name = ''
            self._content_type = 'application/octet-stream'
            self._size = 0
            self._handle = -1
    
    @property
    def name(self):
        return self._name

    @name.setter
    def name(self, value):
        self._name = value
        proxy.invoke('setOutputStreamInfo', [self._handle, {'name':value}])

    @property
    def content_type(self):
        return self._content_type

    @content_type.setter
    def content_type(self, value):
        self._content_type = value
        proxy.invoke('setOutputStreamInfo', [self._handle, {'content_type':value}])

    @property
    def env(self):
        return output_env

    def create(self, name=None, content_type='text/plain', structure=None):
        properties = { 'content_type': content_type }
        if name:
            properties['name'] = name
        if structure:
            properties['structure'] = structure
        if proxy.invoke('managedCreate', [self._handle, properties]) is False:
            return False
        else:
            return self

    def write(self, data):
        proxy.invoke('write', [self._handle, data])

    def insert_row(self, row):
        proxy.invoke('insertRow', [self._handle, row])
    
    def insert_rows(self, rows):
        proxy.invoke('insertRows', [self._handle, rows])


class PipeFunctions(object):
    def __init__(self):
        pass

    def echo(self, msg):
        definition = {
            "type": "flexio.echo",
            "params": {
                "msg": msg
            }
        }
        proxy.invoke('runJob', [json.dumps(definition)])



class Context(object):
    def __init__(self, input, output):
        self.input = input
        self.output = output
        self._query = None
        self.pipe = PipeFunctions()

    @property
    def query(self):
        if self._query is None:
            self._query = proxy.invoke('getQueryParameters', [])
        return self._query


def run(handler):

    stdin_stream_info  = proxy.invoke('getInputStreamInfo', ['_fxstdin_'])
    stdout_stream_info = proxy.invoke('getOutputStreamInfo', ['_fxstdout_'])

    input = Input(stdin_stream_info)
    output = Output(stdout_stream_info)
    
    context = Context(input, output)

    handler(context)


