import store from '../store'
import { ROUTE_BUILDER } from '../constants/route'
import BuilderPage from '../components/BuilderPage.vue'

export default {
  path: '/builder/:template',
  name: ROUTE_BUILDER,
  component: BuilderPage,
  meta: { requiresAuth: true }
}
