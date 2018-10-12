import store from '../store'
import { ROUTE_ACCOUNT } from '../constants/route'
import AccountHome from '../components/AccountHome.vue'

export default {
  path: '/account/:section?',
  name: ROUTE_ACCOUNT,
  component: AccountHome,
  meta: { requiresAuth: true }
}
