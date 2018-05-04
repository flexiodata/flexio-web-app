import { ROUTE_PIPES } from '../constants/route'
import PipeHome from '../components/PipeHome.vue'

export default {
  path: '/pipes/:eid/:view?/:state?',
  name: ROUTE_PIPES,
  component: PipeHome,
  meta: { requiresAuth: true }
}
