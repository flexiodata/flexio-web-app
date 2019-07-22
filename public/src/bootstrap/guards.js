import router from '@/router' // VueRouter
import store from '@/store' // Vuex store
import { ROUTE_INITSESSION_PAGE, ROUTE_SIGNIN_PAGE } from '../constants/route'
import { CHANGE_ACTIVE_DOCUMENT, CHANGE_ACTIVE_TEAM } from '../store/mutation-types'

const tryFetchTeams = (team_name) => {
  if (!store.state.teams_fetched && !store.state.teams_fetching) {
    store.dispatch('v2_action_fetchTeams', { team_name })
  }
}

const tryFetchMembers = (team_name) => {
  if (!store.state.members.is_fetched && !store.state.members.is_fetching) {
    store.dispatch('members/fetch', { team_name })
  }
}

const tryFetchConnections = (team_name) => {
  if (!store.state.connections.is_fetched && !store.state.connections.is_fetching) {
    store.dispatch('connections/fetch', { team_name })
  }
}

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
  const redirectToSignIn = () => {
    next({
      name: ROUTE_SIGNIN_PAGE,
      query: { redirect: to.fullPath }
    })
  }

  const goNext = () => {
    var active_username = store.getters.getActiveUsername
    var team_name = to.params.team_name || active_username

    store.commit(CHANGE_ACTIVE_TEAM, team_name)

    tryFetchTeams(team_name)
    tryFetchMembers(team_name)
    tryFetchConnections(team_name)
    next()
  }

  // update the active document in the store
  store.commit(CHANGE_ACTIVE_DOCUMENT, to.params.object_name || to.name)

  if (store.state.active_user_eid.length > 0) {
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
    } else {
      // the 'init session' route already attempts to fetch the current user; we're done
      if (to.name == ROUTE_INITSESSION_PAGE) {
        next()
        return
      }

      // this route does not require authentication; try to sign in just to make
      // sure we know who the active user is and move to the next route
      store.dispatch('v2_action_fetchCurrentUser').then(response => {
        if (store.state.active_user_eid.length > 0) {
          // user is signed in; move to the next route
          goNext()
        }
      }).catch(error => {
        next()
      })
    }
  }
})
