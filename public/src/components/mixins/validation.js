// allow generalized validation

import api from '../../api'

export default {
  methods: {
    $_Validation_validateAll: _.debounce(function(team_name, validate_attrs, callback) {
      api.validate(team_name, validate_attrs).then(response => {
        var errors = _.keyBy(response.data, 'key')

        if (_.isFunction(callback)) {
          callback(response, errors)
        }
      })
    }, 300),
    $_Validation_validateName: function(team_name, eid_type, name, callback) {
      var validate_attrs = [{ eid_type, key: 'name', type: 'name', value: name }]
      return this.$_Validation_validateAll(team_name, _.omitBy(validate_attrs, _.isEmpty), callback)
    },
    $_Validation_validateUsername: function(key, username, callback) {
      var validate_attrs = [{ key, type: 'username', value: username }]
      return this.$_Validation_validateAll(null, _.omitBy(validate_attrs, _.isEmpty), callback)
    },
    $_Validation_validatePassword: function(key, password, callback) {
      var validate_attrs = [{ key, type: 'password', value: password }]
      return this.$_Validation_validateAll(null, _.omitBy(validate_attrs, _.isEmpty), callback)
    }
  }
}
