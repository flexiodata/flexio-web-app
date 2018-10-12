import { ROUTE_APP_HOME } from '../../constants/route'

// allows any component to set the active project; if the project hasn't yet
// been fetched from the server, this mixin will do that as well and then
// set the active project after it has been fetched)

export default {
  methods: {
    $_Redirect_redirect: function(redirect) {
      if (!_.isString(redirect)) {
        // grab the redirect from the query string if it exists
        redirect = _.get(this.$route, 'query.redirect', '')
      }

      // fix problem with /app/app when redirecting
      if (redirect.substr(0, 5) == '/app/') {
        redirect = redirect.substr(4)
      }

      if (redirect && redirect.length > 0) {
        this.$router.push({ path: redirect })
      } else {
        this.$router.push({ name: ROUTE_APP_HOME })
      }
    }
  }
}
