"use strict"

var utf8 = require('utf8')
var fs = require('fs')

class StdinoutProxy {

    constructor() {
        this.stdin = fs.openSync('/dev/stdin', 'rs')
        this.stdout = fs.openSync('/dev/stdout', 'w')
    }


    invokeAsync(func, params, callback) {
        // placeholder for future async version of invoke
        var res = this.invokeSync(func, params)
        var err = null
        callback(err, res)
    }


    invokeSync(func, params) {
        var payload = this.encodePart(func)
        for (var i = 0; i < params.length; ++i) {
            payload += this.encodePart(params[i])
        }
        
        this.sendMessageSync(payload)

        const response = this.readMessageSync()
        if (response === null)
            throw('Cannot read message')
        //console.log("Received reply " + response)
        var decoded_part = this.decodePart(response, 0)
        return decoded_part.value
    }

    sendMessageSync(payload) {
        var msg = '--MSGqQp8mf~' + payload.length + ',' + payload
        fs.writeSync(this.stdout, msg, null, 'binary')
        //fs.fdatasyncSync(process.stdout.fd)
    }

    readMessageSync() {
        var buf = ''
        var data = new Buffer(1)
        var ch
        while (true) {
            fs.readSync(this.stdin, data, 0, 1, null)
            ch = String.fromCharCode(data[0])
            buf += ch
            if (buf.length > 100) {
                break
            }
            if (buf == '--MSGqQp8mf~') {
                var lenstr = ''
                var ch
                while (true) {
                    fs.readSync(this.stdin, data, 0, 1, null)
                    ch = String.fromCharCode(data[0])
                    if (ch < '0' || ch > '9') {
                        break
                    }
                    lenstr += ch
                }
                if (ch != ',') {
                    //print("***" + ch.decode() + "***" + lenstr.decode())
                    return null
                }
                var msglen = parseInt(lenstr)
                data = new Buffer(msglen)
                fs.readSync(this.stdin, data, 0, msglen, null)
                //return this.u8arrToString(data)
                return data.toString('binary')
            }
        }
        return null
    }
    
    

    encodePart(v) {
        if (v === null)
            return 'N0,'
        var type = 's'
        if (v instanceof Buffer) {
            type = 'B'
            v = v.toString('binary')
        } else if (typeof(v) === 'boolean') {
            type = 'b'
            v = (v ? '1' : '0')
        } else if (Number.isInteger(v)) {
            type = 'i'
            v = ''+v
        }  else if (typeof(v) === 'number') {
            type = 'f'
            v = ''+v
        } else if (typeof(v) === 'string' || v instanceof String) {
            type = 's'
            v = utf8.encode(v)
        } else if (Array.isArray(v)) {
            type = 'a'
            var arrpayload = ''
            for (var i = 0; i < v.length; ++i) {
                arrpayload += this.encodePart(v[i])
            }
            v = arrpayload
        } else if (typeof(v) == 'object') {
            type = 'o'
            var objpayload = ''
            var keys = Object.keys(v)
            for (var i = 0; i < keys.length; ++i) {
                objpayload += this.encodePart(keys[i])
                objpayload += this.encodePart(v[keys[i]])
            }
            v = objpayload
        }
        return type + v.length + ',' + v
    }


    decodeParts(vars) {
        var off = 0
        var res = []
        while (off !== null) {
            if (off >= vars.length) {
                break;
            }
            var decoded_part = this.decodePart(vars, off)
            res.push(decoded_part.value)
            off = decoded_part.next
        }
        return res
    }

    decodePart(v, offset) {
        var commapos = v.indexOf(',', offset)
        if (commapos == -1) {
            return { value: null, next: null }
        }
        var type = v[offset]
        var lenpart = parseInt(v.slice(offset+1,commapos))
        var start = commapos+1
        var end = start+lenpart
        var part = v.slice(start,end)
        if (end >= v.length) {
            end = null
        }
        if (type == 'i') {
            return { value: parseInt(part), next: end }
        }
        if (type == 'f') {
            return { value: parseFloat(part), next: end }
        }
        else if (type == 'b') {
            return { value: (part == '0' ? false : true), next: end }
        }
        else if (type == 'B') {
            var len = part.length
            var buf = new Buffer(part, 'binary')
            return { value: buf, next: end }
        }
        else if (type == 's') {
            return { value: utf8.decode(part), next: end }
        }
        else if (type == 'a') {
            return { value: this.decodeParts(part), next: end }
        }
        else if (type == 'o') {
            var key, value
            var off = 0
            var res = {}
            while (off !== null) {
                var decoded_part = this.decodePart(part, off)
                key = decoded_part.value
                off = decoded_part.next
                if (off !== null) {
                    decoded_part = this.decodePart(part, off)
                    value = decoded_part.value
                    off = decoded_part.next
                    res[key] = value
                }
            }
            return { value: res, next: end }
        }
        else if (type == 'N') {
            return { value: null, next: end }
        }
        else if (type == 'E') {
            throw new Error(part)
        }
        else throw "Unknown marshal payload: " + v
    }

