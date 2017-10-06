import store from '../store'
import { ROUTE_ACCOUNT } from '../constants/route'
import Account from '../components/Account.vue'

export default {
  path: '/account',
  name: ROUTE_ACCOUNT,
  component: Account,
  meta: { requiresAuth: true }
}
