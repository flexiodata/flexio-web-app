import router from '@/router' // VueRouter
import store from '@/store' // Vuex store
import {
  ROUTE_APP_BUILDER,
  ROUTE_INITSESSION_PAGE,
  ROUTE_SIGNIN_PAGE,
  ROUTE_SIGNUP_PAGE
} from '../constants/route'
import {
  CHANGE_ACTIVE_DOCUMENT,
  CHANGE_ACTIVE_TEAM
} from '../store/mutation-types'

// global route guards

router.afterEach((to, from) => {
  // log each route as a separate page view
  setTimeout(() => {
    if (analytics) {
      analytics.page()
    }
  }, 100)
})

router.beforeEach((to, from, next) => {
  // update the active document in the store
  store.commit(CHANGE_ACTIVE_DOCUMENT, to.params.identifier || to.name)

  const tryFetchConnections = () => {
    if (!store.state.connections_fetched && !store.state.connections_fetching) {
      store.dispatch('v2_action_fetchConnections', {}).catch(error => {
        // TODO: add error handling?
      })
    }
  }

  const tryFetchTokens = () => {
    if (!store.state.tokens_fetched && !store.state.tokens_fetching) {
      store.dispatch('v2_action_fetchTokens', {}).catch(error => {
        // TODO: add error handling?
      })
    }
  }

  const redirectToSignIn = () => {
    var redirect_to_signup = to.name == ROUTE_APP_BUILDER
    next({
      name: redirect_to_signup ? ROUTE_SIGNUP_PAGE : ROUTE_SIGNIN_PAGE,
      query: { redirect: to.fullPath }
    })
  }

  const goNext = () => {
    var user = store.getters.getActiveUser
    var active_username = _.get(user, 'username', store.state.active_user_eid)

    store.commit(CHANGE_ACTIVE_TEAM, to.params.user_identifier || active_username)

    tryFetchConnections()
    tryFetchTokens()
    next()
  }

  if (store.state.active_user_eid.length > 0)
  {
    // user is signed in; move to the next route
    goNext()
  } else {
    // we're already fetching the user; we're done
    if (store.state.user_fetching) {
      return
    }

    if (to.matched.some(record => record.meta.requiresAuth)) {
      // this route requires authentication; check if the user is signed in...
      store.dispatch('v2_action_fetchCurrentUser').then(response => {
        if (store.state.active_user_eid.length > 0) {
          // user is signed in; move to the next route
          goNext()
        } else {
          // user is not signed in; redirect them to the sign in page
          redirectToSignIn()
        }
      }).catch(error => {
        // error fetching current user; bail out
        redirectToSignIn()
      })
    }
     else
    {
      // the 'init session' route already attempts to fetch the current user; we're done
      if (to.name == ROUTE_INITSESSION_PAGE) {
        next()
        return
      }

      // this route does not require authentication; try to sign in just to make
      // sure we know who the active user is and move to the next route
      store.dispatch('v2_action_fetchCurrentUser').then(response => {
        goNext()
      }).catch(error => {
        goNext()
      })
    }
  }
})
