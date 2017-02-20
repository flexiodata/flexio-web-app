// process promise response errors

export default {
  methods: {
    getResponseText(promise_obj, callback) {
      promise_obj.blob().then(function(blob) {
        var reader = new FileReader()
        reader.onload = function(e) { callback(reader.result) }
        reader.readAsText(blob)
      })
    }
  }
}
