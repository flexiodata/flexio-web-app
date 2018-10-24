
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
      this.$store.dispatch('v2_action_updateUser', { eid: user.eid, attrs: { config: cfg } }).catch(error => {
        // TODO: add error handling?
      })
    },
    $_Config_reset(path) {
      var user = this.$store.getters.getActiveUser
      var cfg = _.get(user, 'config', {})
      if (_.isArray(cfg)) {
        cfg = {}
      }

      delete cfg[path]
      this.$store.dispatch('v2_action_updateUser', { eid: user.eid, attrs: { config: cfg } }).catch(error => {
        // TODO: add error handling?
      })
    },
    $_Config_resetAll() {
      var user = this.$store.getters.getActiveUser
      var cfg = _.get(user, 'config', {})
      if (_.isArray(cfg)) {
        cfg = {}
      }

      cfg = {}
      this.$store.dispatch('v2_action_updateUser', { eid: user.eid, attrs: { config: cfg } }).catch(error => {
        // TODO: add error handling?
      })
    }
  }
}
