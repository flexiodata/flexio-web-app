import { ROUTE_PIPE_PAGE } from '../constants/route'
import PipeDocument from '../components/PipeDocument.vue'

export default {
  path: '/pipes/:identifier/:view?/:state?',
  name: ROUTE_PIPE_PAGE,
  component: PipeDocument,
  meta: { requiresAuth: true }
}
