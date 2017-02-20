(function (){

console.log(Date() + " - Started");    

var ConnectionMgr = require('./connectionmgr').ConnectionMgr;


var http = require('http');
var https = require('https');
var url = require('url');
var fs = require('fs');
var util = require('util');
var multiparty = require('multiparty');
var config = require('./config/config');
var querystring = require('querystring');

function var_dump(a) { console.log(require('util').inspect(a)); }



function http_callback(request, response) {

    var url_parsed = url.parse(request.url,true);
    var url_parts = url_parsed.pathname.split('/');
    var get_params = url_parsed.query;
    var conn = null;
    if (url_parts.length > 1)
        conn = ConnectionMgr.getConnection(url_parts[1]);
    
    var path = url_parsed.pathname.substr(url_parts[1].length + 1); // strip off connection id;
    if (path.length == 0)
        path = '/';
    
	console.log("Received request: " + request.url);
    if (!conn)
    {
        // no connection, return error
        response.writeHead(200, {"Content-Type": "text/plain"});
        response.end('{ "success": false, "msg": "No such connection found" }');
        return;
    }
    

    var doInvoke = function(params)
    {
        var method = '';
        if (params.m) method = params.m;
        
        console.log("INVOKE %s %s\n", method, path);

        conn.invoke(method, path, params,
            function(msg) { 
                // success
                response.writeHead(200, {"Content-Type": "text/plain"});
                response.end(msg);
            },

            function(msg) {
                // failure
                response.writeHead(200, {"Content-Type": "text/plain"});
                response.end('{ "success": false, "msg": "Call timed out" }');
            }
        );
    };
    
    
    if (request.method=='GET')
    {
        doInvoke(get_params);
    }
     else
    {
        var content_type = '' + request.headers['content-type'];
        if (content_type.indexOf('multipart/') != -1)
        {
            var form = new multiparty.Form();
            form.parse(request, function (err, fields, files) {
                var params = get_params;
                for (var k in fields)
                {
                    if (!params.hasOwnProperty(k))
                        params[k] = fields[k];
                }
                doInvoke(params);
            });
        }
         else
        {
            var post_data = '';
        
            request.on('data', function (data) {
                if (post_data.length < 10000000)
                    post_data += data;
             });
        
            request.on('end', function () {
                var params = querystring.parse(post_data);

                // merge in get params
                for (var k in get_params)
                {
                    if (!params.hasOwnProperty(k))
                        params[k] = get_params[k];
                }
                        
                doInvoke(params);
            });
        }
    }
    

}

var server = http.createServer(http_callback);
server.listen(80);







var placeholder_callback = function (request, response) {
  response.writeHead(200, {"Content-Type": "text/plain"});
  response.end("");
};



if (config.ssl)
{
    var https_config = {
        key: fs.readFileSync(config.ssl_key),
        cert: fs.readFileSync(config.ssl_cert),
                ca: [ fs.readFileSync(config.ssl_ca) ]
    };

    var wshttp = https.createServer(https_config, placeholder_callback);
    wshttp.listen(443);
}
 else
{
    var wshttp = http.createServer(placeholder_callback);
    wshttp.listen(8080);
}



var WebSocketServer = require('ws').Server;
var wss = new WebSocketServer({server: wshttp});

wss.on('connection', function(ws) {

    var conn = ConnectionMgr.registerConnection(ws);

    ws.send('connection_id #' + conn.connection_id);
    console.log('visitor #' + conn.connection_id + ' arrived');

    ws.on('message', function(msg) {
        conn.onMessage(msg);
    });

    ws.on('close', function() {
        ConnectionMgr.unregisterConnection(conn.connection_id);
        console.log('Client %s disconnected.', conn.connection_id);
    });
});



}());

