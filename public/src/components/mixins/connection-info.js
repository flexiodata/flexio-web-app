import * as connections from '../../constants/connection-info'

export default {
  methods: {
    isStorageConnection(c) {
      var connection_type = c.connection_type
      var info = _.find(connections, { connection_type })
      return _.get(info, 'is_storage', false)
    }
  }
}
