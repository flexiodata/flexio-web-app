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
    def __init__(self, monitor=False):
        self.context = zmq.Context()
        self.socket = self.context.socket(zmq.REQ)
        self.socket.connect(os.environ['FLEXIO_RUNTIME_SERVER'])
        self.socket.setsockopt(zmq.RCVTIMEO, 250)
        if monitor:
            self.monitor = self.socket.get_monitor_socket(zmq.EVENT_CLOSED)
        else:
            self.monitor = None

    def invoke(self, method, params = []):

        call_id = ''.join(random.SystemRandom().choice(string.ascii_letters + string.digits) for _ in range(20))

        payload = {
            "version": 1,
            "access_key": os.environ['FLEXIO_RUNTIME_KEY'],
            "method": method,
            "params": params,
            "id": call_id
        }

        moniker = '~' + call_id + '/'
        self.convert_binary_to_base64(payload['params'], moniker)

        self.socket.send(json.dumps(payload).encode())

        while True:
            try:
                resobj = self.socket.recv_json()
                return self.convert_base64_to_binary(resobj['result'], moniker)
            except zmq.ZMQError:
                pass

            if self.monitor:
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
            return moniker + 'bin.b64:' + base64.b64encode(var).decode()
        elif isinstance(var, object) and getattr(var, '_handle', None) is not None:
            return moniker + 'stream:' + str(var._handle)
        return var

    def convert_base64_to_binary(self, var, moniker):
        moniker_len = len(moniker)
        if isinstance(var, list):
            for key, value in enumerate(var):
                var[key] = self.convert_base64_to_binary(var[key], moniker)
        elif isinstance(var, dict):
            for key, value in var.items():
                var[key] = self.convert_base64_to_binary(var[key], moniker)
        elif isinstance(var, str) and var[:moniker_len+8] == moniker + 'bin.b64:':
            return base64.b64decode(var[moniker_len+8:].encode())
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
        self.name = info['name']
        self.description = info['description']

    def get_credentials(self):
        return proxy.invoke('getConnectionCredentials', [self.eid])

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
            self.fx_connection_map[connobj.name] = connobj
            self.fx_connection_list.append(connobj)
        connections = proxy.invoke('getLocalConnections', [])
        for connection in connections:
            connobj = Connection(connection)
            self.fx_connection_map[connobj.name] = connobj
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



class Stream(object):

    # used for setting fetch style
    Dictionary = 1
    Tuple = 2

    def __init__(self, info):
        if info:
            self._name = info['name']
            self._content_type = info['content_type']
            self._size = info['size']
            self._handle = info['handle']
            self._structure = None
            self._dict = False
            self._fetch_style = self.Dictionary
            self._casts = None
            self._casting = True
            self._need_commit = False
        else:
            self._name = ''
            self._content_type = 'application/octet-stream'
            self._size = 0
            self._handle = -1
            self._structure = None
            self._dict = False
            self._fetch_style = self.Dictionary
            self._casts = None
            self._casting = True
            self._need_commit = False

    def __del__(self):
        if self._handle != 0 and self._need_commit:
            self.close()

    @property
    def name(self):
        return self._name

    @name.setter
    def name(self, value):
        self._name = value
        proxy.invoke('setStreamInfo', [self._handle, {'name':value}])

    @property
    def content_type(self):
        return self._content_type

    @content_type.setter
    def content_type(self, value):
        self._content_type = value
        proxy.invoke('setStreamInfo', [self._handle, {'content_type':value}])

    @property
    def size(self):
        return self._size

    @property
    def structure(self):
        if not self._structure:
            self._structure = proxy.invoke('getInputStreamStructure', [self._handle])
        return self._structure

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

    def read(self, length=None):
        if length is None:
            return self.readall()
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

    def write(self, data):
        proxy.invoke('write', [self._handle, data])

    def insert_row(self, row):
        proxy.invoke('insertRow', [self._handle, row])

    def insert_rows(self, rows):
        proxy.invoke('insertRows', [self._handle, rows])

    def close(self):
        if self._handle != 0 and self._need_commit:
            proxy.invoke('fsCommit', [self._handle])
            self._need_commit = False



