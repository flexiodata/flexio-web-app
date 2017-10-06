import {
  ROUTE_HOME,
  ROUTE_HOME_DASHBOARD,
  ROUTE_HOME_PIPES,
  ROUTE_HOME_MEMBERS,
  ROUTE_HOME_CONNECTIONS,
  ROUTE_HOME_STORAGE,
  ROUTE_HOME_STORAGE_OLD,
  ROUTE_HOME_TRASH
} from '../constants/route'
import * as types from '../store/mutation-types'
import store from '../store'
import AppHome from '../components/AppHome.vue'
import AppDashboard from '../components/AppDashboard.vue'
import PipeManager from '../components/PipeManager.vue'
import MemberManager from '../components/MemberManager.vue'
import StorageManager from '../components/StorageManager.vue'
import TrashManager from '../components/TrashManager.vue'

export default {
  path: '/home',
  component: AppHome,
  meta: { requiresAuth: true },
  children: [
    {
      // redirect to /home/dashboard
      path: '',
      name: ROUTE_HOME,
      redirect: '/dashboard'
    },
    {
      path: '/dashboard',
      name: ROUTE_HOME_DASHBOARD,
      component: AppDashboard
    },
    {
      path: '/pipes',
      name: ROUTE_HOME_PIPES,
      component: PipeManager
    },
    {
      path: '/members',
      name: ROUTE_HOME_MEMBERS,
      component: MemberManager
    },
    {
      path: '/connections',
      name: ROUTE_HOME_CONNECTIONS,
      component: { template: '<div class="pa4">TODO: Connections</div>' }
    },
    {
      path: '/storage',
      name: ROUTE_HOME_STORAGE,
      component: { template: '<div class="pa4">TODO: Storage</div>' }
    },
    {
      path: '/storage-old',
      name: ROUTE_HOME_STORAGE_OLD,
      component: StorageManager
    },
    {
      path: '/trash',
      name: ROUTE_HOME_TRASH,
      component: TrashManager
    }
  ]
}
