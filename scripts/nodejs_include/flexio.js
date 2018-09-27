"use strict"

var utf8 = require('utf8')
var fs = require('fs')
var Flexio = require('flexio-sdk-js')
var zmq = require('zeromq')

var proxy, context
var input = null
var output = null


class CallProxy {


    constructor(config) {

        var pThis = this

        this.server = process.env.hasOwnProperty('FLEXIO_RUNTIME_SERVER') ? ''+process.env.FLEXIO_RUNTIME_SERVER : ''
        this.access_key = process.env.hasOwnProperty('FLEXIO_RUNTIME_KEY') ? ''+process.env.FLEXIO_RUNTIME_KEY : ''
        this.requester = null
        this.calls = {}

        if (config !== null && typeof config == 'object') {
            if (config.hasOwnProperty('server')) {
                this.server = config.server
            }
            if (config.hasOwnProperty('access_key')) {
                this.access_key = config.access_key
            }
        }


        if (this.server.length > 0)
        {
            this.requester = zmq.socket('req')
            this.requester.connect(this.server)

            this.requester.on("message", function(msg) {
                msg = msg.toString()
                //console.log("RECEIVED MESSAGE " + msg)
                pThis.onMessage(msg)
            })

            process.on('SIGINT', function() {
                pThis.requester.close();
            });
        }


    }


    close() {
        this.requester.close()
        this.requester = null
    }

    onMessage(reply) {
        
        var result = undefined, call_id = ''

        try
        {
            reply = JSON.parse(reply)
            if (!reply.hasOwnProperty('id'))
                throw "Call response is missing id"
            if (reply.hasOwnProperty('result'))
                result = reply.result
            call_id = reply.id
            var moniker = '~' + call_id + '/bin.b64:'
            result = this.convertBase64ToBinary(result, moniker)
        }
        catch (e)
        {
        }

        if (this.calls.hasOwnProperty(call_id))
        {
            var callback = this.calls[call_id]
            delete this.calls[call_id]
            callback(result)
        }
    }

    invokeAsync(method, params) {

        var pThis = this

        var call_id = [...Array(20)].map(i=>(~~(Math.random()*36)).toString(36)).join('')
        var moniker = '~' + call_id + '/bin.b64:'

        var payload = {
            "version": 1,
            "access_key": this.access_key,
            "method": method,
            "params": this.convertBinaryToBase64(params, moniker),
            "id": call_id
        }

        return new Promise(function(resolve, reject) {
            pThis.calls[call_id] = resolve
            var msg = JSON.stringify(payload)
            //console.log("SENDING " + msg)
            pThis.requester.send(msg)
        })
    }


    invokeSync(method, params) {

        var done = false
        var result = undefined

        this.invokeAsync(method, params).then(function(r) {
            //console.log("Function returned")
            result = r
            done = true
        }).catch(function(err) {

        })

        //console.log("waiting")
        require('deasync').loopWhile(function(){return !done})
        //console.log("done waiting")

        return result
    }


    convertBinaryToBase64(obj, moniker)
    {
        var res, k, len

        if (Array.isArray(obj)) {
            res = []
            len = obj.length
            for (k = 0; k < len; ++k) {
                res.push(this.convertBinaryToBase64(obj[k], moniker))
            }
            return res
        } else if (Object.prototype.toString.call(obj) === '[object Object]' /* if is plain object */) {
            res = {}
            for (k in obj) {
                if (obj.hasOwnProperty(k)) {
                    res[k] = this.convertBinaryToBase64(obj[k], moniker)
                }
            }
            return res
        } else if (obj instanceof Buffer) {
            return moniker + obj.toString('base64')
        } else {
            return obj
        }
          
    }
    
