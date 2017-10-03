exports.flexio_handler = function (input, output) {
    //var data = input.read(); // get data from stdin
    var data = "Hello, world.";
    output.write(data.toUpperCase());
  }
