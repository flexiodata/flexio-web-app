import sys
import json
import csv
import datetime


class TableReader(object):
    def __init__(self, reader, casts):
        self.reader = reader
        self.casts = casts

    def __iter__(self):
        return self

    def __next__(self):
        row = next(self.reader)
        if row:
            for key,value in self.casts.items():
                row[key] = value(row[key])
            return row
        else:
            raise StopIteration

class Input(object):
    def __init__(self):
        self.inited = False

    def initialize(self):

        input_header = ''
        for i in range(0,10000):
            input_header += sys.stdin.buffer.read(1).decode('utf-8')
            if input_header[-4:] == "\r\n\r\n":
                break
        if input_header[-4:] != "\r\n\r\n":
            sys.stdout.write('{"content_type":"application/octet-stream"}\r\n\r\nMissing payload header')
            sys.exit()
        input_header = json.loads(input_header)

        self.header_written = False
        if 'content_type' in input_header:
            self._content_type = input_header['content_type']
        else:
            self._content_type = 'application/octet-stream'
        if 'structure' in input_header:
            self._structure = input_header['structure']
        else:
            self._structure = None

        self.inited = True

    @property
    def stream(self):
        if not self.inited:
            self.initialize()
        return sys.stdin

    @property
    def content_type(self):
        if not self.inited:
            self.initialize()
        return self._content_type

    @property
    def is_table(self):
        if not self.inited:
            self.initialize()
        return bool(self._structure)
    
    @property
    def structure(self):
        if not self.inited:
            self.initialize()
        return self._structure
    
    def table_reader(self, dict=False):
        if not self.inited:
            self.initialize()
        if dict:
            fieldnames = []
            casts = {}
            if self.structure:
                for col in self.structure:
                    fieldnames.append(col['name'])
                    if col['type'] == 'numeric':
                        casts[col['name']] = float
                    elif col['type'] == 'integer':
                        casts[col['name']] = int
                    elif col['type'] == 'date':
                        casts[col['name']] = lambda s: datetime.datetime.strptime(s, '%Y-%m-%d').date()
                    elif col['type'] == 'datetime':
                        casts[col['name']] = lambda s: datetime.datetime.strptime(s, '%Y-%m-%d')
            reader = csv.DictReader(sys.stdin, fieldnames)
        else:
            casts = {}
            index = 0
            if self.structure:
                for col in self.structure:
                    if col['type'] == 'numeric':
                        casts[index] = float
                    elif col['type'] == 'integer':
                        casts[index] = int
                    elif col['type'] == 'date':
                        casts[index] = lambda s: datetime.datetime.strptime(s, '%Y-%m-%d').date()
                    elif col['type'] == 'datetime':
                        casts[index] = lambda s: datetime.datetime.strptime(s, '%Y-%m-%d')
                    index = index + 1
            reader = csv.reader(sys.stdin)
        return TableReader(reader, casts)


class TableWriter(object):
    def __init__(self, structure, stream):
        self.structure = structure
        self.fieldnames = [col['name'] for col in structure]
        self.writer = csv.writer(stream)

    def writerow(self, row):
        if type(row) is list:
            self.writer.writerow(row)
        else:
            values = []
            for f in self.fieldnames:
                if f in row:
                    values.append(row[f])
                else:
                    values.append('')
            self.writer.writerow(values)
        
    def writerows(self, rows):
        for row in rows:
            self.writerow(row)


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
        return TableWriter(structure, self.stream)

def run(func):
    input = Input()
    output = Output()
    func(input, output)

