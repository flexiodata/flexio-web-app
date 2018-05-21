// allow generalized validation

import api from '../../api'

export default {
  methods: {
    $_Validation_validateAll: _.debounce(function(validate_attrs, callback) {
      api.validate({ attrs: validate_attrs }).then(response => {
        var errors = _.keyBy(response.body, 'key')

        if (_.isFunction(callback))
          callback(response, errors)
      }, response => {
        // error callback
      })
    }, 300),
    $_Validation_validateAlias: function(eid_type, alias, callback) {
      var validate_attrs = [{ eid_type, key: 'alias', value: alias, type: 'alias' }]
      return this.$_Validation_validateAll(_.omitBy(validate_attrs, _.isEmpty), callback)
    },
    $_Validation_validateUsername: function(key, username, callback) {
      var validate_attrs = [{ key, value: username, type: 'username' }]
      return this.$_Validation_validateAll(_.omitBy(validate_attrs, _.isEmpty), callback)
    },
    $_Validation_validatePassword: function(key, password, callback) {
      var validate_attrs = [{ key, value: password, type: 'password' }]
      return this.$_Validation_validateAll(_.omitBy(validate_attrs, _.isEmpty), callback)
    }
  }
}
