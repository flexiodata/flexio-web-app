import sys
import os
import random
import string
import json
import csv
import datetime
import pprint
import zmq
import base64
from zmq.utils.monitor import recv_monitor_message
from types import ModuleType



class CallProxy(object):
    def __init__(self):
        self.context = zmq.Context()
        self.socket = self.context.socket(zmq.REQ)
        self.socket.connect(os.environ['FLEXIO_RUNTIME_SERVER'])
        self.socket.setsockopt(zmq.RCVTIMEO, 250)
        self.monitor = self.socket.get_monitor_socket(zmq.EVENT_CLOSED)

    def invoke(self, method, params = []):

        call_id = ''.join(random.SystemRandom().choice(string.ascii_letters + string.digits) for _ in range(20))

        #params = [ '~'+call_id+'/bin.b64:' + base64.b64encode(x).decode() if isinstance(x, (bytes, bytearray)) else x for x in params ]
        payload = {
            "version": 1,
            "access_key": os.environ['FLEXIO_RUNTIME_KEY'],
            "method": method,
            "params": params,
            "id": call_id
        }

        moniker = '~' + call_id + '/bin.b64:'
        self.convert_binary_to_base64(payload['params'], moniker)

        self.socket.send(json.dumps(payload).encode())

        while True:
            try:
                resobj = self.socket.recv_json()
                return self.convert_base64_to_binary(resobj['result'], moniker)
            except zmq.ZMQError:
                pass
            
            try:
                evt = recv_monitor_message(self.monitor, zmq.NOBLOCK)
                if evt['event'] == zmq.EVENT_CLOSED:
                    print("Connection broken")
                    sys.exit(1)
            except zmq.ZMQError:
                pass

    def close(self):
        self.socket.close()
        self.socket = None


    def convert_binary_to_base64(self, var, moniker):
        if isinstance(var, list):
            for key, value in enumerate(var):
                var[key] = self.convert_binary_to_base64(var[key], moniker)
        elif isinstance(var, dict):
            for key, value in var.items():
                var[key] = self.convert_binary_to_base64(var[key], moniker)
        elif isinstance(var, (bytes, bytearray)):
            return moniker + base64.b64encode(var).decode()
        return var

    def convert_base64_to_binary(self, var, moniker):
        moniker_len = len(moniker)
        if isinstance(var, list):
            for key, value in enumerate(var):
                var[key] = self.convert_base64_to_binary(var[key], moniker)
        elif isinstance(var, dict):
            for key, value in var.items():
                var[key] = self.convert_base64_to_binary(var[key], moniker)
        elif isinstance(var, str) and var[:moniker_len] == moniker:
            return base64.b64decode(var[moniker_len:].encode())
        return var



proxy = CallProxy()




class EnvVars(object):

    def __init__(self):
        self.fx_inited = False
        self.fx_env = {}

    def initialize(self):
        self.fx_env = proxy.invoke('getEnv', [])
        self.fx_inited = True

    def __getitem__(self, key):
        if not self.fx_inited:
            self.initialize()
        return self.fx_env[key]

    def __getattr__(self, key):
        if not self.fx_inited:
            self.initialize()
        if key in self.fx_env:
            return self.fx_env[key]
        else:
            return None

    def __setitem__(self, key, value):
        if not self.fx_inited:
            self.initialize()
        if proxy.invoke('setEnvValue', [key,value]) is True:
            self.fx_env[key] = value

    def __setattr__(self, key, value):
        if key[:3] == 'fx_':
            return super().__setattr__(key, value)
        if not self.fx_inited:
            self.initialize()
        if proxy.invoke('setEnvValue', [key,value]) is True:
            self.fx_env[key] = value

    def __iter__(self):
        if not self.fx_inited:
            self.initialize()
        return iter(self.fx_env)

    def items(self):
        if not self.fx_inited:
            self.initialize()
        return self.fx_env.items()

    def keys(self):
        if not self.fx_inited:
            self.initialize()
        return self.fx_env.keys()

    def values(self):
        if not self.fx_inited:
            self.initialize()
        return self.fx_env.values()

