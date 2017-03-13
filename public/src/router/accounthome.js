import store from '../store'
import { CHANGE_ACTIVE_PROJECT } from '../store/mutation-types'
import { ROUTE_ACCOUNT } from '../constants/route'
import Account from '../components/Account.vue'

export default {
  path: '/account',
  name: ROUTE_ACCOUNT,
  component: Account,
  beforeEnter: (to, from, next) => {
    // update the active project in the store
    store.commit(CHANGE_ACTIVE_PROJECT, '')

    // move to the next route
    next()
  }
}
