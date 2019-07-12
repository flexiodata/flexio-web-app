import store from '@/store'
import * as types from '@/store/mutation-types'
import { ROUTE_APP_ADMIN } from '@/constants/route'
import AdminHome from '@comp/AdminHome'
import AdminActivity from '@comp/AdminActivity'
import AdminTest from '@comp/AdminTest'
import AdminActivityByUser from '@comp/AdminActivityByUser'
import AdminBuilder from '@comp/AdminBuilder'
import AdminCodeTranslator from '@comp/AdminCodeTranslator'
import AdminCode from '@comp/AdminCode'
import AdminModals from '@comp/AdminModals'
import AdminPrototype from '@comp/AdminPrototype'

export default {
  path: '/admin',
  component: AdminHome,
  meta: { requiresAuth: true },
  children: [
    {
      // redirect to /admin/users
      path: '',
      name: ROUTE_APP_ADMIN,
      redirect: 'users'
    },
    {
      path: 'tests',
      component: AdminTest
    },
    {
      path: 'processes',
      component: AdminActivity
    },
    {
      path: 'users',
      component: AdminActivityByUser
    },
    {
      path: 'builder',
      component: AdminBuilder
    },
    {
      path: 'prototype',
      component: AdminPrototype
    }/*,
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
    }
    */
  ]
}
