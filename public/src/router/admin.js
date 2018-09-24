import store from '../store'
import * as types from '../store/mutation-types'
import { ROUTE_ADMIN } from '../constants/route'
import AdminHome from '../components/AdminHome.vue'
import AdminDashboard from '../components/AdminDashboard.vue'
import AdminTest from '../components/AdminTest.vue'
import AdminBuilder from '../components/AdminBuilder.vue'
import AdminCodeTranslator from '../components/AdminCodeTranslator.vue'
import AdminCode from '../components/AdminCode.vue'
import AdminModals from '../components/AdminModals.vue'
import AdminPrototype from '../components/AdminPrototype.vue'

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
      path: 'dashboard',
      component: AdminDashboard
    },
    {
      path: 'tests',
      component: AdminTest
    },
    {
      path: 'builder',
      component: AdminBuilder
    },
    {
      path: 'translator',
      component: AdminCodeTranslator
    },
    {
      path: 'code',
      component: AdminCode
    },
    {
      path: 'modals',
      component: AdminModals
    },
    {
      path: 'prototype',
      component: AdminPrototype
    }
  ]
}
