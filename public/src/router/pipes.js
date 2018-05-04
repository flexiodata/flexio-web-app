import { ROUTE_PIPES } from '../constants/route'
import PipeDocument from '../components/PipeDocument.vue'

export default {
  path: '/pipes/:eid/:view?/:state?',
  name: ROUTE_PIPES,
  component: PipeDocument,
  meta: { requiresAuth: true }
}
