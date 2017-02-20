import { ROUTE_EMBEDHOME } from '../constants/route'
import EmbedHome from '../components/EmbedHome.vue'

export default {
  path: '/embed/:eid',
  name: ROUTE_EMBEDHOME,
  component: EmbedHome
}
