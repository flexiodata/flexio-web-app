import store from '../store'
import { ROUTE_BUILDER_PAGE } from '../constants/route'
import BuilderDocument from '../components/BuilderDocument.vue'

export default {
  path: '/builder/:template',
  name: ROUTE_BUILDER_PAGE,
  component: BuilderDocument,
  meta: { requiresAuth: true }
}
