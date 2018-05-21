import * as connections from '../../constants/connection-info'

export default {
  methods: {
    $_Connection_getInfo(c, key, def) {
      var connection_type = c.connection_type || c
      var info = _.find(connections, { connection_type })
      if (!_.isString(key)) {
        return info
      }
      return _.get(info, key, def)
    },
    $_Connection_getServiceName(c) {
      return this.$_Connection_getInfo(c, 'service_name', '')
    },
    $_Connection_isStorage(c) {
      return this.$_Connection_getInfo(c, 'is_storage', false)
    }
  }
}
