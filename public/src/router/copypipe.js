import { ROUTE_COPYPIPE } from '../constants/route'
import CopyPipe from '../components/CopyPipe.vue'

export default {
  path: '/copypipe',
  name: ROUTE_COPYPIPE,
  component: CopyPipe,
  meta: { requiresAuth: true }
}
