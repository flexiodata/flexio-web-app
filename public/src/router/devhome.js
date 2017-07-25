import store from '../store'
import * as types from '../store/mutation-types'
import { ROUTE_DEVHOME } from '../constants/route'
import DevHome from '../components/DevHome.vue'
import DevDashboard from '../components/DevDashboard.vue'
import DevParser from '../components/DevParser.vue'
import DevCode from '../components/DevCode.vue'
import DevTests from '../components/DevTests.vue'
import DevModals from '../components/DevModals.vue'
import DevPlayground from '../components/DevPlayground.vue'

export default {
  path: '/dev',
  component: DevHome,
  meta: { requiresAuth: true },
  children: [
    {
      // redirect to /dev/parser
      path: '',
      name: ROUTE_DEVHOME,
      redirect: 'dashboard',
      component: DevTests
    },
    {
      // DevDashboard will be rendered inside DevHome's <router-view>
      // /dev/dashboard is matched
      path: 'dashboard',
      component: DevDashboard
    },
    {
      // DevTests will be rendered inside DevHome's <router-view>
      // /dev/tests is matched
      path: 'tests',
      component: DevTests
    },
    {
      // DevParser will be rendered inside DevHome's <router-view>
      // /dev/parser is matched
      path: 'parser',
      component: DevParser
    },
    {
      // DevCode will be rendered inside DevHome's <router-view>
      // /dev/code is matched
      path: 'code',
      component: DevCode
    },
    {
      // DevModals will be rendered inside DevHome's <router-view>
      // /dev/modals is matched
      path: 'modals',
      component: DevModals
    },
    {
      // DevPlayground will be rendered inside DevHome's <router-view>
      // /dev/playground is matched
      path: 'playground',
      component: DevPlayground
    }
  ]
}
