"use strict"

var utf8 = require('utf8')
var fs = require('fs')

class StdinoutProxy {

    constructor() {
    }


    invoke(func, params, callback) {
        // placeholder for future async version of invoke
        var res = this.invokeSync(func, params)
        var err = null
        callback(err, res)
    }


    invokeSync(func, params) {
        var payload = this.encodePart(func)
        for (var i = 0; i < params.length; ++i) {
            payload += this.encodePart(param)
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
        fs.writeSync(process.stdout.fd, msg, null, 'binary')
        //fs.fdatasyncSync(process.stdout.fd)
    }

    readMessageSync(self) {
        var buf = ''
        var data = new Uint8Array(1)
        var ch
        while (true) {
            fs.readSync(process.stdin.fd, data, 0, 1, null)
            ch = String.fromCharCode(data[0])
            buf += ch
            if (buf.length > 100) {
                break
            }
            if (buf == '--MSGqQp8mf~') {
                var lenstr = ''
                var ch
                while (true) {
                    fs.readSync(process.stdin.fd, data, 0, 1, null)
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
                data = new Uint8Array(msglen)
                fs.readSync(process.stdin.fd, data, 0, msglen, null)
                return this.u8arrToString(data)
            }
        }
        return null
    }
    
    

    encodePart(v) {
        if (v === null)
            return 'N0,'
        var type = 's'
        if (v instanceof Uint8Array) {
            type = 'B'
            var len = v.length
            var res = ''
            for (var i = 0; i < len; ++i) {
                res += String.fromCharCode(v[i])
            }
            v = res
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
            var buf = new Uint8Array(len)
            for (var i = 0; i < len; ++i) {
                buf[i] = part.charCodeAt(i)
            }
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
    }


    u8arrToString(u8arr) {
        var len = u8arr.length
        var res = ''
        for (var i = 0; i < len; ++i) {
            res += String.fromCharCode(u8arr[i])
        }
        return res
    }

    stringToU8arr(str) {
        var len = str.length
        var res = new Uint8Array(str.length)
        for (var i = 0; i < len; ++i) {
            res[i] = str.charCodeAt(i)
        }
        return res
    }


    test() {
        console.log("Hello")
    }
}






class Inputs {

    constructor() {
        this.inputs = []
    }

    initialize(callback) {
        proxy.invoke('getInputStreamInfo', [], function(err, res) {
            stream_infos = res
            for (var i = 0; i < stream_infos.length; ++i) {
                this.inputs.push(new Input(stream_infos[i]))
            }
        })
    }
}

class Outputs {

    constructor() {
        this.outputs = []
    }

    initialize(callback) {
        proxy.invoke('getOutputStreamInfo', [], function(err, res) {
            stream_infos = res
            for (var i = 0; i < stream_infos.length; ++i) {
                this.outputs.push(new Output(stream_infos[i]))
            }
        })
    }

}




var proxy = new StdinoutProxy()
var inputs = new Inputs()
var outputs = new Outputs()
var inited = false



function checkModuleInit(callback) {
    if (initied) {
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

        proxy.invoke('getManagedStreamIndex', [], function(err, stream_idx) {
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
