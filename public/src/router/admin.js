import { ROUTE_APP_ADMIN } from '@/constants/route'
import AdminHome from '@comp/AdminHome'
import AdminTest from '@comp/AdminTest'
import AdminActivity from '@comp/AdminActivity'
import AdminActivityByUser from '@comp/AdminActivityByUser'
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
      path: 'prototype',
      component: AdminPrototype
    }
  ]
}
