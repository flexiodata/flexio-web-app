import { ROUTE_ACTIVITY } from '../constants/route'
import AppActivity from '../components/AppActivity.vue'

export default {
  path: '/activity',
  name: ROUTE_ACTIVITY,
  component: AppActivity,
  meta: { requiresAuth: true }
}
