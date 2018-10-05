import { OBJECT_TYPE_CONNECTION } from '../../constants/object-type'
import * as connections from '../../constants/connection-info'

export default {
  methods: {
    $_Connection_getConnectionByIdentifier(id) {
      var connections = _.filter(this.$store.state.objects, { eid_type: OBJECT_TYPE_CONNECTION })
      return _.find(connections, (c) => {
        var alias = _.get(c, 'alias', '')
        return alias.length > 0 ? alias == id : c.eid == id
      })
    },
    $_Connection_getConnectionIdentifier(c) {
      var alias = _.get(c, 'alias', '')
      var eid = _.get(c, 'eid', '')
      return alias.length > 0 ? alias : eid
    },
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
    $_Connection_isOauth(c) {
      return this.$_Connection_getInfo(c, 'is_oauth', false)
    }
    $_Connection_isStorage(c) {
      return this.$_Connection_getInfo(c, 'is_storage', false)
    }
  }
}
