import {
  ROUTE_HOME,
  ROUTE_HOMEDASHBOARD,
  ROUTE_HOMEPIPES,
  ROUTE_HOMEMEMBERS,
  ROUTE_HOMECONNECTIONS,
  ROUTE_HOMESTORAGE,
  ROUTE_HOMETRASH
} from '../constants/route'
import * as types from '../store/mutation-types'
import store from '../store'
import AppHome from '../components/AppHome.vue'
import HomeDashboard from '../components/HomeDashboard.vue'
import PipeManager from '../components/PipeManager.vue'
import MemberManager from '../components/MemberManager.vue'
import StorageManager from '../components/StorageManager.vue'
import TrashManager from '../components/TrashManager.vue'

export const basepath_redirect = {
  path: '/',
  component: AppHome,
  redirect: '/home' // TODO: use 'alias' here instead of 'redirect'?
}

export const home = {
  path: '/home',
  component: AppHome,
  meta: { requiresAuth: true },
  children: [
    {
      // redirect to /home/dashboard
      path: '',
      name: ROUTE_HOME,
      redirect: 'dashboard'
    },
    {
      path: 'dashboard',
      name: ROUTE_HOMEDASHBOARD,
      component: HomeDashboard
    },
    {
      path: 'pipes',
      name: ROUTE_HOMEPIPES,
      component: PipeManager
    },
    {
      path: 'members',
      name: ROUTE_HOMEMEMBERS,
      component: MemberManager
    },
    {
      path: 'sharing',
      component: { template: '<div class="pa4">TODO: Sharing</div>' }
    },
    {
      path: 'connections',
      name: ROUTE_HOMECONNECTIONS,
      component: { template: '<div class="pa4">TODO: Connections</div>' }
    },
    {
      path: 'storage',
      name: ROUTE_HOMESTORAGE,
      component: StorageManager
    },
    {
      path: 'trash',
      name: ROUTE_HOMETRASH,
      component: TrashManager
    }
  ]
}
