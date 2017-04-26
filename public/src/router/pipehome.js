import { ROUTE_PIPEHOME } from '../constants/route'
import PipeHome from '../components/PipeHome.vue'

export default {
  path: '/pipe/:eid/:mode?',
  name: ROUTE_PIPEHOME,
  component: PipeHome
}
