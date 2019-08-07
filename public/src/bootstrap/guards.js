import router from '@/router' // VueRouter
import store from '@/store' // Vuex store
import { OBJECT_STATUS_AVAILABLE } from '@/constants/object-status'
import { ROUTE_SIGNIN_PAGE, ROUTE_SIGNUP_PAGE } from '@/constants/route'

const isLocalhost = () => {
  return location.hostname === 'localhost' || location.hostname === '127.0.0.1'
}

const hasActiveUser = () => {
 return store.state.users.active_user_eid.length > 0
}

const canProceed = () => {
  var active_user = store.getters['users/getActiveUser']

  if (_.get(active_user, 'eid', '').length == 0) {
    return false
  }

  if (_.get(active_user, 'eid_status') != OBJECT_STATUS_AVAILABLE) {
    return false
  }

  return true
}

const debugRoute = (msg, to) => {
  if (isLocalhost()) {
    console.log(msg, to)
  }
}

// global route guards

router.beforeEach((to, from, next) => {
  const redirectToSignIn = () => {
    debugRoute('redirect to sign in:', to)

    // redirect to the sign up page if URL contains a verify code (most likely
    // meaning they are a new user entering the app from an email link)
    var verify_code = _.get(to, 'query.verify_code', '')
    next({
      name: verify_code.length > 0 ? ROUTE_SIGNUP_PAGE : ROUTE_SIGNIN_PAGE,
      query: _.assign({}, to.query, { redirect: to.fullPath })
    })
  }

  const goNext = () => {
    debugRoute('go next:', to)

    var active_user = store.getters['users/getActiveUser']
    var active_username = active_user.username
    var team_name = to.params.team_name || active_username

    store.dispatch('teams/changeActiveTeam', { team_name })

    next()
  }

  debugRoute('initial route:', to)

  // update the active document in the store
  store.commit('CHANGE_ACTIVE_DOCUMENT', to.params.object_name || to.name)

  if (canProceed()) {
    debugRoute('user is signed in and not pending:', to)

    // user is signed in and not pending; move to the next route
    goNext()
  } else {
    // we're already fetching the user; we're done
    if (store.state.users.is_signing_in || store.state.users.is_initializing) {
      debugRoute('already fetching user:', to)
      return
    }

    if (to.matched.some(record => record.meta.requiresAuth)) {
      debugRoute('requires auth:', to)

      // this route requires authentication; check if the user is signed in...
      store.dispatch('users/fetch', { eid: 'me' }).then(response => {
        debugRoute('requires auth (fetched user):', to)

        // if the user is signed in and not pending, move to the next route,
        // otherwise, redirect them to the sign in or sign up page
        canProceed() ? goNext() : redirectToSignIn()
      }).catch(error => {
        debugRoute('requires auth (fetched user failed):', to)

        // error fetching current user; bail out
        redirectToSignIn()
      })
    } else {
      debugRoute('public:', to)

      // this route does not require authentication; try to sign in just to make
      // sure we know who the active user is and move to the next route
      if (!hasActiveUser()) {
        store.dispatch('users/fetch', { eid: 'me' }).then(response => {
          debugRoute('public (fetched user):', to)
          goNext()
        }).catch(error => {
          debugRoute('public (fetched user failed):', to)
          next()
        })
      } else {
        // we have an active user; move along
        debugRoute('public (has active user):', to)
        next()
      }
    }
  }
})

router.afterEach((to, from) => {
  // log each route as a separate page view
  setTimeout(() => {
    if (analytics) {
      analytics.page()
    }
  }, 100)
})