    test() {
        console.log("Hello")
    }
}


var proxy = new StdinoutProxy()


class InputEnv {
}

class OutputEnv {
}

var inputEnv = new InputEnv()
var outputEnv = new OutputEnv()




class Input {

    constructor(info) {
        // used for setting fetch style
        this.FETCH_OBJECT = 1
        this.FETCH_ARRAY = 2

        if (info) {
            this._name = info['name']
            this._contentType = info['content_type']
            this._isTable = (this.contentType == 'application/vnd.flexio.table') ? true:false
            this._size = info['size']
            this._idx = info['idx']
            this._structure = null
            this._dict = false
            this._fetchStyle = this.Dictionary
            this._casts = null
            this._casting = true
        } else {
            this._name = ''
            this._contentType = 'application/octet-stream'
            this._isTable = false
            this._size = 0
            this._idx = -1
            this._structure = null
            this._dict = false
            this._fetchStyle = this.Dictionary
            this._casts = null
            this._casting = true
        }
    }
    
    get env() {
        return inputEnv
    }

    get name() {
        return this._name
    }

    get contentType() {
        return this._contentType
    }

    get size() {
        return this._size
    }

    get structure() {
        if (!this._isTable) {
            return null
        }
        if (!this._structure) {
            this._structure = proxy.invokeSync('getInputStreamStructure', [this._idx])
        }
        return this._structure
    }
    
    get isTable() {
        return this._isTable
    }

    get fetchStyle() {
        return this._fetchStyle
    }

    set fetchStyle(value) {
        this._casts = null
        if (value == Object || value == this.FETCH_OBJECT) {
            this._fetchStyle = this.FETCH_OBJECT
        } else if (value == Array || value == this.FETCH_ARRAY) {
            this._fetchStyle = this.FETCH_ARRAY
        }
    }

    get casting() {
        return this._casting
    }

    set casting(value) {
        this._casting = value
    }

    read(length) {
        if (length === undefined || length === null) {
            return this.readAll()
        }
        if (this._isTable) {
            return null
        }
        var data = proxy.invokeSync('read', [this._idx, length])
        if (data === false || data === null) {
            return null
        }
        return data
    }

    readLine() {
        var row = proxy.invokeSync('readline', [this._idx, (this._fetchStyle == this.FETCH_ARRAY ? false : true)])
        if (row === false || row === null) {
            return null
        }
        if (this._casting) {
            this.typeCasts(row)
        }
        return row
    }
    
    forEach(callback) {
        var row
        while ((row = this.readLine()) !== null) {
            callback(row)
        }
    }
    readAll() {
        if (this._isTable) {
            var rows = [], row
            while (true) {
                row = this.readLine()
                if (row === null) {
                    break
                }
                rows.push(row)
            }
            return rows
        } else {
            var buf = '', chunk
            while (true) {
                chunk = this.read(4096)
                if (chunk === null) {
                    break
                }
                buf += chunk
            }
            return buf
        }
    }

    typeCasts(row) {
        var key
        if (this._casts === null) {
            this._casts = {}
            var structure = this.structure
            var i, idx = 0, colCount = structure.length, col
            for (i = 0; i < colCount; ++i) {
                col = structure[i]
                if (this._fetchStyle == this.FETCH_ARRAY) {
                    key = idx
                } else {
                    key = col['name']
                }
                if (col['type'] == 'numeric') 
                    this._casts[key] = function(val) { return parseFloat(val) }
                else if (col['type'] == 'integer')
                    this._casts[key] = function(val) { return parseInt(val) }
                else if (col['type'] == 'date')
                    this._casts[key] = function(val) { return new Date(val) }
                else if (col['type'] == 'datetime')
                    this._casts[key] = function(val) { return new Date(val) }
                idx = idx + 1
            }
        }

        for (key in this._casts) {
            if (this._casts.hasOwnProperty(key) && row.hasOwnProperty(key)) {
                row[key] = this._casts[key](row[key])
            }
        }
    }
}

