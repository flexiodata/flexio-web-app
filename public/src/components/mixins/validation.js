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
    validateAlias: function(eid_type, alias, callback) {
      var validate_attrs = [{ eid_type, key: 'alias', value: alias, type: 'alias' }]
      return this.validate(_.omitBy(validate_attrs, _.isEmpty), callback)
    },
    validatePassword: function(key, password, callback) {
      var validate_attrs = [{ key, value: password, type: 'password' }]
      return this.validate(_.omitBy(validate_attrs, _.isEmpty), callback)
    }
  }
}
