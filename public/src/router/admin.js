import { ROUTE_APP_ADMIN } from '@/constants/route'
import AdminHome from '@/components/AdminHome'
import AdminTest from '@/components/AdminTest'
import AdminActivity from '@/components/AdminActivity'
import AdminActivityByUser from '@/components/AdminActivityByUser'
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
