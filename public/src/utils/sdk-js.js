import Flexio from 'flexio-sdk-js'

var getTaskJSON = function(code) {
  // create a function to create the JS SDK code to call
  var fn = (Flexio, callback) => { return eval(code) }

  // get access to pipe object
  var pipe = fn.call(this, Flexio)

  // check pipe syntax
  if (!Flexio.util.isPipeObject(pipe)) {
    throw({ message: 'Invalid pipe syntax. Pipes must start with `Flexio.pipe()`.' })
  }

  // get the pipe task JSON
  var task = _.get(pipe.getJSON(), 'task', { op: 'sequence', items: [] })

  return task
}

export default {
  getTaskJSON: getTaskJSON
}