env_vars_obj = EnvVars()






class Connection(object):
    def __init__(self, info):
        self.eid = info['eid']
        self.alias = info['alias']
        self.name = info['name']
        self.description = info['description']

    def get_access_token(self):
        return proxy.invoke('getConnectionAccessToken', [self.eid])


class ContextConnections(object):

    def __init__(self):
        self.fx_inited = False
        self.fx_connection_list = []
        self.fx_connection_map = {}

    def initialize(self):
        connections = proxy.invoke('getConnections', [])
        for connection in connections:
            connobj = Connection(connection)
            self.fx_connection_map[connobj.eid] = connobj
            self.fx_connection_map[connobj.alias] = connobj
            self.fx_connection_list.append(connobj)
        connections = proxy.invoke('getLocalConnections', [])
        for connection in connections:
            connobj = Connection(connection)
            self.fx_connection_map[connobj.alias] = connobj
        self.fx_inited = True

    def __getitem__(self, key):
        if not self.fx_inited:
            self.initialize()
        return self.fx_connection_map[key]

    def __getattr__(self, key):
        if not self.fx_inited:
            self.initialize()
        if key in self.fx_connection_map:
            return self.fx_connection_map[key]
        else:
            return None

    def __iter__(self):
        if not self.fx_inited:
            self.initialize()
        return iter(self.fx_connection_list)

    def items(self):
        if not self.fx_inited:
            self.initialize()
        return self.fx_connection_list.items()

    def keys(self):
        if not self.fx_inited:
            self.initialize()
        return self.fx_connection_list.keys()

    def values(self):
        if not self.fx_inited:
            self.initialize()
        return self.fx_connection_list.values()

context_connections_obj = ContextConnections()






class ContextFsFile(object):
    
    def __init__(self, handle, writing):
        self.handle = handle
        self.writing = writing
        pass

    def __del__(self):
        if self.handle != 0 and self.writing:
            self.close()

    def read(self, len = None):
        buf = proxy.invoke('read', [self.handle, len])
        if buf is False:
            return False
        return buf

    def write(self, data='', connection=''):
        proxy.invoke('write', [self.handle, data])

    def close(self):
        if self.handle != 0 and self.writing:
            proxy.invoke('fsCommit', [self.handle])
            self.writing = False
            self.handle = False


class ContextFs(object):

    def __init__(self):
        pass

    def create(self, path, connection=''):
        handle = proxy.invoke('fsCreate', [path, connection])
        f = ContextFsFile(handle, writing=True)
        return f

    def open(self, path, connection=''):
        handle = proxy.invoke('fsOpen', ['r+', path, connection])
        f = ContextFsFile(handle, writing=False)
        return f

    def read(self, path, connection=''):
        handle = proxy.invoke('fsOpen', ['r+', path, connection])

        buf = b''
        while True:
            chunk = proxy.invoke('read', [handle, 4096])
            if chunk is False:
                break
            buf += chunk
        return buf

    def write(self, path, data='', connection=''):
 
        handle = proxy.invoke('fsOpen', ['w', path, connection])

        idx = 0
        buf = data
        while len(buf) > 0:
            chunk = buf[:4096]
            buf = buf[4096:]
            proxy.invoke('write', [handle, chunk])
            idx = idx + 1

        handle = proxy.invoke('fsCommit', [handle])

    def exists(self, path, connection=''):
 
        return proxy.invoke('fsExists', [path, connection])

    def remove(self, path, connection=''):
 
        return proxy.invoke('fsRemove', [path, connection])



