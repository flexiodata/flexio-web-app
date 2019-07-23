import router from '@/router' // VueRouter
import store from '@/store' // Vuex store
import { ROUTE_INITSESSION_PAGE, ROUTE_SIGNIN_PAGE } from '../constants/route'
import { CHANGE_ACTIVE_DOCUMENT } from '../store/mutation-types'

const tryFetchTeams = (team_name) => {
  if (!store.state.teams.is_fetched && !store.state.teams.is_fetching) {
    store.dispatch('teams/fetch', { team_name })
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
  var team_name = to.params.team_name

  const redirectToSignIn = () => {
    next({
      name: ROUTE_SIGNIN_PAGE,
      query: { redirect: to.fullPath }
    })
  }

  const goNext = () => {
    var active_user = _.cloneDeep(store.getters['users/getActiveUser'])
    var active_username = active_user.username
    team_name = team_name || active_username

    store.commit('teams/CHANGE_ACTIVE_TEAM', team_name)

    tryFetchTeams(team_name)
    tryFetchMembers(team_name)
    tryFetchConnections(team_name)
    next()
  }

  // update the active document in the store
  store.commit(CHANGE_ACTIVE_DOCUMENT, to.params.object_name || to.name)

  if (store.state.users.active_user_eid.length > 0) {
    // user is signed in; move to the next route
    goNext()
  } else {
    // we're already fetching the user; we're done
    if (store.state.users.is_signing_in) {
      return
    }

    if (to.matched.some(record => record.meta.requiresAuth)) {
      // this route requires authentication; check if the user is signed in...
      store.dispatch('users/fetch', { eid: 'me' }).then(response => {
        if (store.state.users.active_user_eid.length > 0) {
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
      store.dispatch('users/fetch', { eid: 'me' }).then(response => {
        if (store.state.users.active_user_eid.length > 0) {
          // user is signed in; move to the next route
          goNext()
        }
      }).catch(error => {
        next()
      })
    }
  }
})
