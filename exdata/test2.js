url = require('url');

var obj = { "a": "B ", "b": "C" };

var p = { };
p.query = obj;

console.log(url.format(p));
