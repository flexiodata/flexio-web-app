import { ROUTE_APP_ADMIN } from '@/constants/route'

import AppAdmin from '@/components/AppAdmin'
import AdminTest from '@/components/AdminTest'
import AdminProcessActivity from '@/components/AdminProcessActivity'
import AdminUserActivity from '@/components/AdminUserActivity'

export default {
  path: '/admin',
  component: AppAdmin,
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
    }
  ]
}