class ContextFs(object):

    def __init__(self):
        pass

    def create(self, path, connection=''):
        info = proxy.invoke('fsCreate', [path, connection])
        stream = Stream(info)
        stream._need_commit = True
        return

    def create_tempfile(self):
        info = proxy.invoke('fsCreateTempFile', [])
        stream = Stream(info)
        stream._need_commit = True
        return stream

    def open(self, path, mode='r', connection=''):
        info = proxy.invoke('fsOpen', [mode, path, connection])
        stream = Stream(info)
        if mode == 'w' or mode == 'w+' or mode == 'a' or mode == 'a+':
            stream._need_commit = True
        return stream

    def read(self, path, connection=''):
        info = proxy.invoke('fsOpen', ['r', path, connection])
        stream = Stream(info)
        return stream.readall()

    def write(self, path, data='', connection=''):

        info = proxy.invoke('fsOpen', ['w', path, connection])
        handle = info['handle']

        idx = 0
        buf = data
        while len(buf) > 0:
            chunk = buf[:4096]
            buf = buf[4096:]
            proxy.invoke('write', [handle, chunk])
            idx = idx + 1

        handle = proxy.invoke('fsCommit', [handle])


    def list(self, path, connection=''):
        return proxy.invoke('fsList', [path, connection])

    def exists(self, path, connection=''):
        return proxy.invoke('fsExists', [path, connection])

    def remove(self, path, connection=''):
        return proxy.invoke('fsRemove', [path, connection])



class ContextEmail(object):

    def __init__(self):
        pass

    def send(self, *args, **kwargs):
        info = proxy.invoke('invoke_service', ['email.send', args[0] if len(args) == 1 else kwargs])



class ContextKeyValue(object):
    def __init__(self):
        pass

    def set(self, k, v):
        return proxy.invoke('kvSet', [k, v])

    def get(self, k):
        return proxy.invoke('kvGet', [k])

    def incr(self, k, v=1):
        return proxy.invoke('kvIncr', [k,v])

    def decr(self, k, v=1):
        return proxy.invoke('kvDecr', [k,v])



class ContextIndex(object):

    def __init__(self):
        pass

    def create(self, name, params={}):
        return proxy.invoke('indexCreate', [name, json.dumps(params)])

    def remove(self, name):
        return proxy.invoke('indexRemove', [name])

    def clear(self, name):
        return proxy.invoke('indexClear', [name])

    def insert(self, name, params):
        return proxy.invoke('indexInsert', [name, json.dumps(params)])

    def exists(self, name):
        return proxy.invoke('indexExists', [name])

    def rename(self, old_name, new_name):
        return proxy.invoke('indexRename', [old_name, new_name])


class Context(object):
    def __init__(self):
        self._input = None
        self._output = None
        self._query = None
        self._form = None
        self._files = None
        self.email = ContextEmail()
        self.fs = ContextFs()
        self.kv = ContextKeyValue()
        self.index = ContextIndex()

    @property
    def input(self):
        if self._input is None:
            stdin_stream_info  = proxy.invoke('getStreamInfo', [0])
            self._input = Stream(stdin_stream_info)
        return self._input

    @property
    def output(self):
        if self._output is None:
            stdout_stream_info  = proxy.invoke('getStreamInfo', [1])
            self._output = Stream(stdout_stream_info)
        return self._output

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
            fileinfos = proxy.invoke('getFilesParameters', [])
            for fileinfo in fileinfos:
                self.files[fileinfo['tag']] = Stream(fileinfo)
        return self._files

    @property
    def vars(self):
        return env_vars_obj

    @property
    def connections(self):
        return context_connections_obj

    def json(self, obj):
        self.output.content_type = "application/json"
        self.output.write(json.dumps(obj))
        proxy.close()
        sys.exit()

    def status(self, code):
        fileinfos = proxy.invoke('status', [code])

    def end(self, content=None, content_type=None):
        if content_type is not None:
            self.output.content_type = content_type
        if content is not None:
            self.output.write(content)
        proxy.close()
        sys.exit()



def run(handler):

    context = Context()

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
