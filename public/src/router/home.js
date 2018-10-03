import {
  ROUTE_HOME,
  ROUTE_HOME_DASHBOARD,
  ROUTE_HOME_LEARN,
  ROUTE_HOME_DOCS,
  ROUTE_HOME_PIPES,
  ROUTE_HOME_MEMBERS,
  ROUTE_HOME_CONNECTIONS,
  ROUTE_HOME_STORAGE
} from '../constants/route'
import * as types from '../store/mutation-types'
import store from '../store'
import AppHome from '../components/AppHome.vue'
import AppDashboard from '../components/AppDashboard.vue'
import AppLearn from '../components/AppLearn.vue'
import AppDocs from '../components/AppDocs.vue'
import AppPipes from '../components/AppPipes.vue'
import AppConnections from '../components/AppConnections.vue'
import AppStorage from '../components/AppStorage.vue'

export default {
  path: '/home',
  component: AppHome,
  meta: { requiresAuth: true },
  children: [
    {
      // redirect to /pipes
      path: '',
      name: ROUTE_HOME,
      redirect: '/pipes'
    },
    {
      path: '/dashboard',
      name: ROUTE_HOME_DASHBOARD,
      component: AppDashboard
    },
    {
      path: '/learn',
      name: ROUTE_HOME_LEARN,
      component: AppLearn
    },
    {
      path: '/docs',
      name: ROUTE_HOME_DOCS,
      component: AppDocs,
      alias: '/documentation'
    },
    {
      path: '/pipes',
      name: ROUTE_HOME_PIPES,
      component: AppPipes
    },
    {
      path: '/connections',
      name: ROUTE_HOME_CONNECTIONS,
      component: AppConnections
    },
    {
      path: '/storage',
      name: ROUTE_HOME_STORAGE,
      component: AppStorage
    }
  ]
}
