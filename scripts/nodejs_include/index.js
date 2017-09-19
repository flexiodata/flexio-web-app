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
        return decoded_part.result
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

    test() {
        console.log("Hello")
    }
}

module.exports = {

    StdinoutProxy

};
