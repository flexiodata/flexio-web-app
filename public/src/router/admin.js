import { ROUTE_APP_ADMIN } from '@/constants/route'
import AdminHome from '@/components/AdminHome'
import AdminTest from '@/components/AdminTest'
import AdminProcessActivity from '@/components/AdminProcessActivity'
import AdminUserActivity from '@/components/AdminUserActivity'
import AdminPrototype from '@/components/AdminPrototype'

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
      component: AdminProcessActivity
    },
    {
      path: 'users',
      component: AdminUserActivity
    },
    {
      path: 'prototype',
      component: AdminPrototype
    }
  ]
}
