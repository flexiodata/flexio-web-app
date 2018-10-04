exports.flexio_handler = function (context) {
    //var data = input.read(); // get data from stdin
    var data = "Hello, world.";
    context.output.write(data.toUpperCase());
    context.end()
  }
