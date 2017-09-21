
var fxutil = require('./index.js')
var script = require(process.argv[3])

if (process.argv[2] == 'managed')
    fxutil.runStream(script.flexio_file_handler)
     else
    fxutil.run(script.flexio_handler)
