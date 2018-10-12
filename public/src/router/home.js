import {
  ROUTE_APP_HOME,
  ROUTE_APP_DASHBOARD,
  ROUTE_APP_LEARN,
  ROUTE_APP_PIPES,
  ROUTE_APP_CONNECTIONS,
  ROUTE_APP_STORAGE
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
      name: ROUTE_APP_HOME,
      redirect: '/pipes'
    },
    {
      path: '/pipes',
      name: ROUTE_APP_PIPES,
      component: AppPipes
    },
    {
      path: '/connections',
      name: ROUTE_APP_CONNECTIONS,
      component: AppConnections
    },
    {
      path: '/storage',
      name: ROUTE_APP_STORAGE,
      component: AppStorage
    },
    {
      path: '/activity',
      name: ROUTE_APP_ACTIVITY,
      component: AppActivity
    }
  ]
}