/*
    def __iter__(self):
        return self

    def __next__(self):
        row = self.readline()
        if row is None:
            raise StopIteration
        return row


*/





class Output {


    constructor(info) {
        if (info) {
            this._name = info['name']
            this._contentType = info['content_type']
            this._size = info['size']
            this._idx = info['idx']
        } else {
            this._name = ''
            this._contentType = 'application/octet-stream'
            this._size = 0
            this._idx = -1
        }
    }
    
    get name() {
        return this._name
    }

    set name(value) {
        this._name = value
        proxy.invoke('setOutputStreamInfo', [this._idx, {'name':value}])
    }

    get contentType() {
        return this._contentType
    }

    set contentType(value) {
        this._contentType = value
        proxy.invoke('setOutputStreamInfo', [this._idx, {'content_type':value}])
    }

    get env() {
        return outputEnv
    }

//  create(structure)
//  create(name, structure)
//  create(name, contentType, structure)
    create(p1,p2,p3) {
        var name, structure, contentType, properties
        if (Array.isArray(p1)) {
            // handle this call format:  create(structure)
            structure = p1
            name = null
            contentType = 'application/vnd.flexio.table'
        }
        else if (Array.isArray(p2)) {
            // handle this call format:  create(name, structure)
            name = p1
            structure = p2
            contentType = 'application/vnd.flexio.table'
        }
        else {
            name = (p1 === undefined) ? null : p1
            structure = (p2 === undefined) ? null : p2
            contentType = (p3 === undefined) ? 'text/plain' : p3
        }
        properties = { 'content_type': contentType }
        if (name) {
            properties['name'] = name
        }
        if (structure) {
            properties['structure'] = structure
        }
        var res = proxy.invokeSync('managedCreate', [this._idx, properties])

        if (res === false) {
            return false
        } else {
            return this
        }
    }

    write(data) {
        this.typeCasts(data)
        proxy.invokeSync('write', [this._idx, data])
    }

    insertRow(row) {
        this.typeCasts(row)
        proxy.invokeSync('insertRow', [this._idx, row])
    }
    
    insertRows(rows) {
        for (var i = 0, cnt = rows.length; i < cnt; ++i) {
            this.typeCasts(rows[i])
        }
        proxy.invokeSync('insertRows', [this._idx, rows])
    }


    castValue(value) {
        if (value instanceof Date) {
            var y = value.getFullYear(), m = value.getMonth()+1, d = value.getDate()
            return '' + y + '-' + (m<10?'0':'') + m + '-' + (d<10?'0':'') + d
        } else {
            return value
        }
    }

    typeCasts(row) {
        if (row instanceof Buffer) {
        } else if (Array.isArray(row)) {
            for (var i = 0, cnt = row.length; i < cnt; ++i) {
                row[i] = this.castValue(row[i])
            }
        } else if (typeof row === 'object') {
            for (var key in row) {
                if (row.hasOwnProperty(key)) {
                    row[key] = this.castValue(row[key])
                }
            }
        }
    }


}




class Inputs {

    constructor() {
        this.inputs = []
    }

    initialize(callback) {
        var me = this
        proxy.invokeAsync('getInputStreamInfo', [], function(err, stream_infos) {
            for (var i = 0; i < stream_infos.length; ++i) {
                me.inputs.push(new Input(stream_infos[i]))
            }
            callback()
        })
    }
}

class Outputs {

    constructor() {
        this.outputs = []
    }

    initialize(callback) {
        var me = this
        proxy.invokeAsync('getOutputStreamInfo', [], function(err, stream_infos) {
            for (var i = 0; i < stream_infos.length; ++i) {
                me.outputs.push(new Output(stream_infos[i]))
            }
            callback()
        })
    }

}





var inputs = new Inputs()
var outputs = new Outputs()
var inited = false



function checkModuleInit(callback) {
    if (inited) {
        callback()
        return
    } else {
        inited = true
        inputs.initialize(function() {
            outputs.initialize(function() {
                callback()
            })
        })
    }
}


function runStream(handler) {

    checkModuleInit(function() {

        proxy.invokeAsync('getManagedStreamIndex', [], function(err, stream_idx) {
            var input = inputs.inputs[stream_idx]
            var output = outputs.outputs[stream_idx]
            handler(input, output)
        })

    })
}





function run(handler) {
    checkModuleInit(function() {
        handler(inputs, outputs)
    })
}



module.exports = {

    StdinoutProxy,
    run,
    runStream

};
