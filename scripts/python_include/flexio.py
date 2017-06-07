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
    
input_env = InputEnv()


class Input(object):

    # used for setting fetch style
    Dictionary = 1
    Tuple = 2

    def __init__(self, info):
        if info:
            self._name = info['name']
            self._content_type = info['content_type']
            self._size = info['size']
            self._idx = info['idx']
            self._structure = None
            self._dict = False
            self._fetch_style = self.Tuple
            self._casts = None
            self._casting = True
        else:
            self._name = ''
            self._content_type = 'application/octet-stream'
            self._size = 0
            self._idx = -1
            self._structure = None
            self._dict = False
            self._fetch_style = self.Tuple
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
            self._structure = proxy.invoke('getInputStreamStructure', [self._idx])
        return self._structure
    
    @property
    def is_table(self):
        return True if self._content_type == 'application/vnd.flexio.table' else False

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
        if self._fetch_style == self.Tuple:
            row = proxy.invoke('read', [self._idx, length, False])
            if row is False:
                return False
            if self._casting:
                self.type_casts(row)
            return tuple(row)
        else:
            row = proxy.invoke('read', [self._idx, length, True])
            if row is False:
                return False
            if self._casting:
                self.type_casts(row)
            return row

    def __iter__(self):
        return self

    def __next__(self):
        row = self.read()
        if type(row) == type(False):
            if not row:
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
        self._env = {}
        if info:
            self._name = info['name']
            self._content_type = info['content_type']
            self._size = info['size']
            self._idx = info['idx']
        else:
            self._name = ''
            self._content_type = 'application/octet-stream'
            self._size = 0
            self._idx = -1
    
    @property
    def name(self):
        return self._name

    @name.setter
    def name(self, value):
        self._name = value
        proxy.invoke('setOutputStreamInfo', [self._idx, {'name':value}])

    @property
    def content_type(self):
        return self._content_type

    @content_type.setter
    def content_type(self, value):
        self._content_type = value
        proxy.invoke('setOutputStreamInfo', [self._idx, {'content_type':value}])

    @property
    def env(self):
        return self._env

    @env.setter
    def env(self, value):
        self._env = value
        self.header["env"] = self._env

    @property
    def stream(self):
        if not self.header_written:
            str = json.dumps(self.header) + "\r\n\r\n"
            sys.stdout.write(str)
            sys.stdout.flush()
            self.header_written = True
        return sys.stdout

    def create(self, name=None, structure=None, content_type='text/plain'):
        properties = { 'content_type': content_type }
        if name:
            properties['name'] = name
        if structure:
            properties['structure'] = structure
        return proxy.invoke('managedCreate', [self._idx, properties])

    def write(self, data):
        proxy.invoke('write', [self._idx, data])

    def insert_row(self, row):
        proxy.invoke('insertRow', [self._idx, row])
    
    def insert_rows(self, rows):
        proxy.invoke('insertRows', [self._idx, rows])
    
class Inputs(object):
    def __init__(self):
        self.inited = False
        self.inputs = []

    def initialize(self):
        stream_infos = proxy.invoke('getInputStreamInfo', [])
        for info in stream_infos:
            self.inputs.append(Input(info))
            self.inited = True
    
    def __getitem__(self, idx):
        if not self.inited:
            self.initialize()
        return self.inputs[idx]

    @property
    def env(self):
        return input_env

class Outputs(object):
    def __init__(self):
        self.inited = False
        self.outputs = []

    def initialize(self):
        stream_infos = proxy.invoke('getOutputStreamInfo', [])
        for info in stream_infos:
            self.outputs.append(Output(info))
            self.inited = True
    
    def __getitem__(self, idx):
        if not self.inited:
            self.initialize()
        return self.outputs[idx]

    def create(self, name=None, structure=None, content_type='text/plain'):
        properties = { 'content_type': content_type }
        if name:
            properties['name'] = name
        if structure:
            properties['structure'] = structure
        info = proxy.invoke('createOutputStream', [properties])
        output = Output(info)
        self.outputs.append(output)
        return output
    
inputs = Inputs()
outputs = Outputs()

def run_stream(func):
    stream_idx = proxy.invoke('getManagedStreamIndex', [])
    input = inputs[stream_idx]
    output = outputs[stream_idx]
    func(input, output)


def run(func):
    input = Inputs()
    output = Outputs()
    func(input, output)

