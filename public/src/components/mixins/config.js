
export default {
  methods: {
    $_Config_get(path, default_val) {
      var user = this.$store.getters.getActiveUser
      var cfg = _.get(user, 'config', {})
      return cfg[path] === undefined ? default_val : cfg[path]
    },
    $_Config_set(path, val) {
      var user = this.$store.getters.getActiveUser
      var cfg = _.get(user, 'config', {})
      if (_.isArray(cfg)) {
        cfg = {}
      }

      cfg[path] = val

      return this.$store.dispatch('v2_action_updateUser', { eid: user.eid, attrs: { config: cfg } })
    },
    $_Config_reset(path) {
      var user = this.$store.getters.getActiveUser
      var cfg = _.get(user, 'config', {})
      if (_.isArray(cfg)) {
        cfg = {}
      }

      delete cfg[path]

      return this.$store.dispatch('v2_action_updateUser', { eid: user.eid, attrs: { config: cfg } })
    },
    $_Config_resetAll() {
      var user = this.$store.getters.getActiveUser
      var cfg = _.get(user, 'config', {})
      if (_.isArray(cfg)) {
        cfg = {}
      }

      cfg = {}

      return this.$store.dispatch('v2_action_updateUser', { eid: user.eid, attrs: { config: cfg } })
    }
  }
}
