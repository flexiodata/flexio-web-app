import * as connections from '../../constants/connection-info'

export default {
  methods: {
    getConnectionInfo(c, key, def) {
      var connection_type = c.connection_type || c
      var info = _.find(connections, { connection_type })
      if (!_.isString(key))
        return info
      return _.get(info, key, def)
    },
    getConnectionServiceName(c) {
      return this.getConnectionInfo(c, 'service_name', '')
    },
    isStorageConnection(c) {
      return this.getConnectionInfo(c, 'is_storage', false)
    }
  }
}
