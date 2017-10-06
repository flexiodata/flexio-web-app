import { ROUTE_PIPEHOME } from '../constants/route'
import PipeHome from '../components/PipeHome.vue'

export default {
  path: '/pipes/:eid/:view?/:state?',
  name: ROUTE_PIPEHOME,
  component: PipeHome/*,
  meta: { requiresAuth: true }*/
}
