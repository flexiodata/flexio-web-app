import { ROUTE_EMBED } from '../constants/route'
import EmbedHome from '../components/EmbedHome.vue'

export default {
  path: '/embed/:eid',
  name: ROUTE_EMBED,
  component: EmbedHome
}
