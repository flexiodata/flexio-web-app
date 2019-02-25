import store from '../store'
import { ROUTE_ACCOUNT_PAGE } from '../constants/route'
import AppAccount from '../components/AppAccount.vue'

export default {
  path: '/account/:section?',
  name: ROUTE_ACCOUNT_PAGE,
  component: AppAccount,
  meta: { requiresAuth: true }
}
