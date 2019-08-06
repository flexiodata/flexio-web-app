import * as connections from '@/constants/connection-info'

export default {
  methods: {
    $_Connection_getConnectionByIdentifier(id) {
      return _.find(_.get(this.$store, 'state.connections.items', []), (c) => {
        var cname = _.get(c, 'name', '')
        return cname.length > 0 ? cname == id : c.eid == id
      })
    },
    $_Connection_getConnectionIdentifier(c) {
      var cname = _.get(c, 'name', '')
      var eid = _.get(c, 'eid', '')
      return cname.length > 0 ? cname : eid
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
    },
    $_Connection_isStorage(c) {
      return this.$_Connection_getInfo(c, 'is_storage', false)
    },
    $_Connection_isEmail(c) {
      return this.$_Connection_getInfo(c, 'is_email', false)
    }
  }
}
