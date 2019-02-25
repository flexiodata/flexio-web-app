import { ROUTE_ACTIVITY_LIST_PAGE } from '../constants/route'
import AppActivity from '../components/AppActivity.vue'

export default {
  path: '/activity',
  name: ROUTE_ACTIVITY_LIST_PAGE,
  component: AppActivity,
  meta: { requiresAuth: true }
}
