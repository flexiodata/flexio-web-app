// allow generalized validation

import api from '../../api'

export default {
  methods: {
    validate: _.debounce(function(validate_attrs, callback) {
      api.validate({ attrs: validate_attrs }).then(response => {
        var errors = _.keyBy(response.body, 'key')

        if (_.isFunction(callback))
          callback(response, errors)
      }, response => {
        // error callback
      })
    }, 300),
    validateEname: function(ename, callback) {
      var validate_attrs = [{ key: 'ename', value: ename, type: 'ename' }]
      return this.validate(validate_attrs, callback)
    }
  }
}
