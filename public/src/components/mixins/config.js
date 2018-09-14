
export default {
  methods: {
    $_Config_get(path, default_val) {
      var user = this.$store.getters.getActiveUser
      var cfg = _.get(user, 'config', {})

      // allow config to be overridden by GET params
      //var params = _.get(this.$route, 'query', {})
      //if (params['app.prompt.onboarding.shown'] === 'true')

      return cfg[path] === undefined ? default_val : cfg[path]
    },
    $_Config_set(path, val) {
      var user = this.$store.getters.getActiveUser
      var cfg = _.get(user, 'config', {})
      if (_.isArray(cfg)) {
        cfg = {}
      }

      cfg[path] = val
      this.$store.dispatch('updateUser', { eid: user.eid, attrs: { config: cfg } })
    },
    $_Config_reset(path) {
      var user = this.$store.getters.getActiveUser
      var cfg = _.get(user, 'config', {})
      if (_.isArray(cfg)) {
        cfg = {}
      }

      delete cfg[path]
      this.$store.dispatch('updateUser', { eid: user.eid, attrs: { config: cfg } })
    },
    $_Config_resetAll() {
      var user = this.$store.getters.getActiveUser
      var cfg = _.get(user, 'config', {})
      if (_.isArray(cfg)) {
        cfg = {}
      }

      cfg = {}
      this.$store.dispatch('updateUser', { eid: user.eid, attrs: { config: cfg } })
    }
  }
}
