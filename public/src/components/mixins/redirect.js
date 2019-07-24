import { ROUTE_APP_PIPES } from '@/constants/route'

// allows any component to set the active project; if the project hasn't yet
// been fetched from the server, this mixin will do that as well and then
// set the active project after it has been fetched)

export default {
  methods: {
    // method: 'push' or 'replace'; default to 'push'
    $_Redirect_redirect: function(redirect, redirect_method) {
      var method = 'push'
      if (redirect_method == 'replace') {
        method = redirect_method
      }

      if (!_.isString(redirect)) {
        // grab the redirect from the query string if it exists
        redirect = _.get(this.$route, 'query.redirect', '')
      }

      // fix problem with /app/app when redirecting
      if (redirect.substr(0, 5) == '/app/') {
        redirect = redirect.substr(4)
      }

      if (redirect && redirect.length > 0) {
        this.$router[method]({ path: redirect })
      } else {
        this.$router[method]({ name: ROUTE_APP_PIPES })
      }
    }
  }
}
