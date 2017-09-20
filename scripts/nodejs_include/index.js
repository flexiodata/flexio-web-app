"use strict"

var utf8 = require('utf8')

class StdinoutProxy {

    constructor() {
    }


    invoke(func, params) {
        var payload = this.encodePart(func)
        for (var i = 0; i < params.length; ++i) {
            payload += this.encodePart(param)
        }
        this.sendMessage(payload)
        var response = this.readMessage()
        //console.log("Received reply " + response)
        var decoded_part = this.decodePart(response, 0)
        return decoded_part.value
    }

    sendMessage(payload) {
        var msg = '--MSGqQp8mf~' + payload.length + ',' + payload
        process.stdout.write(msg)
        sys.stdout.flush()
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


    test() {
        console.log("Hello")
    }
}

module.exports = {

    StdinoutProxy

};
