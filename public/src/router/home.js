import store from '../store'
import * as types from '../store/mutation-types'
import { ROUTE_HOME } from '../constants/route'
import AppHome from '../components/AppHome.vue'

export const basepath_redirect = {
  path: '/',
  component: AppHome,
  redirect: '/home' // TODO: use 'alias' here instead of 'redirect'?
}

export const home = {
  path: '/home',
  name: ROUTE_HOME,
  component: AppHome,
  meta: { requiresAuth: true },
  beforeEnter: (to, from, next) => {
    // update the active project in the store
    store.commit(types.CHANGE_ACTIVE_PROJECT, '')

    // move to the next route
    next()
  }
}
