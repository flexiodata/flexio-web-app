import {
  ROUTE_PROJECTPIPES,
  ROUTE_PROJECTMEMBERS,
  ROUTE_PROJECTCONNECTIONS,
  ROUTE_PROJECTTRASH
} from '../constants/route'
import * as types from '../store/mutation-types'
import store from '../store'
import ProjectHome from '../components/ProjectHome.vue'
import PipeManager from '../components/PipeManager.vue'
import MemberManager from '../components/MemberManager.vue'
import ConnectionManager from '../components/ConnectionManager.vue'
import TrashManager from '../components/TrashManager.vue'

export default {
  path: '/project/:eid',
  component: ProjectHome,
  meta: { requiresAuth: true },
  beforeEnter: (to, from, next) => {
    // update the active project in the store
    store.commit(types.CHANGE_ACTIVE_PROJECT, to.params.eid)

    // move to the next route
    next()
  },
  children: [
    {
      // redirect to /project/:eid/pipes
      path: '',
      redirect: 'pipes'
    },
    {
      // PipeManager will be rendered inside ProjectHome's <router-view>
      // when /project/:eid/pipes is matched
      path: 'pipes',
      name: ROUTE_PROJECTPIPES,
      component: PipeManager
    },
    {
      // MemberManager will be rendered inside ProjectHome's <router-view>
      // when /project/:eid/members is matched
      path: 'members',
      name: ROUTE_PROJECTMEMBERS,
      component: MemberManager
    },
    {
      // ConnectionManager will be rendered inside ProjectHome's <router-view>
      // when /project/:eid/connections is matched
      path: 'connections',
      name: ROUTE_PROJECTCONNECTIONS,
      component: ConnectionManager
    },
    {
      // TrashManager will be rendered inside ProjectHome's <router-view>
      // when /project/:eid/trash is matched
      path: 'trash',
      name: ROUTE_PROJECTTRASH,
      component: TrashManager
    }
  ]
}
