import {
  ROUTE_HOME_PAGE,
  ROUTE_DASHBOARD_PAGE,
  ROUTE_LEARN_PAGE,
  ROUTE_PIPE_LIST_PAGE,
  ROUTE_CONNECTION_LIST_PAGE,
  ROUTE_STORAGE_PAGE,
  ROUTE_ACTIVITY_LIST_PAGE
} from '../constants/route'
import * as types from '../store/mutation-types'
import store from '../store'
import AppHome from '../components/AppHome.vue'
import AppPipes from '../components/AppPipes.vue'
import AppConnections from '../components/AppConnections.vue'
import AppStorage from '../components/AppStorage.vue'
import AppActivity from '../components/AppActivity.vue'

export default {
  path: '/home',
  component: AppHome,
  meta: { requiresAuth: true },
  children: [
    {
      // redirect to /pipes
      path: '',
      name: ROUTE_HOME_PAGE,
      redirect: '/pipes'
    },
    {
      path: '/pipes',
      name: ROUTE_PIPE_LIST_PAGE,
      component: AppPipes
    },
    {
      path: '/connections',
      name: ROUTE_CONNECTION_LIST_PAGE,
      component: AppConnections
    },
    {
      path: '/storage',
      name: ROUTE_STORAGE_PAGE,
      component: AppStorage
    },
    {
      path: '/activity',
      name: ROUTE_ACTIVITY_LIST_PAGE,
      component: AppActivity
    }
  ]
}
