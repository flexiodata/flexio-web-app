(function (){


var sha1 = require('sha1');
var url = require('url');
var http = require('http');
var request = require('request');

function var_dump(a) { console.log(require('util').inspect(a)); }
    
var Connection = function() {

    var call_hash_base = 'dataex;call;' + Date() + ';';
    var call_hash_counter = 0;

    this.mgr = null;
    this.calls = [];
    this.cookie_jar = request.jar();
    this.connection_id = '';
    this.session_id = '';
    this.socket = null;
    this.logged_in = false;

    this.invoke = function(method, path, params, success, failure)
    {
        // generate a unique token for the call
        var token = sha1(call_hash_base + (call_hash_counter++));

        // record information which will be used for function response
        this.calls[token] = { "success": success, "failure": failure, "timestamp": (new Date().getTime()) };

        
        var strparams = url.format( { query: params } ).substr(1);
        var call = "Message-Type: Method\n" + 
                   "Method: " + method + "\n" +
                   "Token: " + token + "\n" +
                   "Path: " + path + "\n" +
                   "Parameters: " + strparams + "\n\n";
        
        console.log("INVOKING: " + call);
        this.socket.send(call, function(error) {
            console.log("ws send error:"); console.log(error);
        });
    }


    this.onMessage = function (msg)
    {
console.log(msg);
        if (msg == "PING_FROM_CLIENT")
        {
            console.log(Date() + " received ping from client " + this.connection_id);
            this.socket.send("PING_FROM_SERVER");
        }

        var hdr_len = msg.indexOf("\n\n");
        if (hdr_len == -1)
            return;
        
        var token = '';
        var msgtype = '';
        var method = '';
        var parameters = '';
        
        var hdrs = msg.substr(0, hdr_len);
        var body = msg.substr(hdr_len+2);
        
        hdrs = hdrs.split("\n");
        for (var hdr in hdrs)
        {
            var arr = hdrs[hdr].split(':');
            if (arr.length >= 2)
            {
                if (arr[0] == 'Message-Type')
                    msgtype = arr[1].trim();
                else if (arr[0] == 'Token')
                    token = arr[1].trim();
                else if (arr[0] == 'Method')
                    method = arr[1].trim();
                else if (arr[0] == 'Parameters')
                    parameters = arr[1].trim();
            }
        }
        

        if (msgtype == 'Response')
        {
            if (this.calls.hasOwnProperty(token))
            {
                this.calls[token].success(body);
                delete this.calls[token];
            }
        }

        if (method.length > 0)
        {
            var params = {};
            var parsed = url.parse('?'+parameters, true);
            if (parsed && parsed.hasOwnProperty('query'))
                params = parsed.query;
            if (method == 'login') this.onMethodLogin(params);
            if (method == 'assets') this.onMethodAssets(params);
        }
    }


    this.onMethodLogin = function(params)
    {
    }



    this.onMethodAssets = function(params)
    {
    }



}

var ConnectionMgr = function() {

    var session_hash_base = 'dataex;session;' + Date() + ';';
    var session_hash_counter = 0;

    var pThis = this;
    this.connections = {};
    this.session_to_connection_map = {};



    // start call garbage collector;  This fails out calls
    // which received no response within a fixed period of time

    setInterval(function() {

        var ts = (new Date().getTime());

        var i, conn, call, to_delete;
        for (connidx in pThis.connections)
        {
            to_delete = []; 
            conn = pThis.connections[connidx];

            for (callidx in conn.calls)
            {
                call = conn.calls[callidx];
                if (ts - call.timestamp > 30000)
                {
                    if (call.failure) call.failure(); 
                    to_delete.push(callidx);
                }
            }

            for (i = 0; i < to_delete.length; ++i)
            {
                delete conn.calls[ to_delete[i] ];
            }
        }
                         
         
    },10000);


    this.registerConnection = function(socket)
    {
         var conn = new Connection;
         conn.mgr = this;
         conn.connection_id = this.getUniqueConnectionId();
         conn.session_id = sha1(session_hash_base + (session_hash_counter++));
         conn.socket = socket;
         this.connections[conn.connection_id] = conn;

         var msg = "Message-Type: Notify\n" + 
                   "Notify: Welcome\n" +
                   "Connection: " + conn.connection_id + "\n\n" + 
                   "Session: " + conn.session_id + "\n\n";
         socket.send(msg);

         return conn;
    }

    this.unregisterConnection = function(connection_id)
    {
        if (this.connections[connection_id])
            delete this.session_to_connection_map[ this.connections[connection_id].session_id ];
        delete this.connections[connection_id];

console.log("CONNECTIONS:");
console.log(this.connections);
console.log("SESSIONS:");
console.log(this.session_to_connection_map);
    }


    this.registerSession = function(conn, rejoin_session)
    {
        if (rejoin_session)
            conn.session_id = rejoin_session;

        if (rejoin_session && this.session_to_connection_map.hasOwnProperty(rejoin_session))
        {
            var rejoin_conn_id = this.session_to_connection_map[rejoin_session];

            delete this.connections[conn.connection_id];
            delete this.connections[rejoin_conn_id];
            conn.connection_id = rejoin_conn_id;
            this.connections[rejoin_conn_id] = conn;
        }
         else
        {
            this.session_to_connection_map[conn.session_id] = conn.connection_id;   
        }

console.log("CONNECTIONS:");
console.log(this.connections);
console.log("SESSIONS:");
console.log(this.session_to_connection_map);
    }


    this.getAllConnections = function()
    {
        return this.connections;
    }
    
    this.getConnection = function(connection_id)
    {
        if (!this.connections.hasOwnProperty(connection_id))
            return null;
        return this.connections[connection_id];
    }

    this.getUniqueConnectionId = function()
    {
//    return "a";
        while (true)
        {
            var hash = sha1('dataex;connid;'+Date());
            hash = hash.substr(0,8);
            if (!pThis.connections.hasOwnProperty(hash))
                return hash;
        }
    }
}



exports.ConnectionMgr = new ConnectionMgr;


}());

