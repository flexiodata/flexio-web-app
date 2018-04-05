import store from '../store'
import * as types from '../store/mutation-types'
import { ROUTE_ADMIN } from '../constants/route'
import AdminHome from '../components/AdminHome.vue'
import AdminDashboard from '../components/AdminDashboard.vue'
import AdminTests from '../components/AdminTests.vue'
import AdminCodeTranslator from '../components/AdminCodeTranslator.vue'
import AdminCode from '../components/AdminCode.vue'
import AdminModals from '../components/AdminModals.vue'

export default {
  path: '/admin',
  component: AdminHome,
  meta: { requiresAuth: true },
  children: [
    {
      // redirect to /admin/tests
      path: '',
      name: ROUTE_ADMIN,
      redirect: 'tests'
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
      // AdminCodeTranslator will be rendered inside AdminHome's <router-view>
      // /admin/translator is matched
      path: 'translator',
      component: AdminCodeTranslator
    },
    {
      // AdminCode will be rendered inside AdminHome's <router-view>
      // /admin/code is matched
      path: 'code',
      component: AdminCode
    },
    {
      // AdminModals will be rendered inside AdminHome's <router-view>
      // /admin/modals is matched
      path: 'modals',
      component: AdminModals
    }
  ]
}
