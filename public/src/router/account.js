import store from '../store'
import { ROUTE_ACCOUNT } from '../constants/route'
import AppAccount from '../components/AppAccount.vue'

export default {
  path: '/account/:section?',
  name: ROUTE_ACCOUNT,
  component: AppAccount,
  meta: { requiresAuth: true }
}
