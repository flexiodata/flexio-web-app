import sys
import json
import csv




class TableReader(object):
    def __init__(self, reader):
        self.reader = reader

    def __iter__(self):
        return self

    def __next__(self):
        row = next(self.reader)
        if row:
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
            fieldnames = [col['name'] for col in self.structure]
            reader = csv.DictReader(sys.stdin, fieldnames)
        else:
            reader = csv.reader(sys.stdin)
        return TableReader(reader)


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

input = Input()
output = Output()
