// allow generalized validation

import api from '../../api'

export default {
  methods: {
    $_Validation_validateAll: _.debounce(function(validate_attrs, callback) {
      api.v2_validate(validate_attrs).then(response => {
        var errors = _.keyBy(response.data, 'key')

        if (_.isFunction(callback)) {
          callback(response, errors)
        }
      })
    }, 300),
    $_Validation_validateName: function(eid_type, name, callback) {
      var validate_attrs = [{ eid_type, key: 'name', value: name, type: 'name' }]
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
