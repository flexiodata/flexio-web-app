import store from '../store'
import { ROUTE_BUILDER } from '../constants/route'
import BuilderDocument from '../components/BuilderDocument.vue'

export default {
  path: '/builder/:template',
  name: ROUTE_BUILDER,
  component: BuilderDocument,
  meta: { requiresAuth: true }
}