context_fs = ContextFs()



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
                chunk = proxy.invoke('read', [self._handle, 4096])
                if chunk is False:
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
        if not isinstance(row, dict):
            return row
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

    # general purpose task proxy for testing/debugging
    def task(self, params):
        return proxy.invoke('runJob', [json.dumps(params)])

    # (mostly) standard task bindings
    def connect(self, params):
        params['op'] = 'connect'
        proxy.invoke('runJob', [json.dumps(params)])

    def convert(self, params):
        params['op'] = 'convert'
        proxy.invoke('runJob', [json.dumps(params)])

    def copy(self, params):
        params['op'] = 'copy'
        proxy.invoke('runJob', [json.dumps(params)])

    def create(self, params):
        params['op'] = 'create'
        proxy.invoke('runJob', [json.dumps(params)])

    def delete(self, params):
        params['op'] = 'delete'
        proxy.invoke('runJob', [json.dumps(params)])

    def echo(self, params):
        params['op'] = 'echo'
        proxy.invoke('runJob', [json.dumps(params)])

    def email(self, params):
        params['op'] = 'email'
        proxy.invoke('runJob', [json.dumps(params)])

    def execute(self, params):
        params['op'] = 'execute'
        proxy.invoke('runJob', [json.dumps(params)])

    def exit(self, params):
        params['op'] = 'exit'
        proxy.invoke('runJob', [json.dumps(params)])

    def filter(self, params):
        params['op'] = 'filter'
        proxy.invoke('runJob', [json.dumps(params)])

    def insert(self, params):
        params['op'] = 'insert'
        proxy.invoke('runJob', [json.dumps(params)])

    def list(self, params):
        params['op'] = 'list'
        proxy.invoke('runJob', [json.dumps(params)])

    def mkdir(self, params):
        params['op'] = 'mkdir'
        proxy.invoke('runJob', [json.dumps(params)])

    def read(self, params):
        params['op'] = 'read'
        proxy.invoke('runJob', [json.dumps(params)])

    def render(self, params):
        params['op'] = 'render'
        proxy.invoke('runJob', [json.dumps(params)])

    def rename(self, params):
        params['op'] = 'rename'
        proxy.invoke('runJob', [json.dumps(params)])

    def request(self, params):
        params['op'] = 'request'
        proxy.invoke('runJob', [json.dumps(params)])

    def select(self, params):
        params['op'] = 'select'
        proxy.invoke('runJob', [json.dumps(params)])

    def write(self, params):
        params['op'] = 'write'
        proxy.invoke('runJob', [json.dumps(params)])


class Result(object):
    def __init__(self, output):
        self.output = output

    def json(self, obj):
        self.output.content_type = "application/json"
        self.output.write(json.dumps(obj))
        proxy.close()

    def end(self, content):
        self.output.write(content)
        proxy.close()


class Context(object):
    def __init__(self, input, output):
        self.input = input
        self.output = output
        self._query = None
        self._form = None
        self._files = None
        self.pipe = PipeFunctions()
        self.res = Result(output)

    @property
    def query(self):
        if self._query is None:
            self._query = proxy.invoke('getQueryParameters', [])
        return self._query

    @property
    def form(self):
        if self._form is None:
            self._form = proxy.invoke('getFormParameters', [])
        return self._form

    @property
    def files(self):
        if self._files is None:
            self._files = {}
            fileinfo = proxy.invoke('getFilesParameters', [])
            for key, value in fileinfo.items():
                info  = proxy.invoke('getInputStreamInfo', [key])
                self.files[key] = Input(info)
        return self._files

    @property
    def vars(self):
        return env_vars_obj

    @property
    def fs(self):
        return context_fs

    @property
    def connections(self):
        return context_connections_obj

g_print_output = None
def print_redirect_to_output(*args, **kwargs):
    format_str = '{} ' * len(args)
    format_str = format_str.rstrip(' ')
    val = format_str.format(*args, **kwargs)
    g_print_output.write(val)


import builtins as __builtin__



def create_context():
    stdin_stream_info  = proxy.invoke('getInputStreamInfo', ['_fxstdin_'])
    stdout_stream_info = proxy.invoke('getOutputStreamInfo', ['_fxstdout_'])

    input = Input(stdin_stream_info)
    output = Output(stdout_stream_info)

    return Context(input, output)

def run(handler):

    context = create_context()

    if isinstance(handler, ModuleType):
        func = getattr(handler, 'flex_handler', None)
        if callable(func):
            func(context)
            return
        func = getattr(handler, 'flexio_handler', None)
        if callable(func):
            func(context)
            return
    else:
        handler(context)