    convertBase64ToBinary(obj, moniker)
    {
        var res, k, len

        if (Array.isArray(obj)) {
            res = []
            len = obj.length
            for (k = 0; k < len; ++k) {
                res.push(this.convertBase64ToBinary(obj[k], moniker))
            }
            return res
        } else if (Object.prototype.toString.call(obj) === '[object Object]' /* if is plain object */) {
            res = {}
            for (k in obj) {
                if (obj.hasOwnProperty(k)) {
                    res[k] = this.convertBase64ToBinary(obj[k], moniker)
                }
            }
            return res
        } else if ((typeof obj === 'string' || obj instanceof String) && obj.substr(0, moniker.length) == moniker) {
            return Buffer.from(obj.substr(moniker.length), 'base64')
        } else {
            return obj
        }
    }
}



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
            this._handle = info['handle']
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
            this._handle = -1
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
            this._structure = proxy.invokeSync('getInputStreamStructure', [this._handle])
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
        var data = proxy.invokeSync('read', [this._handle, length])
        if (data === false || data === null) {
            return null
        }
        return data
    }

    readLine() {
        var row = proxy.invokeSync('readline', [this._handle, (this._fetchStyle == this.FETCH_ARRAY ? false : true)])
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
            this._handle = info['handle']
        } else {
            this._name = ''
            this._contentType = 'application/octet-stream'
            this._size = 0
            this._handle = -1
        }
    }
    
    get name() {
        return this._name
    }

    set name(value) {
        this._name = value
        proxy.invokeSync('setOutputStreamInfo', [this._handle, {'name':value}])
    }

    get contentType() {
        return this._contentType
    }

    set contentType(value) {
        this._contentType = value
        proxy.invokeSync('setOutputStreamInfo', [this._handle, {'content_type':value}])
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
        var res = proxy.invokeSync('managedCreate', [this._handle, properties])

        if (res === false) {
            return false
        } else {
            return this
        }
    }

    write(data) {
        this.typeCasts(data)
        proxy.invokeSync('write', [this._handle, data])
    }

    insertRow(row) {
        this.typeCasts(row)
        proxy.invokeSync('insertRow', [this._handle, row])
    }
    
    insertRows(rows) {
        for (var i = 0, cnt = rows.length; i < cnt; ++i) {
            this.typeCasts(rows[i])
        }
        proxy.invokeSync('insertRows', [this._handle, rows])
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


class Result {
    json(obj) {
        output.contentType = "application/json"
        output.write(JSON.stringify(obj))
        proxy.close()
    }

    end(content) {
        if (content !== null && content !== undefined) {
            output.write(content)
        }
        proxy.close()
    }
}


class Context {

    constructor() {
        var pThis = this
        this.res = new Result()
        this._query = null
        this._form = null
        this._vars = {}
        this._vars_inited = false

        this.pipe = {}
        for (var task_name in Flexio.task) {

            (function(task_name, task_func){
                if (Flexio.task.hasOwnProperty(task_name) && task_name != 'toCode') {
                  pThis.pipe[task_name] = function() {
                      proxy.invokeSync('runJob', [ JSON.stringify(task_func.apply(this, arguments)) ])
                      return pThis.pipe
                  }
                }
              })(task_name, Flexio.task[task_name])
        }


        this.vars = new Proxy(pThis._vars, {
            checkPopulate: function(target) {
                if (!pThis._vars_inited) {
                    var env = proxy.invokeSync('getEnv', [])
                    var keys = Object.keys(env)
                    for (var i = 0; i < keys.length; ++i) {
                        target[keys[i]] = env[keys[i]]
                    }
                    pThis._vars_inited = true
                }
            },
            get: function(target, prop, receiver) {
                this.checkPopulate(target)
                return pThis._vars.hasOwnProperty(prop) ? pThis._vars[prop] : null
            },
            set: function(target, prop, value) {
                this.checkPopulate(target)
                pThis._vars[prop] = value
                proxy.invokeSync('setEnvValue', [prop,value])
            },
            ownKeys: function(target) {
                this.checkPopulate(target)
                return Object.keys(target)
            }
        });
        
    }

    get query() {
        if (this._query === null) {
            this._query = proxy.invokeSync('getQueryParameters', [])
        }
        return this._query
    }

    get form() {
        if (this._form === null) {
            this._form = proxy.invokeSync('getFormParameters', [])
        }
        return this._form
    }

}





var inited = false
function checkModuleInit(callback) {
    if (inited) {
        callback()
        return
    } else {
        inited = true
        proxy.invokeAsync('getInputStreamInfo', ['_fxstdin_']).then((stdin_stream_info) => {
            input = new Input(stdin_stream_info)
            context.input = input
            proxy.invokeAsync('getOutputStreamInfo', ['_fxstdout_']).then((stdout_stream_info) => {
                output = new Output(stdout_stream_info)
                context.output = output
                inited = true
                callback()
            }).catch((err)=>{ console.log("Error initializing stdin stream"); process.exit(1); })
        }).catch((err)=>{ console.log("Error initializing stdout stream"); process.exit(1); })
    }
}


function isFunction(f) {
    return f && {}.toString.call(f) === '[object Function]';
}

function run(handler) {

    if (handler.hasOwnProperty('flex_handler')) {
        handler = handler.flex_handler
    }
    else if (handler.hasOwnProperty('flexio_handler')) {
        handler = handler.flexio_handler
    }

    if (isFunction(handler)) {
        proxy = new CallProxy()
        context = new Context()
        context.proxy = proxy
        
        checkModuleInit(function() {
            handler(context)
        })
    }
}



module.exports = {

    CallProxy,
    run

};
