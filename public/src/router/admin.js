import store from '../store'
import * as types from '../store/mutation-types'
import { ROUTE_DEV } from '../constants/route'
import AdminHome from '../components/AdminHome.vue'
import AdminDashboard from '../components/AdminDashboard.vue'
import DevParser from '../components/DevParser.vue'
import DevCode from '../components/DevCode.vue'
import AdminTests from '../components/AdminTests.vue'
import DevModals from '../components/DevModals.vue'
import DevPlayground from '../components/DevPlayground.vue'

export default {
  path: '/admin',
  component: AdminHome,
  meta: { requiresAuth: true },
  children: [
    {
      // redirect to /admin/parser
      path: '',
      name: ROUTE_DEV,
      redirect: 'dashboard'
    },
    {
      // AdminDashboard will be rendered inside AdminHome's <router-view>
      // /admin/dashboard is matched
      path: 'dashboard',
      component: AdminDashboard
    },
    {
      // AdminTests will be rendered inside AdminHome's <router-view>
      // /admin/tests is matched
      path: 'tests',
      component: AdminTests
    },
    {
      // DevParser will be rendered inside AdminHome's <router-view>
      // /admin/parser is matched
      path: 'parser',
      component: DevParser
    },
    {
      // DevCode will be rendered inside AdminHome's <router-view>
      // /admin/code is matched
      path: 'code',
      component: DevCode
    },
    {
      // DevModals will be rendered inside AdminHome's <router-view>
      // /admin/modals is matched
      path: 'modals',
      component: DevModals
    },
    {
      // DevPlayground will be rendered inside AdminHome's <router-view>
      // /admin/playground is matched
      path: 'playground',
      component: DevPlayground
    }
  ]
}
